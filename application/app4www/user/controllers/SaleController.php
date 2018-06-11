<?php

class SaleController extends Kyapi_Controller_Action
{
    public function preDispatch() {
        $this->view->cur_pos = 'info';
        //清除 免登陆session
        $this->IsAuth($this->view->visitor);

        if (empty($this->view->userID)) {
            // 提示：请先登录系统
            Mobile_Browser::redirect($this->view->translate('tip_login_please'), $this->view->seed_Setting['user_app_server'] . "/login");
        }
        $this->view->cur_pos = $this->_request->getParam('controller');
        $cururl = $this->getRequestUri();
        if ($cururl == '/sale') {
            $this->_request->setParam('all', 'all');
            $this->indexAction();
            exit;
        }

        preg_match('/(.*)\.html/', $cururl, $arr);

        if (isset($arr[1]) && !empty($arr[1])) {

            preg_match_all('/^\/user\/sale\/(index|top|price)-([\d]+)-([\d]+).html/isU', $cururl, $arr);
            if (is_array($arr) && count($arr) > 1) {
                $this->_request->setParam('status', $arr[2][0]);
                $this->_request->setParam('page', $arr[3][0]);
                $this->indexAction();
                exit;
            }
            //没有找到相关信息！
            Mobile_Browser::redirect($this->view->translate('tip_find_no'), $this->view->seed_BaseUrl . "/");
        }
    }

    public function indexAction() {
        $this->view->resultMsg = $this->_request->getParam('resultMsg');

        //统计所有商品数量
        $resultObject = $this->json->countSaleOrderStatusApi($this->_requestObject);
        $countOrder = $this->objectToArray(json_decode($resultObject));
        $this->view->countOrder = $countOrder['result'];

        //开始处理【顶部】最新订单状态查询
        $orderNEW = $this->json->getQuickSaleOrderApi($this->_requestObject);
        $existNEW = json_decode($orderNEW);
        $existNEWtt = $this->objectToArray($existNEW);
        $this->view->newE = $existNEWtt['result'];
        $this->view->vestut = $existNEWtt['result']['vendorExecStatus'];
        $this->view->veorderID = $existNEWtt['result']['orderID'];
        //订单进度
        $this->view->plan = $this->planAction($existNEWtt['result']);

        // bootstrap-table查询状态
        if (empty($this->_request->getParam('orderStatus'))) {
            $this->view->orderStatus = '04';
        } else {
            $this->view->orderStatus = $this->_request->getParam('orderStatus');
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/sale/index.phtml");
            echo $content;
            exit;
        }
    }

    public function saleOrderListAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $queryParams = array();
        $orderStatus = strval($this->_request->getParam('orderStatus'));
        if (empty($orderStatus)) {
            $orderStatus = '04';
        }
        $queryParams['orderStatus'] = $orderStatus;

        $querySorts = array();
        // $querySorts['createTime'] = "DESC";

        $keyword = $this->_request->getParam('keyword');
        if (empty($keyword)) {
            $keyword = null;
        }

        $limit = $this->_request->getParam('limit');
        if (empty($limit) || $limit <= 0) {
            $limit = 10;
        }

        $skip = $this->_request->getParam('skip');
        if (empty($limit) || $limit <= 0) {
            $skip = 0;
        }

        if (is_array($queryParams)) {
            $queryParams = $this->arrayToObject($queryParams);
        }

        if (is_array($querySorts)) {
            $querySorts = $this->arrayToObject($querySorts);
        }

        $resultObject = $this->json->listSaleOrderApi($requestObject, $orderStatus, $querySorts, $keyword, $skip, $limit);
        $msg["total"] = json_decode($resultObject)->extData->totalSize;
        $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    public function countSaleOrderAjaxAction() {
        $requestObject = $this->_requestObject;

        $resultObject = $this->json->countSaleOrderStatusApi($requestObject);
        $msg = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    /*新增订单*/
    public function addAction() {
        /*获取默认联系人 start*/
        $_queryP = new queryAccount();
        /**contactStatus 01有效 02禁用*/
        $_queryP->contactStatus = "01";

        // 请求Hessian服务端方法
        $userKY = $this->json->listContactApi($this->_requestObject, $_queryP);
        $userData = $this->objectToArray(json_decode($userKY));
        $userList = $userData['result'];

        foreach ($userList as $k => $v) {
            if ($v['isPublicContact'] == true) {
                $this->view->dfContactName = $v['name'];
                $this->view->dfContactID = $v['contactID'];
            }
        }
        /*获取默认联系人 END*/

        if ($this->_request->isPost()) {
            //获取附件ID
            $Atachlist = array();
            $Atachlist["attachID"] = $this->_request->getParam('attachNid');
            $Atachlist["attachType"] = $this->_request->getParam('attachType');
            $Atachlist["bizType"] = $this->_request->getParam("bizType");
            $Atachlist["attachName"] = $this->_request->getParam("attachName");
            $Atachlist["attachSize"] = $this->_request->getParam("attachSize");
            $_attach2 = array();
            foreach ($Atachlist as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $_attach2[$k1][$k] = $v1;
                }
            }
            $_attachList = array();
            foreach ($_attach2 as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $_attachList[$k] = new Kyapi_Model_Attachment();
                    $_attachList[$k]->attachID = $_attach2[$k]['attachID'];
                    $_attachList[$k]->attachType = 'ODOD';
                    $_attachList[$k]->bizType = 'OD';
                    $_attachList[$k]->name = $_attach2[$k]['attachName'];
                    $_attachList[$k]->size = (int)$_attach2[$k]['attachSize'];

                }
            }


            //订单商品列表
            $iterm = array();
            //				$iterm["orderID"] =  $this->_request->getParam("orderID");    //新增不存在
            //				$iterm["totalVolume"] = $this->_request->getParam("totalVolume"); 体积
            $iterm["hscode"] = $this->_request->getParam('hscode');
            $iterm["itemID"] = null;//
            $iterm["productID"] = $this->_request->getParam("productID");
            $iterm["supplierID"] = $this->_request->getParam("supplierID");
            $iterm["packingType"] = $this->_request->getParam('packingType');
            $iterm["productName"] = $this->_request->getParam("productName");
            $iterm["productEnName"] = $this->_request->getParam("productEnName");
            $iterm["productSize"] = $this->_request->getParam("productSize");
            $iterm["pricingUnit"] = $this->_request->getParam("pricingUnit");
            $iterm["quantity"] = $this->_request->getParam("quantity");
            $iterm["grossWeight"] = $this->_request->getParam("grossWeight");
            $iterm["netWeight"] = $this->_request->getParam("netWeight");
            $iterm["totalGrossWeight"] = $this->_request->getParam("totalGrossWeight");
            $iterm["totalNetWeight"] = $this->_request->getParam("totalNetWeight");
            $iterm["totalPackage"] = $this->_request->getParam("totalPackage");
            $iterm["totalPrice"] = $this->_request->getParam("totalPrice");
            $iterm["unitPrice"] = $this->_request->getParam("unitPrice");
            $iterm["purUnitPrice"] = $this->_request->getParam("purUnitPrice");
            $iterm["totalPurPrice"] = $this->_request->getParam("totalPurPrice");
            $iterm["productBrand"] = $this->_request->getParam("productBrand");
            $iterm["productModel"] = $this->_request->getParam("productModel");  //商品型号
            $iterm["productionMode"] = $this->_request->getParam("productionMode");//商品生产方式
            $iterm["functionUsage"] = $this->_request->getParam("functionUsage");
            $iterm["productMaterial"] = $this->_request->getParam("productMaterial");

            //组装orderIterm商品列表
            $it2 = array();
            foreach ($iterm as $key => $value) {
                foreach ($value as $k => $v) {
                    $it2[$k][$key] = $v;
                }
            }

            $it3 = array();
            foreach ($it2 as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $it3[$k] = new Kyapi_Model_OrderItem();
                    $it3[$k]->hscode = $it2[$k]['hscode'];
                    $it3[$k]->productID = $it2[$k]['productID'];
                    $it3[$k]->itemID = $it2[$k]['itemID'];
                    $it3[$k]->supplierID = $it2[$k]['supplierID'];
                    $it3[$k]->quantity = (double)$it2[$k]['quantity'];
                    $it3[$k]->grossWeight = (double)$it2[$k]['grossWeight'];
                    $it3[$k]->netWeight = (double)$it2[$k]['netWeight'];
                    $it3[$k]->totalGrossWeight = (double)$it2[$k]['totalGrossWeight'];
                    $it3[$k]->totalNetWeight = (double)$it2[$k]['totalNetWeight'];
                    $it3[$k]->totalPackage = (int)$it2[$k]['totalPackage'];
                    $it3[$k]->totalPrice = (double)$it2[$k]['totalPrice'];
                    $it3[$k]->totalPurPrice = (double)$it2[$k]['totalPurPrice'];
                    //						$it3[$k]->totalVolume=(double)$it2[$k]['totalVolume'];
                    $it3[$k]->unitPrice = (double)$it2[$k]['unitPrice'];
                    $it3[$k]->purUnitPrice = (double)$it2[$k]['purUnitPrice'];
                    $it3[$k]->productName = $it2[$k]['productName'];
                    $it3[$k]->productEnName = $it2[$k]['productEnName'];
                    $it3[$k]->productSize = $it2[$k]['productSize'];
                    $it3[$k]->pricingUnit = $it2[$k]['pricingUnit'];
                    $it3[$k]->productMaterial = $it2[$k]['productMaterial'];
                    $it3[$k]->functionUsage = $it2[$k]['functionUsage'];
                    $it3[$k]->productBrand = $it2[$k]['productBrand'];
                    $it3[$k]->productModel = $it2[$k]['productModel'];
                    $it3[$k]->packingType = $it2[$k]['packingType'];
                    $it3[$k]->productMaterial = $it2[$k]['productMaterial'];
                    $it3[$k]->functionUsage = $it2[$k]['functionUsage'];
                    $it3[$k]->productionMode = $it2[$k]['productionMode'];
                    if ($it2[$k]['productionMode'] == "01") {
                        $it3[$k]->isOwnProduct = true;
                    } else {
                        $it3[$k]->isOwnProduct = false;
                    };


                }
            }

