<?php
class SystemController extends Kyapi_Controller_Action
{
    public function indexAction()
    {


        $_bizType =$this->_request->getParam('bizType');
        if(empty( $_bizType)){ $_bizType =null;}

        $_bizID =$this->_request->getParam('bizID');
        if(empty( $_bizID)){ $_bizID =null;}

        $_baseCrn =$this->_request->getParam('baseCrn');
        if(empty( $_baseCrn)){ $_baseCrn =null;}

        $_contraCrn =$this->_request->getParam('contraCrn');
        if(empty( $_contraCrn)){ $_contraCrn =null;}


        $_requestOb=$this->_requestObject;
        $resultObject= $this->json->getExchangeRateApi($_requestOb,$_bizType,$_bizID,$_baseCrn,$_contraCrn);
        $existData = $resultObject->result;

        echo $existData;
        exit;
    }

    public function orderlistAction()
    {
     //本方法主要用于订单商品ITERM 查询使用
        $_bizType =$this->_request->getParam('bizType');
        if(empty( $_bizType)){ $_bizType ='OD';}

        $_bizID =$this->_request->getParam('bizID');
        if(empty( $_bizID)){ $_bizID =null;}

        $_baseCrn =$this->_request->getParam('baseCrn');
        if(empty( $_baseCrn)){ $_baseCrn ='USD';}

        $_contraCrn =$this->_request->getParam('contraCrn');
        if(empty( $_contraCrn)){ $_contraCrn ='CNY';}


        $_requestOb=$this->_requestObject;
        $resultObject= $this->json->getExchangeRateApi($_requestOb,$_bizType,$_bizID,$_baseCrn,$_contraCrn);
        //$existData = $resultObject->result;

        echo $resultObject;
        exit;
    }

    public function topviewAction()
    {
        //本方法主要用于订单顶部汇率查询 查询使用
        $_bizType =$this->_request->getParam('bizType');
        if(empty( $_bizType)){ $_bizType ='OD';}

        $_bizID =$this->_request->getParam('bizID');


        $_baseCrn =$this->_request->getParam('baseCrn');
        if(empty( $_baseCrn)){ $_baseCrn =null;}

        $_contraCrn =$this->_request->getParam('contraCrn');
        if(empty( $_contraCrn)){ $_contraCrn =null;}

        if(empty( $_bizID)){
            echo 'bizID not null';
            exit;
        }else{
        $_requestOb=$this->_requestObject;
     //   $resultObject= $this->json->listExchangeRateApi($_requestOb,'OD','53917A63-20D2-FFC5-80B6-D8A58AD4DC38',null,null);
        $resultObject= $this->json->listExchangeRateApi($_requestOb,$_bizType,$_bizID,$_baseCrn,$_contraCrn);
          //  $exdata=json_decode($resultObject)
        echo $resultObject;
        exit;
        }
    }

    public function langCodeAction() {
        $langCode = $this->_request->getParam('langCode');
        $langCode = trim($langCode);

        if ($langCode) {
            $_SESSION['rev_session']['contactPreference']['langCode'] = $langCode;
            echo json_encode($_SESSION['rev_session']['contactPreference']['langCode']);
            exit;
        } else {
            return false;
        }
    }

    function checkloginpwdAction(){


           $password = $this->_request->getParam('passpwd');
           $_password = trim($password);
           $_requestOb=$this->_requestObject;
           $resultObject= $this->json->checkPasswordApi($_requestOb, $_password);
           echo $resultObject;
           exit;
    }
}
