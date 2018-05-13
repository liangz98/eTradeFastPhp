<?php
class AccountController extends Kyapi_Controller_Action
{
    public function preDispatch()
    {
        $this->view->cur_pos = 'account';
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
    }

    public function indexAction()
    {

        $f1 = new Seed_Filter_Alnum();
        $mod = $f1->filter($this->_request->getParam('mod'));
        if (empty($mod)) {$mod = "index";}

        $_PStatus =strval($this->_request->getParam('status'));
        if(empty( $_PStatus)){  $_PStatus ='01';}

        $this->view->PStatus=$_PStatus;
        $_querySorts=$this->_request->getParam('querySorts');
        if(empty($_querySorts)){ $_querySorts =null;}

        $_keyword=$this->_request->getParam('keyword');
        if(empty($_keyword)){ $_keyword =null;}

        $page =intval($this->_request->getParam('page'));
        if($page<1)$page=1;
        $_limit=8;
        $_skip=$_limit*($page-1);


        // 设置请求数据
        $_requestOb=$this->_requestObject;
        /*用户信息*/
        $_queryP =new queryAccount();
        /**contactStatus 01有效 02禁用*/
        if($_PStatus=="00"){
            $_queryP= null;
        }else{
            $_queryP->contactStatus= $_PStatus;
        }

        $_querySorts=new querySorts();
        $_querySorts->createTime="DESC";


        // 请求Hessian服务端方法
        $userKY= $this->json->listContactApi($_requestOb,$_queryP,$_querySorts,$_keyword, $_skip, $_limit);
        $userData= $this->objectToArray(json_decode($userKY));
        $userList= $userData['result'];
        $this->view->userList=$userList;


        //统计正常状态数量、分页
        $existCount = $userData['extData'];
        $total = $existCount['totalSize'];
        $page=$existCount['totalPage'];

        //设置视图状态
        $this->view->status= $_PStatus;

        $file = "user/account/" . $mod . "-" . $_PStatus;
        $_limit=8;
        $pageObj = new Seed_Page($this->_request,$total,$_limit);
        $this->view->page = $pageObj->getPageArray();
        $this->view->page['pageurl'] = '/' . $file;
        if ($page > $this->view->page['totalpage'])
            $page = $this->view->page['totalpage'];
        if ($page < 1) $page = 1;


        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/account/index.phtml");
            echo $content;
            exit;
        }
    }

    public function contactListAjaxAction() {
        $msg = array();
        $_requestOb = $this->_requestObject;

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

        $resultObject = $this->json->listContactApi($_requestOb, $queryParams, $querySorts, $keyword, $skip, $limit);
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

    public function viewAction()
    {
        // 设置请求数据
        $_requestOb=$this->_requestObject;
        $contactId=$_SERVER['QUERY_STRING'];
        $_contactID =base64_decode($contactId);
        // 请求Hessian服务端方法

        $userKY= $this->json->getContactApi($_requestOb,$_contactID);
        $userData= $this->objectToArray(json_decode($userKY));
        $this->view->e=$userData['result'];

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/account/view.phtml");
            echo $content;
            exit;
        }
    }

    public function addAction()
    {
        if ($this->_request->isPost()) {

            // 设置请求数据
            $_requestOb=$this->_requestObject;
            //编辑用户信息
            $_account = array();
//                $_account['contactID'] = $this->view->userID;
            $_account['name'] = $this->_request->getParam('name');
            $_account['ecommloginname'] = $this->_request->getParam('email');
            $_account['phone'] = $this->_request->getParam('phone');

            $emrole=$this->_request->getParam('ecommrole');
            $emrole2=implode(',',$emrole);
            $_account['ecommrole'] = $emrole2;

            $_account['mobilePhone'] = $this->_request->getParam('mobilePhone');
            $_account['email'] = $this->_request->getParam('email');
            $_account['salutation'] = $this->_request->getParam('salutation');
            $ddtime= $this->_request->getParam('birthdate');
            if (!empty($ddtime)) {
                $date3 = date("Y-m-d\TH:i:s", strtotime($ddtime));
            } else {
                $date3 = null;
            }
            $_account['birthdate'] = $date3;
            $_account['department'] = $this->_request->getParam('department');
            //判断是否为默认联系人
//            $isDefaultPublic=($this->_request->getParam('isDefaultPublic')=='1')?true:false;
//            $_account['isDefaultPublic']=$isDefaultPublic;
            //判断是否为订单联系人

            $isPublicContact=$this->_request->getParam('isPublicContact');
            if($isPublicContact=='1'){
                $_account['isPublicContact']=true;
            }else{
                $_account['isPublicContact']=false;
            }


            $_account['title'] = $this->_request->getParam('title');
            $_account['sex'] = $this->_request->getParam('sex');
            $_account['fax'] = $this->_request->getParam('fax');
            $_account['assistantName'] = $this->_request->getParam('assistantName');
            $_account['assistantPhone'] = $this->_request->getParam('assistantPhone');
            $_account['mailingCountryCode'] = $this->_request->getParam('mailingCountryCode');
            $_account['mailingStateCode'] = $this->_request->getParam('mailingStateCode');
            $_account['mailingCityCode'] = $this->_request->getParam('mailingCityCode');
            $_account['mailingAddress'] = $_account['mailingCountryCode'].$_account['mailingStateCode'].$_account['mailingCityCode'];
            $_account['mailingStreet'] = $this->_request->getParam('mailingStreet');
            $_account['mailingZipCode'] = $this->_request->getParam('mailingZipCode');
            $_account['othAddress'] = $this->_request->getParam('othAddress');
            $_account['mailingStreet'] = $this->_request->getParam('mailingStreet');
            $_account['certificateType'] = $this->_request->getParam('certificateType');
            $_account['certificateNo'] = $this->_request->getParam('certificateNo');
            /*添加字段证件号码  证件类型*/
          //  $_account['identityType'] = $this->_request->getParam('identityType');
            $_account['identityType'] = '01';
            $_account['identityNo'] = $this->_request->getParam('identityNo');

            $userKY = $this->json->addContactApi($_requestOb, $_account);
            $userData= $this->objectToArray(json_decode($userKY));

            if ($userData['status']!= 1) {
                Shop_Browser::redirect($this->view->translate('tip_add_fail').$userData['error'],$this->view->seed_Setting['user_app_server'].'/account');
            } else {
                Shop_Browser::redirect($this->view->translate('tip_add_success'),$this->view->seed_Setting['user_app_server'].'/account');
            }

        }
        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/account/add.phtml");
            echo $content;
            exit;
        }
    }
    public function editAction()
    {
        // 设置请求数据
        $_requestOb=$this->_requestObject;
        $contactId=$_SERVER['QUERY_STRING'];
        $_contactID =base64_decode($contactId);
        $userKY= $this->json->getContactApi($_requestOb,$_contactID);
        $userData= $this->objectToArray(json_decode($userKY));
        $this->view->e=$userData['result'];
        $this->view->ecommrole=explode(",",$userData['result']['ecommrole']);


        if ($this->_request->isPost()) {
                //编辑用户信息
                $_account = array();
                $_account['contactID'] = $_contactID;
             //   $_account['name'] = $this->_request->getParam('name');
                $_account['ecommloginname'] = $userData['result']['ecommloginname'];
                $_account['phone'] = $this->_request->getParam('phone');
            //判断是否为默认联系人
//                $isDefaultPublic=($this->_request->getParam('isDefaultPublic')=='1')?true:false;
//                $_account['isDefaultPublic']=$isDefaultPublic;
            //判断是否为订单联系人
            $isPublicContact=$this->_request->getParam('isPublicContact');
            if($isPublicContact=='1'){
                $_account['isPublicContact']=true;
            }else{
                $_account['isPublicContact']=false;
            }
            //员工状态 4.10 文档提出 编辑、view页面停用  contactStatus
                $emrole=$this->_request->getParam('ecommrole');
                $emrole2=implode(',',$emrole);
                $_account['ecommrole'] = $emrole2;
                $_account['mobilePhone'] = $this->_request->getParam('mobilePhone');
                $_account['email'] = $this->_request->getParam('email');
                $_account['salutation'] = $this->_request->getParam('salutation');
                $ddtime= $this->_request->getParam('birthdate');
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
                $_account['assistantName'] = $this->_request->getParam('assistantName');
                $_account['assistantPhone'] = $this->_request->getParam('assistantPhone');
                $_account['mailingCountryCode'] = $this->_request->getParam('mailingCountryCode');
                $_account['mailingStateCode'] = $this->_request->getParam('mailingStateCode');
                $_account['mailingCityCode'] = $this->_request->getParam('mailingCityCode');
                $_account['mailingAddress'] = $_account['mailingCountryCode'].$_account['mailingStateCode'].$_account['mailingCityCode'];
                $_account['mailingStreet'] = $this->_request->getParam('mailingStreet');
                $_account['mailingZipCode'] = $this->_request->getParam('mailingZipCode');
                $_account['mailingStreet'] = $this->_request->getParam('mailingStreet');
                 /*添加字段证件号码  证件类型*/
                $_account['identityType'] = '01';
              //  $_account['identityType'] = $this->_request->getParam('identityType');
            //判断状态修改name 和身份证号码
            if($userData['result']['realAuthStatus']==0||$userData['result']['realAuthStatus']==-1){
                $_account['name'] = $this->_request->getParam('name');
                $_account['identityNo'] = $this->_request->getParam('identityNo');
            }else{
                $_account['name'] = $userData['result']['name'];
                $_account['identityNo'] = $userData['result']['identityNo'];
            }

                $userKY = $this->json->editContactApi($_requestOb, $_account);
               $userEdit= $this->objectToArray(json_decode($userKY));

                if ($userEdit['status']!= 1) {
                    //编辑失败
                    Shop_Browser::redirect($this->view->translate('tip_edit_fail').$userEdit['error'], $this->view->seed_Setting['user_app_server'].'/account');
                } else {
                    //编辑成功
                    Shop_Browser::redirect($this->view->translate('tip_edit_success'), $this->view->seed_Setting['user_app_server'].'/account');
                }
        }
        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/account/edit.phtml");
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
    public  function validAction()
    {

        if ($this->_request->isPost()) {
            //设为默认
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
            $opData= $this->json->editContactValidApi($_requestOb,$_objID);
            echo $opData;
        }
        exit;
    }
}
