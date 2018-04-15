<?php
class Zend_View_Helper_ShopLevel extends Shop_View_Helper
{
	function shopLevel($level)
	{
		require(SEED_CONF_ROOT."/param.php");
		$levelName = "";
		if(isset($SHOP_LEVEL_STATUS[$level]) && $SHOP_LEVEL_STATUS[$level]>0){
			$levelName = $SHOP_LEVEL_NAMES[$level];
		}
		return $levelName;
	}
}