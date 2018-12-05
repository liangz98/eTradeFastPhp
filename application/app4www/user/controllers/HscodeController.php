<?php
class HscodeController extends Kyapi_Controller_Action
{
    public function preDispatch()
    {
        $this->view->cur_pos = 'info';

        if(empty($this->view->userID)){
            //请先登录系统
            Mobile_Browser::redirect($this->view->translate('tip_login_please'),$this->view->seed_Setting['user_app_server']."/login");
        }
        $this->view->cur_pos = $this->_request->getParam('controller');

        // 更新session时间
        $this->updateRedisExpire();
    }
    public function listAction()
    {
         $keyword=$this->_request->getParam('q');

        //获取商品列表信息
        $_resultData =$this->json->listHSCodeApi($this->_requestObject,null,null, $keyword, 0, 0);
            $userKY=json_decode($_resultData);
            $existData = $userKY->result;

            $existDatt = $this->objectToArray($existData);
        $msg=array();
        foreach($existDatt as $k=>$v){
            $msg[$k]['name']=$v['codeName'];
            $msg[$k]['text']=$v['codeName'];
            $msg[$k]['id']=$v['hscode'];
            $msg[$k]['taxRate']=$v['taxRate'];
            $msg[$k]['rebateRate']=$v['rebateRate'];
            $msg[$k]['pricingUnit']=$v['pricingUnit'];
        }
            echo json_encode($msg);
        exit;

    }

    public function getAction()
    {
        $msg="";
        $keyword=$this->_request->getParam('q');
        //获取商品列表信息
        $_resultData =$this->json->listHSCodeApi($this->_requestObject,null,null, $keyword, 0, 0);
        $userKY=json_decode($_resultData);
        $existData = $userKY->result;

        $existDatt = $this->objectToArray($existData);

        foreach($existDatt as $k=>$v){
            $msg=$v['codeName'];
        }
        echo json_encode($msg);
        exit;

    }
}