            //装柜数量
            $orderMM = array();
            $orderMM["20GP"] = (int)$this->_request->getParam("sizeQuantityMap_20GP");
            $orderMM["40GP"] = (int)$this->_request->getParam("sizeQuantityMap_40GP");
            $orderMM["40HP"] = (int)$this->_request->getParam("sizeQuantityMap_40HP");
            $orderMM["45HP"] = (int)$this->_request->getParam("sizeQuantityMap_45HP");
            $orderMM["20OT"] = (int)$this->_request->getParam("sizeQuantityMap_20OT");
            $orderMM["40OT"] = (int)$this->_request->getParam("sizeQuantityMap_40OT");

            //获取当前订单时间
            $ddtime = $this->_request->getParam("deliveryDate");
            if (!empty($ddtime)) {
                $date3 = date("Y-m-d\TH:i:s", strtotime($ddtime));
            } else {
                $date3 = date("Y-m-d\TH:i:s", time());
            }

            $_order = new Kyapi_Model_order();
            //   $_order->orderID = $_orderIDget;  //订单ID
            //委托方信息公司ID、公司名称、联系人、联系人ID（本方联系人CRNCODE货币 不做传递服务端）
            $_order->vendor = $this->view->accountID;
            $_order->vendorName = $this->_request->getParam("accountName");
            $_order->vendorContactID = $this->_request->getParam("vendorContactID");
            $_order->vendorContactName = $this->_request->getParam("vendorContactName");
            // $_order->vendorCrnCode = $this->_request->getParam("CrnCode");
            //客户方信息 ID、Name、货币（Crncode）、联系人ID、联系人姓名
            $_order->buyer = $this->_request->getParam("buyer");
            $_order->buyerName = $this->_request->getParam("buyerName");
            $_order->buyerCrnCode = $this->_request->getParam("buyerCrnCode");
            $_order->buyerContactID = $this->_request->getParam("buyerContactID");
            $_order->buyerContactName = $this->_request->getParam("buyerContactName");

            $_order->vendorOrderRequest = $this->_request->getParam("vendorOrderRequest");// 订单要求(2选1)
            $_order->packingMode = $this->_request->getParam("packingMode");// 包装方式
            $_order->packingDesc = $this->_request->getParam("packingDesc");// 包装描述
            $_order->paymentPeriod = (int)$this->_request->getParam("paymentPeriod");// 付款期限
            $_order->paymentTerm = $this->_request->getParam("paymentTerm");//   结算方式
            $_order->priceTerm = $this->_request->getParam("priceTerm");// 价格条款
            $_order->quotationMode = $this->_request->getParam("quotationMode");// 计价方式
            $_order->totalAmount = (double)$this->_request->getParam("totalAmount");// 订单金额
            if (empty($_order->totalAmount)) {
                $_order->totalAmount = 0;
            }
            $_order->shippingMethod = $this->_request->getParam("shippingMethod");// 运输方式(海运和空运显示的是港口 路运显示的是城市)
            $_order->clearancePlace = $this->_request->getParam("clearancePlace");// 报关口岸 (是否自营为false时;
            if ($_order->shippingMethod == "SEA" || $_order->shippingMethod == "AIR") {
                $_order->loadingPort = $this->_request->getParam("loadingPort");    // 起运港
                $_order->dischargePort = $this->_request->getParam("dischargePort");    // 卸货港
                $_order->deliveryPort = $this->_request->getParam("deliveryPort");    // 交货港
            } else {
                $_order->loadingCity = $this->_request->getParam("loadingPort");           // 起运港城市
                $_order->dischargeCity = $this->_request->getParam("dischargePort");     // 卸货城市
                $_order->deliveryCity = $this->_request->getParam("deliveryPort");    // 交货地点
            }
            $_order->deliveryDate = $date3;                                            // 交货日期
            $_order->lastUpdate = $date3 = date("Y-m-d\TH:i:s", time());                // 最后更新时间
            $_order->shippingServiceType = $this->_request->getParam("shippingServiceType");        // 装柜类型
            $_order->sizeQuantityMap = $orderMM;                    // 货柜数量
            $_order->isSelfSupport = (boolean)$this->_request->getParam("isSelfSupport");// 是否自营进出口
            /*金融服务模块开始*/
            if ($_order->paymentTerm == "T/T") {
                $_order->needFinancing = false;
                $_order->financingRequest = null;
            } else {
                $_order->needFinancing = (boolean)$this->_request->getParam("needFinancing");// 是否需要融资服务
                $_order->financingRequest = $this->_request->getParam("financingRequest");// 融资服务要求
            }
            /*报关行模块开始*/
            $regdCountryCode = $this->_request->getParam("regdCountryCode");
            $buyerRegdCountryCode = $this->_request->getParam("buyerRegdCountryCode");
            if ($regdCountryCode == $buyerRegdCountryCode && $regdCountryCode == "CN") {
                $_order->isAssignCustomsAgency = false;
                $_order->customsAgencyName = null;// 报关行名称
                $_order->customsAgencyCode = null;// 报关行代码
                $_order->customClearanceRequest = null;// 报关要求
            } else {
                $_order->isAssignCustomsAgency = (boolean)$this->_request->getParam("isAssignCustomsAgency");
                $_order->customsAgencyName = $this->_request->getParam("customsAgencyName");// 报关行名称
                if ($_order->customsAgencyName) {
                    $_order->customsAgencyCode = null;
                } else {
                    $_order->customsAgencyCode = $this->_request->getParam("customsAgencyCode");// 报关行代码
                }
                $_order->customClearanceRequest = $this->_request->getParam("customClearanceRequest");// 报关要求
            }

            /*物流服务模块*/
            if ($_order->priceTerm == "EXW") {
                $_order->needShipping = false;    // 物流安排
                $_order->shippingRequest = null;// 物流服务要求
            } else {
                $_order->needShipping = (boolean)$this->_request->getParam("needShipping");    // 物流安排
                $_order->shippingRequest = $this->_request->getParam("shippingRequest");// 物流服务要求
            }

            //		$_order->orderType = $this->_request->getParam("orderType");                 // 订单类型
            //				$_order->saleContractNo = (boolean)$this->_request->getParam("saleContractNo");// 销售合同号码
            //				$_order->saleContractID = (boolean)$this->_request->getParam("saleContractID");// 销售合同号码

            $_order->orderItemList = $it3;//订单商品集合
            $_order->attachmentList = $_attachList;//附件集合

            $requestObject = $this->_requestObject;
            $_resultData = $this->json->addOrderApi($requestObject, $_order);
            $resultObject = json_decode($_resultData);

