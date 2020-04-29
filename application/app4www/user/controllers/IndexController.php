<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/12
 * Time: 17:26
 */
class IndexController extends Kyapi_Controller_Action {

    public function preDispatch() {
        $this->view->cur_pos = 'index';
        //清除 免登陆session
        $this->IsAuth($this->view->visitor);

        // echo $_SESSION['rev_session']['userID'];

        if (empty($this->view->userID)) {
            Mobile_Browser::redirect('请先登录系统！', $this->view->seed_Setting['user_app_server'] . "/login");
        }

        // 更新session时间
        $this->updateRedisExpire();
    }

    public function indexAction() {
        if (empty($_SESSION['rev_session']['userID'])) {
            if (defined('SEED_WWW_TPL')) {
                $content = $this->view->render(SEED_WWW_TPL . "/login/index.phtml");
                echo $content;
                exit;
            }
        } else {

            //会员中心订单数量统计
            $orderCount = $this->json->countOrderStatusApi($this->_requestObject);
            $existCOUNT= json_decode($orderCount);
            $existCOUNT = $this->objectToArray($existCOUNT);
            $this->view->COUNT = $existCOUNT['result'];

            if ($_SESSION['rev_session']['accountType'] == 'CO07') {
                $this->redirect("/user/finance/channel");
            } else if ($_SESSION['rev_session']['accountType'] == 'CO09') {
                $this->redirect("/user/transport");
            }

            if (defined('SEED_WWW_TPL')) {
                $content = $this->view->render(SEED_WWW_TPL . "/index/index.phtml");
                echo $content;
                exit;
            }
        }
    }

