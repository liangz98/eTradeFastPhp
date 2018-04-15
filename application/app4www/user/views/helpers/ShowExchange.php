<?php
 class Zend_View_Helper_ShowExchange extends Shop_View_Helper
{

	//汇率查询
	function ShowExchange($_requestObject,$_baseCrn,$_contraCrn,$_bizType,$_bizID,$KyUrl){
        //$_bizType,$_bizID,$_baseCrn（基础货币）,$_contraCrn（对应货币）

        $this->json = new Kyapi_Controller_Json($KyUrl);
        $resultObject= $this->json->getExchangeRateApi($_requestObject,$_bizType,$_bizID,$_baseCrn,$_contraCrn);
        $existData = json_decode($resultObject);
        $EXdata=$existData->result;;
        return $EXdata;

	}
}