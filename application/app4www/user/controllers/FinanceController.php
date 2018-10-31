<?php

class FinanceController extends Kyapi_Controller_Action
{
    public function preDispatch()
    {
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

    }

    /**列表页**/
    public function indexAction() {
        $requestObject = $this->_requestObject;
        // 信用评级信息
        $resultObject = $this->json->getCreditRating($requestObject);
        $creditRating = json_decode($resultObject)->result;

        $this->view->creditRating = $creditRating;
        $this->view->level = $creditRating->level;
        $this->view->instance = $creditRating->instance;
        $this->view->validDate = $creditRating->validDate;
        $this->view->expiryDate = $creditRating->expiryDate;
        $this->view->applyStatus = $creditRating->instance->applyStatus;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/finance/index.phtml");
            echo $content;
            exit;
        }
    }

    public function factoringListAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $queryParams = array();

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

        $factoringStatus = $this->_request->getParam('factoringStatus');
        if (empty($factoringStatus)) {
            $factoringStatus = null;
        } else {
            if ($factoringStatus == 'all') {
                $factoringStatus = null;
            }
        }
        $factoringMode = $this->_request->getParam('factoringMode');
        if (!empty($factoringMode)) {
            if ($factoringMode == 'all') {
                $factoringMode = null;
            }
        }
        $factoringNo = $this->_request->getParam('factoringNo');
        $orderNo = $this->_request->getParam('orderNo');
        $crnCode = $this->_request->getParam('crnCode');
        if (!empty($crnCode)) {
            if ($crnCode == 'all') {
                $crnCode = null;
            }
        }
        $startDate = $this->_request->getParam('startDate');
        if (empty($startDate)) {
            $startDate = null;
        } else {
            $startDate = date("Y-m-d\TH:i:s", strtotime($startDate));
        }
        $endDate = $this->_request->getParam('endDate');
        if (empty($endDate)) {
            $endDate = null;
        } else {
            $endDate = date("Y-m-d\TH:i:s", strtotime($endDate));
        }

        $lowerAmount = null;
        $upperAmount = null;
        $factoringAmount = $this->_request->getParam('factoringAmount');
        if (!empty($factoringAmount)) {
            if ($factoringAmount == 'A1') {
                $lowerAmount = 0;
                $upperAmount = 5000;
            } else if ($factoringAmount == 'A2') {
                $lowerAmount = 5000;
                $upperAmount = 20000;
            } else if ($factoringAmount == 'A3') {
                $lowerAmount = 20000;
                $upperAmount = 50000;
            } else if ($factoringAmount == 'A4') {
                $lowerAmount = 50000;
                $upperAmount = 100000;
            } else if ($factoringAmount == 'A5') {
                $lowerAmount = 100000;
                $upperAmount = 200000;
            } else if ($factoringAmount == 'A6') {
                $lowerAmount = 200000;
            }
        }

        $resultObject = $this->json->listFactoring($requestObject, $queryParams, $querySorts, $keyword, $skip, $limit,
            $factoringStatus, $factoringMode, $factoringNo, $waitConfirmed = false, $waitPayServiceCharge = false, $orderNo, $crnCode,
            $startDate, $endDate, $lowerAmount, $upperAmount);
        $msg["totalPage"] = json_decode($resultObject)->extData->totalPage;
        $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    public function doApplyLoanAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $factoringLoan = array();
        $factoringLoan['loanID'] = $this->_request->getParam('loanID');
        $factoringLoan['loanDate'] =  date("Y-m-d\TH:i:s", strtotime($this->_request->getParam('loanDate')));
        // $factoringLoan['loanAmount'] = $this->_request->getParam('loanAmount');
        $factoringLoan['loanAmount'] = $this->_request->getParam('loanAmount');


        if (is_array($factoringLoan)) {
            $factoringLoan = $this->arrayToObject($factoringLoan);
        }

        $resultObject = $this->json->doApplyLoan($requestObject, $factoringLoan);
        $msg["resultObject"] = json_decode($resultObject);

        // 取回资用申请附件
        $bizType = 'FL';
        $bizContractResultObject = $this->json->listBizContract($requestObject, $bizType, json_decode($resultObject)->result->loanID);
        $msg["bizContract"] = json_decode($bizContractResultObject)->result;

        // 附件服务器URL
        $msg["kyAttachUrl"] = $this->view->seed_Setting['KyUrlex'];

        echo json_encode($msg);
        exit;
    }

    public function getFactoringLoanAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $loanID = $this->_request->getParam('loanID');

        $resultObject = $this->json->getFactoringLoanView($requestObject, $loanID);
        $msg = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    /* init Evaluation */
    public function initEvaluationApplyAction() {
        $dataType = $this->_request->getParam('dataType');;
        $requestObject = $this->_requestObject;

        if ($dataType == 'init') {
            $resultObject = $this->json->initEvaluationApply($requestObject);
            $creditRating = json_decode($resultObject)->result;
            $this->view->instance = $creditRating->instance;
        } elseif ($dataType == 'instance') {
            $resultObject = $this->json->getCreditRating($requestObject);
            $creditRating = json_decode($resultObject)->result;
            $this->view->instance = $creditRating->instance;
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/finance/evaluation.phtml");
            echo $content;
            exit;
        }
    }

    /**异步请求时间详情**/
    public function datelistAction()
    {
        $_requestOb = $this->_requestObject;
        $DeTime = [];
        $crnCode = '';
        if ($this->_request->isPost()) {
            //获取附件ID
            $DateParam = $this->_request->getParam('thisDate');
            $Data = $this->json->listRepaymentPlanByDate($_requestOb, $DateParam, $crnCode);
            $existData = $this->objectToArray(json_decode($Data));
            $DeTime = empty($existData['result']) ? null : $existData['result'];
            /*     $DeTime['result'] = $existData['result'];
                 foreach ($existData['result'] as $k => $v) {
                     $DeTime['amount'] += (float)$v['totalAmount'];
                 }*/
        }
        echo json_encode($DeTime);
        exit;
    }

    /**详情页**/
    public function viewAction() {
        $requestObject = $this->_requestObject;

        $queryString = $_SERVER['QUERY_STRING'];
        $factoringID = base64_decode($queryString);

        $LoanView = $this->json->getFactoringView($requestObject, $factoringID);
        $existData = $this->objectToArray(json_decode($LoanView));
        $this->view->LoanView = $LoanView = $existData['result'];
        $this->view->mathDate = ($LoanView['expiryDate'] == 0) ? 0 : date('Y-m-d', strtotime($LoanView['expiryDate'])) - date('Y-m-d', time());

        // 取回当前公司的企业认证状态
        $_accountID = $this->view->accountID;
        $account = $this->json->getAccountApi($requestObject, $_accountID);
        $this->view->hasIDCertificate = json_decode($account)->result->hasIDCertificate;

        /*文档签署模块*/
        if ($existData['result']['factoringID']) {
            $bizType = 'FT';
            $_resultKY = $this->json->listBizContract($requestObject, $bizType, $existData['result']['factoringID']);
            $res_contract = json_decode($_resultKY);
            if ($res_contract->result) {
                $this->view->contractList = $this->objectToArray($res_contract->result);
            }
        } else {
            $this->view->contractList = [];
        }

        // 封装费用集合
        $serviceChargeList = array();
        $serviceChargeTotalAmount = 0;
        foreach ($LoanView['factoringItem']['factoringLoanList'] as $loanKey => $factoringLoan) {
            if ($factoringLoan['loanType'] == 'P' && $factoringLoan['serviceChargeTradingID'] != null) {
                $serviceCharge['loanNo'] = $factoringLoan['loanNo'];
                $serviceCharge['loanAmount'] = $factoringLoan['loanAmount'];
                $serviceCharge['serviceChargePercent'] = $LoanView['serviceChargePercent'];
                $serviceCharge['loanCrnCode'] = $factoringLoan['loanCrnCode'];
                $serviceCharge['serviceCharge'] = $factoringLoan['serviceCharge'];
                $serviceCharge['serviceChargeTradingID'] = $factoringLoan['serviceChargeTradingID'];
                $serviceCharge['serviceChargeTradingStatus'] = $factoringLoan['serviceChargeTradingStatus'];
                $serviceChargeTotalAmount += $factoringLoan['serviceCharge'];
                array_push($serviceChargeList, $serviceCharge);
            }
        }
        $this->view->serviceChargeList = $serviceChargeList;
        $this->view->serviceChargeTotalAmount = $serviceChargeTotalAmount;

        /*组装项目列表*/
        $this->view->planAmount = [];

        /*判断服务收费 和利息 是否显示*/
        $this->view->planService = [];
        $this->view->planInterest = [];
        $this->view->planRepayment = [];
        if ($LoanView['appCustomerID'] == $this->view->accountID) {
            $j = 0;
            /*基础保理服务费*/
            if ($LoanView['serviceCharge'] > 0) {
                $this->view->planService[$j]['amount'] = $LoanView['serviceCharge'];
                $this->view->planService[$j]['title'] = $this->view->translate('Finance_serviceCharge');/*基础保理服务费*/
                $this->view->planService[$j]['tips'] = $LoanView['serviceChargeDesc'];
                $j = $j + 1;
            }
            /*担保保理服务费*/
            if ($LoanView['hasGuaranteeService']) {
                if ($LoanView['guaranteeServiceFee'] > 0) {
                    $this->view->planService[$j]['amount'] = $LoanView['guaranteeServiceFee'];
                    $this->view->planService[$j]['title'] = $this->view->translate('Finance_guaranteeServiceFee');/*担保保理服务费*/
                    $this->view->planService[$j]['tips'] = $LoanView['guaranteeServiceFeeDesc'];
                    $j = $j + 1;
                }
            }
            /*判断利息 是否显示*/

            $h = 0;
            /*融资保理利息*/
            if ($LoanView['hasLoanService']) {
                if ($LoanView['interest'] > 0) {
                    $this->view->planInterest[$j]['amount'] = $LoanView['interest'] + $LoanView['graceInterest '] + $LoanView['overdueInterest'];
                    $this->view->planInterest[$j]['title'] = $this->view->translate('Finance_interest');/*融资保理利息*/
                    $this->view->planInterest[$j]['tips'] = $LoanView['interestDesc'];
                    $h = $h + 1;
                }
            }
            /*逾期利息*/
            if ($LoanView['overdueInterest'] > 0) {
                $this->view->planInterest[$j]['amount'] = $LoanView['overdueInterest'];
                $this->view->planInterest[$j]['title'] = $this->view->translate('Finance_overdueInterest');/*逾期利息*/
                $this->view->planInterest[$j]['tips'] = $LoanView['overdueInterestDesc'];
                $h = $h + 1;
            }
        }
        /*还款计划*/

        $h = 0;
        if (is_array($LoanView['factoringRepaymentPlanList']) && count($LoanView['factoringRepaymentPlanList']) > 0) {
            foreach ($LoanView['factoringRepaymentPlanList'] as $k => $v) {
                $this->view->planRepayment[$k]['amount'] = $v['repaymentTotalAmount'];
                $this->view->planRepayment[$k]['tips'] = date('Y-m-d', strtotime($v['actualDate']));
                if ($v['repaymentStatus'] == '01') {
                    $this->view->planRepayment[$k]['title'] = $this->view->translate('Finance_repayment_ok');/*待还本金*/
                } else {
                    $this->view->planRepayment[$k]['titles'] = $this->view->translate('Finance_repayment_no');/*已还本金*/
                }

            }
        }
        /*repaymentTotalAmount*/
        //判断当前用户是债权还是债务方
        if ($this->view->accountID == $existData['result']['creditorCustomerID']) {
            $this->view->customer = 'creditor';
        } else {
            $this->view->customer = 'debtor';
        }


        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/finance/view.phtml");
            echo $content;
            exit;
        }
    }

    public function factoringInterestListAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $loanID = $this->_request->getParam('loanID');
        if (empty($loanID)) {
            $loanID = null;
        }

        $resultObject = $this->json->listFactoringInterest($requestObject, $loanID);
        $msg["total"] = count(json_decode($resultObject)->result);
        $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    /*折线图*/
    public function countgainAction(){
        //项目列表
        if ($this->_request->isPost()) {
            //获取附件ID
            $_crncode = $this->_request->getParam('crncode');
            $_year = $this->_request->getParam('year');
            $countArr = $this->json->countGains($this->_requestObject, $_year, $_crncode);
            $existData = $this->objectToArray(json_decode($countArr));
            $resData=$existData['result'];
            echo json_encode($resData);
            exit;

        }
        exit;
    }

    /*还款计算器*/
    public function calculationAction()
    {
        //项目列表
        if ($this->_request->isPost()) {
            //获取附件ID
            $_recordID = $this->_request->getParam('ID');
            $_repaymentDate = $this->_request->getParam('Date');
            $LoanView = $this->json->calculation($this->_requestObject, $_recordID, $_repaymentDate);
            $existData = $this->objectToArray(json_decode($LoanView));

            exit;
            echo json_encode($existData['result']);

        }
        exit;
    }

    /**还款页**/
    public function repayAction()
    {
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/finance/repay.phtml");
            echo $content;
            exit;
        }
    }

    /**费用支付页**/
    public function payAction()
    {
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/finance/pay.phtml");
            echo $content;
            exit;
        }
    }

    /**渠道页**/
    public function channelAction()
    {
        $_requestOb = $this->_requestObject;
        $_crnCode = $this->_request->getParam('crnCode');
        if (empty($_crnCode)) {
            $_crnCode = $this->view->crnCode;
        }

        $_querySorts = $this->_request->getParam('querySorts');
        if ($this->_request->isPost()) {
            $_startDate = $this->_request->getPost('startDate');
            $_endDate = $this->_request->getPost('endDate');
            $_lowerAmount = $this->_request->getPost('lowerAmount');
            $_upperAmount = $this->_request->getPost('upperAmount');
            $_crnCode = $this->_request->getPost('crnCode');
            $_loanNo = $this->_request->getPost('loanNo');
        }

        if (empty($_querySorts)) {
            $_querySorts = null;
        }

        $_keyword = $this->_request->getParam('keyword');
        if (empty($_keyword)) {
            $_keyword = null;
        }

        $page = intval($this->_request->getParam('page'));
        if ($page < 1) $page = 1;
        $_limit = 5;
        $_skip = $_limit * ($page - 1);


        //头部统计
        $countAccumulative = $this->json->countAccumulative($_requestOb, $_crnCode);
        $testcal = $this->objectToArray(json_decode($countAccumulative));
        $this->view->cal = $testcal['result'];

        $list = $this->json->listFinancingChannel($_requestOb, null, $_querySorts, $_keyword, 0, null, $_loanNo, $_crnCode, $_startDate, $_endDate, $_lowerAmount, $_upperAmount);
        $existData = $this->objectToArray(json_decode($list));


        // $this->view->list = $existData['result'];


//        $str = "{
//    \"extData\": {
//        \"totalSize\": 1,
//        \"totalPage\": 1
//    },
//    \"latency\": 268,
//    \"result\": [
//        {
//            \"accountsReceivable\": 4788,
//            \"crnCode\": \"USD\",
//            \"expiryDate\": \"2017-11-07T00:00:00\",
//            \"loanDate\": \"2017-10-07T00:00:00\",
//            \"hasAttachment\": false,
//            \"interestAmount\": 30.02,
//            \"interestPercent\": 0.00033,
//            \"factoringID\": \"0C553429-882A-DEC0-47BC-0750FA90CCDA\",
//            \"factoringNo\": \"L2017101315054110001\",
//            \"period\": 18,
//            \"factoringStatus\": \"03\",
//            \"orderID\": \"39B90E20-F757-8B65-09DE-605603C8887C\",
//            \"orderNo\": \"PO201709191000\",
//            \"payAcctID\": \"4C954253-51EA-EBB5-2631-2376CBE5BBEA\",
//            \"channelName\": \"我是海外来投资的\",
//            \"channelStatus\": \"01\",
//            \"financingAmount\": 4788,
//            \"tradingStatus\": 0
//        }
//    ],
//    \"status\": 1
//}
//";
//        $existData = $this->objectToArray(json_decode($str));
        $list = $existData['result'];
        foreach ($list as $k => $v) {
            $list[$k]['diffTime'] = $this->diffBetweenTwoDays($v['loanDate'], $v['expiryDate']);

        }


        $this->view->list = $list;
        $this->view->crnCode = $_crnCode;
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/finance/channel.phtml");
            echo $content;
            exit;
        }
    }


    /**渠道详情页**/
    public function channelviewAction()
    {

        $_channelID = $this->_request->getParam('id');

        $channelInfo = $this->json->getFinancingChannelView($this->_requestObject, $_channelID);

        // $channelRow = $this->objectToArray(json_decode($channelInfo));

        //  $this->view->channelRow  = $channelRow['result'];


        $channelRow = $this->objectToArray(json_decode($channelInfo));
        $list = $channelRow['result'];


        $list['diffTime'] = $this->diffBetweenTwoDays(date("Y-m-d", time()), $list['expiryDate']);
        $list['companyName'] = trim($list['companyName']);
        // print_r($list); exit;
        $this->view->channelRow = $list;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/finance/channelview.phtml");
            echo $content;
            exit;
        }
    }


    function diffBetweenTwoDays($day1, $day2)
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second1 - $second2) / 86400;
    }


}

