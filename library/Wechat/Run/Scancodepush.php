<?php

class Wechat_Run_Scancodepush{
    protected $_wechat_controller = null;

    function  __construct() {
        if(null === $this->_wechat_controller){
            $this->_wechat_controller = Wechat_Base_Controller::getInstance();
        }
    }
    
    function run(){
      
      
        
        exit;
    }
    
}