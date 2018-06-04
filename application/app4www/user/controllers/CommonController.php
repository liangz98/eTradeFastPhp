<?php

class CommonController extends Kyapi_Controller_Action {

    public function preDispatch() {
        $this->view->cur_pos = 'info';

        if (empty($this->view->userID)) {
            //请先登录系统
            Mobile_Browser::redirect($this->view->translate('tip_login_please'), $this->view->seed_Setting['user_app_server'] . "/login");
        }
        $this->view->cur_pos = $this->_request->getParam('controller');
    }

    // 取数据字典缓存

    /**
     * @throws Exception
     */
    public function dictAjaxAction() {
        $dictCode = $this->_request->getParam('dictCode');
        $langCode = $this->_request->getParam('langCode');

        $cacheM = new Seed_Model_Cache2File();
        $result = array();
        foreach ($dictCode as $code) {
            $dic = $cacheM->get('abc_'. $code);

            $str = array();
            foreach ($dic as $key => $value) {
                if ($value['baseLangList']) {
                    $setArr = $value['baseLangList'];
                    foreach ($setArr as $k1 => $v1) {
                        if ($v1['langCode'] == $langCode) {
                            //输出当前语言的name
                            $str[$key]['code'] = $value['code'];
                            $str[$key]['name'] = $v1['nameText'];
                            //设置缺省
                            if (empty($str[$key]['name'])) {
                                if ($v1['langCode'] == "zh_CN") {
                                    $str[$key]['name'] = $v1['nameText'];
                                }
                            }
                        }
                    }
                } else {
                    $str = $dic;
                }
            }
            $result[$code] = $str;
        }
        echo json_encode($result);
        exit;
    }

    /**
     * @throws Exception
     */
    public function dictFuzzyQueryAjaxAction() {
        $dictCode = $this->_request->getParam('dictCode');
        $langCode = $this->_request->getParam('langCode');
        $keyword = $this->_request->getParam('keyword');
        if (empty($keyword)) {
            $keyword = null;
        }

        $cacheM = new Seed_Model_Cache2File();
        $result = array();

            $dic = $cacheM->get($dictCode);
            $str = array();
            foreach ($dic['result'] as $key => $value) {
                if ($value['baseLangList']) {
                    $setArr = $value['baseLangList'];
                    foreach ($setArr as $k1 => $v1) {
                        if ($v1['langCode'] == $langCode) {
                            // 输出当前语言的name
                            $str[$key]['code'] = $value['code'];
                            $str[$key]['name'] = $v1['nameText'];
                            // 设置缺省
                            if (empty($str[$key]['name'])) {
                                if ($v1['langCode'] == "zh_CN") {
                                    $str[$key]['name'] = $v1['nameText'];
                                }
                            }
                        }
                    }
                } else {
                    $str = $dic;
                }
            }

            foreach ($str as $key => $value) {
                if ($keyword == null) {
                    if ($value['name'] != null) {
                        $result[$key]['name'] = $value['name'];
                        $result[$key]['id'] = $value['code'];
                    }
                } else {
                    if (stripos($value['name'], $keyword) != false) {
                        $result[$key]['name'] = $value['name'];
                        $result[$key]['id'] = $value['code'];
                    }
                }

                // if (count($result) == 10) {
                //     break;
                // }
            }
        echo json_encode($result);
        exit;
    }

    public function hscodeAjaxAction() {
        $keyword = $this->_request->getParam('keyword');
        if (empty($keyword)) {
            $keyword = null;
        }

        $resultObject = $this->json->listHSCodeApi($this->_requestObject, null, null, $keyword, 0, 0);
        $msg = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    public function exchangeRateAjaxAction() {
        $bizType = $this->_request->getParam('bizType');
        if (empty($bizType)) {
            $bizType = null;
        }
        $bizID = $this->_request->getParam('bizID');
        if (empty($bizID)) {
            $bizID = null;
        }
        $baseCrn = $this->_request->getParam('baseCrn');
        if (empty($baseCrn)) {
            $baseCrn = 'USD';
        }
        $contraCrn = $this->_request->getParam('contraCrn');
        if (empty($contraCrn)) {
            $contraCrn = 'CNY';
        }

        $resultObject = $this->json->getExchangeRateApi($this->_requestObject, $bizType, $bizID, $baseCrn, $contraCrn);
        $msg = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }
}
