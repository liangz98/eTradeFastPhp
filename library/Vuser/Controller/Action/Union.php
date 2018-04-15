<?php
class Vuser_Controller_Action_Union extends Vuser_Controller_Action
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
		$this->view->addHelperPath(SEED_LIB_ROOT . '/Vmall/View/Helper');
		parent::initHelperSetting();
        parent::initCommon();
	}
	
	protected function checkSystem()
	{
		if(!defined('CURRENT_MODULE_NAME') || !defined('CURRENT_MODULE_TYPE'))throw new Exception("CURRENT_MODULE_NAME or CURRENT_MODULE_TYPE not defined");
		///验证域名
		if(!defined('HOST_NAME')){
			Mobile_Browser::error('访问路径错误，返回主页！');
		}
		
		$shop_id=0;
		preg_match('/(shop|mall)(\d+)\./isU',HOST_NAME,$arr);
		if(isset($arr[2]) && $arr[2]>0)$shop_id=$arr[2];
		
    	$shopM = new Shop_Model_Shop('shop');
		$shop = $shopM->fetchRow(array('is_m_actived'=>'1','shop_id'=>$shop_id));
		if($shop['shop_id']<1){
			Mobile_Browser::error('没有找到相应店铺，返回主页！');
		}
		if($shop['mobile_host_name']!=HOST_NAME)Mobile_Browser::redirect("正在跳转","http://".$shop['mobile_host_name']);
		$shopM->updateRow(array('view_cnt' => ($shop['view_cnt'] + 1)),  array('shop_id' => $shop['shop_id']));
		$this->view->shop = $shop;
	}
}
?>