            // 页面跳转
            if ($resultObject->status != 1) {
                // 统计所有商品数量
                $countResultObject = $this->json->countSaleOrderStatusApi($requestObject);
                $countOrder = $this->objectToArray(json_decode($countResultObject));
                $this->view->countOrder = $countOrder['result'];

                // bootstrap-table查询状态
                $this->view->orderStatus = '00';

                $this->view->order = $this->objectToArray($_order);
                $this->view->errMsg = $this->view->translate('tip_add_fail') . $resultObject->error;
            } else {
                $resultMsg = base64_encode($this->view->translate('tip_add_success'));
                $this->redirect("/sale/index?resultMsg=" . $resultMsg . "&orderStatus=00");
            }
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/sale/add.phtml");
            echo $content;
            exit;
        }
    }

    /*编辑订单*/
    public function editAction() {
        $this->view->langcode = "zh_CN";
        $this->view->ddsetting = "datatest_setting";
        // 请求Hessian服务端方法
        $orderIDget = $_SERVER['QUERY_STRING'];
        $_orderIDget = base64_decode($orderIDget);
        $_requestOb = $this->_requestObject;

        //获取数据
        $_resultData = $this->json->getOrderApi($_requestOb, $_orderIDget);
        $userView = json_decode($_resultData);
        $viewData = $userView->result;
        $Viewlist = $this->objectToArray($viewData);
        //当前返回数据为空时 前端显示为无
        if (!isset($Viewlist['packingDesc']))
            $Viewlist['packingDesc'] = $this->view->translate('NoData');  //包装描述
        if (!isset($Viewlist['financingRequest']))
            $Viewlist['financingRequest'] = $this->view->translate('noData');  //金融要求
        if (!isset($Viewlist['customClearanceRequest']))
            $Viewlist['customClearanceRequest'] = $this->view->translate('noData'); //报关要求
        // 物流要求
        if (!isset($Viewlist['shippingRequest'])) {
            if (isset($Viewlist['truckingRequest'])) {
                $Viewlist['shippingRequest'] = $Viewlist['truckingRequest'];
            } else {
                $Viewlist['shippingRequest'] = "";
            }
        }

        $this->view->orders = $Viewlist;
        $this->view->orderItem = json_encode($userView->result->orderItemList);

        //处理根据返回的运输方式来判断 起运|卸货|交货查询的缓存目录名称
        if ($Viewlist['shippingMethod'] == 'SEA') {
            $this->view->port = "SEA_PORT";
        } else if ($Viewlist['shippingMethod'] == 'AIR') {
            $this->view->port = "AIR_PORT";
        } else {
            $this->view->port = "CITY_ISO_CODE";
        };

        //        /*获取默认联系人 start*/
        $_queryP = new queryAccount();
        /**contactStatus 01有效 02禁用*/
        $_queryP->contactStatus = "01";
        $userKY = $this->json->listContactApi($this->_requestObject, $_queryP, null, null, 0, 0);
        $userData = $this->objectToArray(json_decode($userKY));
        $userList = $userData['result'];

        foreach ($userList as $k => $v) {
            if ($v['isPublicContact'] == true) {
                $this->view->dfContactName = $v['name'];
                $this->view->dfContactID = $v['contactID'];
            }
        }
        /*获取默认联系人 END*/


        //处理编辑
        if ($this->_request->isPost()) {
            //订单商品列表
            $iterm = array();
            $iterm["orderID"] = $_orderIDget;    //新增不存在
            //				$iterm["totalVolume"] = $this->_request->getParam("totalVolume"); 体积
            $iterm["hscode"] = $this->_request->getParam('hscode');
            $iterm["itemID"] = $this->_request->getParam("itemID");//
            $iterm["productID"] = $this->_request->getParam("productID");
            $iterm["supplierID"] = $this->_request->getParam("supplierID");
            $iterm["packingType"] = $this->_request->getParam('packingType');
            $iterm["productName"] = $this->_request->getParam("productName");
            $iterm["productEnName"] = $this->_request->getParam("productEnName");
            $iterm["productSize"] = $this->_request->getParam("productSize");
            $iterm["pricingUnit"] = $this->_request->getParam("pricingUnit");
            $iterm["quantity"] = $this->_request->getParam("quantity");
            $iterm["grossWeight"] = $this->_request->getParam("grossWeight");
            $iterm["netWeight"] = $this->_request->getParam("netWeight");
            $iterm["totalGrossWeight"] = $this->_request->getParam("totalGrossWeight");
            $iterm["totalNetWeight"] = $this->_request->getParam("totalNetWeight");
            $iterm["totalPackage"] = $this->_request->getParam("totalPackage");
            $iterm["totalPrice"] = $this->_request->getParam("totalPrice");
            $iterm["unitPrice"] = $this->_request->getParam("unitPrice");
            $iterm["purUnitPrice"] = $this->_request->getParam("purUnitPrice");
            $iterm["totalPurPrice"] = $this->_request->getParam("totalPurPrice");
            $iterm["productBrand"] = $this->_request->getParam("productBrand");
            $iterm["productModel"] = $this->_request->getParam("productModel");  //商品型号
            $iterm["productionMode"] = $this->_request->getParam("productionMode");//商品生产方式
            if ($iterm["productionMode"] == "01") {
                $iterm["isOwnProduct"] = true;
            } else {
                $iterm["isOwnProduct"] = false;
            }
            $iterm["functionUsage"] = $this->_request->getParam("functionUsage");
            $iterm["productMaterial"] = $this->_request->getParam("productMaterial");
            $it2 = array();

            //组装商品列表
            foreach ($iterm as $key => $value) {
                foreach ($value as $k => $v) {
                    $it2[$k][$key] = $v;
                }
            }

            $it3 = array();
            foreach ($it2 as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $it3[$k] = new Kyapi_Model_OrderItem();
                    $it3[$k]->orderID = $it2[$k]['orderID'];
                    $it3[$k]->hscode = $it2[$k]['hscode'];
                    $it3[$k]->productID = $it2[$k]['productID'];
                    $it3[$k]->itemID = $it2[$k]['itemID'];
                    $it3[$k]->supplierID = $it2[$k]['supplierID'];
                    $it3[$k]->quantity = (double)$it2[$k]['quantity'];
                    $it3[$k]->grossWeight = (double)$it2[$k]['grossWeight'];
                    $it3[$k]->netWeight = (double)$it2[$k]['netWeight'];
                    $it3[$k]->totalGrossWeight = (double)$it2[$k]['totalGrossWeight'];
                    $it3[$k]->totalNetWeight = (double)$it2[$k]['totalNetWeight'];
                    $it3[$k]->totalPackage = (int)$it2[$k]['totalPackage'];
                    $it3[$k]->totalPrice = (double)$it2[$k]['totalPrice'];
                    $it3[$k]->totalPurPrice = (double)$it2[$k]['totalPurPrice'];
                    //						$it3[$k]->totalVolume=(double)$it2[$k]['totalVolume'];
                    $it3[$k]->unitPrice = (double)$it2[$k]['unitPrice'];
                    $it3[$k]->purUnitPrice = (double)$it2[$k]['purUnitPrice'];
                    $it3[$k]->productName = $it2[$k]['productName'];
                    $it3[$k]->productEnName = $it2[$k]['productEnName'];
                    $it3[$k]->productSize = $it2[$k]['productSize'];
                    $it3[$k]->pricingUnit = $it2[$k]['pricingUnit'];
                    $it3[$k]->productMaterial = $it2[$k]['productMaterial'];
                    $it3[$k]->functionUsage = $it2[$k]['functionUsage'];
                    $it3[$k]->productBrand = $it2[$k]['productBrand'];
                    $it3[$k]->productModel = $it2[$k]['productModel'];
                    $it3[$k]->productionMode = $it2[$k]['productionMode'];
                    $it3[$k]->isOwnProduct = $it2[$k]['isOwnProduct'];
                    $it3[$k]->packingType = $it2[$k]['packingType'];
                    $it3[$k]->productMaterial = $it2[$k]['productMaterial'];
                    $it3[$k]->functionUsage = $it2[$k]['functionUsage'];
                }
            }
            //获取附件ID
            $Atachlist = array();
            $Atachlist["attachID"] = $this->_request->getParam('attachNid');
            $Atachlist["attachType"] = $this->_request->getParam('attachType');
            $Atachlist["bizType"] = $this->_request->getParam("bizType");
            $Atachlist["attachName"] = $this->_request->getParam("attachName");
            $Atachlist["attachSize"] = $this->_request->getParam("attachSize");
            $_attach2 = array();
            foreach ($Atachlist as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $_attach2[$k1][$k] = $v1;
                }
            }
            $_attachList = array();
            foreach ($_attach2 as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $_attachList[$k] = new Kyapi_Model_Attachment();
                    $_attachList[$k]->attachID = $_attach2[$k]['attachID'];
                    $_attachList[$k]->attachType = 'ODOD';
                    $_attachList[$k]->bizType = 'OD';
                    $_attachList[$k]->name = $_attach2[$k]['attachName'];
                    $_attachList[$k]->size = (int)$_attach2[$k]['attachSize'];
                }
            }

            //装柜数量
            $orderMM = array();
            $orderMM["20GP"] = (int)$this->_request->getParam("sizeQuantityMap_20GP");
            $orderMM["40GP"] = (int)$this->_request->getParam("sizeQuantityMap_40GP");
            $orderMM["40HP"] = (int)$this->_request->getParam("sizeQuantityMap_40HP");
            $orderMM["45HP"] = (int)$this->_request->getParam("sizeQuantityMap_45HP");
            $orderMM["20OT"] = (int)$this->_request->getParam("sizeQuantityMap_20OT");
            $orderMM["40OT"] = (int)$this->_request->getParam("sizeQuantityMap_40OT");
            $_orderMM = $this->arrayToObject($orderMM);
            //获取当前订单时间
            $ddtime = $this->_request->getParam("deliveryDate");
            if (!empty($ddtime)) {
                $date3 = date("Y-m-d\TH:i:s", strtotime($ddtime));
            } else {
                $date3 = date("Y-m-d\TH:i:s", time());
            }

            $_order = new Kyapi_Model_order();
            $_order->orderID = $_orderIDget;  //订单ID
            //本方信息公司ID、公司名称、联系人、联系人ID（本方联系人CRNCODE货币 不做传递服务端）
            $_order->vendor = $this->view->accountID;
            $_order->vendorName = $this->_request->getParam("accountName");
            $_order->vendorContactID = $this->_request->getParam("vendorContactID");
            $_order->vendorContactName = $this->_request->getParam("vendorContactName");
            $_order->vendorCrnCode = $this->_request->getParam("CrnCode");
            //委托方信息 ID、Name、货币（Crncode）、联系人ID、联系人姓名
            $_order->buyer = $this->_request->getParam("buyer");
            $_order->buyerName = $this->_request->getParam("buyerName");
            $_order->buyerCrnCode = $this->_request->getParam("buyerCrnCode");
            $_order->buyerContactID = $this->_request->getParam("buyerContactID");
            $_order->buyerContactName = $this->_request->getParam("buyerContactName");

            $_order->vendorOrderRequest = $this->_request->getParam("vendorOrderRequest");// 订单要求(2选1)
            $_order->packingMode = $this->_request->getParam("packingMode");// 包装方式
            $_order->packingDesc = $this->_request->getParam("packingDesc");// 包装描述
            $_order->paymentPeriod = (int)$this->_request->getParam("paymentPeriod");// 付款期限
            $_order->paymentTerm = $this->_request->getParam("paymentTerm");//   结算方式
            $_order->priceTerm = $this->_request->getParam("priceTerm");// 价格条款
            $_order->quotationMode = $this->_request->getParam("quotationMode");// 计价方式
            $_order->totalAmount = (double)$this->_request->getParam("totalAmount");// 订单金额
            if (empty($_order->totalAmount)) {
                $_order->totalAmount = 0;
            }
            $_order->shippingMethod = $this->_request->getParam("shippingMethod");// 运输方式(海运和空运显示的是港口 路运显示的是城市)
            $_order->clearancePlace = $this->_request->getParam("clearancePlace");// 报关口岸 (是否自营为false时;
            if ($_order->shippingMethod == "SEA" || $_order->shippingMethod == "AIR") {
                $_order->loadingPort = $this->_request->getParam("loadingPort");    // 起运港
                $_order->dischargePort = $this->_request->getParam("dischargePort");    // 卸货港
                $_order->deliveryPort = $this->_request->getParam("deliveryPort");    // 交货港
            } else {
                $_order->loadingCity = $this->_request->getParam("loadingPort");           // 起运港城市
                $_order->dischargeCity = $this->_request->getParam("dischargePort");     // 卸货城市
                $_order->deliveryCity = $this->_request->getParam("deliveryPort");    // 交货地点
            }
            $_order->deliveryDate = $date3;                                            // 交货日期
            $_order->lastUpdate = $date3 = date("Y-m-d\TH:i:s", time());                // 最后更新时间
            $_order->shippingServiceType = $this->_request->getParam("shippingServiceType");        // 装柜类型
            $_order->sizeQuantityMap = $orderMM;                    // 货柜数量
            $_order->isSelfSupport = (boolean)$this->_request->getParam("isSelfSupport");// 是否自营进出口
            /*金融服务模块开始*/
            if ($_order->paymentTerm == "T/T") {
                $_order->needFinancing = false;
                $_order->financingRequest = null;
            } else {
                $_order->needFinancing = (boolean)$this->_request->getParam("needFinancing");// 是否需要融资服务
                $_order->financingRequest = $this->_request->getParam("financingRequest");// 融资服务要求
            }
            /*报关行模块开始*/
            $regdCountryCode = $this->_request->getParam("regdCountryCode");
            $buyerRegdCountryCode = $this->_request->getParam("buyerRegdCountryCode");
            if ($regdCountryCode == $buyerRegdCountryCode && $regdCountryCode == "CN") {
                $_order->isAssignCustomsAgency = false;
                $_order->customsAgencyName = null;// 报关行名称
                $_order->customsAgencyCode = null;// 报关行代码
                $_order->customClearanceRequest = null;// 报关要求
            } else {
                $_order->isAssignCustomsAgency = (boolean)$this->_request->getParam("isAssignCustomsAgency");
                $_order->customsAgencyName = $this->_request->getParam("customsAgencyName");// 报关行名称
                $_order->customsAgencyCode = $this->_request->getParam("customsAgencyCode");// 报关行代码
                $_order->customClearanceRequest = $this->_request->getParam("customClearanceRequest");// 报关要求
            }

            /*物流服务模块*/
            if ($_order->priceTerm == "EXW") {
                $_order->needShipping = false;    // 物流安排
                $_order->shippingRequest = null;// 物流服务要求
            } else {
                $_order->needShipping = (boolean)$this->_request->getParam("needShipping");    // 物流安排
                $_order->shippingRequest = $this->_request->getParam("shippingRequest");// 物流服务要求
            }

            //		$_order->orderType = $this->_request->getParam("orderType");                 // 订单类型
            //				$_order->saleContractNo = (boolean)$this->_request->getParam("saleContractNo");// 销售合同号码
            //				$_order->saleContractID = (boolean)$this->_request->getParam("saleContractID");// 销售合同号码
            $_order->orderItemList = $it3;//订单商品集合
            $_order->attachmentList = $_attachList;//附件集合

            $requestObject = $this->_requestObject;
            $_resultData = $this->json->editOrderApi($requestObject, $_order);
            $resultObject = json_decode($_resultData);


            // 页面跳转
            if ($resultObject->status != 1) {
                // 统计所有商品数量
                $countResultObject = $this->json->countSaleOrderStatusApi($requestObject);
                $countOrder = $this->objectToArray(json_decode($countResultObject));
                $this->view->countOrder = $countOrder['result'];

                // bootstrap-table查询状态
                $this->view->orderStatus = '00';

                $this->view->order = $this->objectToArray($_order);
                $this->view->errMsg = $this->view->translate('tip_edit_fail') . $resultObject->error;
            } else {
                $orderStatus = "00";
                if ($viewData->orderStatus != '00') {
                    $orderStatus = $viewData->orderStatus;
                }
                $resultMsg = base64_encode($this->view->translate('tip_edit_success'));
                $this->redirect("/sale/index?resultMsg=" . $resultMsg . "&orderStatus=".$orderStatus);
            }
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/sale/edit.phtml");
            echo $content;
            exit;
        }
    }

    /*查看订单*/
    public function viewAction() {
        //读取数据字典
        $cacheM = new Seed_Model_Cache2File();
        $DATAlist = $cacheM->get("datatest_setting");
        $this->view->Datalist = $DATAlist;
        // 请求Hessian服务端方法
        $orderID = $_SERVER['QUERY_STRING'];
        $_orderID = base64_decode($orderID);
        $this->view->orderid = $_orderID;

        $_requestOb = $this->_requestObject;
        $_resultKY = $this->json->getOrderApi($_requestOb, $_orderID);
        $userKY = json_decode($_resultKY);

        // 取回当前公司的企业认证状态
        $_accountID = $this->view->accountID;
        $account = $this->json->getAccountApi($_requestOb, $_accountID);
        $this->view->hasIDCertificate = json_decode($account)->result->hasIDCertificate;

        $accountData = $this->objectToArray(json_decode($account)->result);
        $this->view->account = $accountData;


        $existData = $userKY->result;
        $existDatt = $this->objectToArray($existData);

        //当前返回数据为空时 前端显示为无
        if (!isset($existDatt['packingDesc']))
            $existDatt['packingDesc'] = $this->view->translate('noData');  //包装描述
        if (!isset($existDatt['financingRequest']))
            $existDatt['financingRequest'] = $this->view->translate('noData');  //金融要求
        if (!isset($existDatt['customClearanceRequest']))
            $existDatt['customClearanceRequest'] = $this->view->translate('noData'); //报关要求
        if (!isset($existDatt['shippingRequest']))
            $existDatt['shippingRequest']=$this->view->translate('noData');   //物流要求

        $this->view->orders = $existDatt;

        // 订单合同列表
        $bizType = 'OD';
        $listBizContractResultObject = $this->json->listBizContract($_requestOb, $bizType, $_orderID);
        $listBizContract = json_decode($listBizContractResultObject)->result;
        $this->view->contractList = empty($listBizContract) ? null : $this->objectToArray($listBizContract);

        $this->view->vestut = $existDatt['vendorExecStatus'];
        $this->view->veorderID = $existDatt['orderID'];


        // 取回物流信息
        $deliveryList = $this->json->listDelivery($_requestOb, $_orderID);
        $this->view->deliveryList = json_decode($deliveryList)->result;

        // 是否存在已完成的物流
        $this->view->hasOneTakeOverDelivery = False;
        $deliveryData = $this->objectToArray(json_decode($deliveryList));
        $deliveryDataList = $deliveryData['result'];
        foreach ($deliveryDataList as $value) {
            if ($value['deliveryStatus'] == 4) {
                $this->view->hasOneTakeOverDelivery = True;
                break;
            }
        }

        // 物流相关附件
        $this->view->DLPG = array();    // 备货相关附件
        $this->view->DLEG = array();    // 验货相关附件
        $this->view->DLDG = array();    // 发货相关附件
        $this->view->DLRG = array();    // 收货相关附件
        $this->view->DLQT = array();    // 质量保证函模板
        $this->view->DLQF = array();    // 质量保证函正本
        $this->view->DLCT = array();    // 收货确认函模板
        $this->view->DLCF = array();    // 收货确认函正本
        $deliveryAttachDataList = $deliveryData['result'];


        //处理根据返回的运输方式来判断 起运|卸货|交货查询的缓存目录名称
        if ($existDatt['shippingMethod'] == 'SEA') {
            $this->view->port = "SEA_PORT";
            $this->view->loadingPort = $existDatt['loadingPort'];
            $this->view->dischargePort = $existDatt['dischargePort'];
            $this->view->deliveryPort = $existDatt['deliveryPort'];

        } else if ($existDatt['shippingMethod'] == 'AIR') {
            $this->view->port = "AIR_PORT";
            $this->view->loadingPort = $existDatt['loadingPort'];
            $this->view->dischargePort = $existDatt['dischargePort'];
            $this->view->deliveryPort = $existDatt['deliveryPort'];
        } else {
            $this->view->port = "CITY_ISO_CODE";
            $this->view->loadingPort = $existDatt['loadingCity'];
            $this->view->dischargePort = $existDatt['dischargeCity'];
            $this->view->deliveryPort = $existDatt['deliveryCity'];
        };

        if ($this->view->accountID == $existDatt['client']) {
            if ($existDatt['orderStatus'] == 05 || $existDatt['orderStatus'] == 00 || $existDatt['orderStatus'] == 02) {
                $this->view->allowEdit = 1;
            } else {
                $this->view->allowEdit = 0;
            }
        } else {
            $this->view->allowEdit = 0;
        }


        //判断港口/城市
        if ($existDatt['shippingMethod'] == 'SEA') {
            $this->view->shipping = 'SEA_PORT';
        } else if ($existDatt['shippingMethod'] == 'AIR') {
            $this->view->shipping = 'AIR_PORT';
        } else {
            $this->view->shipping = 'CITY_ISO_CODE';
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/sale/view.phtml");
            echo $content;
            exit;
        }
    }

    public function downloadAction(){
        if ($this->_request->isPost()) {
            $id= $this->_request->getParam("id");
            $type = $this->_request->getParam("type");
            $_requestOb=$this->_requestObject;
            if($type=='PDF'){
                $resData= $this->json->getPdfUrl($_requestOb,$id);
            }else{
                $resData= $this->json->getImgUrl($_requestOb,$id);
            }

            echo $resData;
        }
        exit;
    }
    public function signAction(){
        if ($this->_request->isPost()) {
            $id= $this->_request->getParam("id");
            $url = $this->_request->getParam("url");
            $_requestOb=$this->_requestObject;
            $resData= $this->json->initContractSignView($_requestOb,$id,$url);
            echo $resData;
        }
        exit;
    }

    public function planAction($data){
        /*处理订单进度说明 逻辑关系
        因为关系复杂 故处理顺序如下，先通过买家，卖家身份 对特殊状态进行赋值，发货，备货，验货，收货，等
        （包括状态7时【验货，收货】第二个状态是否显示和隐藏）
        $data 为订单数据
        $exArray 为返回的数据 包括 plan 进度文字，display是否显示：0隐藏，1显示
        */
        $exArray=array();
        if($data['vendor']== $this->view->accountID){
            $exStatus=$data['vendorExecStatus'];
            $lodingSt=$data['buyerExecStatus'];
            $planing02=$this->view->translate('going002_01v');
            $planing031=$this->view->translate('going003_01');
            $planing032=$this->view->translate('going003_02');
            $planing033=$this->view->translate('going003_03');
            $planing051=$this->view->translate('going005_01');
            $planing052=$this->view->translate('going005_02');
            $display052=1;
            $planing053=$this->view->translate('going005_03');
            $planing07=$this->view->translate('going007v');
            $planing08=$this->view->translate('going008v');
        }elseif ($data['buyer']== $this->view->accountID){
            $exStatus=$data['buyerExecStatus'];
            $lodingSt=$data['vendorExecStatus'];
            $planing02=$this->view->translate('going002_01b');
            $planing031=$this->view->translate('going004_01');
            $planing032=$this->view->translate('going004_02');
            $planing033=$this->view->translate('going004_03');
            $planing051=$this->view->translate('going006_01');
            $planing052=$this->view->translate('going006_02');
            $display052=0;
            $planing053=$this->view->translate('going006_03');
            $planing07=$this->view->translate('going007b');
            $planing08=$this->view->translate('going008b');
        }else{
            $exStatus=null;
            $planing02=null;
            $lodingSt=null;
            $planing033=$planing032=$planing031=null;
            $planing08=$planing07=$planing051=$planing052=$display052=$planing053=null;
        }


        switch ($exStatus)
        {
            case "-1":
                $exArray['plan']= $this->view->translate('going000');
                $exArray['display']=null;
                break;
            case "0":
                $exArray['plan']= $this->view->translate('going001');
                $exArray['display']=null;
                break;
            case "1":

                if($lodingSt=='0'&&$data['orderStatus']=='02') {
                    $exArray['plan'] = $planing02;
                    $exArray['display'] = 0;
                }elseif($lodingSt=='1'&&$data['orderStatus']=='02'){
                    $exArray['plan'] = $this->view->translate('going002_02');
                    $exArray['display'] = 0;
                }else{
                    $exArray['plan'] = $this->view->translate('going002_03');
                    $exArray['display'] = 1;
                }

                break;
            case "3":
                if($lodingSt=='1'&&$data['orderStatus']=='03') {
                    $exArray['plan'] = $planing031;
                    $exArray['display'] = 0;
                }elseif($lodingSt=='3'&&$data['orderStatus']=='03'){
                    $exArray['plan'] = $planing032;
                    $exArray['display'] = 0;
                }else{
                    $exArray['plan'] = $planing033;
                    $exArray['display'] = 1;
                }
                break;
            case "7":
                if($lodingSt=='3'&&$data['orderStatus']=='04') {
                    $exArray['plan'] = $planing051;
                    $exArray['display'] = 0;
                }elseif($lodingSt=='7'&&$data['orderStatus']=='04'){
                    $exArray['plan'] = $planing052;
                    $exArray['display'] = $display052;
                }else{
                    $exArray['plan'] = $planing053;
                    $exArray['display'] = 1;
                }
                break;
            case "15":
                $exArray['plan']= $planing07;
                $exArray['display']=null;
                break;
            case "63":
                $exArray['plan']= $planing08;
                $exArray['display']=null;
                break;
            default:
                $exArray['display']=null;
                $exArray['plan']= null;
        }

       return $exArray;

    }

    //删除草稿订单
    public function delAction()
    {
        if ($this->_request->isPost()) {
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
            $opData= $this->json->doRemoveDraftApi($_requestOb,$_objID);
            echo $opData;
        }
        exit;
    }

    //取消订单
    public function cancelAction()
    {
        if ($this->_request->isPost()) {
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
           // $_operationReason = $this->_request->getParam('operationReason');
            $opData= $this->json->doCancelOrderApi($_requestOb,$_objID/*,$_operationReason*/);
            echo $opData;
        }
        exit;
    }

    //提交订单
    public function submitAction()
    {
        if ($this->_request->isPost()) {
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
            $opData= $this->json->doSubmitOrderApi($_requestOb,$_objID);
            echo $opData;
        }
        exit;
    }

    //确认订单
    public function confirmAction()
    {
        // 请求Hessian服务端方法
        $_orderID = $this->_request->getParam('orderID');
        $_requestOb = $this->_requestObject;
        $_resultData = $this->json->doConfirmOrderApi($_requestOb, $_orderID);
        $existData = json_decode($_resultData);
        echo json_encode($existData->status);

//        if ($existData->status != 1) {
//            echo json_encode("未成功确认" . $existData->errorCode);
//        } else {
//            echo json_encode("已成功确认");
//        }
        exit;
    }

    public function confirmsAction()
    {
        // 请求Hessian服务端方法
        $orderID = $_SERVER['QUERY_STRING'];
        $_orderID = base64_decode($orderID);

        $_requestOb = $this->_requestObject;
        $_resultData = $this->json->doConfirmOrderApi($_requestOb, $_orderID);
        $existData = json_decode($_resultData);
        echo json_encode($existData->status);

//        if ($existData->status != 1) {
//            Shop_Browser::redirect('未成功提交订单！', $this->view->seed_BaseUrl . "/orderxs");
//        } else {
//            Shop_Browser::redirect('已成功提交订单！', $this->view->seed_BaseUrl . "/orderxs");
//        }
        exit;
    }

    //签订协议
    public function agreeAction()
    {
        // 请求Hessian服务端方法
        $name = $_POST['name'];
        $nid = $_POST['nid'];
        $size = $_POST['size'];
		$attachType=$_POST['attachType'];
        $_contractID = $_POST['contractID'];

        $_nid = explode("|", $nid);
        $_name = explode("|", $name);
        $_size = explode("|", $size);
        $_attachType = explode("|", $attachType);

        foreach ($_nid as $k => $v) {
            if (!$v)
                unset($_nid[$k]);
        }
        foreach ($_name as $k => $v) {
            if (!$v)
                unset($_name[$k]);
        }
        foreach ($_size as $k => $v) {
            if (!$v)
                unset($_size[$k]);
        }
        foreach ($_attachType as $k => $v) {
            if (!$v)
                unset($_attachType[$k]);
        }

        $attach = array();
        $attach['attachID'] = $_nid;
        $attach['name'] = $_name;
        $attach['size'] = $_size;
		$attach['attachType']=$_attachType;
        $_attach2 = array();

        foreach ($attach as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $_attach2[$k1][$k] = $v1;
            }
        }
        $_attachList = array();
        foreach ($_attach2 as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $_attachList[$k] = new Kyapi_Model_Attachment();
                $_attachList[$k]->attachID = $_attach2[$k]['attachID'];
                $_attachList[$k]->name = $_attach2[$k]['name'];
                $_attachList[$k]->size = (int)$_attach2[$k]['size'];
                $_attachList[$k]->attachType = $_attach2[$k]['attachType'];
                if($_attach2[$k]['attachType']=="CRSE"){
                    $_attachList[$k]->bizType = "CR";
                }else{
                    $_attachList[$k]->bizType = "OD";
                }

//				$_attachList[$k]->bizID=$_attach2[$k]['bizID'];
            }
        }


        $_requestOb = $this->_requestObject;
        if (count($_attachList) == 0) {
            $_attachList = null;
        }
        $_resultData = $this->json->doAgreeContractApi($_requestOb, $_contractID, $_attachList);
        $existData = json_decode($_resultData);
        echo json_encode($existData->status);
        exit;
    }

    /* 发货单详情 */
    public function deliveryviewAction() {
        $deliveryID = $this->_request->getParam('deliveryID');
        $_requestOb = $this->_requestObject;
        $resultObject = $this->json->getDeliveryView($_requestOb, $deliveryID);

        $delivery = json_decode($resultObject)->result;
        $this->view->delivery = json_decode($resultObject)->result;
        $attachmentList = $delivery->attachmentList;

        $this->view->prepareAttachmentList = array();   // 备货
        $this->view->examineAttachmentList = array();   // 验货
        $this->view->deliverAttachmentList = array();   // 发货
        $this->view->receiptAttachmentList = array();   // 收货
        $this->view->qualityAttachmentList = array();   // 质量保证函正本
        $this->view->receiptConfirmationAttachmentList = array();   // 收货确认函正本

        foreach ($attachmentList as $k => $v) {
            if ($v->attachType == "DLPG") {
                $this->view->prepareAttachmentList[] = $v;
            } else if ($v->attachType == "DLEG") {
                $this->view->examineAttachmentList[] = $v;
            } else if ($v->attachType == "DLDG") {
                $this->view->deliverAttachmentList[] = $v;
            } else if ($v->attachType == "DLRG") {
                $this->view->receiptAttachmentList[] = $v;
            } else if ($v->attachType == "DLQF") {
                $this->view->qualityAttachmentList[] = $v;
            } else if ($v->attachType == "DLCF") {
                $this->view->receiptConfirmationAttachmentList[] = $v;
            }
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/sale/deliveryView.phtml");
            echo $content;
            exit;
        }
    }

    // 订单商品列表
    public function listorderitemAction() {
        $orderID = $this->_request->getParam('orderID');
        $deliveryCrnCode = $this->_request->getParam('deliveryCrnCode');
        $_requestOb = $this->_requestObject;
        $resultObject = $this->json->listOrderItem($_requestOb, $orderID);


        $this->view->orderItemList = json_decode($resultObject)->result;
        $this->view->deliveryCrnCode = $deliveryCrnCode;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/sale/deliveryAdd.phtml");
            echo $content;
            exit;
        }
    }

    //备货
    public function preparesaveAction() {
        $_requestOb = $this->_requestObject;
        // 请求Hessian服务端方法
        $name = $_POST['name'];
        $nid = $_POST['nid'];
        $size = $_POST['size'];
        $attachType=$_POST['attachType'];
        $_orderID = $_POST['orderID'];
        $_nid = explode("|", $nid);
        $_name = explode("|", $name);
        $_size = explode("|", $size);
        $_attachType = explode("|", $attachType);

        foreach ($_nid as $k => $v) {
            if (!$v)
                unset($_nid[$k]);
        }
        foreach ($_name as $k => $v) {
            if (!$v)
                unset($_name[$k]);
        }
        foreach ($_size as $k => $v) {
            if (!$v)
                unset($_size[$k]);
        }
        foreach ($_attachType as $k => $v) {
            if (!$v)
                unset($_attachType[$k]);
        }

        $attach = array();
        $attach['nid'] = $_nid;
        $attach['name'] = $_name;
        $attach['size'] = $_size;
        $attach['attachType'] = $_attachType;

        $_attach2 = array();
        foreach ($attach as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $_attach2[$k1][$k] = $v1;
            }
        }
        $_attachList = array();
        foreach ($_attach2 as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $_attachList[$k] = new Kyapi_Model_Attachment();
                $_attachList[$k]->attachID = $_attach2[$k]['nid'];
                $_attachList[$k]->name = $_attach2[$k]['name'];
                $_attachList[$k]->size = (int)$_attach2[$k]['size'];
                $_attachList[$k]->attachType = $_attach2[$k]['attachType'];
                $_attachList[$k]->bizType = "OD";
            }
        }

        $delivery = new Kyapi_Model_Delivery();
        $delivery->orderID = $_orderID;

        $orderItemID = explode("|", $_POST['orderItemID']);
        $deliveryQuantity = explode("|", $_POST['deliveryQuantity']);

        foreach ($orderItemID as $k => $v) {
            if (!$v)
                unset($orderItemID[$k]);
        }
        foreach ($deliveryQuantity as $k => $v) {
            if (!$v)
                unset($deliveryQuantity[$k]);
        }

        $deliveryItemList = array();
        foreach ($orderItemID as $index => $value) {
            $deliveryItemList[$index] = new Kyapi_Model_DeliveryItem();
            $deliveryItemList[$index]->orderItemID = $value;
            $deliveryItemList[$index]->deliveryQuantity = $deliveryQuantity[$index];
        }

        if (count($_attachList) == 0) {
            $_attachList = null;
        }
        $_resultData = $this->json->doPrepareGoodsApi($_requestOb, $delivery, $deliveryItemList, $_attachList);
        $existData = json_decode($_resultData);

        echo json_encode($existData->status);
        exit;
    }

    // 编辑备货单 - view
    public function editdeliveryAction() {
        $deliveryID = $this->_request->getParam('deliveryID');
        $_requestOb = $this->_requestObject;
        $resultObject = $this->json->getDeliveryView($_requestOb, $deliveryID);

        $delivery = json_decode($resultObject)->result;
        $this->view->delivery = json_decode($resultObject)->result;
        $attachmentList = $delivery->attachmentList;

        $this->view->attachmentList = array();
        foreach ($attachmentList as $k => $v) {

            if ($v->attachType == "DLPG") {
                $this->view->attachmentList[] = $v;
            }
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/sale/deliveryEdit.phtml");
            echo $content;
            exit;
        }
    }

    // 编辑备货单 - 保存
    public function editdeliverysaveAction() {
        $_requestOb = $this->_requestObject;
        // 请求Hessian服务端方法
        $deliveryID = $_POST['deliveryID'];
        $name = $_POST['name'];
        $nid = $_POST['nid'];
        $size = $_POST['size'];
        $attachType=$_POST['attachType'];
        $_nid = explode("|", $nid);
        $_name = explode("|", $name);
        $_size = explode("|", $size);
        $_attachType = explode("|", $attachType);

        foreach ($_nid as $k => $v) {
            if (!$v)
                unset($_nid[$k]);
        }
        foreach ($_name as $k => $v) {
            if (!$v)
                unset($_name[$k]);
        }
        foreach ($_size as $k => $v) {
            if (!$v)
                unset($_size[$k]);
        }
        foreach ($_attachType as $k => $v) {
            if (!$v)
                unset($_attachType[$k]);
        }

        $attach = array();
        $attach['nid'] = $_nid;
        $attach['name'] = $_name;
        $attach['size'] = $_size;
        $attach['attachType'] = $_attachType;

        $_attach2 = array();
        foreach ($attach as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $_attach2[$k1][$k] = $v1;
            }
        }
        $_attachList = array();
        foreach ($_attach2 as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $_attachList[$k] = new Kyapi_Model_Attachment();
                $_attachList[$k]->attachID = $_attach2[$k]['nid'];
                $_attachList[$k]->name = $_attach2[$k]['name'];
                $_attachList[$k]->size = (int)$_attach2[$k]['size'];
                $_attachList[$k]->attachType = $_attach2[$k]['attachType'];
                $_attachList[$k]->bizType = "OD";
            }
        }


        $orderItemID = explode("|", $_POST['orderItemID']);
        $deliveryQuantity = explode("|", $_POST['deliveryQuantity']);

        foreach ($orderItemID as $k => $v) {
            if (!$v)
                unset($orderItemID[$k]);
        }
        foreach ($deliveryQuantity as $k => $v) {
            if (!$v)
                unset($deliveryQuantity[$k]);
        }

        $deliveryItemList = array();
        foreach ($orderItemID as $index => $value) {
            $deliveryItemList[$index] = new Kyapi_Model_DeliveryItem();
            $deliveryItemList[$index]->itemID = $value;
            $deliveryItemList[$index]->deliveryQuantity = $deliveryQuantity[$index];
        }

        if (count($_attachList) == 0) {
            $_attachList = null;
        }
        $_resultData = $this->json->editDeliveryApi($_requestOb, $deliveryID, $deliveryItemList, $_attachList);
        $existData = json_decode($_resultData);

        echo json_encode($existData->status);
        exit;
    }

    // 删除备货单
    public function deldeliveryAction() {
        $_requestOb = $this->_requestObject;
        // 请求Hessian服务端方法
        $deliveryID = $_POST['deliveryID'];

        $_resultData = $this->json->delDeliveryApi($_requestOb, $deliveryID);
        $existData = json_decode($_resultData);

        echo json_encode($existData->status);
        exit;
    }

    // 发货 - view
    public function deliverAction() {
        $deliveryID = $this->_request->getParam('deliveryID');
        $_requestOb = $this->_requestObject;
        $resultObject = $this->json->getDeliveryView($_requestOb, $deliveryID);

        $delivery = json_decode($resultObject)->result;
        $this->view->delivery = json_decode($resultObject)->result;
        $attachmentList = $delivery->attachmentList;

        $this->view->attachmentList = array();
        foreach ($attachmentList as $k => $v) {
            if ($v->attachType == "DLDG") {
                $this->view->attachmentList[] = $v;
            }
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/sale/deliveryDeliver.phtml");
            echo $content;
            exit;
        }
    }

    // 发货 - 保存
    public function deliversaveAction() {
        $_requestOb = $this->_requestObject;
        // 请求Hessian服务端方法
        $name = $_POST['name'];
        $nid = $_POST['nid'];
        $size = $_POST['size'];
        $attachType = $_POST['attachType'];
        $deliveryID = $_POST['deliveryID'];
        $_nid = explode("|", $nid);
        $_name = explode("|", $name);
        $_size = explode("|", $size);
        $_attachType = explode("|", $attachType);

        foreach ($_nid as $k => $v) {
            if (!$v)
                unset($_nid[$k]);
        }
        foreach ($_name as $k => $v) {
            if (!$v)
                unset($_name[$k]);
        }
        foreach ($_size as $k => $v) {
            if (!$v)
                unset($_size[$k]);
        }
        foreach($_attachType as $k=>$v){
            if( !$v )
                unset( $_attachType[$k] );
        }

        $attach = array();
        $attach['nid'] = $_nid;
        $attach['name'] = $_name;
        $attach['size'] = $_size;
        $attach['attachType']=$_attachType;

        $_attach2 = array();
        foreach ($attach as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $_attach2[$k1][$k] = $v1;
            }
        }
        $_attachList = array();
        foreach ($_attach2 as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $_attachList[$k] = new Kyapi_Model_Attachment();
                $_attachList[$k]->attachID = $_attach2[$k]['nid'];
                $_attachList[$k]->name = $_attach2[$k]['name'];
                $_attachList[$k]->size = (int)$_attach2[$k]['size'];
                $_attachList[$k]->attachType=$_attach2[$k]['attachType'];
                $_attachList[$k]->bizType = "OD";
            }
        }

        if (count($_attachList) == 0) {
            $_attachList = null;
        }
        $_resultData = $this->json->doDeliverGoodsApi($_requestOb, $deliveryID, $_attachList);
        $existData = json_decode($_resultData);
        echo json_encode($existData->status);

        exit;
    }

    // 开票资料 - view
    public function deliverbillinginfoAction() {
        $deliveryID = $this->_request->getParam('deliveryID');
        $_requestOb = $this->_requestObject;
        $resultObject = $this->json->getDeliveryView($_requestOb, $deliveryID);

        $delivery = json_decode($resultObject)->result;
        $this->view->deliveryID = $deliveryID;
        $this->view->delivery = json_decode($resultObject)->result;
        $this->view->deliverySupplierList = $delivery->deliverySupplierList;

        // 取回当前公司的企业认证状态
        $_accountID = $this->view->accountID;
        $account = $this->json->getAccountApi($_requestOb, $_accountID);
        $this->view->hasIDCertificate = json_decode($account)->result->hasIDCertificate;

        if (defined('SEED_WWW_TPL')) {
            if ($delivery->needTransfer && $delivery->transferRequestDate == null) {
                $content = $this->view->render(SEED_WWW_TPL . "/sale/genBillingInfo.phtml");
                echo $content;
                exit;
            } else {
                $billingInfoResultObject = $this->json->getBillingInfoApi($_requestOb, $deliveryID);
                $billingInfo = json_decode($billingInfoResultObject)->result;
                $this->view->billingInfo = $billingInfo;
                $this->view->deliverySupplierList = $billingInfo->deliverySupplierList;
                // $purContractAttachmentList = $billingInfo->deliverySupplierList->purContract->attachmentList;

                $content = $this->view->render(SEED_WWW_TPL . "/sale/getBillingInfo.phtml");
                echo $content;
                exit;
            }
        }
    }

    public function genbillinginfoAction() {
        if ($this->_request->isPost()) {
            $deliveryID = $this->_request->getParam('deliveryID');
            $supplierID = $this->_request->getParam('supplierID');
            $bankAcctID = $this->_request->getParam('bankAcctID');

            $delivery = new Kyapi_Model_Delivery();
            $delivery->deliveryID = $deliveryID;

            $_supplierID = explode("|", $supplierID);
            foreach ($_supplierID as $k => $v) {
                if (!$v)
                    unset($_supplierID[$k]);
            }

            $_bankAcctID = explode("|", $bankAcctID);
            foreach ($_supplierID as $k => $v) {
                if (!$v)
                    unset($_bankAcctID[$k]);
            }

            foreach ($_supplierID as $index => $value) {
                $deliverySupplier = new Kyapi_Model_DeliverySupplier();
                $deliverySupplier->supplierID = $value;
                $deliverySupplier->bankAcctID = $_bankAcctID[$index];
                $delivery->deliverySupplierList[$index] = $deliverySupplier;
            }

            $_requestOb = $this->_requestObject;
            $_resultData = $this->json->genBillingInfoApi($_requestOb, $delivery);
            $existData = json_decode($_resultData);

            echo json_encode($existData->status);
            exit;
        }
    }

    // 编辑物流信息
    public function editexpresssaveAction() {
        if ($this->_request->isPost()) {
            $deliveryID = $this->_request->getPost('deliveryID');
            $supplierID = $this->_request->getPost('supplierID');
            $expressType = $this->_request->getPost('expressType');
            $expressNo = $this->_request->getPost('expressNo');
            $_requestOb = $this->_requestObject;

            $deliverySupplier = new Kyapi_Model_DeliverySupplier();
            $deliverySupplier->deliveryID = $deliveryID;
            $deliverySupplier->supplierID = $supplierID;
            $deliverySupplier->expressType = $expressType;
            $deliverySupplier->expressNo = $expressNo;

            // var_dump($deliverySupplier);
            // print_r("----------------");
            // $delObj = json_encode($deliverySupplier);
            // print_r("-------2222222222---------");
            // var_dump($delObj);
            // exit;

            $resultObject = $this->json->editExpressNoApi($_requestOb, $deliverySupplier);
            echo $resultObject->status;
        }
        exit;
    }

    // 封装银行列表
    public function genbankaccountlistAction() {
        $bankOptions = "";
        $_requestOb = $this->_requestObject;
        $resultObject = $this->json->listBankAccountApi($_requestOb, null, null, null, null, null);
        $bankAccountList = json_decode($resultObject)->result;
        foreach ($bankAccountList as $bankAccount) {
            $bankOptions .= '<option value="' . $bankAccount->bankNo . '">' . $bankAccount->bankName . '</option>';
        }
        echo $bankOptions;
        exit;
    }

    //订单跟踪日志
    public function trackAction()
    {
        // 请求Hessian服务端方法
        $_requestOb = $this->_requestObject;
        //    $_orderID='EB7E79BD-A9B9-42DC-CBB5-D431264ADC25';
        $_orderID = $this->_request->getParam('orderID');
        $_view = $this->_request->getParam('view');
        if (empty($_view)) {
            $_view = 'date';
        }
        $_resultData = $this->json->getOrderEventLogApi($_requestOb, $_orderID, $_view);
        $existData = json_decode($_resultData);
        $trackData = $existData->result;
        $tracklist = $this->objectToArray($trackData);
        $this->view->tracklog = $tracklist;
        $this->view->trackview = $_view;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/sale/track.phtml");
            echo $content;
            exit;
        }
    }

    //报关单
    public function declarationAction()
    {
        // 请求Hessian服务端方法
        //	$_orderID='EB7E79BD-A9B9-42DC-CBB5-D431264ADC25';
        $_orderID = $this->_request->getParam('orderID');
        $_requestOb = $this->_requestObject;
        $_resultData = $this->json->listDeclarationApi($_requestOb, $_orderID);
        $bgdData = json_decode($_resultData);
        $bgOb = $bgdData->result;
        $bgd = $this->objectToArray($bgOb);

        $this->view->bgd = $bgd;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/sale/declaration.phtml");
            echo $content;
            exit;
        }
    }

