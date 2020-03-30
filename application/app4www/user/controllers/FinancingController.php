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
        $loanStatisticResultobject = $this->json->listLoanStatisticData($requestObject);
        $this->view->loanStatistic = $this->objectToArray(json_decode($loanStatisticResultobject)->result);

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/financing/financingItem.phtml");
            echo $content;
            exit;
        }
    }

    // 金融方案详情 - 项目列表
    public function financingItemListAjaxAction() {
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

        $itemStatus = $this->_request->getParam('itemStatus');
        if (empty($itemStatus)) {
            $itemStatus = null;
        } else {
            if ($itemStatus == 'all') {
                $itemStatus = null;
            }
        }

        if (is_array($querySorts)) {
            $querySorts = $this->arrayToObject($querySorts);
        }

        $resultObject = $this->json->listFinancingItem($requestObject, $financingID, $itemStatus, $querySorts, $keyword, $skip, $limit);
        // $msg["totalPage"] = json_decode($resultObject)->extData->totalPage;
        // $msg["rows"] = json_decode($resultObject)->result;
        //
        // echo json_encode($msg);
        echo json_encode(json_decode($resultObject)->result);
        exit;
    }

    // 金融方案详情 - 项目详情
    public function financingItemViewAction() {
        $requestObject = $this->_requestObject;

        $queryString = $_SERVER['QUERY_STRING'];
        $itemID = base64_decode($queryString);

        $resultObject = $this->json->getFinancingItemView($requestObject, $itemID);
        $this->view->financingItem = $this->objectToArray(json_decode($resultObject)->result);
        $financingItem = json_decode($resultObject)->result;

        // 文档签署模
        $contractList = [];
        $bizType = 'NL';
        foreach ($financingItem->financingLoanList as $financingLoan) {
            $contractResultObject = $this->json->listBizContract($requestObject, $bizType, $financingLoan->loanID);
            $resContract = json_decode($contractResultObject);
            if ($resContract->result) {
                $contractList = array_merge($contractList, $this->objectToArray($resContract->result));
            }
        }
        $this->view->contractList = $contractList;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/financing/financingItemView.phtml");
            echo $content;
            exit;
        }
    }

    // 金融项目申请
    public function addFinancingItemAction() {
        $requestObject = $this->_requestObject;

        if ($this->_request->isPost()) {
            $financingItem = array();
            $financingItem['financingID'] = $this->_request->getParam('financingID');
            $loanDate = $this->_request->getParam('loanDate');
            $expiryDate = $this->_request->getParam('expiryDate');
            if (empty($loanDate)) {
                $loanDate = date("Y-m-d\TH:i:s");
            }
            if (empty($expiryDate)) {
                $expiryDate = date("Y-m-d\TH:i:s");
            }
            $financingItem['loanDate'] = date("Y-m-d\TH:i:s", strtotime($loanDate));
            $financingItem['expiryDate'] = date("Y-m-d\TH:i:s", strtotime($expiryDate));
            $financingItem['receivableAmount'] = (double)$this->_request->getParam('receivableAmount');
            $financingItem['assignmentAmount'] = (double)$this->_request->getParam('assignmentAmount');
            $financingItem['financingAmount'] = (double)$this->_request->getParam('financingAmount');
            if (is_array($financingItem)) {
                $financingItem = $this->arrayToObject($financingItem);
            }

            $financingObjectList = array();
            $objBizType = $_POST['objBizType'];
            $objBizID = $_POST['objBizID'];
            $objBizNo = $_POST['objBizNo'];
            $summary = $_POST['summary'];
            $crnCode = $_POST['crnCode'];
            $totalAmount = $_POST['totalAmount'];
            foreach ($objBizType as $key=>$value) {
                $financingObjectList[$key]['objBizType'] = $objBizType[$key];
                $financingObjectList[$key]['objBizID'] = $objBizID[$key];
                $financingObjectList[$key]['objBizNo'] = $objBizNo[$key];
                $financingObjectList[$key]['summary'] = $summary[$key];
                $financingObjectList[$key]['crnCode'] = $crnCode[$key];
                $financingObjectList[$key]['totalAmount'] = (double)$totalAmount[$key];
            }

            $resultObject = $this->json->addFinancingItem($requestObject, $financingItem, $financingObjectList);
            $resultObject = json_decode($resultObject);
            $resultMsg = base64_encode($resultObject->result->itemID);

            // 页面跳转
            if ($resultObject->status == 1) {
                $this->redirect("/financing/financing-item-view?" . $resultMsg);
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
        $itemID = base64_decode($queryString);

        $resultObject = $this->json->getFinancingItemView($requestObject, $itemID);
        $this->view->financingItem = $this->objectToArray(json_decode($resultObject)->result);
        $financingItem = json_decode($resultObject)->result;

        // 转帐信息
        $bankAcctResultObject = $this->json->getBankAccountApi($requestObject, $financingItem->companyBankAcctID);
        $this->view->bankAcct = $this->objectToArray(json_decode($bankAcctResultObject)->result);

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/financing/financingRepayment.phtml");
            echo $content;
            exit;
        }
    }
}
