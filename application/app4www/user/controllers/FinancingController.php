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
        $this->view->financingList = $this->objectToArray(json_decode($resultObject)->result);

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

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/financing/financingItemView.phtml");
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
