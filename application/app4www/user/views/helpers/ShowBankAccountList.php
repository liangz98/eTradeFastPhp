<?php

class Zend_View_Helper_ShowBankAccountList extends Shop_View_Helper {
    
    // 封装公司银行列表
    function ShowBankAccountList() {
        $ip = $_SERVER['REMOTE_ADDR'];
        // return $ip; exit;
        
        // 获取系统缓存
        $cacheM = new Seed_Model_Cache2File();
        $mod_name = CURRENT_MODULE_NAME . "_" . CURRENT_MODULE_TYPE;
        if (defined('SEED_HOST_NAME')) {
            $seed_host_name = str_replace(".", "_", SEED_HOST_NAME);
            $cachefile = $mod_name . "_" . strtolower($seed_host_name) . "_setting";
        } else {
            $cachefile = $mod_name . "_setting";
        }
        $setting = $cacheM->get($cachefile);
        
        
        //requestObject 入参条件
        $_requestObject = new Kyapi_Model_requestObject();
        if (!empty($_SESSION)) {
            $_requestObject->sessionID = session_id();
        } else {
            $_requestObject->sessionID = "sessionNull";
        }
        $_requestObject->userID = $userID = $this->view->userID;
        $_requestObject->accountID = $accountID = $this->view->accountID;
        if (empty($userlangCode))
            $userlangCode = "zh_CN";
        $_requestObject->lang = $userlangCode;
        $_requestObject->client = $_SERVER['REMOTE_ADDR'];
        $_requestObject->timeZone = "GMT +8:00";
        
        $_json = new Kyapi_Controller_Json($setting['KyUrl']);
        
        $resultObject = $_json->listBankAccountApi($_requestObject, null, null, null, null, null);
        
        $bankOptions = "";
        $bankAccountList = json_decode($resultObject)->result;
        foreach ($bankAccountList as $bankAccount) {
            $bankOptions .= '<option value="' . $bankAccount->bankAcctID . '">' . $bankAccount->bankAcctName . ' | ' . $bankAccount->bankAcctNo . ' | ' . $bankAccount->bankName . '</option>';
        }
        return $bankOptions;
    }
}