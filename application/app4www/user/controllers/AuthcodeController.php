<?php
class AuthcodeController extends Kyapi_Controller_Action
{

	function indexAction(){
		$authCode=$_SERVER['QUERY_STRING'];
		//$authCode=base64_decode($_SERVER['QUERY_STRING']);
		//$authCode=$this->_request->getParam('authCode');;
		if(empty($authCode)){
			Shop_Browser::redirect($this->view->translate('tip_auth_no'),"/");
			exit;
		}

		$_requestObject = new Kyapi_Model_requestObject();
		$_requestObject->client = "192.168.5.100";
		$_resultData= $this->json->getAccountViewByAuthCodeApi($authCode);
		$existDatt=json_decode($_resultData);
		$existData=$this->objectToArray($existDatt);

		if ($existData['status']!= 1) {
			if($existData['errorCode']=='INVALID'){
			    //用户名已存在
				Shop_Browser::redirect($this->view->translate('tip_register_ready'),"/");
			}else{
			    //authcode 验证失败
			Shop_Browser::redirect($this->view->translate('tip_auth_check').$existData->errorCode,"/");
			}
			exit;
		}
        $this->view->account=$existData['result'];

		if ($this->_request->isPost()) {
			$_requestObject = new Kyapi_Model_requestObject();
			$_requestObject->client = "192.168.5.100";
			$_requestObject->sessionID = "temp";
			$_requestObject->timeZone = "GMT +8:00";
			$_requestObject->accountID=$existData['result']['accountID'];
			$_requestObject->userID=$existData['result']['contactID'];

			/*获取语言*/
			$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4); //只取前4位，这样只判断最优先的语言。如果取前5位，可能出现en,zh的情况，影响判断。
			if (preg_match("/zh-c/i", $lang))
			{$_requestObject->lang="zh_CN";}
			else {
				$_requestObject->lang="en_US";}


			/* 注册会员模块 */
			$_account =  new Kyapi_Model_account();
			$_account ->accountName =  $this->_request->getParam('accountName');
			$_account->regdAddress = $this->_request->getParam('regdAddress');
			$_account->regdCountryCode = $this->_request->getParam('countryCode');
			$_account->email = $this->_request->getParam('email');
			$_contact = new Kyapi_Model_contact();
			$_contact->name = $this->_request->getParam('name');
			$_contact->ecommloginname = $this->_request->getParam('ecommloginname');
			$_contact->ecommpasswsd = $this->_request->getParam('ecommpasswsd');
			$_contact->mobilePhone = $this->_request->getParam('mobilePhone');
			$_contact->account =$_account;


			$_resultData = $this->json->registerByAuthCodeApi($_requestObject, $_contact, $authCode);
			$resultObject = json_decode($_resultData);
			if ($resultObject->status != 1) {
			    //注册失败
				Seed_Browser::redirect($this->view->translate('tip_register_fail') . $resultObject->error, $this->view->seed_BaseUrl . "/login");
			} else {
			    //注册成功
				Shop_Browser::redirect($this->view->translate('tip_register_success'), $this->view->seed_BaseUrl . "/login");
			}
		}
		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/authcode/index.phtml");
			echo $content;
			exit;
		}

	}

    function contactAction(){
        $this->view->resultMsg = $this->_request->getParam('resultMsg');

        echo $this->_request->getParam('resultMsg');

        $authCode=$_SERVER['QUERY_STRING'];
        //$authCode=base64_decode($_SERVER['QUERY_STRING']);
        //$authCode=$this->_request->getParam('authCode');;
        if(empty($authCode)){
            Shop_Browser::redirect($this->view->translate('tip_auth_no'),"/");
            exit;
        }

        $_requestObject = new Kyapi_Model_requestObject();
        $_requestObject->client = "192.168.5.100";
        $_resultData= $this->json->getContactViewByAuthCodeApi($authCode);
        $existDatt=json_decode($_resultData);
        $existData=$this->objectToArray($existDatt);

        if ($existData['status']!= 1) {
            if($existData['errorCode']=='INVALID'){
                //用户名已存在
                Shop_Browser::redirect($this->view->translate('tip_register_ready'),"/");
            }else{
                //authcode 验证失败
                Shop_Browser::redirect($this->view->translate('tip_auth_check').$existData->errorCode,"/");
            }
            exit;
        }
        $this->view->account=$existData['result'];

        if ($this->_request->isPost()) {
            $_requestObject = new Kyapi_Model_requestObject();
            $_requestObject->client = "192.168.5.100";
            $_requestObject->sessionID = "temp";
            $_requestObject->timeZone = "GMT +8:00";
            $_requestObject->accountID=$existData['result']['accountID'];
            $_requestObject->userID=$existData['result']['contactID'];

            /*获取语言*/
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4); //只取前4位，这样只判断最优先的语言。如果取前5位，可能出现en,zh的情况，影响判断。
            if (preg_match("/zh-c/i", $lang)) {
                $_requestObject->lang="zh_CN";
            }
            else {
                $_requestObject->lang="en_US";
            }


            /* 注册会员模块 */
           $_account =  new Kyapi_Model_account();
            $_account ->accountName =  $this->view->account['account']['accountName'];
            $_account->regdAddress = $this->view->account['account']['regdAddress'];
            $_account->regdCountryCode = $this->view->account['account']['regdCountryCode'];
            $_account->email = $this->view->account['account']['email'];

            $_contact = new Kyapi_Model_contact();
            $_contact->name = $this->view->account['name'];
            $_contact->ecommloginname = $this->_request->getParam('ecommloginname');
            $_contact->ecommpasswsd = $this->_request->getParam('ecommpasswsd');
            $_contact->mobilePhone = $this->_request->getParam('mobilePhone');

            $_contact->accountID = $this->view->account['account']['accountID'];
            $_contact->accountType = $this->view->account['account']['accountType'];
            $_contact->accountName = $this->view->account['account']['accountName'];
            $_contact->regdAddress = $this->view->account['account']['regdAddress'];
            $_contact->regdCountryCode = $this->view->account['account']['regdCountryCode'];
            $_contact->email = $this->view->account['account']['email'];

            $_resultData = $this->json->contactRegisterByAuthCodeApi($_requestObject, $_contact, $authCode);
            $resultObject = json_decode($_resultData);
            if ($resultObject->status != 1) {
                $this->view->resultMsg = $this->view->translate('tip_register_fail') . $resultObject->error;
            } else {
                //注册成功
                Shop_Browser::redirect($this->view->translate('tip_register_success'), $this->view->seed_BaseUrl . "/login");
            }
        }
        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/authcode/contact.phtml");
            echo $content;
            exit;
        }

    }


}
