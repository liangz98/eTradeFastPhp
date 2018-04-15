<?php
class Zend_View_Helper_ShowImage extends Shop_View_Helper
{
	function showImage($image)
	{
		if(!preg_match("/^http:\/\//", $image)){
			$image = $this->view->seed_Setting['upload_view_server'] . $image;
		}
		return $image;
	}
}