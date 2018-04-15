<?php
class Zend_View_Helper_BaseUrl
{
	function BaseUrl()
	{
		$fc = Zend_Controller_Front::getInstance();
		return $fc->getBaseUrl();
	}
}
