<?php
/**
 * 用户意图
 */
class Wechat_Advance_Intent{
    public static function intent($intent){
          if(is_string($intent) && !empty($intent)){
                $method = ucfirst($intent);
                $class_name = 'Wechat_Intent_'.$method;
                $w_controller = Wechat_Base_Controller::getInstance();
                if($w_controller->isRunnable($class_name, $method)){
                    $classObj = new $class_name();
                    $classObj->$method();
                }
          }
    }
}