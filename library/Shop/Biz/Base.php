<?php
class Shop_Biz_Base
{
	/**
	 * 获取参数设置
	 * @throws Exception
	 * @return Ambigous <mixed, NULL>
	 */
	public static function getSetting(){
		if(!defined('CURRENT_MODULE_NAME'))throw new Exception("CURRENT_MODULE_NAME not defined");
		$mod_name = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;
		//获取访问控制列表ACL
		$fileM = new Seed_Model_Cache2File();
		//获取系统设置
		$setting = $fileM->get($mod_name."_setting");
		$seed_Setting = $setting;
		return $seed_Setting;
	}
}