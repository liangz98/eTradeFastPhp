<?php
class Zend_View_Helper_AttrInput
{
	function attrInput($attr,$value="")
	{
		switch($attr['attr_input_type']){
			case "0":
				return '<input class="admin_txt1" name="attr_'.$attr['field_name'].'" type="text" value="'.$value.'"/>';
				break;
			case "1":
				return '<textarea class="textarea" name="attr_'.$attr['field_name'].'" >'.$value.'</textarea>';
				break;
			case "2":
				$tmp="";
				$values=explode("<br />",nl2br($attr['attr_values']));
				$displays=explode("<br />",nl2br($attr['attr_displays']));
				$myvalues=explode("_SEEDATTR_",$value);
				foreach($values as $k=>$v){
					$v=trim($v);
					if(!empty($v)){
						$tmp.='<label><input type="radio" name="attr_'.$attr['field_name'].'" value="'.$v.'" ';
						if(in_array($v,$myvalues))$tmp.=" checked";
						$tmp.='/>'.$displays[$k]."</label>\n";
					}
				}
				return $tmp;
				break;
			case "3":
				$tmp="";
				$values=explode("<br />",nl2br($attr['attr_values']));
				$displays=explode("<br />",nl2br($attr['attr_displays']));
				$myvalues=explode("_SEEDATTR_",$value);
				foreach($values as $k=>$v){
					$v=trim($v);
					if(!empty($v)){
						$tmp.='<label><input type="checkbox" name="attr_'.$attr['field_name'].'[]" value="'.$v.'" ';
						if(in_array($v,$myvalues))$tmp.=" checked";
						$tmp.='/>'.$displays[$k]."</label>\n";
					}
				}
				return $tmp;
				break;
			default:
				return "";
		}
	}
}
