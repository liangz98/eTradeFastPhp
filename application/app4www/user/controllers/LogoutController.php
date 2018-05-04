<?php

class LogoutController extends Kyapi_Controller_Action {
    function indexAction() {
        //销毁绑定sessionID的key
        $config = array();
        $config['server'] = $this->view->seed_Setting['KyUrlRedis'];
        $config['port'] = $this->view->seed_Setting['KyUrlRedisPort'];
        $redis = new Kyapi_Model_redisInit();
        $redis->connect($config);
        $redis->delete('PHPREDIS_ACTIVE_USERS:' . $this->view->userID);
        $redis->delete('PHPREDIS_ACTIVE_SESSION:' . session_id());

        //销毁session
        session_unset(session_id());
        session_destroy();

        //设置cookie
        $expiretime = -1;
        Seed_Cookie::setCookie('PHPSESSID', '', $expiretime, $this->view->seed_Setting['cookie_path'], $this->view->seed_Setting['cookie_host']);
        $ajax = trim($this->_request->getParam('ajax'));


        $this->redirect("../");
        exit;
    }
}
