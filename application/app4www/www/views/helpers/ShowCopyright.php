<?php
class Zend_View_Helper_ShowCopyright
{
	function ShowCopyright()
	{
		if(!defined('HIDE_COPYRIGHT') || HIDE_COPYRIGHT!='1'){
		$html ='<p class="copyright">技术支持 © 萌芽网络</p>';
		echo $html;
		}
	}
}
