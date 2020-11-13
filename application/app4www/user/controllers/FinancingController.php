<?php

class FinancingController extends Kyapi_Controller_Action {

    public function preDispatch() {
        $this->view->cur_pos = 'finance';

        if (empty($this->view->userID)) {
            // 提示：请先登录系统
            Mobile_Browser::redirect($this->view->translate('tip_login_please'), $this->view->seed_Setting['user_app_server'] . "/login");
        }
        $this->view->cur_pos = $this->_request->getParam('controller');
        $cururl = $this->getRequestUri();
        if ($cururl == '/finance') {
            $this->_request->setParam('all', 'all');
            $this->indexAction();
            exit;
        }

        preg_match('/(.*)\.html/', $cururl, $arr);

        if (isset($arr[1]) && !empty($arr[1])) {

            preg_match_all('/^\/user\/finance\/(index)-([\d]+)-([\d]+).html/isU', $cururl, $arr);
            if (is_array($arr) && count($arr) > 1) {
                $this->_request->setParam('status', $arr[2][0]);
                $this->_request->setParam('page', $arr[3][0]);
                $this->indexAction();
                exit;
            }
            /*  //没有找到相关信息！*/
            Mobile_Browser::redirect($this->view->translate('tip_find_no'), $this->view->seed_BaseUrl . "/");

        }

        // 更新session时间
        $this->updateRedisExpire();
    }