//派车单
    public function truckingAction()
    {

        // 请求Hessian服务端方法
        $_orderID = $this->_request->getParam('orderID');
        $_requestOb = $this->_requestObject;
        $_resultData = $this->json->listTruckingOrderApi($_requestOb, $_orderID);
        $pcdData = json_decode($_resultData);
        $pcOb = $pcdData->result;
        $pcd = $this->objectToArray($pcOb);
        $this->view->pcd = $pcd;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/sale/trucking.phtml");
            echo $content;
            exit;
        }
    }

    // 订仓单
    public function shippingAction()
    {

        // 请求Hessian服务端方法
        $_orderID = $this->_request->getParam('orderID');
        $_requestOb = $this->_requestObject;
        $_resultData = $this->json->listShippingOrderApi($_requestOb, $_orderID);
        $dcdData = json_decode($_resultData);
        $dcOb = $dcdData->result;
        $dcd = $this->objectToArray($dcOb);
        $this->view->dcd = $dcd;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/sale/shipping.phtml");
            echo $content;
            exit;
        }
    }

    // 结算 模块交易列表
    public function tradingAction() {
        // 请求Hessian服务端方法
        $_orderID = $this->_request->getParam('orderID');
        $_requestOb = $this->_requestObject;
        $_resultData = $this->json->Trading4OrderApi($_requestOb, $_orderID);
        $jsdData = json_decode($_resultData);
        $jsdOb = $jsdData->result;
        $jsd = $this->objectToArray($jsdOb);
        $this->view->jsd = $jsd;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/sale/trading.phtml");
            echo $content;
            exit;
        }
    }

    // 订单文档
    public function attachmentAction() {
        // 请求Hessian服务端方法
        $_orderID = $this->_request->getParam('orderID');
        $_requestOb = $this->_requestObject;
        $_resultData = $this->json->listOrderAttachment($_requestOb, $_orderID);
        $docData = json_decode($_resultData);
        $docOb = $docData->result;
        $doc = $this->objectToArray($docOb);

        //判断文档下级分类重新组装数据
        $this->view->ODOD = array();    // 订单附件
        $this->view->ODTA = array();
        $this->view->ODSE = array();
        $this->view->ODEG = array();
        $this->view->ODQS = array();
        $this->view->CRCT = array();
        $this->view->ODBQ = array();
        $this->view->CRSE = array();
        $this->view->ODQA = array();
        $this->view->ODVQ = array();
        $this->view->ODRG = array();
        $this->view->ODPG = array();
        $this->view->ODDG = array();
        $this->view->SOTM = array();    // 订舱单
        $this->view->TOTM = array();    // 派车单
        $this->view->EDCT = array();    // 报关单
        $this->view->PL4C = array();    // 装箱单

        $this->view->DLPG = array();    // 备货相关附件
        $this->view->DLEG = array();    // 验货相关附件
        $this->view->DLDG = array();    // 发货相关附件
        $this->view->DLRG = array();    // 收货相关附件
        $this->view->DLQT = array();    // 质量保证函模板
        $this->view->DLQF = array();    // 质量保证函正本
        $this->view->DLCT = array();    // 收货确认函模板
        $this->view->DLCF = array();    // 收货确认函正本

        foreach ($doc as $k => $v) {
            if ($v['attachType'] == "SOTM") {
                $this->view->SOTM[] = $v;
                //订舱单
                $this->view->SOTM_name = $this->view->translate('booking') . '(' . $v['attachType'] . ')';
            }

            if ($v['attachType'] == "TOTM") {
                $this->view->TOTM[] = $v;
                //派车单
                $this->view->TOTM_name = $this->view->translate('carsbook') . '(' . $v['attachType'] . ')';
            }

            if ($v['attachType'] == "EDCT") {
                $this->view->EDCT[] = $v;
                //报关单
                $this->view->EDCT_name = $this->view->translate('customs') . '(' . $v['attachType'] . ')';
            }

            if ($v['attachType'] == "PL4C") {
                $this->view->PL4C[] = $v;
                //装箱单
                $this->view->PL4C_name = $this->view->translate('packing') . '(' . $v['attachType'] . ')';
            }
            if ($v['attachType'] == "ODOD") {
                $this->view->ODOD[] = $v;
                //订单附件
                $this->view->ODOD_name = $this->view->translate('orderATCH') . '(' . $v['attachType'] . ')';
            }
            if ($v['attachType'] == "ODTA") {
                $this->view->ODTA[] = $v;
                //委托书
                $this->view->ODTA_name = $this->view->translate('proxyNo') . '(' . $v['attachType'] . ')';
            }

            if ($v['attachType'] == "ODSE") {

                $this->view->ODSE[] = $v;
                //盖章委托书
                $this->view->ODSE_name = $this->view->translate('delegation') . '(' . $v['attachType'] . ')';
            }

            if ($v['attachType'] == "CRCT") {

                $this->view->CRCT[] = $v;
                //合同范本
                $this->view->CRCT_name = $this->view->translate('contract_tmp') . '(' . $v['attachType'] . ')';
            }

            if ($v['attachType'] == "CRSE") {
                $this->view->CRSE[] = $v;
                //盖章合同
                $this->view->CRSE_name = $this->view->translate('contract_seal') . '(' . $v['attachType'] . ')';
            }

            if ($v['attachType'] == "ODBQ") {
                $this->view->ODBQ[] = $v;
                //买家计价单
                $this->view->ODBQ_name = $this->view->translate('buyers') . $this->view->translate('valuationNo') . '(' . $v['attachType'] . ')';
            }
            if ($v['attachType'] == "ODVQ") {
                $this->view->ODVQ[] = $v;
                //卖家计价单
                $this->view->ODVQ_name = $this->view->translate('seller') . $this->view->translate('valuationNo') . '(' . $v['attachType'] . ')';
            }

            if ($v['attachType'] == "ODQA") {
                $this->view->ODQA[] = $v;
                //质保函范本
                $this->view->ODQA_name = $this->view->translate('quality_tmp') . '(' . $v['attachType'] . ')';
            }

            if ($v['attachType'] == "ODQS") {
                $this->view->ODQS[] = $v;
                //盖章质保函
                $this->view->ODQS_name = $this->view->translate('quality_seal') . '(' . $v['attachType'] . ')';
            }

            // *******************

            // 备货相关附件
            if ($v['attachType'] == "DLPG") {
                $this->view->DLPG[] = $v;
                $this->view->DLPG_name = $this->view->translate('stockVIEW') . '(' . $v['attachType'] . ')';
            }

            // 验货相关附件
            if ($v['attachType'] == "DLEG") {
                $this->view->DLEG[] = $v;
                $this->view->DLEG_name = $this->view->translate('stockVIEW') . '(' . $v['attachType'] . ')';
            }

            // 发货相关附件
            if ($v['attachType'] == "DLDG") {
                $this->view->DLDG[] = $v;
                $this->view->DLDG_name = $this->view->translate('deliverVIEW') . '(' . $v['attachType'] . ')';
            }

            // 收货相关附件
            if ($v['attachType'] == "DLRG") {
                $this->view->DLRG[] = $v;
                $this->view->DLRG_name = $this->view->translate('receivingVIEW') . '(' . $v['attachType'] . ')';
            }

            // 质量保证函模板
            if ($v['attachType'] == "DLQT") {
                $this->view->DLQT[] = $v;
                $this->view->DLQT_name = $this->view->translate('quality_tmp') . '(' . $v['attachType'] . ')';
            }

            // 质量保证函正本
            if ($v['attachType'] == "DLQF") {
                $this->view->DLQF[] = $v;
                $this->view->DLQF_name = $this->view->translate('quality_seal') . '(' . $v['attachType'] . ')';
            }

            // 收货确认函模板
            if ($v['attachType'] == "DLCT") {
                $this->view->DLCT[] = $v;
                $this->view->DLCT_name = $this->view->translate('receipt_confirmation_templ') . '(' . $v['attachType'] . ')';
            }

            // 收货确认函正本
            if ($v['attachType'] == "DLCF") {
                $this->view->DLCF[] = $v;
                $this->view->DLCF_name = $this->view->translate('receipt_confirmation_formal') . '(' . $v['attachType'] . ')';
            }
        }

        $this->view->doc = $doc;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/sale/attachment.phtml");
            echo $content;
            exit;
        }
    }
}
