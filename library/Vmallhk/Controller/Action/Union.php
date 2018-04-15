<?php
class Vmallhk_Controller_Action_Union extends Vmallhk_Controller_Action
{	
	function init()
	{
		if(file_exists(SEED_LICENSE_ROOT."/init.php")){
			require(SEED_LICENSE_ROOT."/init.php");
		}else{
			exit("License File Not Found!");
		}
		
		$this->checkSystem();
		parent::initView();
		parent::initSetting();
		parent::initUser();
		
		$this->view->seed_BaseUrl = $this->_request->getBaseUrl();
		$this->view->cur_pos = $this->_request->getParam('controller');
		$this->view->addHelperPath(SEED_LIB_ROOT . '/Vmallhk/View/Helper');
		parent::initHelperSetting();
        parent::initCommon();
	}
	
	protected function checkSystem()
	{
		if(!defined('CURRENT_MODULE_NAME') || !defined('CURRENT_MODULE_TYPE'))throw new Exception("CURRENT_MODULE_NAME or CURRENT_MODULE_TYPE not defined");
		///验证域名
		if(!defined('HOST_NAME')){
			Mobile_Browser::error('访问路径错误！');
		}
		
		$shop_id=0;
		preg_match('/(shop|mall)(\d+)\./isU',HOST_NAME,$arr);
		if(isset($arr[2]) && $arr[2]>0)$shop_id=$arr[2];
		
    	$shopM = new Shop_Model_Shop('shop');
		$shop = $shopM->fetchRow(array('shop_id'=>$shop_id));
		if($shop['shop_id']<1){
			Mobile_Browser::error('没有找到店铺！');
		}
		if($shop['is_m_actived']!='1'){
			Mobile_Browser::error('店铺正在审核中！');
		}
		if($shop['mobile_host_name']!=HOST_NAME)Mobile_Browser::redirect("正在跳转","http://".$shop['mobile_host_name']);
		$shopM->updateRow(array('view_cnt' => ($shop['view_cnt'] + 1)),  array('shop_id' => $shop['shop_id']));
		$this->view->shop = $shop;
                //我访问过的店铺记录 by jacent  2015-07-03
                $accessRecordM = new Shop_Model_UrlAccessRecord('shop');
                $user_id = intval(Seed_Cookie::getCookie('seed_UserId'));
                if(empty($user_id)){
                    $user_id = 0;
                }
                $accessRecordM->updateUrl($user_id, $shop['mobile_host_name']);
	}
}
?>