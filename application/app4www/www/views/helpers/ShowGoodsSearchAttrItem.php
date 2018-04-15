<?php
class Zend_View_Helper_ShowGoodsSearchAttrItem extends Shop_View_Helper
{	
	public function showGoodsSearchAttrItem($attrs,$select_attrs)
	{
		$template = <<<EOD
		
<p><strong><a>所有{attr.name}</a></strong><small>{attr.loop}</small></p>

EOD;

		$template_attr_loop = <<<EOD
		
<a {attr.cur} href="{attr.url}">{attr.value}</a>

EOD;
		if(is_array($attrs) && !empty($attrs)){
			$html = '';
			foreach ($attrs as $k=>$attr){
				if(isset($attr['attr_values']) && !empty($attr['attr_values'])){
					$attr_loop = '';
					$pattern = array();
					foreach($attr['attr_values'] as $key=>$value){
						$pattern['/{attr.cur}/'] = ($select_attrs['attr_'.$attr['attr_id']]==$key+1)?' class="crumb" ':'';
						$pattern['/{attr.url}/'] = $this->getBaseUrl() . '/' . $this->view->goodsSearchAttr()->getAttrUrl(array($attr['attr_id'],$key+1));
						$pattern['/{attr.value}/'] =  $this->view->escape($value);
						$attr_loop .= preg_replace(array_keys($pattern), array_values($pattern), $template_attr_loop);
					}
				}
				$pattern = array();
				$pattern['/{attr.name}/'] = $this->view->escape($attr['attr_name']);
				$pattern['/{attr.loop}/'] = $attr_loop;
				$html .= preg_replace(array_keys($pattern), array_values($pattern), $template);
			}
			return $html;
		}
		return null;
	}
}