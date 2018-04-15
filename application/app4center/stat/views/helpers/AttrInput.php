<?php
class Zend_View_Helper_AttrInput
{
	function attrInput($attr,$value="")
	{
		switch($attr['attr_input_type']){
			case "0":
				return '<input name="attr['.$attr['attr_id'].']" type="text" id="attr['.$attr['attr_id'].']" value="'.$value.'" class="admin_txt1"/>';
				break;
			case "1":
				return '<textarea name="attr['.$attr['attr_id'].']" id="attr['.$attr['attr_id'].']" style="width: 300px;height: 100px;">'.$value.'</textarea>';
				break;
			case "2":
				$tmp="";
				$values=explode("<br />",nl2br($attr['attr_values']));
				$myvalues=explode("_SEEDATTR_",$value);
				foreach($values as $k=>$v){
					$v=trim($v);
					if(!empty($v)){
						$tmp.='<label><input type="radio" name="attr['.$attr['attr_id'].']" value="'.$v.'" ';
						if(in_array($v,$myvalues))$tmp.=" checked";
						$tmp.='/>'.$v."</label>&nbsp;\n";
					}
				}
				return $tmp;
				break;
			case "3":
				$tmp="";
				$values=explode("<br />",nl2br($attr['attr_values']));
				$myvalues=explode("_SEEDATTR_",$value);
				foreach($values as $k=>$v){
					$v=trim($v);
					if(!empty($v)){
						$tmp.='<label><input type="checkbox" name="attr['.$attr['attr_id'].'][]" value="'.$v.'" ';
						if(in_array($v,$myvalues))$tmp.=" checked";
						$tmp.='/>'.$v."</label>&nbsp;\n";
					}
				}
				return $tmp;
				break;
			case "4":
				$tmp="";
				$tmp.='<label><input type="radio" name="attr['.$attr['attr_id'].'][]" value="1" ';
				if($value=='1')$tmp.=" checked";
				$tmp.="/>支持</label>&nbsp;\n";
				$tmp.='<label><input type="radio" name="attr['.$attr['attr_id'].'][]" value="0" ';
				if($value=='0')$tmp.=" checked";
				$tmp.="/>不支持</label>&nbsp;\n";
				return $tmp;
				break;
			default:
				return "";
		}
	}
}
