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

        // 更新session时间
        $this->updateRedisExpire();
    }

    // 列表页
    public function indexAction() {
        $this->view->resultMsg = $this->_request->getParam('resultMsg');

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

        // 设置列表页分布配置
        $this->view->limit = 4;
        $this->view->offset = 0;

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
        $queryString = $_SERVER['QUERY_STRING'];
        $dataType = base64_decode($queryString);

        // $dataType = $this->_request->getParam('dataType');;
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

        if ($this->_request->isPost()) {
            $evaluationInstance = array();
            $evaluationInstance['instanceID'] = $this->_request->getParam('instanceID');
            if (is_array($evaluationInstance)) {
                $evaluationInstance = $this->arrayToObject($evaluationInstance);
            }

            $evaluationApplyDocumentList = array();

            $documentIDList = $this->_request->getParam('documentID');

            $requestAttachList = array();
            $requestAttachList["attachID"] = $this->_request->getParam('attachNid');
            $requestAttachList["attachName"] = $this->_request->getParam("attachName");
            $requestAttachList["attachSize"] = $this->_request->getParam("attachSize");
            $requestAttachList["attachType"] = $this->_request->getParam("attachType");
            $requestAttachList["attachBizID"] = $this->_request->getParam("attachBizID");
            $requestAttachListInit = array();
            foreach ($requestAttachList as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $requestAttachListInit[$k1][$k] = $v1;
                }
            }

            // 判断是保存还是提交
            $submitType = $this->_request->getParam('submitType');
            if ($submitType == 'save') {
                foreach ($documentIDList as $key => $documentID) {
                    $documentType = $this->_request->getParam("documentType_".$documentID);
                    $mandatory = $this->_request->getParam("mandatory_".$documentID);

                    $evaluationApplyDocumentList[$key]['documentID'] = $documentID;
                    $contents = $this->_request->getParam("contents_".$documentID);
                    $evaluationApplyDocumentList[$key]['contents'] = $contents;

                    $attachmentList = array();
                    $i = 0;
                    foreach ($requestAttachListInit as $attachKey => $attach) {
                        if ($attach['attachBizID'] == $documentID) {
                            $attachmentList[$i]['attachID'] = $attach['attachID'];
                            $attachmentList[$i]['attachType'] = "0000";
                            $attachmentList[$i]['name'] = $attach['attachName'];
                            $attachmentList[$i]['size'] = (int)$attach['attachSize'];
                            $i++;
                        }
                    }

                    if (is_array($attachmentList) && !empty($attachmentList)) {
                        $evaluationApplyDocumentList[$key]['attachmentList'] = $attachmentList;
                    }
                }

                // 保存
                $resultObject = $this->json->saveEvaluationApply($requestObject, $evaluationInstance, $evaluationApplyDocumentList);
                $resultObject = json_decode($resultObject);

                // 跳转
                $resultMsg = base64_encode($this->view->translate('save').' Success');
                if ($resultObject->status != 0) {
                    $resultMsg = base64_encode($this->view->translate('save').' fail');
                }
                $this->redirect("/finance/index?resultMsg=" . $resultMsg);
            } else {
                foreach ($documentIDList as $key => $documentID) {
                    $documentType = $this->_request->getParam("documentType_".$documentID);
                    $mandatory = $this->_request->getParam("mandatory_".$documentID);

                    $evaluationApplyDocumentList[$key]['documentID'] = $documentID;
                    $contents = $this->_request->getParam("contents_".$documentID);
                    $evaluationApplyDocumentList[$key]['contents'] = $contents;

                    $attachmentList = array();
                    $i = 0;
                    foreach ($requestAttachListInit as $attachKey => $attach) {
                        if ($attach['attachBizID'] == $documentID) {
                            $attachmentList[$i]['attachID'] = $attach['attachID'];
                            $attachmentList[$i]['attachType'] = "0000";
                            $attachmentList[$i]['name'] = $attach['attachName'];
                            $attachmentList[$i]['size'] = (int)$attach['attachSize'];
                            $i++;
                        }
                    }

                    if (is_array($attachmentList) && !empty($attachmentList)) {
                        $evaluationApplyDocumentList[$key]['attachmentList'] = $attachmentList;
                    }

                    // 验证必填项
                    if ($mandatory) {
                        if ($documentType == 'TA' || $documentType == 'TX') {
                            if (empty($contents) || empty($attachmentList)) {
                                $this->view->errMsg = $this->view->translate('tip_forReview_fail') . $documentID . '  ' . $documentType;

                                $content = $this->view->render(SEED_WWW_TPL . "/finance/evaluation.phtml");
                                echo $content;
                                exit;
                            }
                        } elseif ($documentType == 'AT') {
                            if (empty($attachmentList)) {
                                $this->view->errMsg = $this->view->translate('tip_forReview_fail') . $documentID . '  ' . $documentType;

                                $content = $this->view->render(SEED_WWW_TPL . "/finance/evaluation.phtml");
                                echo $content;
                                exit;
                            }
                        } elseif ($documentType == 'TX') {
                            if (empty($contents)) {
                                $this->view->errMsg = $this->view->translate('tip_forReview_fail') . $documentID . '  ' . $documentType;

                                $content = $this->view->render(SEED_WWW_TPL . "/finance/evaluation.phtml");
                                echo $content;
                                exit;
                            }
                        }
                    }
                }

                // 提交
                $resultObject = $this->json->submitEvaluationApply($requestObject, $evaluationInstance, $evaluationApplyDocumentList);
                $resultObject = json_decode($resultObject);

                // 跳转
                $resultMsg = base64_encode($this->view->translate('et_tips05'));
                if ($resultObject->status != 0) {
                    $resultMsg = base64_encode($this->view->translate('tip_forReview_fail'));
                }
                $this->redirect("/finance/index?resultMsg=" . $resultMsg);
            }
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

    // 详情页
    public function viewAction() {
        $requestObject = $this->_requestObject;

        $queryString = $_SERVER['QUERY_STRING'];
        $factoringID = base64_decode($queryString);

        $LoanView = $this->json->getFactoringView($requestObject, $factoringID);
        $existData = $this->objectToArray(json_decode($LoanView));
        $this->view->factoring = $this->objectToArray(json_decode($LoanView)->result);
        $this->view->LoanView = $LoanView = $existData['result'];
        $this->view->mathDate = ($LoanView['expiryDate'] == 0) ? 0 : date('Y-m-d', strtotime($LoanView['expiryDate'])) - date('Y-m-d', time());

        // 取回当前公司的企业认证状态
        $account = $this->json->getAccountApi($requestObject);
        $this->refreshAccountCertificateByResult(json_decode($account)->result->hasIDCertificate);

        // 取回当前登录用户的实名认证状态
        $contactID = $this->view->userID;
        $contact = $this->json->getContactApi($requestObject, $contactID);
        $this->view->contactHasIDCertificate = json_decode($contact)->result->hasIDCertificate;

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

        // 合计服务费用
        $serviceChargeTotalAmount = 0;
        foreach ($LoanView['factoringItemList'] as $factoringitem) {
            foreach ($factoringitem['factoringLoanList'] as $factoringLoan) {
                if ($factoringLoan['serviceCharge'] > 0 && $factoringLoan['serviceChargeTradingID'] != null) {
                    $serviceChargeTotalAmount += $factoringLoan['serviceCharge'];
                }
            }
        }
        $this->view->serviceChargeTotalAmount = $serviceChargeTotalAmount;

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

    // 渠道页
    public function channelAction() {
        // $_requestOb = $this->_requestObject;
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/finance/channel.phtml");
            echo $content;
            exit;
        }
    }

    // 统计累计收益/累计投资金额/平均收益率
    public function countAccumulativeAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $crnCode = $this->_request->getParam('crnCode');

        $resultObject = $this->json->countAccumulative($requestObject, $crnCode);
        $msg = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    // 折线图
    public function countGainsAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $crnCode = $this->_request->getParam('crnCode');
        $year = $this->_request->getParam('year');
        $chartType = $this->_request->getParam('chartType');

        // $resultObject = $this->json->countGains($requestObject, $year, $crnCode);
        // $msg = json_decode($resultObject)->result;

        $totalYear[] = (string)2017;
        if (($year - 5) > (int)$totalYear[0]) {
            $totalYear[0] = (string)($year - 5);
        }
        for ($n = 1; $n < 6; $n++) {
            if ((int)$totalYear[count($totalYear) - 1] < $year) {
                $totalYear[] = (string)($totalYear[count($totalYear) - 1] + 1);
            }
        }

        $month = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        $optionsArray = array();
        $seriesArray = array();

        foreach ($totalYear as $currYear) {
            $resultObject = $this->json->countGains($requestObject, $currYear, $crnCode);

            if (count(json_decode($resultObject)->result) > 0) {
                $dataCont = array();
                $monthDataArray = array();
                $seriesArray = array();

                if ($chartType == 'gains') {
                    $textInfo['text'] = $currYear . ' 收益数';
                    $seriesArray['title'] = $textInfo;

                    // gains
                    foreach ($month as $monthItem) {
                        $resultItem = array();
                        foreach (json_decode($resultObject)->result as $key => $item) {
                            if ($item->month == $monthItem) {
                                $resultItem = $item;
                                break;
                            }
                        }
                        if (!empty($resultItem)) {
                            $monthDataArray[] = $resultItem->gains;
                        } else {
                            $monthDataArray[] = 0;
                        }
                    }
                    $dataCont['data'] = $monthDataArray;
                    $dataCont['name'] = '实际收益';
                    $seriesArray['series'][] = $dataCont;

                    // arGains
                    $monthDataArray = array();
                    foreach ($month as $monthItem) {
                        $resultItem = array();
                        foreach (json_decode($resultObject)->result as $key => $item) {
                            if ($item->month == $monthItem) {
                                $resultItem = $item;
                                break;
                            }
                        }

                        if (!empty($resultItem)) {
                            $monthDataArray[] = $resultItem->arGains;
                        } else {
                            $monthDataArray[] = 0;
                        }
                    }
                    $dataCont['data'] = $monthDataArray;
                    $dataCont['name'] = '预计收益';
                    $seriesArray['series'][] = $dataCont;
                    $seriesArray['title']['subtext'] = '收益(元)';
                    $seriesArray['legend']['data'] = array("实际收益", "预计收益");
                } elseif ($chartType == 'quantity') {
                    $textInfo['text'] = $currYear . ' 项目笔数';
                    $seriesArray['title'] = $textInfo;

                    // quantity
                    foreach ($month as $monthItem) {
                        $resultItem = array();
                        foreach (json_decode($resultObject)->result as $key => $item) {
                            if ($item->month == $monthItem) {
                                $resultItem = $item;
                                break;
                            }
                        }
                        if (!empty($resultItem)) {
                            $monthDataArray[] = $resultItem->quantity;
                        } else {
                            $monthDataArray[] = 0;
                        }
                    }

                    $dataCont['data'] = $monthDataArray;
                    $dataCont['name'] = '项目笔数';
                    $seriesArray['series'][] = $dataCont;


                    // arGains
                    $monthDataArray = array();
                    $dataCont['data'] = $monthDataArray[] = 0;
                    $seriesArray['series'][] = $dataCont;

                    $seriesArray['title']['subtext'] = '笔数(笔)';
                    $seriesArray['legend']['data'] = array('项目笔数');
                } else {
                    $textInfo['text'] = $currYear . ' 项目金额';
                    $seriesArray['title'] = $textInfo;

                    // loanAmount
                    foreach ($month as $monthItem) {
                        $resultItem = array();
                        foreach (json_decode($resultObject)->result as $key => $item) {
                            if ($item->month == $monthItem) {
                                $resultItem = $item;
                                break;
                            }
                        }

                        if (!empty($resultItem)) {
                            $monthDataArray[] = $resultItem->arLoanAmount;
                        } else {
                            $monthDataArray[] = 0;
                        }
                    }
                    $dataCont['data'] = $monthDataArray;
                    $dataCont['name'] = '项目金额';
                    $seriesArray['series'][] = $dataCont;

                    // quantity
                    $monthDataArray = array();
                    $dataCont['data'] = $monthDataArray[] = 0;
                    $seriesArray['series'][] = $dataCont;

                    $seriesArray['title']['subtext'] = '金额(元)';
                    $seriesArray['legend']['data'] = array('项目金额');
                }
            } else {
                $seriesArray = array();

                $textInfo['text'] = $currYear . '收益分析';
                $seriesArray['title'] = $textInfo;

                for ($n = 1; $n < 3; $n++) {
                    $dataCont = array();
                    $monthDataArray = array();
                    foreach ($month as $monthItem) {
                        $monthDataArray[] = 0;
                    }
                    $dataCont['data'] = $monthDataArray;

                    $seriesArray['series'][] = $dataCont;
                }
            }

            $optionsArray[] = $seriesArray;
        }


        $msg['totalYear'] = $totalYear;
        $msg['options'] = $optionsArray;

        echo json_encode($msg);
        exit;
    }

    // 列表
    public function listFactoringItemChannelAjaxAction() {
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

        $itemStatus = $this->_request->getParam('factoringStatus');
        if (empty($itemStatus)) {
            $itemStatus = null;
        } else {
            if ($itemStatus == 'all') {
                $itemStatus = null;
            }
        }
        $factoringMode = $this->_request->getParam('factoringMode');
        if (!empty($factoringMode)) {
            if ($factoringMode == 'all') {
                $factoringMode = null;
            }
        }
        $factoringNo = $this->_request->getParam('factoringNo');
        $itemNo = $this->_request->getParam('itemNo');
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

        $resultObject = $this->json->listFactoringItem4Channel($requestObject, $itemNo, $factoringNo, $orderNo, $itemStatus, $crnCode,
            $startDate, $endDate, $lowerAmount, $upperAmount);
        $msg["totalPage"] = json_decode($resultObject)->extData->totalPage;
        $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    // 渠道详情页
    public function channelviewAction() {
        $requestObject = $this->_requestObject;
        $queryString = $_SERVER['QUERY_STRING'];
        $itemID = base64_decode($queryString);


        $channelInfo = $this->json->getFinancingItemView4Channel($requestObject, $itemID);

        $channelRow = $this->objectToArray(json_decode($channelInfo));
        $list = $channelRow['result'];

        $list['diffTime'] = $this->diffBetweenTwoDays(date("Y-m-d", time()), $list['expiryDate'], $list['factoring']['gracePeriod']);
        $list['companyName'] = trim($list['companyName']);
        $this->view->channelRow = $list;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/finance/channelview.phtml");
            echo $content;
            exit;
        }
    }

    function diffBetweenTwoDays($day1, $day2, $gracePeriod) {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        } else {
            $second2 = $second2 + $gracePeriod * 86400;

            if ($second1 < $second2) {
                $tmp = $second2;
                $second2 = $second1;
                $second1 = $tmp;
            }
        }
        return ($second1 - $second2) / 86400;
    }

    // 销售订单详情页
    public function orderViewAction() {
        $orderID = $_SERVER['QUERY_STRING'];
        $_orderID = base64_decode($orderID);
        $this->view->orderid = $_orderID;

        $requestObject = $this->_requestObject;
        $orderResultObject = $this->json->getOrder4FundApi($requestObject, $_orderID);
        $order = $this->objectToArray(json_decode($orderResultObject)->result);

        // 当前返回数据为空时 前端显示为无
        if (!isset($order['packingDesc']))
            $order['packingDesc'] = $this->view->translate('noData');  //包装描述
        if (!isset($order['financingRequest']))
            $order['financingRequest'] = $this->view->translate('noData');  //金融要求
        if (!isset($order['customClearanceRequest']))
            $order['customClearanceRequest'] = $this->view->translate('noData'); //报关要求
        if (!isset($order['shippingRequest'])) {
            if (isset($order['truckingRequest']) && !empty($order['truckingRequest'])) {
                $order['shippingRequest'] = $order['truckingRequest'];
            } else {
                $order['shippingRequest'] = $this->view->translate('noData');
            }
        }

        $this->view->orders = $order;

        // 订单商品
        $this->view->orderItem = $order['orderItemList'];

        // 取回当前公司的企业认证状态
        $account = $this->json->getAccountApi($requestObject);
        $this->refreshAccountCertificateByResult(json_decode($account)->result->hasIDCertificate);

        // 取回当前登录用户的实名认证状态
        $contactID = $this->view->userID;
        $contact = $this->json->getContactApi($requestObject, $contactID);
        $this->view->contactHasIDCertificate = json_decode($contact)->result->hasIDCertificate;

        $accountData = $this->objectToArray(json_decode($account)->result);
        $this->view->account = $accountData;

        // 订单合同列表
        $bizType = 'OD';
        $listBizContractResultObject = $this->json->listBizContract($requestObject, $bizType, $_orderID);
        $listBizContract = json_decode($listBizContractResultObject)->result;
        $this->view->contractList = empty($listBizContract) ? null : $this->objectToArray($listBizContract);

        // 买家执行状态
        $this->view->vestut = $order['vendorExecStatus'];
        // $this->view->veorderID = $order->orderID;

        // 取回订单商品
        $orderItemListResultObject = $this->json->listOrderItem($requestObject, $_orderID);
        $this->view->orderItemList = json_decode($orderItemListResultObject)->result;

        // 取回物流信息
        $deliveryList = $this->json->listDelivery($requestObject, $_orderID);
        $this->view->deliveryList = json_decode($deliveryList)->result;

        // 是否存在已完成的物流
        $this->view->hasOneTakeOverDelivery = False;
        $deliveryData = $this->objectToArray(json_decode($deliveryList));
        $deliveryDataList = $deliveryData['result'];
        foreach ($deliveryDataList as $delivery) {
            if ($delivery['deliveryStatus'] == 4) {
                $this->view->hasOneTakeOverDelivery = True;
                break;
            }
        }

        // 订单汇率
        $rateResultObject = $this->json->listExchangeRateApi($requestObject, $bizType, $_orderID);
        $this->view->exchangeRateList = json_decode($rateResultObject)->result;

        // 物流相关附件
        $this->view->DLPG = array();    // 备货相关附件
        $this->view->DLEG = array();    // 验货相关附件
        $this->view->DLDG = array();    // 发货相关附件
        $this->view->DLRG = array();    // 收货相关附件
        $this->view->DLQT = array();    // 质量保证函模板
        $this->view->DLQF = array();    // 质量保证函正本
        $this->view->DLCT = array();    // 收货确认函模板
        $this->view->DLCF = array();    // 收货确认函正本
        $deliveryAttachDataList = $deliveryData['result'];

        // 报关单列表
        $listDeclarationResultObject = $this->json->listDeclarationApi($requestObject, $_orderID);
        $this->view->listDeclaration = json_decode($listDeclarationResultObject)->result;

        // 订舱单列表
        $listShippingOrderResultObject = $this->json->listShippingOrderApi($requestObject, $_orderID);
        $this->view->listShippingOrder = json_decode($listShippingOrderResultObject)->result;

        // 派车单列表
        $listTruckingOrderResultObject = $this->json->listTruckingOrderApi($requestObject, $_orderID);
        $this->view->listTruckingOrder = json_decode($listTruckingOrderResultObject)->result;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/finance/orderView.phtml");
            echo $content;
            exit;
        }
    }

    // 采购订单详情
    public function orderPurViewAction() {
        // 读取数据字典
        $cacheM = new Seed_Model_Cache2File();
        $DATAlist = $cacheM->get("datatest_setting");
        $this->view->Datalist = $DATAlist;
        // 请求Hessian服务端方法
        $orderID = $_SERVER['QUERY_STRING'];
        $_orderID = base64_decode($orderID);
        $this->view->orderid = $_orderID;

        $requestObject = $this->_requestObject;
        $_resultKY = $this->json->getOrder4FundApi($requestObject, $_orderID);
        $userKY = json_decode($_resultKY);

        // 取回当前公司的企业认证状态
        $account = $this->json->getAccountApi($requestObject);
        $this->refreshAccountCertificateByResult(json_decode($account)->result->hasIDCertificate);

        // 取回当前登录用户的实名认证状态
        $contactID = $this->view->userID;
        $contact = $this->json->getContactApi($requestObject, $contactID);
        $this->view->contactHasIDCertificate = json_decode($contact)->result->hasIDCertificate;

        $existData = $userKY->result;
        $existDatt = $this->objectToArray($existData);
        //当前返回数据为空时 前端显示为无
        if (!isset($existDatt['packingDesc']))
            $existDatt['packingDesc'] = $this->view->translate('noData');  //包装描述
        if (!isset($existDatt['financingRequest']))
            $existDatt['financingRequest'] = $this->view->translate('noData');  //金融要求
        if (!isset($existDatt['customClearanceRequest']))
            $existDatt['customClearanceRequest'] = $this->view->translate('noData'); //报关要求
        if (!isset($existDatt['shippingRequest']))
            $existDatt['shippingRequest'] = $this->view->translate('noData');   //物流要求

        $this->view->orders = $existDatt;
        //判断是否请求合同签订

        $bizType = 'OD';
        $listBizContractResultKY = $this->json->listBizContract($requestObject, $bizType, $_orderID);
        $res_contract = json_decode($listBizContractResultKY);
        if ($res_contract->result) {
            $contractList = $this->objectToArray($res_contract->result);
        }

        $this->view->contractList = empty($contractList) ? null : $contractList;


        // 处理根据返回的运输方式来判断 起运|卸货|交货查询的缓存目录名称
        if ($existDatt['shippingMethod'] == 'SEA') {
            $this->view->port = "SEA_PORT";
            $this->view->loadingPort = $existDatt['loadingPort'];
            $this->view->dischargePort = $existDatt['dischargePort'];
            $this->view->deliveryPort = $existDatt['deliveryPort'];

        } else if ($existDatt['shippingMethod'] == 'AIR') {
            $this->view->port = "AIR_PORT";
            $this->view->loadingPort = $existDatt['loadingPort'];
            $this->view->dischargePort = $existDatt['dischargePort'];
            $this->view->deliveryPort = $existDatt['deliveryPort'];
        } else {
            $this->view->port = "CITY_ISO_CODE";
            $this->view->loadingPort = $existDatt['loadingCity'];
            $this->view->dischargePort = $existDatt['dischargeCity'];
            $this->view->deliveryPort = $existDatt['deliveryCity'];
        };

        $this->view->vestut = $existDatt['buyerExecStatus'];
        $this->view->veorderID = $existDatt['orderID'];

        // 取回物流信息
        $deliveryList = $this->json->listDelivery($requestObject, $_orderID);
        $this->view->deliveryList = json_decode($deliveryList)->result;

        // 判断当前订单是否可以更改
        if ($this->view->accountID == $existDatt['client']) {
            if ($existDatt['orderStatus'] == 05 || $existDatt['orderStatus'] == 00 || $existDatt['orderStatus'] == 02) {
                $this->view->allowEdit = 1;
            } else {
                $this->view->allowEdit = 0;
            }
        } else {
            $this->view->allowEdit = 0;
        }

        // 订单汇率
        $rateResultObject = $this->json->listExchangeRateApi($requestObject, $bizType, $_orderID);
        $this->view->exchangeRateList = json_decode($rateResultObject)->result;

        // 报关单列表
        $listDeclarationResultObject = $this->json->listDeclarationApi($requestObject, $_orderID);
        $this->view->listDeclaration = json_decode($listDeclarationResultObject)->result;

        // 订舱单列表
        $listShippingOrderResultObject = $this->json->listShippingOrderApi($requestObject, $_orderID);
        $this->view->listShippingOrder = json_decode($listShippingOrderResultObject)->result;

        // 派车单列表
        $listTruckingOrderResultObject = $this->json->listTruckingOrderApi($requestObject, $_orderID);
        $this->view->listTruckingOrder = json_decode($listTruckingOrderResultObject)->result;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/finance/orderPurView.phtml");
            echo $content;
            exit;
        }
    }

    // 开票资料 - view
    public function deliverBillingInfoAction() {
        $deliveryID = $this->_request->getParam('deliveryID');
        $requestObject = $this->_requestObject;
        $resultObject = $this->json->getDeliveryView($requestObject, $deliveryID);

        $delivery = json_decode($resultObject)->result;
        $this->view->deliveryID = $deliveryID;
        $this->view->delivery = json_decode($resultObject)->result;
        $this->view->deliverySupplierList = $delivery->deliverySupplierList;

        // 取回当前公司的企业认证状态
        $account = $this->json->getAccountApi($requestObject);
        $this->refreshAccountCertificateByResult(json_decode($account)->result->hasIDCertificate);

        if (defined('SEED_WWW_TPL')) {
            $billingInfoResultObject = $this->json->getBillingInfoApi($requestObject, $deliveryID);
            $billingInfo = json_decode($billingInfoResultObject)->result;
            $this->view->billingInfo = $billingInfo;
            $this->view->deliverySupplierList = $billingInfo->deliverySupplierList;

            $content = $this->view->render(SEED_WWW_TPL . "/finance/getBillingInfo.phtml");
            echo $content;
            exit;
        }
    }

    // 上游订单列表
    public function materialPurOrderListAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $orderID = $this->_request->getParam('orderID');

        $resultObject = $this->json->listMaterialPurOrder($requestObject, $orderID);
        $msg["total"] = json_decode($resultObject)->extData->totalSize;
        $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }
}
