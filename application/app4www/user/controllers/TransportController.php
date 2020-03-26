<?php

class TransportController extends Kyapi_Controller_Action {

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

    // 列表页
    public function indexAction() {
        $this->view->resultMsg = $this->_request->getParam('resultMsg');

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/transport/index.phtml");
            echo $content;
            exit;
        }
    }

    public function freightListAjaxAction() {
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

    // 详情页
    public function viewAction() {
        $this->view->resultMsg = $this->_request->getParam('resultMsg');

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/transport/view.phtml");
            echo $content;
            exit;
        }
    }

    // 方案申请页
    public function applyAction() {
        $this->view->resultMsg = $this->_request->getParam('resultMsg');

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/transport/apply.phtml");
            echo $content;
            exit;
        }
    }

    // 方案申请页列表
    public function freightLoanApplyListAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $queryParams = array();
        $crnCode = $this->_request->getParam('crnCode');
        if (!empty($crnCode)) {
            $queryParams['crnCode'] = $crnCode;
        }

        $querySorts = array();
        $querySorts['createTime'] = "DESC";

        $keyword = $this->_request->getParam('keyword');
        if (empty($keyword)) {
            $keyword = null;
        }

        $limit = $this->_request->getParam('limit');
        if (empty($limit) || $limit <= 0) {
            $limit = 50;
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

        $lowerAmount = $this->_request->getParam('lowerAmount');
        if (empty($lowerAmount)) {
            $lowerAmount = null;
        }
        $upperAmount = $this->_request->getParam('upperAmount');
        if (empty($upperAmount)) {
            $upperAmount = null;
        }

        $paymentStatus = strval($this->_request->getParam('paymentStatus'));
        if (empty($paymentStatus)) {
            $paymentStatus = null;
        }

        $tradingType = strval($this->_request->getParam('tradingType'));
        if (empty($tradingType)) {
            $tradingType = null;
        }

        $transNo = strval($this->_request->getParam('transNo'));
        if (empty($transNo)) {
            $transNo = null;
        }

        $resultObject = $this->json->listpaymentTradApi($requestObject, $queryParams, $querySorts, $keyword, $skip, $limit, $startDate, $endDate, $lowerAmount, $upperAmount, $paymentStatus, $tradingType, $transNo);
        // $msg["total"] = json_decode($resultObject)->extData->totalSize;
        // $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode(json_decode($resultObject)->result);
        exit;
    }
}
