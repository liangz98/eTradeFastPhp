<?php

class CompanyController extends Kyapi_Controller_Action
{
    public function preDispatch()
    {
        $this->view->cur_pos = 'company';
        // 清除 免登陆session
        $this->IsAuth($this->view->visitor);

        if (empty($this->view->userID)) {

            // 提示：请先登录系统
            Mobile_Browser::redirect($this->view->translate('tip_login_please'), $this->view->seed_Setting['user_app_server'] . "/login");
        }
    }

    public function indexAction() {
        // 设置请求数据
        $_accountID = $this->view->accountID;
        $_requestOb = $this->_requestObject;

        // 请求Hessian服务端方法
        $userKY = $this->json->getAccountApi($_requestOb, $_accountID);
        $existData = $this->objectToArray(json_decode($userKY));
        $existDatt = $existData['result'];
        $this->view->e = $existDatt;

        $this->view->accountID = $_accountID;

        // echo $existDatt['legalPersonName'];exit;


        if (!empty($existDatt['incorporationDate']['date'])) {
            $creatDate = date('Y-m-d', strtotime($existDatt['incorporationDate']['date']));
        } else {
            $creatDate = null;
        }
        $this->view->date = $creatDate;
        $this->view->attlist = $existDatt['attachmentList'];
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/company/index.phtml");
            echo $content;
            exit;
        }
    }

    public function orderlistAction()
    {
        // 设置请求数据
        $_accountID = $this->view->accountID;
        $_requestOb = $this->_requestObject;

        // 请求Hessian服务端方法
        $userKY = $this->json->getAccountApi($_requestOb, $_accountID);
        $existData = $this->objectToArray(json_decode($userKY));
        $existDatt = $existData['result'];
        $this->view->e = $existDatt;
        $comJSON = json_encode($existDatt);
        echo $comJSON;
        exit;
    }

    public function editAction() {
        //获取信息
        $_accountID = $this->view->accountID;
        $_requestOb = $this->_requestObject;
        $userKY = $this->json->getAccountApi($_requestOb, $_accountID);
        $existData = $this->objectToArray(json_decode($userKY));
        $existDatt = $existData['result'];
        $this->view->e = $existDatt;

        //时间处理
        if (!empty($existDatt['incorporationDate']['date'])) {
            $creatDate = date('Y-m-d', strtotime($existDatt['incorporationDate']['date']));
        } else {
            $creatDate = date('Y-m-d', time());
        }
        $this->view->date = $creatDate;
        $this->view->attlist = $existDatt['attachmentList'];

        if ($this->_request->isPost()) {
            try {
                //获取附件ID
                $Atachlist = array();
                $Atachlist["attachID"] = $this->_request->getParam('attachNid');
                $Atachlist["attachType"] = $this->_request->getParam('attachType');
                //                $Atachlist["bizType"] = $this->_request->getParam("bizType");
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
                        $_attachList[$k] = new Attachment();
                        $_attachList[$k]->attachID = $_attach2[$k]['attachID'];
                        $_attachList[$k]->attachType = '0000';
                        $_attachList[$k]->bizType = '36';
                        $_attachList[$k]->name = $_attach2[$k]['attachName'];
                        $_attachList[$k]->size = (int)$_attach2[$k]['attachSize'];

                    }
                }

                $etime = $this->_request->getParam('incorporationDate');
                $ztime = DateTime::createFromFormat('Y-m-d', $etime);
                /*编辑公司信息*/
                $_company = new Kyapi_Model_account();
                $_company->accountID = $_accountID;
                $_company->accountName = $this->_request->getParam('accountName');
                $_company->accountEnName = $this->_request->getParam('accountEnName');
                $_company->regdAddress = $this->_request->getParam('regdAddress');
                $_company->regdEnAddress = $this->_request->getParam('regdEnAddress');
                $regdCountryCode = $this->_request->getParam('regdCountryCode');
                //判断国家是否允许修改
                $_company->regdCountryCode = empty($regdCountryCode) ? $existDatt['regdCountryCode'] : $regdCountryCode;

                $_company->industryCode = $this->_request->getParam('industryCode');
                $_company->crnCode = $this->_request->getParam('crnCode');
                $_company->langCode = $this->_request->getParam('langCode');
                $_company->phone = $this->_request->getParam('phone');
                $_company->fax = $this->_request->getParam('fax');
                $_company->email = $this->_request->getParam('email');
                $_company->website = $this->_request->getParam('website');
                $_company->legalForm = $this->_request->getParam('legalForm');
                //时间处理.注所有时间处理均在父类Action 的方法DateTo 中实现
                $ddtime = $this->_request->getParam("incorporationDate");
                if (!empty($ddtime)) {
                    $date3 = date("Y-m-d\TH:i:s", strtotime($ddtime));
                } else {
                    $date3 = date("Y-m-d\TH:i:s", time());
                }
                $_company->incorporationDate = $date3;
                $_company->lastUpdate = $date3 = date("Y-m-d\TH:i:s", time());
                if ($_company->regdCountryCode == "CN") {
                    $_company->businessLicenseNo = $this->_request->getParam('businessLicenseNo');
                    $_company->taxNo = $this->_request->getParam('taxNo');
                    $_company->taxpayerName = $this->_request->getParam('taxpayerName');
                    $_company->customsCode = $this->_request->getParam('customsCode');//企业海关编码 首字母小写
                } else {
                    $_company->businessLicenseNo = "";
                    $_company->taxNo = "";
                    $_company->taxpayerName = "";
                    $_company->customsCode = "";//企业海关编码 首字母小写
                }
                $_company->attachmentList = $_attachList;//附件上传

                $_requestOb = $this->_requestObject;

                $resultObject = $this->json->editAccountApi($_requestOb, $_company);
                $existData = $this->objectToArray(json_decode($resultObject));
                $existDatt = $existData['result'];
                $this->view->e = $existDatt;

                $this->redirect("/company");
            } catch (Exception $e) {
                Shop_Browser::redirect($e->getMessage());
            }
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/company/edit.phtml");
            echo $content;
            exit;
        }
    }

    public function aclistAction()
    {
        $f1 = new Seed_Filter_Alnum();
        $mod = $f1->filter($this->_request->getParam('mod'));
        if (empty($mod)) {
            $mod = "index";
        }

        $_PStatus = strval($this->_request->getParam('status'));
        if (empty($_PStatus)) {
            $_PStatus = null;
        }

        $_querySorts = $this->_request->getParam('querySorts');
        if (empty($_querySorts)) {
            $_querySorts = null;
        }

        $_keyword = $this->_request->getParam('keyword');
        if (empty($_keyword)) {
            $_keyword = null;
        }

        $page = intval($this->_request->getParam('page'));
        if ($page < 1) $page = 1;
        $_limit = 10;
        $_skip = $_limit * ($page - 1);


        // 设置请求数据
        $_requestOb = $this->_requestObject;

        // 请求Hessian服务端方法bankAcctID
        $_accountID = $this->view->accountID;
        $_requestOb = $this->_requestObject;
        $existData = $this->json->listAccountPublicContactApi($_requestOb, $_accountID, $_PStatus, $_querySorts, $_keyword, $_skip, $_limit);
        $accountDD = json_decode($existData)->result;
        $accountList = $this->objectToArray($accountDD);
        $this->view->e = $accountList;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/company/aclist.phtml");
            echo $content;
            exit;
        }

    }

    // 企业认证 - view
    public function authviewAction() {
        // $accountID = $this->_request->getParam('accountID');
        $accountID = $this->view->accountID;
        $_requestOb = $this->_requestObject;
        $resultObject = $this->json->getAccountApi($_requestOb, $accountID);

        $account = json_decode($resultObject)->result;
        $this->view->account = json_decode($resultObject)->result;
        $entRealAuthStatus = $account->entRealAuthStatus;


        if (defined('SEED_WWW_TPL')) {
            if ($entRealAuthStatus == 0 || $entRealAuthStatus == -1) {
                $content = $this->view->render(SEED_WWW_TPL . "/company/authDoAuth.phtml");
                echo $content;
                exit;
            } else if ($entRealAuthStatus == 1 || $entRealAuthStatus == -2) {
                $content = $this->view->render(SEED_WWW_TPL . "/company/authDoAuthPay.phtml");
                echo $content;
                exit;
            } else if ($entRealAuthStatus ==3 || $entRealAuthStatus == -3) {
                $content = $this->view->render(SEED_WWW_TPL . "/company/authDoAuthConfirm.phtml");
                echo $content;
                exit;
            } else if ($entRealAuthStatus ==2) {
                $content = $this->view->render(SEED_WWW_TPL . "/company/authView.phtml");
                echo $content;
                exit;
            } else {
                $content = $this->view->render(SEED_WWW_TPL . "/company/authErrView.phtml");
                echo $content;
                exit;
            }
        }
    }

    // 企业认证 - 基本信息
    public function doauthAction() {
        $msg = 0;
        if ($this->_request->isPost()) {
            // 实例account对象
            $Account = new Kyapi_Model_account();
            $Account->accountName = $this->_request->getParam('auth_name');
            $Account->businessLicenseNo = $this->_request->getParam('auth_code');
            $Account->legalPersonName = $this->_request->getParam('auth_mg_name');
            $Account->legalPersonIdentityNo = $this->_request->getParam('auth_mg_id');

            $_requestOb = $this->_requestObject;
            $resultObject = $this->json->doEntRealNameAuth($_requestOb, $Account);
            // 取回接口请求状态
            $apiStatus = json_decode($resultObject)->status;
            if ($apiStatus == 1) {
                // 取回企业认证状态
                if (json_decode($resultObject)->result->errCode == 0) {
                    $msg = 0;
                } else if (json_decode($resultObject)->result->data->entRealAuthStatus == -4) {
                    $msg = -4;
                } else {
                    $msg = json_decode($resultObject)->result->msg;
                }

            } else {
                // 接口请求错误的情况下, 将接口错误返回给页面
                $msg = json_decode($resultObject)->errorCode;
            }
        }
        echo $msg;
        exit;
    }

    // 企业认证 - 银行信息
    public function doauthpayAction() {
        $msg = 0;
        if ($this->_request->isPost()) {
            $acctName = $this->_request->getParam('acctName');
            $auth_acctNo = $this->_request->getParam('auth_acctNo');
            $bankName = $this->_request->getParam('bankName');
            $provinceName = $this->_request->getParam('provinceName');
            $cityName = $this->_request->getParam('cityName');
            $subbranchName = $this->_request->getParam('subbranchName');

            $_requestOb = $this->_requestObject;
            $resultObject = $this->json->doEntRealNameToPay($_requestOb, $acctName, $auth_acctNo, $bankName, $provinceName, $cityName, $subbranchName);
            // 取回接口请求状态
            $apiStatus = json_decode($resultObject)->status;
            if ($apiStatus == 1) {
                // 取回企业认证状态
                if (json_decode($resultObject)->result->errCode == 0) {
                    $msg = 0;
                } else if (json_decode($resultObject)->result->data->entRealAuthStatus == -4) {
                    $msg = -4;
                } else {
                    $msg = json_decode($resultObject)->result->msg;
                }

            } else {
                // 接口请求错误的情况下, 将接口错误返回给页面
                $msg = $this->view->translate(trim(json_decode($resultObject)->errorCode));
            }
        }
        echo $msg;
        exit;
    }

    // 企业认证 - 银行打款金额验证
    public function authconfirmAction() {
        $msg = 0;
        if ($this->_request->isPost()) {
            //获取account数据集合
            $verifyAmount = $this->_request->getParam('authVerifyAmount');

            $_requestOb = $this->_requestObject;
            $resultObject = $this->json->doEntRealNameVerify($_requestOb, $verifyAmount);

            // 取回接口请求状态
            $apiStatus = json_decode($resultObject)->status;
            if ($apiStatus == 1) {
                // 取回企业认证状态
                if (json_decode($resultObject)->result->errCode == 0) {
                    $msg = 0;
                } else if (json_decode($resultObject)->result->data->entRealAuthStatus == -4) {
                    $msg = -4;
                } else {
                    $msg = json_decode($resultObject)->result->msg;
                }

            } else {
                // 接口请求错误的情况下, 将接口错误返回给页面
                $msg = $this->view->translate(trim(json_decode($resultObject)->errorCode));
            }
        }
        echo $msg;
        exit;
    }

    /* 数据字典模糊查询 */
    public function fuzzyAction() {
        $msg = 0;
        if ($this->_request->isPost()) {
            $dataDictCode = $this->_request->getParam('dataDictCode');
            $keyword = $this->_request->getParam('keyword');
            $valuePCode = $this->_request->getParam('valuePCode');

            $existData = $this->json->dataDictFuzzyQuery($dataDictCode, $keyword, $valuePCode, 500);
            $res = json_decode($existData)->result;
            $msg = $res;
            // var_dump($existData);
            // exit;

            // foreach ($res as $value) {
            //     echo 'nameText:'.$value->nameText.' valueCode:'.$value->valueCode.' | ';
            // }
            // exit;
        }
        echo json_encode($msg);
        // echo $msg;
        exit;
    }

    public function finddatadictlistAction() {
        $msg = 0;
        if ($this->_request->isPost()) {
            $dataDictCode = $this->_request->getParam('dataDictCode');

            $_requestOb = $this->_requestObject;
            $existData = $this->json->findDataDictListApi($_requestOb, $dataDictCode);
            $res = json_decode($existData)->result;
            $msg = $res;
            var_dump($res);
            exit;
        }
        echo json_encode($msg);
        exit;
    }
}
