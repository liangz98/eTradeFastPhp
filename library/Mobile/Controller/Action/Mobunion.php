<?php
/**
 * 分销商用户中心，手机版
 */
class Mobile_Controller_Action_Mobunion extends Zend_Controller_Action
{
    function init ()
    {
        if (file_exists(SEED_LICENSE_ROOT . "/init.php")) {
            require (SEED_LICENSE_ROOT . "/init.php");
        } else {
            exit("License File Not Found!");
        }
        $this->checkSystem();
        $this->initView();
        $this->initSetting();
        $this->initUser();
        $this->view->seed_BaseUrl = $this->_request->getBaseUrl();
		$this->view->cur_pos = $this->_request->getParam('controller');
    }
    /**
     * 检查系统完整性
     * @throws Exception
     */
    protected function checkSystem ()
    {
        if (! defined('CURRENT_MODULE_NAME') || ! defined('CURRENT_MODULE_TYPE'))
            throw new Exception(
            "CURRENT_MODULE_NAME or CURRENT_MODULE_TYPE not defined");
    }
    
    
    protected function initSetting ()
    {
        // 获取系统设置
        $fileM = new Seed_Model_Cache2File();
        $mod_name = CURRENT_MODULE_NAME . "_" . CURRENT_MODULE_TYPE;
        if (defined('SEED_HOST_NAME')) {
            $seed_host_name = str_replace(".", "_", SEED_HOST_NAME);
            $cachefile = $mod_name . "_" . strtolower($seed_host_name) .
             "_setting";
        } else {
            $cachefile = $mod_name . "_setting";
        }
        $setting = $fileM->get($cachefile);
        if (isset($setting['mobile_send_ecode']) &&
         isset($setting['mobile_send_username']) &&
         isset($setting['mobile_send_password'])) {
            define('MOBILE_SEND_ECODE', $setting['mobile_send_ecode']);
            define('MOBILE_SEND_USERNAME', $setting['mobile_send_username']);
            define('MOBILE_SEND_PASSWORD', $setting['mobile_send_password']);
        }
        if (! isset($setting['website_domain']))
            $setting['website_domain'] = $_SERVER['HTTP_HOST'];
        if (! isset($setting['upload_app_server']))
            $setting['upload_app_server'] = "http://" . $_SERVER['HTTP_HOST'] .
             "/uploadapi";
        if (! isset($setting['upload_view_server']))
            $setting['upload_view_server'] = "http://" . $_SERVER['HTTP_HOST'] .
             "/upload_files";
        if (! isset($setting['upload_return_server']))
            $setting['upload_return_server'] = "";
        if (! isset($setting['www_app_server']))
            $setting['www_app_server'] = "http://" . $_SERVER['HTTP_HOST'];
        if (! isset($setting['v_app_server']))
            $setting['v_app_server'] = "http://" . $_SERVER['HTTP_HOST'];
        if (! isset($setting['union_app_server']))
            $setting['union_app_server'] = "http://" . $_SERVER['HTTP_HOST'];
        if (! isset($setting['wechat_app_server']))
            $setting['wechat_app_server'] = "http://" . $_SERVER['HTTP_HOST'];
        if (! isset($setting['vuser_app_server']))
            $setting['vuser_app_server'] = "http://" . $_SERVER['HTTP_HOST'] .
             "/vuser";
        if (! isset($setting['vmall_app_server']))
            $setting['vmall_app_server'] = "http://" . $_SERVER['HTTP_HOST'] .
             "/vmall";
        if (! isset($setting['vhome_app_server']))
            $setting['vhome_app_server'] = "http://" . $_SERVER['HTTP_HOST'] .
             "/vhome";
        if (! isset($setting['vservice_app_server']))
            $setting['vservice_app_server'] = "http://" . $_SERVER['HTTP_HOST'] .
             "/vservice";
        if (! isset($setting['vmarketing_app_server']))
            $setting['vmarketing_app_server'] = "http://" . $_SERVER['HTTP_HOST'] .
             "/vmarketing";
        if (! isset($setting['vplug_app_server']))
            $setting['vplug_app_server'] = "http://" . $_SERVER['HTTP_HOST'] .
             "/vplug";
        if (! isset($setting['mobunion_app_server']))
            $setting['mobunion_app_server'] = "http://" . $_SERVER['HTTP_HOST'] .
             "/mobunion";
        if (! isset($setting['static_app_server']))
            $setting['static_app_server'] = "http://" . $_SERVER['HTTP_HOST'] .
             "/static";
        $this->view->seed_Setting = $setting;
        $this->view->controllerName = $this->getRequest()->getControllerName();
        $this->view->actionName = $this->getRequest()->getActionName();
        $this->view->seed_BaseUrl = $this->getRequest()->getBaseUrl();
    }
    /**
     * 初始化用户信息
     * @throws Exception
     */
    protected function initUser ()
    {
        $token = Seed_Cookie::getCookie('seed_ShopToken');
        $user_id = intval(Seed_Cookie::getCookie('seed_ShopUserId'));
        $my = Seed_Token::decode($token);
        if (is_array($my) && isset($my['user_id']) && $user_id == $my['user_id']) {
            $userM = new Seed_Model_User('system');
            $this->view->seed_User = $user = $userM->fetchRow(
            array('user_id' => $my['user_id'], 'token' => $my['token'], 
            'is_actived' => '1'));
            if ($user) {
                //设置cookie
            //$expiretime = time() + $this->view->seed_Setting['cookie_expire'];
            //Seed_Cookie::setCookie('seed_ShopToken',$token,$expiretime,$this->view->seed_Setting['cookie_path'],$this->view->seed_Setting['cookie_host']);
            //Seed_Cookie::setCookie('seed_ShopUserId',$user['user_id'],$expiretime,$this->view->seed_Setting['cookie_path'],$this->view->seed_Setting['cookie_host']);
            //Seed_Cookie::setCookie('seed_ShopUserName',$user['user_name'],$expiretime,$this->view->seed_Setting['cookie_path'],$this->view->seed_Setting['cookie_host']);
            } else {
                if ($_COOKIE) {
                    foreach ($_COOKIE as $cookieName => $cookieValue) {
                        $cookie_name = Seed_Cookie::parseCookieName($cookieName);
                        if ($cookie_name == 'shareuserid') {
                            continue;
                        }
                        Seed_Cookie::delete($cookie_name); //清除cookie
                    }
                }
            }
        }
        $excp_arr = array('login', 'forget', 'register', 'phonevcode');
        ///验证登录
        if ($this->view->seed_User['user_id'] < 1 && ! in_array($this->getRequest()->getControllerName(), $excp_arr)) {
            Mobile_Tips::redirect($this->getRequest()->getBaseUrl() . "/login");
        } elseif ($this->view->seed_User['user_id'] > 0) {
            ///验证信息
            //$wechatUserM = new Wechat_Model_User('wechat');
            //$wechatUser = $wechatUserM->fetchRow(array('user_id'=>$user['user_id']));
            //if($wechatUser['is_unfollow']=='1'){
            //	Mobile_Tips::redirect($this->getRequest()->getBaseUrl()."/register/follow");
            //	exit;
            //}
            $shopM = new Shop_Model_Shop('shop');
            $shopRoleM = new Shop_Model_ShopRole('shop');
            
            $shop = $shopM->fetchRow(array('user_id' => $this->view->seed_User['user_id']));
            
            if ($shop['shop_id'] < 1) {
                Mobile_Tips::show('没有找到与您相应数据');
            }

            $shop['shop_roles'] = $shopRoleM->fetchRows(null,array('shop_id'=>$shop['shop_id']));

            if ($shop['is_m_actived'] == '0') {
                $tips = (isset($this->view->seed_Setting['shop_auth_tips'])) ? trim(
                $this->view->seed_Setting['shop_auth_tips']) : '店铺正在审核中！请耐心等待！';
                Mobile_Tips::show($tips);
            }
            
            $this->view->shop = $shop;
        }
        $inviuid = intval($this->getRequest()->getParam('inviuid'));
        if ($inviuid > 0) {
            $expiretime = time() + $this->view->seed_Setting['cookie_expire'];
            Seed_Cookie::setCookie('inviuid', $inviuid, $expiretime, 
            $this->view->seed_Setting['cookie_path'], 
            $this->view->seed_Setting['cookie_host']);
        }
    }
    /**
     * 获取请求的URI
     * @return Ambigous <NULL, unknown>
     */
    protected function getRequestUri ()
    {
        $requestUri = null;
        if (isset($_SERVER['HTTP_X_REWRITE_URL']) &&
         ! empty($_SERVER['HTTP_X_REWRITE_URL'])) {
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } else {
            $requestUri = $_SERVER["REQUEST_URI"];
        }
        return $requestUri;
    }
}
?>