    public function photoAction()
    {
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/index/photo.phtml");
            echo $content;
            exit;
        }
    }

    public function profileAction() {
        $this->view->resultMsg = $this->_request->getParam('resultMsg');

        // 设置请求数据
        $requestObject = $this->_requestObject;
        $contactID = $this->view->userID;
        $resultObject = $this->json->getContactApi($requestObject, $contactID);
        $userData = $this->objectToArray(json_decode($resultObject));

        $this->view->e = $userData['result'];
        $this->view->ecommrole = explode(",", $userData['result']['ecommrole']);

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/index/profile.phtml");
            echo $content;
            exit;
        }
    }

    public function editAction() {
        // 设置请求数据
        $_requestOb = $this->_requestObject;
        $_contactID = $this->view->userID;
        $userKY = $this->json->getContactApi($_requestOb, $_contactID);
        $userData = $this->objectToArray(json_decode($userKY));

        $this->view->e = $userData['result'];
        $this->view->ecommrole = explode(",", $userData['result']['ecommrole']);


        if ($this->_request->isPost()) {
            //编辑用户信息

            $_account = array();
            $_account['contactID'] = $_contactID;
            $_account['ecommloginname'] = $userData['result']['ecommloginname'];
            $_account['phone'] = $this->_request->getParam('phone');
            //判断是否为默认联系人
            $isDefaultPublic = ($this->_request->getParam('isDefaultPublic') == '1') ? true : false;
            $_account['isDefaultPublic'] = $isDefaultPublic;
            //判断是否为订单联系人
            $isPublicContact = ($this->_request->getParam('isPublicContact') == '1') ? true : false;
            $_account['isPublicContact'] = $isPublicContact;

            $emrole = $this->_request->getParam('ecommrole');
            $emrole2 = implode(',', $emrole);
            $_account['ecommrole'] = $userData['result']['ecommrole'];
            $_account['mobilePhone'] = $this->_request->getParam('mobilePhone');
            $_account['email'] = $this->_request->getParam('email');
            $_account['salutation'] = $this->_request->getParam('salutation');
            $ddtime = $this->_request->getParam('birthdate');
            if (!empty($ddtime)) {
                $date3 = date("Y-m-d\TH:i:s", strtotime($ddtime));
            } else {
                $date3 = null;
            }
            $_account['birthdate'] = $date3;
            $_account['department'] = $this->_request->getParam('department');
            $_account['title'] = $this->_request->getParam('title');
            $_account['sex'] = $this->_request->getParam('sex');
            $_account['fax'] = $this->_request->getParam('fax');

            //添加字段 4-27 助理姓名+电话
            $_account['assistantName'] = $this->_request->getParam('assistantName');//助理姓名
            $_account['assistantPhone'] = $this->_request->getParam('assistantPhone');//助理电话
            $_account['contactPreference']['langCode'] = $this->_request->getParam('langCode');//语言
            $_account['contactPreference']['timeZone'] = $this->_request->getParam('timeZone');//时区

            $_account['mailingCountryCode'] = $this->_request->getParam('mailingCountryCode');
            $_account['mailingStateCode'] = $this->_request->getParam('mailingStateCode');
            $_account['mailingCityCode'] = $this->_request->getParam('mailingCityCode');
            $_account['mailingAddress'] = $this->_request->getParam('mailingAddress');
            $_account['mailingStreet'] = $this->_request->getParam('mailingStreet');
            $_account['mailingZipCode'] = $this->_request->getParam('mailingZipCode');
            $_account['mailingStreet'] = $this->_request->getParam('mailingStreet');
            /*添加字段证件号码  证件类型*/
            $_account['identityType'] = '01';
            //  $_account['identityType'] = $this->_request->getParam('identityType');
            //判断状态修改name 和身份证号码
            if ($userData['result']['realAuthStatus'] == 0 || $userData['result']['realAuthStatus'] == -1) {
                $_account['name'] = $this->_request->getParam('name');
                $_account['identityNo'] = $this->_request->getParam('identityNo');
            } else {
                $_account['name'] = $userData['result']['name'];
                $_account['identityNo'] = $userData['result']['identityNo'];
            }

            $userKY = $this->json->editContactApi($_requestOb, $_account);
            $resultObject = json_decode($userKY);

            // 页面跳转
            if ($resultObject->status != 1) {
                $this->view->resultMsg = $this->view->translate('tip_edit_fail') . '! ' . $resultObject->error;
            } else {
                $resultMsg = base64_encode($this->view->translate('tip_edit_success'));
                $this->redirect("/index/profile?resultMsg=" . $resultMsg);
            }
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/index/edit.phtml");
            echo $content;
            exit;
        }
    }

    /*个人身份认证*/
    function authuserAction(){
        $msg=0;
        if ($this->_request->isPost()) {
            //获取account数据集合
            $_requestOb=$this->_requestObject;
            $resultObject= $this->json->doPersRealNameAuth($_requestOb);

            // 取回接口请求状态
            $apiStatus = json_decode($resultObject)->status;
            if ($apiStatus == 1) {
                // 取回个人认证状态
                if (json_decode($resultObject)->result->errCode == 0) {
                    $msg = json_decode($resultObject)->result->errCode;
                } else {
                    $msg = json_decode($resultObject)->result->msg;
                }
            } else {
                // 接口请求错误的情况下, 将接口错误返回给页面
                $msg = $apiStatus;
            }
        }
        echo json_encode($msg);
        exit;
    }

    function passwdAction(){

        if ($this->_request->isPost()) {
            try {
                $_requestOb=$this->_requestObject;

                /*
                修改密码模块
                 */
                $_contact = array();
                $_contactID = $this->view->userID;
                $_ecommpasswsd = $this->_request->getPost('ecommpasswsd');
                $_newpwd = $this->_request->getPost('newpwd');
                // 设置accountID, 为同一间公司注册用户
                $resultObject= $this->json->changePasswordApi( $_requestOb,$_ecommpasswsd,$_newpwd);
                $existData = $resultObject->result;

                if (json_decode($resultObject)->status != 1) {
                    //密码编辑失败
                    Shop_Browser::redirect($this->view->translate('tip_edit_fail'), $this->view->seed_Setting['user_app_server'].'/passwd');
                } else {
                    Shop_Browser::redirect($this->view->translate('tip_edit_success'), $this->view->seed_Setting['user_app_server'].'/index');
                }

            } catch (HttpError $ex) {
                echo $ex->getMessage();
                Shop_Browser::redirect($ex->getMessage());
            }
        }
        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/index/passwd.phtml");
            echo $content;
            exit;
        }
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

    // 销售订单列表
    public function listsaleorderAction() {
        $_requestOb = $this->_requestObject;
        $orderStatus = "04";
        $resultObject = $this->json->listSaleOrderApi($_requestOb, $orderStatus, null, null, 0, 5);
        $this->view->orderList = json_decode($resultObject)->result;
        $this->view->dataType = 'sale';
        $this->view->listUrl = '/user/sale';

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/index/_listOrder.phtml");
            echo $content;
            exit;
        }
    }

    // 采购订单列表
    public function listpurorderAction() {
        $_requestOb = $this->_requestObject;
        $orderStatus = "04";
        $resultObject = $this->json->listPurOrderApi($_requestOb, $orderStatus, null, null, 0, 5);
        $this->view->orderList = json_decode($resultObject)->result;
        $this->view->dataType = 'pur';
        $this->view->listUrl = '/user/pur';

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/index/_listOrder.phtml");
            echo $content;
            exit;
        }
    }

    // 我的商品列表
    public function listsaleproductAction() {
        $_requestOb = $this->_requestObject;
        $resultObject = $this->json->listSaleProductApi($_requestOb, null, null, null, 0, 5);
        $this->view->productList = json_decode($resultObject)->result;
        $this->view->dataType = 'goods';
        $this->view->listUrl = '/user/goods';

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/index/_listProduct.phtml");
            echo $content;
            exit;
        }
    }

    // 采购的商品列表
    public function listpurproductAction() {
        $_requestOb = $this->_requestObject;
        $resultObject = $this->json->listPurProductApi($_requestOb, null, null, null, 0, 5);
        $this->view->productList = json_decode($resultObject)->result;
        $this->view->dataType = 'purchase';
        $this->view->listUrl = '/user/purchase';

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/index/_listProduct.phtml");
            echo $content;
            exit;
        }
    }

    // 我的买家列表
    public function listbuyerpartnerAction() {
        $_requestOb = $this->_requestObject;
        $resultObject = $this->json->listBuyerPartnerApi($_requestOb, null, null, null, 0, 5);
        $this->view->partnerList = json_decode($resultObject)->result;
        $this->view->dataType = 'buyer';
        $this->view->listUrl = '/user/buyer';

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/index/_listPartner.phtml");
            echo $content;
            exit;
        }
    }

    // 我的卖家列表
    public function listvendorpartnerAction() {
        $_requestOb = $this->_requestObject;
        $resultObject = $this->json->listVendorPartnerApi($_requestOb, null, null, null, 0, 5);
        $this->view->partnerList = json_decode($resultObject)->result;
        $this->view->dataType = 'vendor';
        $this->view->listUrl = '/user/vendor';

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/index/_listPartner.phtml");
            echo $content;
            exit;
        }
    }
}
