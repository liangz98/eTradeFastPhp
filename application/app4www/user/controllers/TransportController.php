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

    public function transportOrderListAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $querySorts = array();
        // $querySorts['createTime'] = "DESC";

        $keyword = $this->_request->getParam('keyword');
        if (empty($keyword)) {
            $keyword = null;
        }

        $customerID = $this->_request->getParam('customerID');
        $transOrderCode = $this->_request->getParam('transOrderCode');
        $carrierName = $this->_request->getParam('carrierName');
        $takeDeliveryTime = $this->_request->getParam('takeDeliveryTime');
        if (empty($takeDeliveryTime)) {
            $takeDeliveryTime = null;
        } else {
            $takeDeliveryTime = date("Y-m-d\TH:i:s", strtotime($takeDeliveryTime));
        }

        $limit = $this->_request->getParam('limit');
        if (empty($limit) || $limit <= 0) {
            $limit = 10;
        }

        $skip = $this->_request->getParam('skip');
        if (empty($limit) || $limit <= 0) {
            $skip = 0;
        }

        if (is_array($querySorts)) {
            $querySorts = $this->arrayToObject($querySorts);
        }

        $resultObject = $this->json->listTransportOrder($requestObject, $querySorts, $keyword, $skip, $limit, $customerID, $transOrderCode, $carrierName, $takeDeliveryTime);
        $msg["total"] = json_decode($resultObject)->extData->totalSize;
        $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    // 详情页
    public function viewAction() {
        $requestObject = $this->_requestObject;

        $queryString = $_SERVER['QUERY_STRING'];
        $transportOrderID = base64_decode($queryString);

        $resultObject = $this->json->getTransOrderView($requestObject, $transportOrderID);
        $this->view->transportOrder = $this->objectToArray(json_decode($resultObject)->result);
        // $transportOrder = json_decode($resultObject)->result;


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
    public function applyListAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $customerID = $this->_request->getParam('customerID');

        $startSignDate = $this->_request->getParam('startSignDate');
        if (empty($startSignDate)) {
            $startSignDate = null;
        } else {
            $startSignDate = date("Y-m-d\TH:i:s", strtotime($startSignDate));
        }

        $endSignDate = $this->_request->getParam('endSignDate');
        if (empty($endSignDate)) {
            $endSignDate = null;
        } else {
            $endSignDate = date("Y-m-d\TH:i:s", strtotime($endSignDate));
        }

        $resultObject = $this->json->listApplyOrderList($requestObject, $customerID, $startSignDate, $endSignDate);
        // $msg["total"] = json_decode($resultObject)->extData->totalSize;
        // $msg["rows"] = json_decode($resultObject)->result;
        if (json_decode($resultObject)->result) {
            echo json_encode(json_decode($resultObject)->result);
        } else {
            echo null;
        }
        exit;
    }

    public function listActivatedFinancingAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $debtor = $this->_request->getParam('debtor');

        $resultObject = $this->json->listActivatedFinancing($requestObject, $debtor);
        // $msg["total"] = json_decode($resultObject)->extData->totalSize;
        // $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode(json_decode($resultObject)->result);
        exit;
    }
}
