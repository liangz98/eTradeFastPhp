<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/12/26
 * Time: 16:12
 */
class TestController extends Kyapi_Controller_Action
{
    function sessionAction(){
        //验证登陆名是否存在
        echo "session info:";
        echo "<pre>";
        print_r($_SESSION['rev_session']);
        exit;
    }

}