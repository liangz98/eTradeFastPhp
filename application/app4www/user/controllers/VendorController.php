<?php

class VendorController extends Kyapi_Controller_Action {
    /**入口文件**/
    public function preDispatch() {
        $this->view->cur_pos = 'vendor';
        //清除 免登陆session
        $this->IsAuth($this->view->visitor);

        if (empty($this->view->userID)) {
            // 提示：请先登录系统
            Mobile_Browser::redirect($this->view->translate('tip_login_please'), $this->view->seed_Setting['user_app_server'] . "/login");
        }
        //		if(empty($this->view->CompPartnerAdmin)){
        //			Mobile_Browser::redirect('暂无权限访问！',$this->view->seed_Setting['user_app_server']."/");
        //		}
        $this->view->cur_pos = $this->_request->getParam('controller');
        $cururl = $this->getRequestUri();
        if ($cururl == '/vendor') {
            $this->_request->setParam('all', 'all');
            $this->indexAction();
            exit;
        }

        preg_match('/(.*)\.html/', $cururl, $arr);

        if (isset($arr[1]) && !empty($arr[1])) {


            preg_match_all('/^\/user\/vendor\/(index|contact|orderlist|superlist)-([\d]+)-([\d]+).html/isU', $cururl, $arr);

            if (is_array($arr) && count($arr) > 1) {
                $this->_request->setParam('mod', $arr[1][0]);
                $this->_request->setParam('status', $arr[2][0]);
                $this->_request->setParam('page', $arr[3][0]);
                if ($arr[1][0] == 'contact') {
                    $this->contactAction();
                } else if ($arr[1][0] == 'orderlist') {
                    $this->orderlistAction();
                } else if ($arr[1][0] == 'superlist') {
                    $this->superlistAction();
                }
                $this->indexAction();
                exit;
            }
            //没有找到相关信息！
            Mobile_Browser::redirect($this->view->translate('tip_find_no'), $this->view->seed_BaseUrl . "/");

        }

        // 更新session时间
        $this->updateRedisExpire();
    }

    // 合作伙伴列表
    public function indexAction() {
        $requestObject = $this->_requestObject;
        $this->view->resultMsg = $this->_request->getParam('resultMsg');

        //合作伙伴状态 数量统计
        $resultObject = $this->json->countVendorPartnerApi($requestObject);
        $countVendorPartner = $this->objectToArray(json_decode($resultObject));
        $this->view->countResult = $countVendorPartner['result'];

        // bootstrap-table查询状态
        if (empty($this->_request->getParam('partnerStatus'))) {
            $this->view->partnerStatus = '01';
        } else {
            $this->view->partnerStatus = $this->_request->getParam('partnerStatus');
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/vendor/index.phtml");
            echo $content;
            exit;
        }
    }

    /**
     * 卖家列表
     */
    public function vendorListAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $queryParams = array();
        $partnerStatus = strval($this->_request->getParam('partnerStatus'));
        if (empty($partnerStatus)) {
            $partnerStatus = '01';
        }
        $queryParams['partnerStatus'] = $partnerStatus;

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

        $resultObject = $this->json->listVendorPartnerApi($requestObject, $queryParams, $querySorts, $keyword, $skip, $limit);
        $msg["total"] = json_decode($resultObject)->extData->totalSize;
        $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    /**
     * 卖家列表 - 销售订单贸易商身份,
     */
    public function vendorListForSaleAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $queryParams = array();
        $partnerStatus = strval($this->_request->getParam('partnerStatus'));
        if (empty($partnerStatus)) {
            $partnerStatus = '01';
        }
        $queryParams['partnerStatus'] = $partnerStatus;

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

        $resultObject = $this->json->listVendorPartnerApi($requestObject, $queryParams, $querySorts, $keyword, $skip, $limit);
        $msg["total"] = json_decode($resultObject)->extData->totalSize;
        $msg["rows"] = json_decode($resultObject)->result;

