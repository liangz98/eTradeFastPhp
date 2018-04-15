<?php
class Zend_View_Helper_GoodsSearchAttr extends Shop_View_Helper
{
	private $attributeGroup  = 'default';
	private $module = "vmall";
	private $controllerName = null;
	private $urlSpec = '{module}/{controllerName}/{other}-{cate}-{brand}-{attrs}-{sort}-{page}.html';
	private $attributeM = null;
	private $regex = null;
	private $attributes = array();
	private $matches = array();
	private $activeUrl = null;
	
	public function goodsSearchAttr()
	{
		$this->init();
		return $this;
	}
	
	public function init()
	{
		$this->controllerName = "products";
		$this->attributeGroup = $this->view->helper_Setting['goods_search_attr_group'];
		
		if(!is_object($this->attributeM)){
			$this->attributeM = new Shop_Model_Attribute('shop');
		}
		if(empty($this->attributes)){
			if(!empty($this->controllerName)){
				$this->urlSpec = preg_replace('/{controllerName}/', $this->controllerName, $this->urlSpec);
			}
			if(!empty($this->module)){
				$this->urlSpec = preg_replace('/{module}/', $this->module, $this->urlSpec);
			}
			
			$this->attributes = $this->attributeM->fetchAttributes(null,array("t2.group_name='{$this->attributeGroup}'"=>null, 'is_search'=>'1'),'order_by ASC');
			
			if(is_array($this->attributes) && !empty($this->attributes)){
				$attr = array();
				foreach($this->attributes as $attribute){
					$attr[] = '{attr_' . $attribute['attr_id'] . '}';
				}
				$this->activeUrl = preg_replace('/{attrs}/', implode('-', $attr), $this->urlSpec);
			}else{
				$this->activeUrl = $this->urlSpec;
			}
			
			if(!empty($this->activeUrl)){
				preg_match_all('/{([\w]+)}/', $this->activeUrl, $matches);
				if(isset($matches['1']) && !empty($matches['1'])){
					$_matches = $_regex = array();
					foreach($matches['1'] as $value){
						$_matches[$value] = '0';
						$_regex[$value] = '([\d]+)';
						if($value == 'page'){
							$_matches[$value] = '1';
						}
						if($value == 'other'){
							$_matches[$value] = 'view';
							$_regex[$value] = '([\w]+)';
						}
					}
					$this->matches['default'] = $_matches;
					$this->regex = '/^\/'.$this->module.'\/' . $this->controllerName . '\/' . implode('-', $_regex) . '.html/isU';
				}
			}else{
				$this->matches['default'] = '';
			}
			
			if(is_array($this->matches['default']) && !empty($this->matches['default'])){
				preg_match_all($this->regex, $this->getRequestUri(), $arr);
				if(isset($arr['0']) && !empty($arr['0'])){
					array_shift($arr);
					$_activeMatches = array();
					$i = 0;
					foreach ($this->matches['default'] as $k=>$value){
						$_activeMatches[$k] = $arr[$i]['0'];
						$i++;
					}
					$this->matches['active'] = $_activeMatches;
				}
			}
		}
	}
	
	public function getAttrUrl($config)
	{
		$matches = $this->prepareMatche();
		if(isset($config['1'])){
			$matches['attr_'.$config['0']] = $config['1'];
		}else{
			$matches['attr_'.$config['0']] = 0;
		}
		return $this->getItemUri($matches, $this->activeUrl);
	}
	
	public function getRegexShort()
	{
		$regex_short = '/^\/'.$this->module.'\/' . $this->controllerName . '\/index.html/isU';
		return $regex_short;
	}
	
	public function getRegex()
	{
		if(empty($this->regex)){
			return $this->getRegexShort();
		}else{
			return $this->regex;
		}
	}
	
	public function getOtherUrl($mark)
	{
		$matches = $this->prepareMatche();
		$matches['other'] = $mark;
		return $this->getItemUri($matches, $this->activeUrl);
	}
	
	public function getCateUrl($cate_id)
	{
		$matches = $this->prepareMatche();
		$matches['cate'] = $cate_id;
		return $this->getItemUri($matches, $this->activeUrl);
	}
	
	public function getBrandUrl($brand_id)
	{
		$matches = $this->prepareMatche();
		$matches['brand'] = $brand_id;
		return $this->getItemUri($matches, $this->activeUrl);
	}
	
	public function getInitUrlByLong()
	{
		return $this->module.'/'.$this->controllerName . '/' .implode('-', $this->matches['default']). '.html';
	}
	
	public function getInitUrl()
	{
		return $this->module.'/'.$this->controllerName . '/index.html';
	}
	
	public function getMatches()
	{
		$matches = $this->prepareMatche();
		return $matches;
	}
	
	public function getPageUrl()
	{
		$matches = $this->prepareMatche();
		if(isset($matches['page']))unset($matches['page']);
		$url = preg_replace('/-{page}.html/', '', $this->activeUrl);
		return $this->getItemUri($matches, $url);
	}
	
	private function prepareMatche()
	{
		$matches = array();
		if(isset($this->matches['active'])){
			$matches = $this->matches['active'];
		}else{
			$matches = $this->matches['default'];
		}
		return $matches;
	}
	
	private function getItemUri($matches, $url)
	{
		$matches['page'] = 1;
		$patterns = array_keys($matches);
		if(is_array($patterns) && !empty($patterns)){
			foreach($patterns as $k=>$value){
				$patterns[$k] = '/{' .$value. '}/';
			}
		}
		return preg_replace($patterns, array_values($matches), $url);
	}
	
	private function getRequestUri()
	{
		$requestUri = null;
		if(isset($_SERVER['HTTP_X_REWRITE_URL']) && !empty($_SERVER['HTTP_X_REWRITE_URL'])){
			$requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
		}else{
			$requestUri = $_SERVER["REQUEST_URI"];
		}
		return $requestUri;
	}
}