<?php
class Zend_View_Helper_ShowPosition extends Shop_View_Helper
{	
	public function showPosition($navs=NULL)
	{
		$position = array();
		$position[] = "<a href='/'>{$this->view->helper_Setting['position_index_name']}</a>";
		if(is_array($navs) && !empty($navs)){
			foreach ($navs as $k=>$nav){
				if($k==count($navs)-1){
					$position[] = "<em>{$nav['1']}</em>";
				}else{
					$position[] = "<a href='{$nav['0']}'>{$nav['1']}</a>";
				}
			}
		}elseif(is_string($navs)){
			$position[] = "<em>{$navs}</em>";
		}
		return implode($this->view->helper_Setting['position_implode_str'], $position);
	}
}