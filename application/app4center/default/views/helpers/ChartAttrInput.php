<?php
class Zend_View_Helper_ChartAttrInput
{
	function chartAttrInput($attr,$value="")
	{
		switch($attr['attr_input_type']){
			case "0":
				if($attr['field_type']=='varchar'){
					return '<input class="admin_txt1" name="attr_'.$attr['field_name'].'" type="text" value="'.$value.'"/>';
				}elseif($attr['field_type']=='image'){
					$html = '<input class="admin_txt1" name="attr_'.$attr['field_name'].'" id="attr_'.$attr['field_name'].'" type="text" value="'.$value.'"/> ';
					$html.= '<input type="button" name="select" class="admin_bnt2" value="选择"  onclick="selectCoverImage(\'chart\',\'attr_'.$attr['field_name'].'\')"> ';
          			$html.= '<input type="button" name="preview_attr_'.$attr['field_name'].'"  id="preview_attr_'.$attr['field_name'].'" value="预览" class="admin_bnt2" onclick="previewCoverImage(\'attr_'.$attr['field_name'].'\')" '. (empty($value)? "style=\"display:none;\"" : '') . '>';
          			return $html;
				}
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
