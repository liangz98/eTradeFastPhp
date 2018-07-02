   <?php
class PurchaseController extends Kyapi_Controller_Action
{
	public function preDispatch()
	{
		$this->view->cur_pos = 'purchase';
        //清除 免登陆session
        $this->IsAuth($this->view->visitor);

        if(empty($this->view->userID)){
            // 提示：请先登录系统
			Mobile_Browser::redirect($this->view->translate('tip_login_please'),$this->view->seed_Setting['user_app_server']."/login");
		}
//		if(empty($this->view->CompProductAdmin)){
//			Mobile_Browser::redirect('暂无权限访问！',$this->view->seed_Setting['user_app_server']."/");
//		}
		$this->view->cur_pos = $this->_request->getParam('controller');
		$cururl = $this->getRequestUri();
		if ($cururl == '/purchase') {
			$this->_request->setParam('all', 'all');
			$this->indexAction();
			exit;
		}

		preg_match('/(.*)\.html/', $cururl, $arr);

		if (isset($arr[1]) && !empty($arr[1])) {


			preg_match_all('/^\/user\/purchase\/(index|orderlist)-([\d]+)-([\d]+).html/isU', $cururl, $arr);

			if (is_array($arr) && count($arr) > 1) {
				$this->_request->setParam('mod', $arr[1][0]);
				$this->_request->setParam('status', $arr[2][0]);
				$this->_request->setParam('page', $arr[3][0]);
				if($arr[1][0]=='orderlist'){
					$this->orderlistAction();
				}
				$this->indexAction();
				exit;
			}
//没有找到相关信息！
			Mobile_Browser::redirect($this->view->translate('tip_find_no'), $this->view->seed_BaseUrl . "/");

		}
	}