        $accountResultObject = $this->json->getAccountApi($requestObject);
        $account = json_decode($accountResultObject)->result;
        $vendor = array();
        $vendor['toID'] = $account->accountID;
        $vendor['toName'] = $account->accountName;
        $vendor['toEnName'] = $account->accountEnName;
        $vendor['industryCode'] = $account->industryCode;
        $vendor['regdCountryCode'] = $account->regdCountryCode;
        $vendor['partnerStatus'] = $account->accountStatus;
        $msg["rows"][] = $vendor;
        $msg["total"] = (int)($msg["total"]) + 1;

        echo json_encode($msg);
        exit;
    }

    //添加订单卖家
    public function orderlistAction() {
        try {
            $f1 = new Seed_Filter_Alnum();
            $mod = $f1->filter($this->_request->getParam('mod'));
            if (empty($mod)) {
                $mod = "orderlist";
            }

            $_PStatus = strval($this->_request->getParam('status'));
            if (empty($_PStatus)) {
                $_PStatus = '01';
            }

            $_queryP = new queryPartner();
            $_queryP->partnerStatus = $_PStatus;

            $_querySorts = new Kyapi_Model_querySorts();
            $_querySorts->createTime = "DESC";

            $_keyword = $this->_request->getParam('keyword');
            if (empty($_keyword)) {
                $_keyword = null;
            }
            $this->view->keyword = $_keyword;

            $page = intval($this->_request->getParam('page'));
            if ($page < 1)
                $page = 1;
            $_limit = 5;
            $_skip = $_limit * ($page - 1);

            $userKY = $this->json->listVendorPartnerApi($this->_requestObject, null, $_querySorts, $_keyword, $_skip, $_limit);

            $existData = $this->objectToArray(json_decode($userKY));
            //统计正常状态数量、分页
            $total = $existData['extData']['totalSize'];
            $page = $existData['extData']['totalPage'];

            $this->view->e = $existData['result'];
            $this->view->status = $_PStatus;

            $file = "user/vendor/" . $mod . "-" . $_PStatus;
            $_limit = 5;
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
            $content = $this->view->render(SEED_WWW_TPL . "/vendor/orderlist.phtml");
            echo $content;
            exit;
        }
    }

    //添加订单卖家
    public function superlistAction() {
        try {
            $f1 = new Seed_Filter_Alnum();
            $mod = $f1->filter($this->_request->getParam('mod'));
            if (empty($mod)) {
                $mod = "superlist";
            }

            $_PStatus = strval($this->_request->getParam('status'));
            if (empty($_PStatus)) {
                $_PStatus = '01';
            }

            $_querySorts = new Kyapi_Model_querySorts();
            $_querySorts->createTime = "DESC";

            $_keyword = $this->_request->getParam('keyword');
            if (empty($_keyword)) {
                $_keyword = null;
            }

            $page = intval($this->_request->getParam('page'));
            if ($page < 1)
                $page = 1;
            $_limit = 5;
            $_skip = $_limit * ($page - 1);

            $_queryP = new queryPartner();
            $_queryP->partnerStatus = $_PStatus;

            $userKY = $this->json->listVendorPartnerApi($this->_requestObject, null, $_querySorts, $_keyword, $_skip, $_limit);
            $existData = $this->objectToArray(json_decode($userKY));
            //统计正常状态数量、分页
            $total = $existData['extData']['totalSize'];
            $page = $existData['extData']['totalPage'];

            $this->view->e = $existData['result'];
            $this->view->status = $_PStatus;

            $file = "user/vendor/" . $mod . "-" . $_PStatus;
            $_limit = 5;
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
            $content = $this->view->render(SEED_WWW_TPL . "/vendor/superlist.phtml");
            echo $content;
            exit;
        }
    }

    /**添加（已存在）合作伙伴步骤一**/
    public function addsAction() {
        try {
            $f1 = new Seed_Filter_Alnum();
            $mod = $f1->filter($this->_request->getParam('mod'));
            if (empty($mod)) {
                $mod = "adds";
            }

            $_PStatus = strval($this->_request->getParam('status'));
            if (empty($_PStatus)) {
                $_PStatus = '00';
            }

            $_querySorts = new Kyapi_Model_querySorts();
            $_querySorts->createTime = "DESC";

            $_keyword = $this->_request->getParam('keyword');
            if (empty($_keyword)) {
                $_keyword = null;
            }
            $this->view->keywords = $_keyword;

            $page = intval($this->_request->getParam('page'));
            if ($page < 1)
                $page = 1;
            $_limit = 10;
            $_skip = $_limit * ($page - 1);

            $_queryP = new queryPartner();
            $_queryP->partnerStatus = $_PStatus;

            $userKY = $this->json->listExistAccountApi($this->_requestObject, null, $_querySorts, $_keyword, 0, 5);
            $existData = $this->objectToArray(json_decode($userKY));

            //统计正常状态数量、分页
            $total = $existData['extData']['totalSize'];
            $page = $existData['extData']['totalPage'];

            $this->view->e = $existData['result'];
            $this->view->status = $_PStatus;

            $file = "user/vendor/" . $mod . "-" . $_PStatus;
            $_limit = 5;
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
            $content = $this->view->render(SEED_WWW_TPL . "/vendor/adds.phtml");
            echo $content;
            exit;
        }
    }

    /**查看合作伙伴信息**/
    public function addsviewAction() {
        // 请求Hessian服务端方法
        $toID = $_SERVER['QUERY_STRING'];
        $_toID = base64_decode($toID);

        $_requestOb = $this->_requestObject;
        $userKY = $this->json->getPartnerApi($_requestOb, $_toID);
        $existData = $this->objectToArray(json_decode($userKY));

        $this->view->e = $existData['result'];
        $this->view->extData = $existData['extData'];
        $this->view->toID = $_toID;
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/vendor/adds_view.phtml");
            echo $content;
            exit;
        }
    }

    /**新增（已存在）合作伙伴步骤二**/
    public function addvAction() {
        // 请求Hessian服务端方法
        $toID = $_SERVER['QUERY_STRING'];
        $_accountID = base64_decode($toID);
        $_rolecode = "Vendor";

        $_requestOb = $this->_requestObject;
        $userKY = $this->json->addPartnerByAccountIDApi($_requestOb, $_accountID, $_rolecode);
        $existData = json_decode($userKY);
        if ($existData->status != 1) {
            //邀请失败
            Shop_Browser::redirect($this->view->translate('tip_request_fail') . $existData->error);
        } else {
            //邀请成功
            Shop_Browser::redirect($this->view->translate('tip_request_success'), '/user/vendor');
        }
        exit;
    }

    // 新增（全新）的合作伙伴
    public function addAction() {
        $windows = $_SERVER['QUERY_STRING'];
        if ($this->_request->isPost()) {

            // 设置请求数据
            $requestObject = $this->_requestObject;

            /*添加合作伙伴信息*/
            $_account = new Kyapi_Model_account();
            $_contact = new Kyapi_Model_contact();
            $_account->accountName = $this->_request->getParam('accountName');
            $_account->roleCode = 'Vendor';
            $_account->regdCountryCode = $this->_request->getParam('regdCountryCode');
            $_account->regdAddress = $this->_request->getParam('regdAddress');
            $_contact->name = $this->_request->getParam('name');
            $_contact->mobilePhone = $this->_request->getParam('mobilePhone');
            $_contact->email = $this->_request->getParam('email');
            $_account->contact = $_contact;

            $userKY = $this->json->addPartnerApi($requestObject, $_account);
            $existData = json_decode($userKY);

            // 页面跳转
            if ($existData->status != 1) {
                $this->view->partner = $this->objectToArray($_account);
                $this->view->errMsg = $this->view->translate('tip_add_fail') . $existData->error;
            } else {
                $resultMsg = base64_encode($this->view->translate('tip_add_success'));
                $this->redirect("/vendor/index?resultMsg=" . $resultMsg . "&partnerStatus=00");
            }
        }
        if (defined('SEED_WWW_TPL')) {
            if ($windows) {
                $content = $this->view->render(SEED_WWW_TPL . "/vendor/add_windows.phtml");
            } else {
                $content = $this->view->render(SEED_WWW_TPL . "/vendor/add.phtml");
            }
            echo $content;
            exit;
        }
    }

    // 查看合作伙伴信息
    public function viewAction() {
        // 请求Hessian服务端方法
        $toID = $_SERVER['QUERY_STRING'];
        $_toID = base64_decode($toID);

        $_requestOb = $this->_requestObject;
        $userKY = $this->json->getPartnerApi($_requestOb, $_toID);
        $existData = $this->objectToArray(json_decode($userKY));
        $existDatt = $existData['result'];
        $this->view->partner = $existDatt;
        $this->view->toID = $_toID;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/vendor/view.phtml");
            echo $content;
            exit;
        }
    }

    // 编辑合作伙伴信息
    public function editAction() {
        // 请求Hessian服务端方法
        $toID = $_SERVER['QUERY_STRING'];
        $_toID = base64_decode($toID);

        $requestObject = $this->_requestObject;
        $userKY = $this->json->getPartnerApi($requestObject, $_toID);
        $existData = $this->objectToArray(json_decode($userKY));
        $existDatt = $existData['result'];
        $this->view->partner = $existDatt;

        if ($this->_request->isPost()) {
            $_account = new Kyapi_Model_account();
            $_contact = new Kyapi_Model_contact();
            $_account->accountID = $_toID;//编辑合作伙伴需要accountID
            $_account->accountName = $this->_request->getParam('accountName');
            $_account->roleCode = 'Vendor';
            $_account->regdCountryCode = $this->_request->getParam('regdCountryCode');
            $_account->regdAddress = $this->_request->getParam('regdAddress');
            $_contact->name = $this->_request->getParam('name');
            $_contact->mobilePhone = $this->_request->getParam('mobilePhone');
            $_contact->email = $this->_request->getParam('email');
            $_account->contact = $_contact;
            $editData = $this->json->editPartnerApi($requestObject, $_account);
            $existData = json_decode($editData);

            if ($existData->status != 1) {
                //合作伙伴状态 数量统计
                $resultObject = $this->json->countVendorPartnerApi($requestObject);
                $countVendorPartner = $this->objectToArray(json_decode($resultObject));
                $this->view->countResult = $countVendorPartner['result'];

                $this->view->partner = $this->objectToArray($_account);
                $this->view->errMsg = $this->view->translate('tip_edit_fail') . $existData->error;
            } else {
                $partnerResultObject = $this->json->getPartnerApi($requestObject, $_toID);
                $partnerList = json_decode($partnerResultObject)->result->partnerList;
                $partnerList = $this->objectToArray($partnerList);
                $resultMsg = base64_encode($this->view->translate('tip_edit_success'));
                $this->redirect("/vendor/index?resultMsg=" . $resultMsg . "&partnerStatus=" . $partnerList[0]['partnerStatus']);
            }
        }
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/vendor/edit.phtml");
            echo $content;
            exit;
        }
    }

    // 删除合作伙伴
    public function deleteAction() {
        if ($this->_request->isPost()) {
            //设为默认
            // 请求Hessian服务端方法
            // 原接口删除合作伙伴时, 传的是对方的角色
            // 20190530修改为删除时传自己的角色
            // $_roleCode = 'Vendor';
            $_roleCode = 'Buyer';
            $_objID = $this->_request->getPost('delID');
            $_requestOb = $this->_requestObject;
            $opData = $this->json->delPartnerApi($_requestOb, $_objID, $_roleCode);
            echo $opData;
        }
        exit;
    }

    /**接受合作伙伴信息**/
    public function acceptAction() {
        if ($this->_request->isPost()) {
            //设为默认
            // 请求Hessian服务端方法
            $_roleCode = 'Buyer';
            $_objID = $this->_request->getPost('delID');
            $_requestOb = $this->_requestObject;
            $opData = $this->json->editPartnerAcceptApi($_requestOb, $_objID, $_roleCode);
            echo $opData;
        }
        exit;
    }

    /**拒绝合作伙伴信息**/
    public function rejectAction() {
        if ($this->_request->isPost()) {
            //设为默认
            // 请求Hessian服务端方法
            $_roleCode = 'Vendor';
            $_objID = $this->_request->getPost('delID');
            $_requestOb = $this->_requestObject;
            $opData = $this->json->editPartnerRejectApi($_requestOb, $_objID, $_roleCode);
            echo $opData;
        }
        exit;
    }

    public function contactListAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $queryParams = array();
        $toID = strval($this->_request->getParam('toID'));

        $querySorts = array();
        $querySorts['createTime'] = "DESC";

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

        $resultObject = $this->json->listAccountPublicContactApi($requestObject, $toID, $queryParams, $querySorts, $keyword, $skip, $limit);
        $msg["total"] = json_decode($resultObject)->extData->totalSize;
        $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    public function contactListForSaleAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $queryParams = array();
        $toID = strval($this->_request->getParam('toID'));

        $querySorts = array();
        $querySorts['createTime'] = "DESC";

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

        // 判断订单的卖家是自己还是另外选的
        if ($toID == $this->view->accountID) {

            $queryParams['contactStatus'] = '01';

            if (is_array($queryParams)) {
                $queryParams = $this->arrayToObject($queryParams);
            }

            if (is_array($querySorts)) {
                $querySorts = $this->arrayToObject($querySorts);
            }

            $resultObject = $this->json->listContactApi($requestObject, $queryParams, $querySorts, $keyword, $skip, $limit);
            $msg["total"] = json_decode($resultObject)->extData->totalSize;
            $msg["rows"] = json_decode($resultObject)->result;
        } else {
            if (is_array($queryParams)) {
                $queryParams = $this->arrayToObject($queryParams);
            }

            if (is_array($querySorts)) {
                $querySorts = $this->arrayToObject($querySorts);
            }

            $resultObject = $this->json->listAccountPublicContactApi($requestObject, $toID, $queryParams, $querySorts, $keyword, $skip, $limit);
            $msg["total"] = json_decode($resultObject)->extData->totalSize;
            $msg["rows"] = json_decode($resultObject)->result;
        }

        echo json_encode($msg);
        exit;
    }

    /**合作伙伴.订单新增卖家联系人列表**/
    public function aclistAction() {
        $f1 = new Seed_Filter_Alnum();
        $mod = $f1->filter($this->_request->getParam('mod'));
        if (empty($mod)) {
            $mod = "aclist";
        }

        $_PStatus = strval($this->_request->getParam('status'));
        if (empty($_PStatus)) {
            $_PStatus = null;
        }

        //QU  查询排序条件
        $_querySorts = new querySorts();
        $_querySorts->createTime = "DESC";

        $_keyword = $this->_request->getParam('keyword');
        if (empty($_keyword)) {
            $_keyword = null;
        }

        $page = intval($this->_request->getParam('page'));
        if ($page < 1)
            $page = 1;
        $_limit = 5;
        $_skip = $_limit * ($page - 1);

        // 设置请求数据
        $_requestOb = $this->_requestObject;

        // 请求Hessian服务端方法AcctID 获取合作伙伴ID

        $toID = $this->_request->getParam("toID");
        //$_toID =base64_decode($toID);
        $userKY = $this->json->listAccountPublicContactApi($_requestOb, $toID, $_PStatus, $_querySorts, $_keyword, $_skip, $_limit);
        $existData = $this->objectToArray(json_decode($userKY));
        $accountList = $existData['result'];
        $this->view->e = $accountList;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/vendor/aclist.phtml");
            echo $content;
            exit;
        }

    }
}

