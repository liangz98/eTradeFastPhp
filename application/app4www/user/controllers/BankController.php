<?php
class BankController extends Kyapi_Controller_Action
{
    public function preDispatch()
    {
        $this->view->cur_pos = 'bank';
        //清除 免登陆session
        $this->IsAuth($this->view->visitor);

        if(empty($this->view->userID)){
            // 提示：请先登录系统
            Mobile_Browser::redirect($this->view->translate('tip_login_please'),$this->view->seed_Setting['user_app_server']."/login");
        }

        $this->view->cur_pos = $this->_request->getParam('controller');
        $cururl = $this->getRequestUri();
        if ($cururl == '/bank') {
            $this->_request->setParam('all', 'all');
            $this->indexAction();
            exit;
        }

        preg_match('/(.*)\.html/', $cururl, $arr);

        if (isset($arr[1]) && !empty($arr[1])) {


            preg_match_all('/^\/user\/bank\/(index|orderlist)-([\d]+)-([\d]+).html/isU', $cururl, $arr);

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
            Mobile_Browser::redirect('没有找到相关信息！', $this->view->seed_BaseUrl . "/");

        }
    }

    public function indexAction()
    {
        $f1 = new Seed_Filter_Alnum();
        $mod = $f1->filter($this->_request->getParam('mod'));
        if (empty($mod)) {$mod = "index";}

        $_PStatus =strval($this->_request->getParam('status'));
        if(empty( $_PStatus)){  $_PStatus ="01";}

        $_querySorts=$this->_request->getParam('querySorts');
        if(empty($_querySorts)){ $_querySorts ="01";}

        $_keyword=$this->_request->getParam('keyword');
        if(empty($_keyword)){ $_keyword =null;}

        $page =intval($this->_request->getParam('page'));
        if($page<1)$page=1;
        $_limit=8;
        $_skip=$_limit*($page-1);
        /*账户条件*/
        $_queryP =new queryAccount();
        $_queryP->accountID= $this->view->accountID;
        $_queryP->contactStatus= $_PStatus;
        /*排序条件*/
//        $_queryP =new queryBank();
//        $_queryP->contactStatus= $_PStatus;
        //QU  查询排序条件
        $_querySorts = new querySorts();
        $_querySorts->createTime= "DESC";

        $_requestOb=$this->_requestObject;
        $userKY= $this->json->listBankAccountApi($_requestOb,null,$_querySorts,  $_keyword, $_skip, $_limit);
        $existDatt =$this->objectToArray(json_decode($userKY));

        //传递到视图
        $this->view->bankList=$existDatt['result'];

        //操作状态数据
        $Dicst=array();
        $Dicst['01']=$this->view->translate('valid');//有效
        $Dicst['02']=$this->view->translate('disable');//禁用
        $Dicst['03']=$this->view->translate('checkIN');//审核中
        $Dicst['04']=$this->view->translate('checkNO');//审核未通过
        $this->view->Dicst=$Dicst;

        //统计正常状态数量、分页
        $existCount = $existDatt['extData'];
        $total = $existCount['totalSize'];
        $page=$existCount['totalPage'];

        //设置视图状态
        $this->view->status= $_PStatus;

        $file = "user/bank/" . $mod . "-" . $_PStatus;
        $_limit=8;
        $pageObj = new Seed_Page($this->_request,$total,$_limit);
        $this->view->page = $pageObj->getPageArray();
        $this->view->page['pageurl'] = '/' . $file;
        if ($page > $this->view->page['totalpage'])
            $page = $this->view->page['totalpage'];
        if ($page < 1) $page = 1;

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/bank/index.phtml");
            echo $content;
            exit;
        }
    }
    public function addAction()
    {
        if ($this->_request->isPost()) {
            try {
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
                        $_attachList[$k]->attachType = '0000';
                        $_attachList[$k]->bizType = 'BA';
                        $_attachList[$k]->name = $_attach2[$k]['attachName'];
                        $_attachList[$k]->size = (int)$_attach2[$k]['attachSize'];

                    }
                }
                //附件end

                // 设置请求数据
                $_requestOb=$this->_requestObject;
                //判断是否为默认账户
                if($this->_request->getParam('isDefault')=='1'){
                    $_isdd=true;} else {$_isdd=false;}

                /*添加银行账户信息*/
                $_bank = new Kyapi_Model_bank();
                $_bank->accountID = $this->view->accountID;
                $_bank->bankAcctType = $this->_request->getParam('bankAcctType');
                $_bank->bankAcctName = $this->_request->getParam('bankAcctName');
                $_bank->bankAcctNo = $this->_request->getParam('bankAcctNo');
                $_bank->bankName = $this->_request->getParam('bankName');
                $_bank->bankAddress = $this->_request->getParam('bankAddress');
                $_bank->swiftcode = $this->_request->getParam('swiftcode');
                //$_bank->isDefault = $_isdd;
                $_bank->remarks = $this->_request->getParam('remarks');
                $_bank->attachmentList = $_attachList;

                if (empty($_attachList)) {
                    //银行附件不能为空
                    Shop_Browser::redirect($this->view->translate('tip_add_fail').$this->view->translate('tip_bank_no'),$this->view->seed_Setting['user_app_server'].'/bank');
                } else {
                    $addBank= $this->json->addBankAccountApi($_requestOb,$_bank);
                    $resultBank =$this->objectToArray(json_decode($addBank));
                    $this->view->e = $resultBank['result'];
                    if ($resultBank['status'] != 1) {
                     Shop_Browser::redirect($this->view->translate('tip_add_fail'). $resultBank['error'],$this->view->seed_Setting['user_app_server'].'/bank');
                    } else {
                        Shop_Browser::redirect($this->view->translate('tip_add_success'),$this->view->seed_Setting['user_app_server'].'/bank');
                    }
                }
            } catch (HttpError $ex) {
                Shop_Browser::redirect($ex->getMessage(),$this->view->seed_Setting['user_app_server'].'/bank');
            }
        }
        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/bank/add.phtml");
            echo $content;
            exit;
        }
    }
    public function editAction()
    {
        // 请求Hessian服务端方法
        $bankAcctID=$_SERVER['QUERY_STRING'];
        $_bankAcctID =base64_decode($bankAcctID);

        $_requestOb=$this->_requestObject;
        $userKY= $this->json->getBankAccountApi($_requestOb,$_bankAcctID);

        //获取银行账户明细
        $resultBank =$this->objectToArray(json_decode($userKY));
        $this->view->bank=$resultBank['result'];

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
                    $_attachList[$k]->attachType = '0000';
                    $_attachList[$k]->bizType = 'BA';
                    $_attachList[$k]->name = $_attach2[$k]['attachName'];
                    $_attachList[$k]->size = (int)$_attach2[$k]['attachSize'];

                }
            }
            //附件end

            // 设置请求数据
            $_requestOb=$this->_requestObject;
            //判断是否为默认账户
            if($this->_request->getParam('isDefault')=='1'){
                $_isdd=true;} else {$_isdd=false;}

            /*添加银行账户信息*/
            $_bank = new Kyapi_Model_bank();
            $_bank->accountID = $this->view->accountID;
            $_bank->bankAcctID = $resultBank['result']['bankAcctID'];
            $_bank->bankAcctType = $this->_request->getParam('bankAcctType');
            $_bank->bankAcctName = $this->_request->getParam('bankAcctName');
            $_bank->bankAcctNo = $this->_request->getParam('bankAcctNo');
            $_bank->bankName = $this->_request->getParam('bankName');
            $_bank->bankAddress = $this->_request->getParam('bankAddress');
            $_bank->swiftcode = $this->_request->getParam('swiftcode');
           // $_bank->isDefault = $this->view->bank['isDefault'];//采用默认值 不编辑
            $_bank->remarks = $this->_request->getParam('remarks');
            $_bank->attachmentList = $_attachList;

            if (empty($_attachList)) {
                //银行附件不能为空
                Shop_Browser::redirect($this->view->translate('tip_edit_fail').$this->view->translate('tip_bank_no'),$this->view->seed_Setting['user_app_server'].'/bank');
            } else {
                $editBank= $this->json->editBankAccountApi($_requestOb,$_bank);
                $_resultBank =$this->objectToArray(json_decode($editBank));
                if ( $_resultBank['status'] != 1) {
                    Shop_Browser::redirect($this->view->translate('tip_edit_fail'). $_resultBank['error'],$this->view->seed_Setting['user_app_server'].'/bank');
                } else {
                    Shop_Browser::redirect($this->view->translate('tip_edit_success'),$this->view->seed_Setting['user_app_server'].'/bank');
                }
            }
        }

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/bank/edit.phtml");
            echo $content;
            exit;
        }
    }

    public function viewAction()
    {
        // 请求Hessian服务端方法
        $bankAcctID=$_SERVER['QUERY_STRING'];
        $_bankAcctID =base64_decode($bankAcctID);

        $_requestOb=$this->_requestObject;
        $userKY= $this->json->getBankAccountApi($_requestOb,$_bankAcctID);
        $existKY =$this->objectToArray(json_decode($userKY));
        $this->view->bank=$existKY['result'];

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/bank/view.phtml");
            echo $content;
            exit;
        }
    }

    public function defaultAction()
    {
        if ($this->_request->isPost()) {
            //设为默认
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
            $opData= $this->json->setDefaultBankAccountApi($_requestOb,$_objID);
            echo $opData;
        }
        exit;

    }

    public function deleteAction()
    {
        if ($this->_request->isPost()) {
               //删除
                // 请求Hessian服务端方法bankAcctID
                $_objID = $this->_request->getPost('delID');
                $_requestOb=$this->_requestObject;
                $opData= $this->json->delBankAccountApi($_requestOb,$_objID);
                echo $opData;
        }
        exit;

    }

    public function invalidAction()
    {
        if ($this->_request->isPost()) {
           //禁用
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
            $opData= $this->json->invalidBankAccountApi($_requestOb,$_objID);
            echo $opData;
        }
        exit;
    }

    public function validAction()
    {
        if ($this->_request->isPost()) {
            //启用
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
            $opData= $this->json->validBankAccountApi($_requestOb,$_objID);
            echo $opData;
        }
        exit;

    }
}
