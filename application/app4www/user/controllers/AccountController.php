<?php
class AccountController extends Kyapi_Controller_Action
{
    public function preDispatch()
    {
        $this->view->cur_pos = 'account';

        echo $this->view->errMsg;
        //清除 免登陆session
        $this->IsAuth($this->view->visitor);

        if(empty($this->view->userID)){
            // 提示：请先登录系统
            Mobile_Browser::redirect($this->view->translate('tip_login_please'),$this->view->seed_Setting['user_app_server']."/login");
        }


        $this->view->cur_pos = $this->_request->getParam('controller');
        $cururl = $this->getRequestUri();
        if ($cururl == '/account') {
            $this->_request->setParam('all', 'all');
            $this->indexAction();
            exit;
        }

        preg_match('/(.*)\.html/', $cururl, $arr);

        if (isset($arr[1]) && !empty($arr[1])) {


            preg_match_all('/^\/user\/account\/(index|orderlist)-([\d]+)-([\d]+).html/isU', $cururl, $arr);

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
            //提示没有找到相关信息
            Mobile_Browser::redirect($this->view->translate('tip_find_no'), $this->view->seed_BaseUrl . "/");

        }

        // 更新session时间
        $this->updateRedisExpire();
    }

    public function indexAction() {
        $this->view->resultMsg = $this->_request->getParam('resultMsg');

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/account/index.phtml");
            echo $content;
            exit;
        }
    }

    public function contactlistajaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $contactStatus = $this->_request->getParam('contactStatus');
        $queryParams = array();
        if (empty($contactStatus)) {
            $queryParams['contactStatus'] = '01';
        }

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

        $resultObject = $this->json->listContactApi($requestObject, $queryParams, $querySorts, $keyword, $skip, $limit);
        $msg["total"] = json_decode($resultObject)->extData->totalSize;
        $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    public function orderlistAction()
    {
        // 设置请求数据
        $_requestOb=$this->_requestObject;
        /*用户信息*/
        $_queryP =new queryAccount();
        /**contactStatus 01有效 02禁用*/
        $_queryP->contactStatus= "01";


        // 请求Hessian服务端方法
        $userKY= $this->json->listContactApi($_requestOb,$_queryP);
        $userData= $this->objectToArray(json_decode($userKY));
        $userList= $userData['result'];
        $this->view->userList=$userList;

        $userJSON=json_encode($userList);
        echo $userJSON;
        exit;
    }

    public function viewAction() {
        // 设置请求数据
        $requestObject = $this->_requestObject;
        $contactId = $_SERVER['QUERY_STRING'];
        $_contactID = base64_decode($contactId);
        // 请求Hessian服务端方法

        $userKY = $this->json->getContactApi($requestObject, $_contactID);
        $userData = $this->objectToArray(json_decode($userKY));
        $this->view->e = $userData['result'];

        // 取加公司签署人信息
        $accountResultObject = $this->json->getAccountApi($requestObject);
        $this->refreshAccountCertificateByResult(json_decode($accountResultObject)->result->hasIDCertificate);
        $this->view->agentName = json_decode(json_decode($accountResultObject)->result->idcertificateinfo)->agentName;
        $this->view->agentIdNo = json_decode(json_decode($accountResultObject)->result->idcertificateinfo)->agentIdNo;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/account/view.phtml");
            echo $content;
            exit;
        }
    }

    public function addAction() {
        if ($this->_request->isPost()) {
            // 设置请求数据
            $_requestOb = $this->_requestObject;
            // 编辑用户信息
            $contact = array();
            $contact['name'] = $this->_request->getParam('name');
            $contact['ecommloginname'] = $this->_request->getParam('email');
            $contact['phone'] = $this->_request->getParam('phone');

            $ecommrole = $this->_request->getParam('ecommrole');
            $contact['ecommrole'] = implode(',', $ecommrole);  // 将数组转成字符串, 并以逗号分隔
            $contact['mobilePhone'] = $this->_request->getParam('mobilePhone');
            $contact['email'] = $this->_request->getParam('email');
            $contact['salutation'] = $this->_request->getParam('salutation');

            $dateTime = $this->_request->getParam('birthdate');
            if (!empty($dateTime)) {
                $birthdate = date("Y-m-d\TH:i:s", strtotime($dateTime));
            } else {
                $birthdate = null;
            }
            $contact['birthdate'] = $birthdate;
            $contact['department'] = $this->_request->getParam('department');

            $isPublicContact = $this->_request->getParam('isPublicContact');
            if ($isPublicContact == '1') {
                $contact['isPublicContact'] = true;
            } else {
                $contact['isPublicContact'] = false;
            }

            $contact['title'] = $this->_request->getParam('title');
            $contact['sex'] = $this->_request->getParam('sex');
            $contact['fax'] = $this->_request->getParam('fax');
            $contact['assistantName'] = $this->_request->getParam('assistantName');
            $contact['assistantPhone'] = $this->_request->getParam('assistantPhone');
            $contact['mailingCountryCode'] = $this->_request->getParam('mailingCountryCode');
            $contact['mailingStateCode'] = $this->_request->getParam('mailingStateCode');
            $contact['mailingCityCode'] = $this->_request->getParam('mailingCityCode');
            $contact['mailingAddress'] = $contact['mailingCountryCode'] . $contact['mailingStateCode'] . $contact['mailingCityCode'];
            $contact['mailingStreet'] = $this->_request->getParam('mailingStreet');
            $contact['mailingZipCode'] = $this->_request->getParam('mailingZipCode');
            $contact['othAddress'] = $this->_request->getParam('othAddress');
            $contact['mailingStreet'] = $this->_request->getParam('mailingStreet');
            $contact['certificateType'] = $this->_request->getParam('certificateType');
            $contact['certificateNo'] = $this->_request->getParam('certificateNo');
            $contact['identityType'] = '01';   // 证件类型:默认身份证
            $contact['identityNo'] = $this->_request->getParam('identityNo');

            $userKY = $this->json->addContactApi($_requestOb, $contact);
            $userEdit = $this->objectToArray(json_decode($userKY));

            if ($userEdit['status'] != 1) {
                $this->view->e = $this->objectToArray($contact);
                $this->view->errMsg = $userEdit['error'];
            } else {
                $resultMsg = base64_encode($this->view->translate('tip_add_success'));
                $this->redirect("/account/index?resultMsg=".$resultMsg);
            }
        } else {
            $requestObject = $this->_requestObject;

            // 请求Hessian服务端方法
            $resultObject = $this->json->getAccountApi($requestObject);
            $account = json_decode($resultObject)->result;
            $this->view->account = $account;
        }
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/account/add.phtml");
            echo $content;
            exit;
        }
    }

    public function editAction() {
        // 设置请求数据
        $_requestOb = $this->_requestObject;
        $contactId = $_SERVER['QUERY_STRING'];
        $_contactID = base64_decode($contactId);
        $userKY = $this->json->getContactApi($_requestOb, $_contactID);
        $userData = $this->objectToArray(json_decode($userKY));
        $this->view->e = $userData['result'];
        $this->view->ecommrole = explode(",", $userData['result']['ecommrole']);

        if ($this->_request->isPost()) {
            // 编辑用户信息
            $contact = array();

            // 判断是否为订单联系人
            $isPublicContact = $this->_request->getParam('isPublicContact');
            if ($isPublicContact == '1') {
                $contact['isPublicContact'] = true;
            } else {
                $contact['isPublicContact'] = false;
            }

            // 员工状态 4.10 文档提出 编辑、view页面停用  contactStatus
            $ecommrole = $this->_request->getParam('ecommrole');
            $contact['ecommrole'] = implode(',', $ecommrole);  // 将数组转成字符串, 并以逗号分隔

            // 补充原来的值
            $contact['contactID'] = $_contactID;
            $contact['ecommloginname'] = $userData['result']['ecommloginname'];
            $contact['phone'] = $userData['result']['phone'];
            $contact['mobilePhone'] = $userData['result']['mobilePhone'];
            $contact['email'] = $userData['result']['email'];
            $contact['salutation'] = $userData['result']['salutation'];
            $contact['birthdate'] = $userData['result']['birthdate'];
            $contact['department'] = $userData['result']['department'];
            $contact['title'] = $userData['result']['title'];
            $contact['sex'] = $userData['result']['sex'];
            $contact['fax'] = $userData['result']['fax'];
            $contact['assistantName'] = $userData['result']['assistantName'];
            $contact['assistantPhone'] = $userData['result']['assistantPhone'];
            $contact['mailingCountryCode'] = $userData['result']['mailingCountryCode'];
            $contact['mailingStateCode'] = $userData['result']['mailingStateCode'];
            $contact['mailingCityCode'] = $userData['result']['mailingCityCode'];
            $contact['mailingAddress'] = $userData['result']['mailingAddress'];
            $contact['mailingStreet'] = $userData['result']['mailingStreet'];
            $contact['mailingZipCode'] = $userData['result']['mailingZipCode'];
            $contact['mailingStreet'] = $userData['result']['mailingStreet'];
            $contact['identityType'] = $userData['result']['identityType'];
            $contact['name'] = $userData['result']['name'];
            $contact['identityNo'] = $userData['result']['identityNo'];

            $userKY = $this->json->editContactApi($_requestOb, $contact);
            $userEdit = $this->objectToArray(json_decode($userKY));

            if ($userEdit['status'] != 1) {
                $this->view->errMsg = $userEdit['error'];
            } else {
                $resultMsg = base64_encode($this->view->translate('tip_edit_success'));
                $this->redirect("/account/index?resultMsg=".$resultMsg);
            }
        }
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/account/edit.phtml");
            echo $content;
            exit;
        }
    }

    /**公司联系人列表.用于订单新增、编辑 增加联系人列表**/
    public function aclistAction()
    {
        // 设置请求数据
        $_requestOb=$this->_requestObject;
        /*用户信息*/
        $_queryP =new queryAccount();
        /**contactStatus 01有效 02禁用*/
        $_queryP->contactStatus= "01";

        // 请求Hessian服务端方法
        $userKY= $this->json->listContactApi($_requestOb,$_queryP);
        $userData= $this->objectToArray(json_decode($userKY));
        $userList= $userData['result'];
        $this->view->e=$userList;


        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/account/aclist.phtml");
            echo $content;
            exit;
        }

    }

    public function deleteAction()
    {
        if ($this->_request->isPost()) {
            //设为默认
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
            $opData= $this->json->deleteContactApi($_requestOb,$_objID);
            echo $opData;
        }
        exit;
    }

    public function defaultAction()
    {
        //设为默认
        if ($this->_request->isPost()) {
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
            $opData= $this->json->setDefaultContactApi($_requestOb,$_objID);
            echo $opData;
        }
        exit;
    }



    /*人员账号新增 禁用账号*/
    public  function invalidAction()
    {
        if ($this->_request->isPost()) {
            //设为默认
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
            $opData= $this->json->editContactInvalidApi($_requestOb,$_objID);
            echo $opData;
        }
        exit;

    }

    /*人员账号新增 启用账号*/
    public function validAction() {
        if ($this->_request->isPost()) {
            //设为默认
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb = $this->_requestObject;
            $opData = $this->json->editContactValidApi($_requestOb, $_objID);
            echo $opData;
        }
        exit;
    }

    public function sendMobileAuthCodeAction() {
        $requestObject = $this->_requestObject;

        $resultObject = $this->json->sendMobileAuthCode($requestObject);
        $msg = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    public function changeSigningAgentAction() {
        $requestObject = $this->_requestObject;

        $authCode = $this->_request->getParam('authCode');
        $contactID = $this->_request->getParam('contactID');
        $resultObject = $this->json->changeSigningAgent($requestObject, $authCode, $contactID);

        $msg['status'] = json_decode($resultObject)->status;
        $msg['result'] = json_decode($resultObject)->result;
        if (json_decode($resultObject)->status <= 0) {
            $msg['error'] = json_decode($resultObject)->error;
        }

        echo json_encode($msg);
        exit;
    }

    public function changeCompAdminAction() {
        $requestObject = $this->_requestObject;

        $contactID = $this->_request->getParam('contactID');
        $resultObject = $this->json->changeCompAdmin($requestObject, $contactID);

        $msg['status'] = json_decode($resultObject)->status;
        $msg['result'] = json_decode($resultObject)->result;
        if (json_decode($resultObject)->status <= 0) {
            $msg['error'] = json_decode($resultObject)->error;
        } else {
            $contactResultObject = $this->json->getContactApi($requestObject, $_SESSION['rev_session']['userID']);

            $_SESSION['rev_session']['ecommrole'] = json_decode($contactResultObject)->result->ecommrole;
            $this->view->CompAdmin = strstr($_SESSION['rev_session']['ecommrole'], 'CompAdmin') ? true : false;//公司管理员
        }

        echo json_encode($msg);
        exit;
    }
}
