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
    public function indexAction()
    {
        $f1 = new Seed_Filter_Alnum();
        $mod = $f1->filter($this->_request->getParam('mod'));
        if (empty($mod)) {
            $mod = "index";
        }

        $_requestOb = $this->_requestObject;
        $crnCode = '';
        if ($this->_request->isPost()) {
            //获取附件ID
            $crnCode = $this->_request->getParam('crnCode');
        }
        if ($this->_request->isGet()) {
            //获取附件ID
            $crnCode = $this->_request->getParam('code');
        }
        // 时间轴接口
        $this->view->DeTime = [];
        $DateTime = $this->json->listRepaymentPlanGroupByDay($_requestOb, $crnCode);
        $existData = $this->objectToArray(json_decode($DateTime));
        $DeTime = $existData['result'];
        foreach ($existData['result'] as $k => $v) {
            if ($k == 'crnCode') {
                /* $this->view->DcrnCode=$v;修改 统一使用crnCode*/
                $this->view->DcrnCode = empty($crnCode)?$this->view->crnCode:$crnCode;

            } else {
                $this->view->DeTime[$k] = $v;
            }
        }


        //指定 月应还款金额\总还款额
        if ($crnCode == null) {
            $crnCode = $this->view->DcrnCode;
        }
        $MonthTotal = $this->json->getRepaymentTotalAmount($_requestOb, $crnCode);
        $existData = $this->objectToArray(json_decode($MonthTotal));
        $this->view->MonthTotal = $existData['result']['cur'];
        $this->view->TotalAmount = $existData['result']['all'];
        //;$this->view->f_crnCode = $existData['result']['crnCode'];修改 统一使用crnCode*/
        $this->view->f_crnCode = $crnCode;

        //信用额度
        $paymentData = $this->json->paymentViewApi($_requestOb);
        $existData = $this->objectToArray(json_decode($paymentData));
        $this->view->paymentData = $paymenRs = $existData['result'];
        //获取信用额度百分比
        if ($paymenRs['creditLimit'] == '0') {
            $this->view->jd = '0';
        } else {
            $this->view->jd = round($paymenRs['creditBal']) / round($paymenRs['creditLimit']) > 0 ? round(($paymenRs['creditBal']) / round($paymenRs['creditLimit']) * 100) : '0';
        }

        /*金融项目列表*/
        /*待激活	待放款	待还款	不通过	完成	(factoringStatus值分别为：”01”、”11”、”12”、”04”、”05”，默认”01”)*/
        $_finStatus = strval($this->_request->getParam('status'));
        if (empty($_finStatus)) {
            $_finStatus = '01';
        }
        $_startDate = $this->_request->getParam('startDate');

        $_endDate = $this->_request->getParam('endDate');

        $_lowerAmount = $this->_request->getParam('lowerAmount');
        $_upperAmount = $this->_request->getParam('upperAmount');

        $_factoringNo = $this->_request->getParam('factoringNo');
        $_orderNo = $this->_request->getParam('orderNo');
        $_listcrnCode = $this->_request->getParam('listcrnCode');
        $_querySorts = $this->_request->getParam('querySorts');
        $_type = $this->_request->getParam('type');

        $_creditor = ($_type == 'creditor') ? $this->view->accountID : null;/*债券*/
        $_debtor = ($_type == 'debtor') ? $this->view->accountID : null;/*债务*/
        if (empty($_listcrnCode)) {
            $_listcrnCode = null;
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
        //项目列表
        $listLoan = $this->json->listFactoring($this->_requestObject, $_queryP = null, $_querySorts = null,
            $_keyword = null, $_skip, $_limit, $_factoringStatus = $_finStatus, $_waitConfirmed = false,
            $_waitPayServiceCharge = false, $_factoringNo, $_orderNo, $_listcrnCode, $_startDate,
            $_endDate, $_lowerAmount, $_upperAmount, $_creditor, $_debtor);

        $existData = $this->objectToArray(json_decode($listLoan));
        $this->view->listLoan = $existData['result'];
        $this->view->status = $_finStatus;
        /*  $file = "user/finance/" . $mod . "-" . $_finStatus;
          $_limit = 5;
          $pageObj = new Seed_Page($this->_request, $total, $_limit);
          $this->view->page = $pageObj->getPageArray();
          $this->view->page['pageurl'] = '/' . $file;
          if ($page > $this->view->page['totalpage'])
              $page = $this->view->page['totalpage'];
          if ($page < 1) $page = 1;*/
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/finance/index.phtml");
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
    public function viewAction()
    {
        //项目列表

        //获取附件ID
        $_factoringID = $this->_request->getParam('id');
        $LoanView = $this->json->getFactoringView($this->_requestObject, base64_decode($_factoringID));
        $existData = $this->objectToArray(json_decode($LoanView));
        $this->view->LoanView = $LoanView = $existData['result'];
        $this->view->mathDate = ($LoanView['expiryDate'] == 0) ? 0 : date('Y-m-d', strtotime($LoanView['expiryDate'])) - date('Y-m-d', time());
        /*文档签署模块*/
        if ($existData['result']['factoringID']) {
            $bizType = 'FT';
            $_resultKY = $this->json->listBizContract($this->_requestObject, $bizType, $existData['result']['factoringID']);
            $res_contract = json_decode($_resultKY);
            if ($res_contract->result) {
                $this->view->contractList = $this->objectToArray($res_contract->result);
            }
        } else {
            $this->view->contractList = [];
        }

        /*组装项目列表*/
        $this->view->planAmount = [];
        /*判断本金是否显示*/
        /* if($LoanView['accountsReceivable']>0){
             $this->view->planAmount['amount']=$LoanView['accountsReceivable'];

         }*/
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
                    $this->view->planInterest[$j]['amount'] = $LoanView['interest']+$LoanView['graceInterest ']+$LoanView['overdueInterest'];
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
        if (is_array($LoanView['factoringRepaymentPlanList'])&&count($LoanView['factoringRepaymentPlanList']) > 0) {
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

