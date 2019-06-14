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

        // 更新session时间
        $this->updateRedisExpire();
    }

    public function indexAction() {
        $this->view->resultMsg = $this->_request->getParam('resultMsg');
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/bank/index.phtml");
            echo $content;
            exit;
        }
    }

    public function bankListAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $queryParams = array();

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

        $resultObject = $this->json->listBankAccountApi($requestObject, $queryParams, $querySorts, $keyword, $skip, $limit);
        $msg["total"] = json_decode($resultObject)->extData->totalSize;
        $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    public function addAction() {
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
            $_requestOb = $this->_requestObject;
            //判断是否为默认账户
            if ($this->_request->getParam('isDefault') == '1') {
                $_isdd = true;
            } else {
                $_isdd = false;
            }

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

            // 20190614, 小周提出:新增银行时附件不再是必填项
            // if (empty($_attachList)) {
            //     $this->view->bank = $this->objectToArray($_bank);
            //     $this->view->errMsg = $this->view->translate('tip_add_fail') . $this->view->translate('tip_bank_no');
            // }
            $addBank = $this->json->addBankAccountApi($_requestOb, $_bank);
            $resultBank = $this->objectToArray(json_decode($addBank));
            $this->view->e = $resultBank['result'];

            if ($resultBank['status'] != 1) {
                $this->view->bank = $this->objectToArray($_bank);
                $this->view->errMsg = $this->view->translate('tip_add_fail') . $resultBank['error'];
            } else {
                $resultMsg = base64_encode($this->view->translate('tip_add_success'));
                $this->redirect("/bank/index?resultMsg=".$resultMsg);
            }

        }
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/bank/add.phtml");
            echo $content;
            exit;
        }
    }

    public function editAction() {
        // 请求Hessian服务端方法
        $bankAcctID = $_SERVER['QUERY_STRING'];
        $_bankAcctID = base64_decode($bankAcctID);

        $_requestOb = $this->_requestObject;
        $userKY = $this->json->getBankAccountApi($_requestOb, $_bankAcctID);

        //获取银行账户明细
        $resultBank = $this->objectToArray(json_decode($userKY));
        $this->view->bank = $resultBank['result'];

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
            $_requestOb = $this->_requestObject;
            //判断是否为默认账户
            if ($this->_request->getParam('isDefault') == '1') {
                $_isdd = true;
            } else {
                $_isdd = false;
            }

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

            // 20190614, 小周提出:新增银行时附件不再是必填项
            // if (empty($_attachList)) {
            //     $this->view->bank = $this->objectToArray($_bank);
            //     $this->view->errMsg = $this->view->translate('tip_edit_fail') . $this->view->translate('tip_bank_no');
            // }

            $editBank = $this->json->editBankAccountApi($_requestOb, $_bank);
            $resultBank = $this->objectToArray(json_decode($editBank));

            if ($resultBank['status'] != 1) {
                $this->view->bank = $this->objectToArray($_bank);
                $this->view->errMsg = $this->view->translate('tip_edit_fail'). $resultBank['error'];
            } else {
                $resultMsg = base64_encode($this->view->translate('tip_edit_success'));
                $this->redirect("/bank/index?resultMsg=".$resultMsg);
            }
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/bank/edit.phtml");
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
