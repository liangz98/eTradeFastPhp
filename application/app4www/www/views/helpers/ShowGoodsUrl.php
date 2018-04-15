<?php
class Zend_View_Helper_ShowGoodsUrl extends Shop_View_Helper
{
	function ShowGoodsUrl($content)
	{
		if(is_array($content) && !empty($content) && isset($content['goods_mark']) && !empty($content['goods_mark'])){
			return '/products/' .$content['goods_mark'] . '.html';
		}else{
			return '/products/' .$content . '.html';
		}
	}
}