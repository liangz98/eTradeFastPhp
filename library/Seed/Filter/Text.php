<?php
require_once 'Zend/Filter/Interface.php';
class Seed_Filter_Text implements Zend_Filter_Interface
{
    public function filter($value)
    {
        // perform some transformation upon $value to arrive on $valueFiltered
		if(get_magic_quotes_gpc()){
			$value=stripslashes($value);
		}
		//去掉SCRIPT
		$farr = array( 
		"/\s /", //过滤多余的空白 
		"/<(\/?)(script|i?frame|style|html|body|title|link|meta|\?|\%)([^>]*?)>/isU", //过滤 <script 等可能引入恶意内容或恶意改变显示布局的代码,假如不需要插入flash等,还可以加入<object的过滤 
		"/(<[^>]*)on[a-zA-Z]+=\"(.*)\"([^>]*>)/isU", //过滤javascript的on事件
		"/(<[^>]*)on[a-zA-Z]+=(.*)([^>]*>)/isU", //过滤javascript的on事件
		"/<!--(.*)-->/isU",
		); 
		$tarr = array( 
		" ", 
		"",
		"\\1\\3", 
		"\\1\\3", 
		"", 
		); 
		$value = preg_replace($farr,$tarr,$value); 
        return $value;
    }
}