    // 金融方案首页
    public function indexAction() {
        $requestObject = $this->_requestObject;

        $financingStatus = $this->_request->getParam('financingStatus');
        if (empty($financingStatus)) {
            $financingStatus = null;
        } else {
            if ($financingStatus == 'all') {
                $financingStatus = null;
            }
        }
        $this->view->resultStatus = $financingStatus;

        $resultObject = $this->json->listFinancing($requestObject, $financingStatus);
        // $this->view->financingList = $this->objectToArray(json_decode($resultObject)->result);
        $financingList = json_decode($resultObject)->result;

        // 文档签署模
        $bizType = 'NC';
        foreach ($financingList as $financing) {
            $contractResultObject = $this->json->listBizContract($requestObject, $bizType, $financing->financingID);
            $resContract = json_decode($contractResultObject);
            if ($resContract->result) {
                $financing->contractList = $resContract->result;
            }
        }

        $this->view->financingList = $this->objectToArray($financingList);

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/financing/index.phtml");
            echo $content;
            exit;
        }
    }


    public function financingListAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $limit = $this->_request->getParam('limit');
        if (empty($limit) || $limit <= 0) {
            $limit = 10;
        }

        $skip = $this->_request->getParam('skip');
        if (empty($limit) || $limit <= 0) {
            $skip = 0;
        }

        $financingStatus = $this->_request->getParam('financingStatus');
        if (empty($financingStatus)) {
            $financingStatus = null;
        } else {
            if ($financingStatus == 'all') {
                $financingStatus = null;
            }
        }

        $resultObject = $this->json->listFinancing($requestObject, $financingStatus);
        $msg["totalPage"] = json_decode($resultObject)->extData->totalPage;
        $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    // 金融方案详情
    public function financingItemAction() {
        $requestObject = $this->_requestObject;

        $queryString = $_SERVER['QUERY_STRING'];
        $financingID = base64_decode($queryString);

        $financingItemResultObject = $this->json->getFinancingView($requestObject, $financingID);
        $this->view->financingItem = $this->objectToArray(json_decode($financingItemResultObject)->result);

        // 右上角图表
        $loanStatisticResultObject = $this->json->listLoanStatisticData($requestObject);
        $this->view->loanStatistic = $this->objectToArray(json_decode($loanStatisticResultObject)->result);

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/financing/financingItem.phtml");
            echo $content;
            exit;
        }
    }

    public function listLoanStatisticDataAjaxAction() {
        $requestObject = $this->_requestObject;

        $resultObject = $this->json->listLoanStatisticData($requestObject);

        echo json_encode(json_decode($resultObject)->result);

        exit;
    }

    // 金融方案详情 - 项目列表
    public function financingLoanListAjaxAction() {
        // $msg = array();
        $requestObject = $this->_requestObject;

        $querySorts = array();

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

        $financingID = $this->_request->getParam('financingID');

        $loanStatus = $this->_request->getParam('loanStatus');
        if (empty($loanStatus)) {
            $loanStatus = null;
        } else {
            if ($loanStatus == 'all') {
                $loanStatus = null;
            }
        }

        $expiryDate = $this->_request->getParam('expiryDate');
        if (empty($loanStatus)) {
            $expiryDate = date("Y-m-d\TH:i:s");
        }

        if (is_array($querySorts)) {
            $querySorts = $this->arrayToObject($querySorts);
        }

        $resultObject = $this->json->listFinancingLoan($requestObject, $financingID, $loanStatus, $expiryDate, $querySorts, $keyword, $skip, $limit);
        // $msg["totalPage"] = json_decode($resultObject)->extData->totalPage;
        // $msg["rows"] = json_decode($resultObject)->result;
        //
        // echo json_encode($msg);
        if (json_decode($resultObject)->status == 1) {
            echo json_encode(json_decode($resultObject)->result);
        } else {
            echo null;
        }

        exit;
    }

    // 金融方案详情 - 项目详情
    public function financingLoanViewAction() {
        $requestObject = $this->_requestObject;

        $queryString = $_SERVER['QUERY_STRING'];
        $itemID = base64_decode($queryString);

        $resultObject = $this->json->getFinancingLoanView($requestObject, $itemID);
        $this->view->financingLoan = $this->objectToArray(json_decode($resultObject)->result);
        $financingLoan = json_decode($resultObject)->result;

        // 判断是否已存在有效还款记录
        $isRepayment = true;
        foreach ($financingLoan->financingRepaymentList as $financingRepayment) {
            if ($financingRepayment->repaymentStatus == 0) {
                $isRepayment = false;
                break;
            }
        }
        $this->view->isRepayment = $isRepayment;

        // 文档签署模
        $contractList = [];
        $bizType = 'NL';
        $contractResultObject = $this->json->listBizContract($requestObject, $bizType, $financingLoan->loanID);
        $resContract = json_decode($contractResultObject);
        if ($resContract->result) {
            $contractList = array_merge($contractList, $this->objectToArray($resContract->result));
        }

        $this->view->contractList = $contractList;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/financing/financingItemView.phtml");
            echo $content;
            exit;
        }
    }

    // 金融项目申请
    public function addFinancingLoanAction() {
        $requestObject = $this->_requestObject;

        if ($this->_request->isPost()) {
            $financingLoan = array();
            $financingLoan['financingID'] = $this->_request->getParam('financingID');
            $applyLoanDate = $this->_request->getParam('applyLoanDate');
            $applyExpiryDate = $this->_request->getParam('applyExpiryDate');
            if (empty($applyLoanDate)) {
                $applyLoanDate = date("Y-m-d\TH:i:s");
            }
            if (empty($applyExpiryDate)) {
                $applyExpiryDate = date("Y-m-d\TH:i:s");
            }
            $financingLoan['applyLoanDate'] = date("Y-m-d\TH:i:s", strtotime($applyLoanDate));
            $financingLoan['applyExpiryDate'] = date("Y-m-d\TH:i:s", strtotime($applyExpiryDate));
            $financingLoan['receivableAmount'] = (double)$this->_request->getParam('receivableAmount');
            $financingLoan['assignmentAmount'] = (double)$this->_request->getParam('assignmentAmount');
            $financingLoan['loanAmount'] = (double)$this->_request->getParam('loanAmount');
            if (is_array($financingLoan)) {
                $financingLoan = $this->arrayToObject($financingLoan);
            }

            $financingObjectList = array();
            $objBizType = $_POST['objBizType'];
            $objBizID = $_POST['objBizID'];
            $objBizNo = $_POST['objBizNo'];
            $summary = $_POST['summary'];
            $crnCode = $_POST['crnCode'];
            $totalAmount = $_POST['totalAmount'];
            $billDate = $_POST['billDate'];
            foreach ($objBizType as $key=>$value) {
                $financingObjectList[$key]['objBizType'] = $objBizType[$key];
                $financingObjectList[$key]['objBizID'] = $objBizID[$key];
                $financingObjectList[$key]['objBizNo'] = $objBizNo[$key];
                $financingObjectList[$key]['summary'] = $summary[$key];
                $financingObjectList[$key]['crnCode'] = $crnCode[$key];
                $financingObjectList[$key]['totalAmount'] = (double)$totalAmount[$key];
                $financingObjectList[$key]['billDate'] = date("Y-m-d\TH:i:s", strtotime($billDate[$key]));
            }

            $resultObject = $this->json->addFinancingLoan($requestObject, $financingLoan, $financingObjectList);
            $resultObject = json_decode($resultObject);
            $resultMsg = base64_encode($resultObject->result->loanID);

            // 页面跳转
            if ($resultObject->status == 1) {
                $this->redirect("/financing/financing-loan-view?" . $resultMsg);
            } else {
                $resultMsg = base64_encode($this->view->translate('tip_add_fail'). '! ' . $resultObject->error);
                $this->redirect("/transport/apply?resultMsg=" . $resultMsg);
            }
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/transport/apply.phtml");
            echo $content;
            exit;
        }
    }

    // 金融方案详情 - 项目详情 - 还款
    public function financingRepaymentAction() {
        $requestObject = $this->_requestObject;

        $queryString = $_SERVER['QUERY_STRING'];
        $loanID = base64_decode($queryString);

        $financingLoanResultObject = $this->json->getFinancingLoanView($requestObject, $loanID);
        $financingLoan = json_decode($financingLoanResultObject)->result;

        $financingID = $financingLoan->financingID;
        $financingResultObject = $this->json->getFinancingView($requestObject, $financingID);
        $financing = json_decode($financingResultObject)->result;
        $this->view->financing = $this->objectToArray($financing);

        // 转帐信息
        $bankAcctResultObject = $this->json->getBankAccountApi($requestObject, $financing->companyBankAcctID);
        $this->view->bankAcct = $this->objectToArray(json_decode($bankAcctResultObject)->result);

        // 随机验证码
//        $this->view->explanationStr = date('d') . str_pad(mt_rand(1, 99999), 3, '0', STR_PAD_LEFT);
        $this->view->explanationStr = $financingLoan->loanNo;

        $paymentLoanIDs = array();
        $paymentLoanIDs[0] = $financingLoan->loanID;
        $this->view->loanIDs = $paymentLoanIDs;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/financing/financingRepayment.phtml");
            echo $content;
            exit;
        }
    }

    // 金融方案详情 - 项目详情 - 批量还款
    public function financingRepaymentsAction() {
        $requestObject = $this->_requestObject;

        $financingID = $this->_request->getParam('financingID');

        $resultObject = $this->json->getFinancingView($requestObject, $financingID);
        $financing = json_decode($resultObject)->result;
        $this->view->financing = $this->objectToArray($financing);
        // 转帐信息
        $bankAcctResultObject = $this->json->getBankAccountApi($requestObject, $financing->companyBankAcctID);
        $this->view->bankAcct = $this->objectToArray(json_decode($bankAcctResultObject)->result);

        // 随机验证码
//        $this->view->explanationStr = date('d') . str_pad(mt_rand(1, 99999), 3, '0', STR_PAD_LEFT);
        $this->view->explanationStr = $this->_request->getParam('explanationStr');

        $paymentLoanIDs = $_POST['paymentLoanIDs'];
        $this->view->loanIDs = $paymentLoanIDs;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/financing/financingRepayment.phtml");
            echo $content;
            exit;
        }
    }

    // 查询利息
    public function calcInterestAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $financingID = $this->_request->getParam('financingID');

        $expiryDate = $this->_request->getParam('expiryDate');
        if (empty($expiryDate)) {
            $expiryDate = date("Y-m-d\TH:i:s");
        } else {
            $expiryDate = date("Y-m-d\TH:i:s", strtotime($expiryDate));
        }

        $loanIDs = $_POST['loanIDs'];

        $resultObject = $this->json->calcInterest($requestObject, $financingID, $loanIDs, $expiryDate);
        if (json_decode($resultObject)->status == 0) {
            echo json_encode(json_decode($resultObject)->result);
        } else {
            echo null;
        }
        exit;
    }

    public function financingRepaymentSaveAction() {
        $requestObject = $this->_requestObject;

        // 基本信息
        $financingID = $this->_request->getParam('financingID');
        $paymentRequest = array();
        $paymentAmount = (double)$this->_request->getParam('paymentAmount');
        $explanation = $this->_request->getParam('explanation');
        $paymentDate = $this->_request->getParam('expiryDate');
        if (empty($paymentDate)) {
            $paymentDate = date("Y-m-d\TH:i:s");
        } else {
            $paymentDate = date("Y-m-d\TH:i:s", strtotime($paymentDate));
        }
        $paymentRequest['paymentAmount'] = $paymentAmount;
        $paymentRequest['explanation'] = $explanation;
        $paymentRequest['paymentDate'] = $paymentDate;

        // 附件
        $attachmentList = array();
        $attachID = $this->_request->getParam('attachNid');
        $attachType = $this->_request->getParam('attachType');
        $attachName = $this->_request->getParam("attachName");
        $attachSize = $this->_request->getParam("attachSize");
        foreach ($attachID as $key=>$value) {
            $attachmentList[$key]['attachID'] = $attachID[$key];
            $attachmentList[$key]['attachType'] = $attachType[$key];
            $attachmentList[$key]['name'] = $attachName[$key];
            $attachmentList[$key]['attachSize'] = $attachSize[$key];
        }
        $paymentRequest['attachmentList'] = $attachmentList;

        // loans
        $loanIDs = $_POST['loanIDs'];


        $this->view->paymentAmount = $paymentAmount;                    // 应付总额
        $this->view->actualFinancingAmount = (double)$this->_request->getParam('actualFinancingAmount');    // 本金
        $this->view->actualInterest = (double)$this->_request->getParam('actualInterest');                  // 利息
        $this->view->actualServiceCharge = (double)$this->_request->getParam('actualServiceCharge');        // 服务费

        // 判断附件是否为空
        if (empty($attachmentList)) {
            $this->view->resultMsg = base64_encode($this->view->translate('tip_add_fail'). '! ' . '附件不能为空。');

            $resultObject = $this->json->getFinancingView($requestObject, $financingID);
            $financing = json_decode($resultObject)->result;
            $this->view->financing = $this->objectToArray($financing);
            // 转帐信息
            $bankAcctResultObject = $this->json->getBankAccountApi($requestObject, $financing->companyBankAcctID);
            $this->view->bankAcct = $this->objectToArray(json_decode($bankAcctResultObject)->result);

            // 随机验证码
            $this->view->explanationStr = date('d') . str_pad(mt_rand(1, 99999), 3, '0', STR_PAD_LEFT);

            $this->view->loanIDs = $loanIDs;

            $content = $this->view->render(SEED_WWW_TPL . "/financing/financingRepayment.phtml");
            echo $content;
            exit;
        }

        // 还款请求
        $resultObject = $this->json->doRepayment($requestObject, $financingID, $paymentRequest, $loanIDs);
        $resultStatus = json_decode($resultObject)->status;
        $resultMsg = base64_encode($financingID);

        // 页面跳转
        if ($resultStatus == 1) {   // 请求成功
            $this->redirect("/financing/financing-item?" . $resultMsg);
        } else {
            $this->view->resultMsg = base64_encode($this->view->translate('tip_add_fail'). '! ' . json_decode($resultObject)->error);

            $resultObject = $this->json->getFinancingView($requestObject, $financingID);
            $financing = json_decode($resultObject)->result;
            $this->view->financing = $this->objectToArray($financing);
            // 转帐信息
            $bankAcctResultObject = $this->json->getBankAccountApi($requestObject, $financing->companyBankAcctID);
            $this->view->bankAcct = $this->objectToArray(json_decode($bankAcctResultObject)->result);

            // 随机验证码
            $this->view->explanationStr = date('d') . str_pad(mt_rand(1, 99999), 3, '0', STR_PAD_LEFT);

            $this->view->loanIDs = $loanIDs;

            $content = $this->view->render(SEED_WWW_TPL . "/financing/financingRepayment.phtml");
            echo $content;
            exit;
        }
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/financing/index.phtml");
            echo $content;
            exit;
        }
    }
}