    public function indexAction() {
        $this->view->resultMsg = $this->_request->getParam('resultMsg');

        // 统计所有商品数量
        $resultObject = $this->json->countPurProductApi($this->_requestObject);
        $countPurProduct = $this->objectToArray(json_decode($resultObject));
        $this->view->countPurProduct = $countPurProduct['result'];

        // 设置视图商品状态
        $this->view->status == '00' ? $linked = 'edit' : $linked = 'view';
        $this->view->linked = $linked;

        // bootstrap-table查询状态
        if (empty($this->_request->getParam('productStatus'))) {
            $this->view->productStatus = '03';
        } else {
            $this->view->productStatus = $this->_request->getParam('productStatus');
        }

        // 附件地址
        $this->view->attachUrl = $this->view->seed_Setting['KyUrlex'];

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/purchase/index.phtml");
            echo $content;
            exit;
        }
    }

    public function purListAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $queryParams = array();
        $productStatus = strval($this->_request->getParam('productStatus'));
        if (empty($productStatus)) {
            $productStatus = '03';
        }
        $queryParams['productStatus'] = $productStatus;

        $supplierID = $this->_request->getParam('supplierID');
        if (!empty($supplierID)) {
            $queryParams['supplierID'] = $supplierID;
        }

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

        $resultObject = $this->json->listPurProductApi($requestObject, $queryParams, $querySorts, $keyword, $skip, $limit);
        $msg["total"] = json_decode($resultObject)->extData->totalSize;
        $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    public function purList4OrderAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $queryParams = array();
        $productStatus = strval($this->_request->getParam('productStatus'));
        if (empty($productStatus)) {
            $productStatus = '03';
        }
        $queryParams['productStatus'] = $productStatus;

        $supplierID = $this->_request->getParam('supplierID');
        if (!empty($supplierID)) {
            $queryParams['supplierID'] = $supplierID;
        } else {
            $msg["total"] = 0;
            $msg["rows"] = null;

            echo json_encode($msg);
            exit;
        }

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

        $resultObject = $this->json->listPurProductApi($requestObject, $queryParams, $querySorts, $keyword, $skip, $limit);
        $msg["total"] = json_decode($resultObject)->extData->totalSize;
        $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    public function countPurProductAjaxAction() {
        $requestObject = $this->_requestObject;

        $resultObject = $this->json->countPurProductApi($requestObject);
        $msg = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    public function addAction() {
        $requestObject = $this->_requestObject;

        // 封装供应商
        $queryParams = array();
        $querySorts = array();
        $querySorts['createTime'] = "DESC";
        $keyword = null;
        $limit = 10;
        $skip = 0;
        if (is_array($queryParams)) {
            $queryParams = $this->arrayToObject($queryParams);
        }
        if (is_array($querySorts)) {
            $querySorts = $this->arrayToObject($querySorts);
        }
        $vendorResultObject = $this->json->listVendorPartnerApi($requestObject, $queryParams, $querySorts, $keyword, $skip, $limit);
        $vendorResult = json_decode($vendorResultObject)->result;
        $vendorList = array();
        foreach ($vendorResult as $key => $item) {
            $vendor = array();
            $vendor["id"] = $item->toID;
            $vendor["name"] = $item->toName;

            $vendorList[$key] = $vendor;
        }
        $this->view->vendorList = $vendorList;

        if ($this->_request->isPost()) {
            $attachList = array();
            $attachList["attachID"] = $this->_request->getParam('attachNid');
            $attachList["attachType"] = $this->_request->getParam('attachType');
            $attachList["attachName"] = $this->_request->getParam("attachName");
            $attachList["attachSize"] = $this->_request->getParam("attachSize");
            $_attach2 = array();
            foreach ($attachList as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $_attach2[$k1][$k] = $v1;
                }
            }
            $_attachList = array();
            foreach ($_attach2 as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $_attachList[$k] = array();
                    $_attachList[$k]['attachID'] = $_attach2[$k]['attachID'];
                    $_attachList[$k]['attachType'] = '0000';
                    $_attachList[$k]['bizType'] = "PD";
                    $_attachList[$k]['name'] = $_attach2[$k]['attachName'];
                    $_attachList[$k]['size'] = (int)$_attach2[$k]['attachSize'];

                }
            }
            if ($this->_request->getParam('needInspection') == 1) {
                $needInSP = true;
            } else {
                $needInSP = false;
            }

            // 实例化商品类
            $_goods = new Kyapi_Model_product();
            // 添加商品信息
            $_goods->productName = $this->_request->getParam('productName');//商品名
            $_goods->productEnName = $this->_request->getParam('productEnName');//EN商品名
            $_goods->productBrand = $this->_request->getParam('productBrand');//商品品牌
            $_goods->productModel = $this->_request->getParam('productModel');//商品型号
            //				$_goods->unitPrice= (double)$this->_request->getParam('unitPrice');//销售单价
            $_goods->crnCode = "CNY";//采购货币
            $_goods->productionMode = "02";//生产方式
            $_goods->purchaseUnitPrice = (double)$this->_request->getParam('purchaseUnitPrice');//采购单价
            $_goods->purchaseCrnCode = $this->_request->getParam('purchaseCrnCode');//采购货币
            $_goods->pricingUnit = $this->_request->getParam('pricingUnit');//交易单位
            $_goods->legalPricingUnit = $this->_request->getParam('legalPricingUnit');//法定计价单位
            $_goods->legalPricingUnit2 = $this->_request->getParam('legalPricingUnit2');//法定计价单位
            $_goods->hscode = $this->_request->getParam('hscode');//hscode
            $_goods->taxRate = (double)$this->_request->getParam('taxRate');//增值税率
            $_goods->rebateRate = (double)$this->_request->getParam('rebateRate');//退税率
            $_goods->declareElements = $this->_request->getParam('declareElements');//申报要素
            $_goods->functionUsage = $this->_request->getParam('functionUsage');//功能用途
            $_goods->productSize = $this->_request->getParam('productSize');//尺寸规格
            $_goods->productMaterial = $this->_request->getParam('productMaterial');//商品材质
            $_goods->supplierID = $this->_request->getParam('supplierID');//供应商ID/
            $_goods->packingVolume = (double)$this->_request->getParam('packingVolume');//包装体积
            $_goods->packingType = $this->_request->getParam('packingType');//包装类型
            $_goods->netWeight = (double)$this->_request->getParam('netWeight');//净重
            $_goods->grossWeight = (double)$this->_request->getParam('grossWeight');//毛重
            $_goods->needInspection = $needInSP;//是否商检
            $_goods->attachmentList = $_attachList;//商品附件


            $_resultData = $this->json->addPurProductApi($requestObject, $_goods);
            $existData = json_decode($_resultData);

            // 页面跳转
            if ($existData->status != 1) {
                // 统计所有商品数量
                $countResultObject = $this->json->countPurProductApi($requestObject);
                $countPurProduct = $this->objectToArray(json_decode($countResultObject));
                $this->view->countPurProduct = $countPurProduct['result'];
                // 附件地址
                $this->view->attachUrl = $this->view->seed_Setting['KyUrlex'];
                // bootstrap-table查询状态
                $this->view->productStatus = '00';

                $this->view->goods = $this->objectToArray($_goods);
                $this->view->errMsg = $this->view->translate('tip_add_fail') . $existData->error;
            } else {
                $resultMsg = base64_encode($this->view->translate('tip_add_success'));
                $this->redirect("/purchase/index?resultMsg=".$resultMsg."&productStatus=00");
            }
        }
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/purchase/add.phtml");
            echo $content;
            exit;
        }
    }

    public function editAction() {
        $requestObject = $this->_requestObject;

        $productID = $_SERVER['QUERY_STRING'];
        $_productID = base64_decode($productID);
        $_resultData = $this->json->getProductApi($requestObject, $_productID);
        $existData = json_decode($_resultData);
        $existArr = $this->objectToArray($existData);
        $this->view->goods = $existArr['result'];
        $this->view->exARR = $existArr;

        // 封装供应商
        $queryParams = array();
        $querySorts = array();
        $querySorts['createTime'] = "DESC";
        $keyword = null;
        $limit = 10;
        $skip = 0;
        if (is_array($queryParams)) {
            $queryParams = $this->arrayToObject($queryParams);
        }
        if (is_array($querySorts)) {
            $querySorts = $this->arrayToObject($querySorts);
        }
        $vendorResultObject = $this->json->listVendorPartnerApi($requestObject, $queryParams, $querySorts, $keyword, $skip, $limit);
        $vendorResult = json_decode($vendorResultObject)->result;
        $vendorList = array();
        foreach ($vendorResult as $key => $item) {
            $vendor = array();
            $vendor["id"] = $item->toID;
            $vendor["name"] = $item->toName;

            $vendorList[$key] = $vendor;
        }
        $this->view->vendorList = $vendorList;

        if ($this->_request->isPost()) {
            //获取附件ID
            $attachList = array();
            $attachList["attachID"] = $this->_request->getParam('attachNid');
            $attachList["attachType"] = $this->_request->getParam('attachType');
            $attachList["attachName"] = $this->_request->getParam("attachName");
            $attachList["attachSize"] = $this->_request->getParam("attachSize");
            $_attach2 = array();
            foreach ($attachList as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $_attach2[$k1][$k] = $v1;
                }
            }
            $_attachList = array();
            foreach ($_attach2 as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $_attachList[$k] = new Kyapi_Model_Attachment();
                    $_attachList[$k]->attachID = $_attach2[$k]['attachID'];
                    $_attachList[$k]->attachType = "PDPD";
                    $_attachList[$k]->bizType = "PD";
                    $_attachList[$k]->name = $_attach2[$k]['attachName'];
                    $_attachList[$k]->size = (int)$_attach2[$k]['attachSize'];

                }
            }

            if ($this->_request->getParam('needInspection') == 1) {
                $needInSP = true;
            } else {
                $needInSP = false;
            }

            // 实例化商品类
            $_goods = new Kyapi_Model_product();
            // 编辑商品信息
            $_goods->productID = $_productID;//商品ID
            $_goods->productName = $this->_request->getParam('productName');//商品名
            $_goods->productEnName = $this->_request->getParam('productEnName');//EN商品名
            $_goods->productBrand = $this->_request->getParam('productBrand');//商品品牌
            $_goods->productModel = $this->_request->getParam('productModel');//商品型号
            $_goods->unitPrice = $existArr['result']['unitPrice'];//销售单价
            //				$_goods->crnCode= $this->_request->getParam('crnCode');//货币代码
            $_goods->purchaseUnitPrice = (double)$this->_request->getParam('purchaseUnitPrice');//采购单价
            $_goods->purchaseCrnCode = $this->_request->getParam('purchaseCrnCode');//采购货币
            $_goods->crnCode = "CNY";//采购货币
            $_goods->productionMode = "02";//生产方式
            $_goods->pricingUnit = $this->_request->getParam('pricingUnit');//交易单位
            $_goods->legalPricingUnit = $this->_request->getParam('legalPricingUnit');//法定计价单位
            $_goods->legalPricingUnit2 = $this->_request->getParam('legalPricingUnit2');//法定计价单位
            $_goods->hscode = $this->_request->getParam('hscode');//hscode
            $_goods->taxRate = (double)$this->_request->getParam('taxRate');//增值税率
            $_goods->rebateRate = (double)$this->_request->getParam('rebateRate');//退税率
            $_goods->declareElements = $this->_request->getParam('declareElements');//申报要素
            $_goods->functionUsage = $this->_request->getParam('functionUsage');//功能用途
            $_goods->productSize = $this->_request->getParam('productSize');//尺寸规格
            $_goods->productMaterial = $this->_request->getParam('productMaterial');//商品材质
            $_goods->supplierID = $this->_request->getParam('supplierID');//供应商ID
            $_goods->packingVolume = (double)$this->_request->getParam('packingVolume');//包装体积
            $_goods->packingType = $this->_request->getParam('packingType');//包装类型
            $_goods->netWeight = (double)$this->_request->getParam('netWeight');//净重
            $_goods->grossWeight = (double)$this->_request->getParam('grossWeight');//毛重
            $_goods->needInspection = $needInSP;//是否商检
            $_goods->attachmentList = $_attachList;//商品附件

            $_resultData = $this->json->editPurProductApi($requestObject, $_goods);
            $existData = json_decode($_resultData);

            if ($existData->status != 1) {
                // 统计所有商品数量
                $countResultObject = $this->json->countPurProductApi($requestObject);
                $countPurProduct = $this->objectToArray(json_decode($countResultObject));
                $this->view->countPurProduct = $countPurProduct['result'];
                // 附件地址
                $this->view->attachUrl = $this->view->seed_Setting['KyUrlex'];

                $this->view->goods = $this->objectToArray($_goods);
                $this->view->errMsg = $this->view->translate('tip_edit_fail') . $existData->error;
            } else {
                $goodsResultObject = $this->json->getProductApi($requestObject, $_goods->productID);
                $goodsResultObject = json_decode($goodsResultObject);
                $resultMsg = base64_encode($this->view->translate('tip_edit_success'));
                $this->redirect("/purchase/index?resultMsg=".$resultMsg."&productStatus=".$goodsResultObject->result->productStatus);
            }
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/purchase/edit.phtml");
            echo $content;
            exit;
        }
    }

    public function copyAction() {
        $requestObject = $this->_requestObject;

        $productID = $_SERVER['QUERY_STRING'];
        $_productID = base64_decode($productID);
        $_resultData = $this->json->getProductApi($requestObject, $_productID);
        $existData = json_decode($_resultData);
        $existArr = $this->objectToArray($existData);
        $this->view->goods = $existArr['result'];
        $this->view->exARR = $existArr;

        // 封装供应商
        $queryParams = array();
        $querySorts = array();
        $querySorts['createTime'] = "DESC";
        $keyword = null;
        $limit = 10;
        $skip = 0;
        if (is_array($queryParams)) {
            $queryParams = $this->arrayToObject($queryParams);
        }
        if (is_array($querySorts)) {
            $querySorts = $this->arrayToObject($querySorts);
        }
        $vendorResultObject = $this->json->listVendorPartnerApi($requestObject, $queryParams, $querySorts, $keyword, $skip, $limit);
        $vendorResult = json_decode($vendorResultObject)->result;
        $vendorList = array();
        foreach ($vendorResult as $key => $item) {
            $vendor = array();
            $vendor["id"] = $item->toID;
            $vendor["name"] = $item->toName;

            $vendorList[$key] = $vendor;
        }
        $this->view->vendorList = $vendorList;

        if ($this->_request->isPost()) {
            //获取附件ID
            $attachList = array();
            $attachList["attachID"] = $this->_request->getParam('attachNid');
            $attachList["attachType"] = $this->_request->getParam('attachType');
            //                $attachList["bizType"] = $this->_request->getParam("bizType");
            $attachList["attachName"] = $this->_request->getParam("attachName");
            $attachList["attachSize"] = $this->_request->getParam("attachSize");
            $_attach2 = array();
            foreach ($attachList as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $_attach2[$k1][$k] = $v1;
                }
            }
            $_attachList = array();
            foreach ($_attach2 as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $_attachList[$k] = array();
                    $_attachList[$k]['attachID'] = $_attach2[$k]['attachID'];
                    $_attachList[$k]['attachType'] = '0000';
                    $_attachList[$k]['bizType'] = "PD";
                    $_attachList[$k]['name'] = $_attach2[$k]['attachName'];
                    $_attachList[$k]['size'] = (int)$_attach2[$k]['attachSize'];

                }
            }

            if ($this->_request->getParam('needInspection') == 1) {
                $needInSP = true;
            } else {
                $needInSP = false;
            }


            $_goods = new Kyapi_Model_product();
            // 实例化商品类
            // add商品信息
            // $_goods->productID= $_productID;//商品ID(复制新增不需要商品ID)
            $_goods->productName = $this->_request->getParam('productName');//商品名
            $_goods->productEnName = $this->_request->getParam('productEnName');//EN商品名
            $_goods->productBrand = $this->_request->getParam('productBrand');//商品品牌
            $_goods->productModel = $this->_request->getParam('productModel');//商品型号
            $_goods->unitPrice = $existArr['result']['unitPrice'];//销售单价
            //				$_goods->crnCode= $this->_request->getParam('crnCode');//货币代码
            $_goods->purchaseUnitPrice = (double)$this->_request->getParam('purchaseUnitPrice');//采购单价
            $_goods->purchaseCrnCode = $this->_request->getParam('purchaseCrnCode');//采购货币
            $_goods->crnCode = "CNY";//采购货币
            $_goods->productionMode = "02";//生产方式
            $_goods->pricingUnit = $this->_request->getParam('pricingUnit');//交易单位
            $_goods->legalPricingUnit = $this->_request->getParam('legalPricingUnit');//法定计价单位
            $_goods->legalPricingUnit2 = $this->_request->getParam('legalPricingUnit2');//法定计价单位
            $_goods->hscode = $this->_request->getParam('hscode');//hscode
            $_goods->taxRate = (double)$this->_request->getParam('taxRate');//增值税率
            $_goods->rebateRate = (double)$this->_request->getParam('rebateRate');//退税率
            $_goods->declareElements = $this->_request->getParam('declareElements');//申报要素
            $_goods->functionUsage = $this->_request->getParam('functionUsage');//功能用途
            $_goods->productSize = $this->_request->getParam('productSize');//尺寸规格
            $_goods->productMaterial = $this->_request->getParam('productMaterial');//商品材质
            $_goods->supplierID = $this->_request->getParam('supplierID');//供应商ID
            $_goods->packingVolume = (double)$this->_request->getParam('packingVolume');//包装体积
            $_goods->packingType = $this->_request->getParam('packingType');//包装类型
            $_goods->netWeight = (double)$this->_request->getParam('netWeight');//净重
            $_goods->grossWeight = (double)$this->_request->getParam('grossWeight');//毛重
            $_goods->needInspection = $needInSP;    // 是否商检
            $_goods->attachmentList = $_attachList; // 商品附件

            $_resultData = $this->json->addPurProductApi($requestObject, $_goods);
            $existData = json_decode($_resultData);

            if ($existData->status != 1) {
                // 统计所有商品数量
                $countResultObject = $this->json->countPurProductApi($requestObject);
                $countPurProduct = $this->objectToArray(json_decode($countResultObject));
                $this->view->countPurProduct = $countPurProduct['result'];
                // 附件地址
                $this->view->attachUrl = $this->view->seed_Setting['KyUrlex'];
                // bootstrap-table查询状态
                $this->view->productStatus = '00';

                $this->view->goods = $this->objectToArray($_goods);
                $this->view->errMsg = $this->view->translate('tip_add_fail') . $existData->error;
            } else {
                $resultMsg = base64_encode($this->view->translate('tip_add_success'));
                $this->redirect("/purchase/index?resultMsg=".$resultMsg."&productStatus=00");
            }
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/purchase/copy.phtml");
            echo $content;
            exit;
        }
    }

    public function viewAction() {
        $requestObject = $this->_requestObject;

        $productID = $_SERVER['QUERY_STRING'];
        $_productID = base64_decode($productID);
        $_resultData = $this->json->getProductApi($requestObject, $_productID);
        $existData = json_decode($_resultData);
        $existArr = $this->objectToArray($existData);
        $this->view->goods = $existArr['result'];

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/purchase/view.phtml");
            echo $content;
            exit;
        }
    }

    public function forreviewAction() {
        //提示：提交审核
        if ($this->_request->isPost()) {
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb = $this->_requestObject;
            $opData = $this->json->forReviewProductApi($_requestOb, $_objID);
            echo $opData;
        }
        exit;

    }

    public function invalidAction() {
        //禁用
        if ($this->_request->isPost()) {
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb = $this->_requestObject;
            $opData = $this->json->invalidProductApi($_requestOb, $_objID);
            echo $opData;
        }
        exit;

    }

    public function validAction() {
        //启用
        if ($this->_request->isPost()) {
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb = $this->_requestObject;
            $opData = $this->json->validProductApi($_requestOb, $_objID);
            echo $opData;
        }
        exit;
    }

    public function confrimAction() {
        //确认
        if ($this->_request->isPost()) {
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb = $this->_requestObject;
            $opData = $this->json->confirmProductApi($_requestOb, $_objID);
            echo $opData;
        }
        exit;
    }

    public function delAction() {
        //删除
        if ($this->_request->isPost()) {
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb = $this->_requestObject;
            $opData = $this->json->delProductApi($_requestOb, $_objID);
            echo $opData;
        }
        exit;
    }

    public function orderlistAction() {
        try {
            $f1 = new Seed_Filter_Alnum();


            $mod = $f1->filter($this->_request->getParam('mod'));
            if (empty($mod)) {
                $mod = "orderlist";
            }

            $_PStatus = strval($this->_request->getParam('status'));
            if (empty($_PStatus)) {
                $_PStatus = '03';
            }

            $_querySorts = $this->_request->getParam('querySorts');
            if (empty($_querySorts)) {
                $_querySorts = null;
            }

            $_keyword = $this->_request->getParam('keyword');
            if (empty($_keyword)) {
                $_keyword = null;
            }
            $this->view->keyword = $_keyword;
            $_toID = $this->_request->getParam("toID");

            $_id = $this->_request->getParam('id');
            if (empty($_id)) {
                $_id = null;
            }
            $idArr = explode("|", $_id);

            $page = intval($this->_request->getParam('page'));
            if ($page < 1)
                $page = 1;
            $_limit = 6;
            $_skip = $_limit * ($page - 1);

            $_queryP = new queryProductPur();
            $_queryP->productStatus = $_PStatus;
            $_queryP->supplierID = $_toID;

            $_resultData = $this->json->countPurProductApi($this->_requestObject);
            $goodsCount = json_decode($_resultData);

            //统计所有商品数量
            $clConut = $goodsCount->result;
            $this->view->clConut = $clConut;
            //获取商品列表信息
            $_goodsData = $this->json->listPurProductApi($this->_requestObject, $_queryP, null, $_keyword, $_skip, $_limit);
            $goodsData = json_decode($_goodsData);
            $existDatt = $this->objectToArray($goodsData);

            foreach ($existDatt['result'] as $k => $v) {
                if (in_array($v['productID'], $idArr)) {
                    $existDatt['result'][$k]['isArr'] = '1';
                } else {
                    $existDatt['result'][$k]['isArr'] = null;
                }
            }
            $this->view->e = $existDatt['result'];
            $this->view->gid = $_id;

            //统计正常状态数量、分页
            $existCount = $existDatt['extData'];
            $total = $existCount['totalSize'];
            $page = $existCount['totalPage'];
            //设置视图商品状态
            $this->view->status = $_PStatus;
            $this->view->status == '00' ? $linked = 'edit' : $linked = 'view';
            $this->view->linked = $linked;

            $file = "user/purchase/" . $mod . "-" . $_PStatus;
            $_limit = 6;
            $pageObj = new Seed_Page($this->_request, $total, $_limit);
            $this->view->page = $pageObj->getPageArray();
            $this->view->page['pageurl'] = '/' . $file;

            if ($page > $this->view->page['totalpage'])
                $page = $this->view->page['totalpage'];
            if ($page < 1)
                $page = 1;
        } catch (Exception $e) {
            Shop_Browser::redirect($e->getMessage());
        }
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/purchase/orderlist.phtml");
            echo $content;
            exit;
        }
    }
}
