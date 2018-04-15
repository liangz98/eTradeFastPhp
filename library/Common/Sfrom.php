<?php
$sfrom = $this->getRequest()->getParam('sfrom');
if($sfrom == 'bm'){//来自微信底部菜单
	Seed_Cookie::delete('seed_VmallLastUrl');//从菜单过来则清除
    if($this->view->seed_User['user_id'] < 1){//没有登陆
        Seed_Cookie::setCookie('redirect_url', "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        header("Location:"."http://".$_SERVER['HTTP_HOST'].'/vuser/login/wechat');
        exit;
    }
}

if($this->getRequest()->getParam('from')){
     Seed_Cookie::delete('seed_VmallLastUrl');//朋友圈过来则清除
}