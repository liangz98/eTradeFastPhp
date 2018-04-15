<?php
//保存推荐人user_id
$shareuserid = intval($this->getRequest()->getParam('shareuserid'));
if($shareuserid > 0){
     $expiretime = time() + $this->view->seed_Setting['cookie_expire'];
     Seed_Cookie::setCookie('shareuserid', $shareuserid, $expiretime);
}

//保存推荐码
$sharetoken = strip_tags(trim($this->getRequest()->getParam('sharetoken')));
if($sharetoken){
     $expiretime = time() + $this->view->seed_Setting['cookie_expire'];
     Seed_Cookie::setCookie('sharetoken', $sharetoken, $expiretime);
}