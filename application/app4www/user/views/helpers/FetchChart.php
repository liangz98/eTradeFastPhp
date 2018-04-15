<?php
class Zend_View_Helper_FetchChart
{
	private $charts = array();
	private $chartM = null;
	
	/**
	 * 返回指定分类名的所有榜单数据
	 * @param string $cate_name
	 * @return multitype:
	 */
	function fetchChart($cate_name)
	{
		if(isset($this->charts[$cate_name]) && !empty($this->charts[$cate_name])){
			return $this->charts[$cate_name];
		}else{
			if(!is_object($this->chartM)){
				$this->chartM = new Seed_Model_Chart('system');
			}
			$this->charts[$cate_name] = $this->chartM->getChart(array('cate_name'=>$cate_name),'*','chart_order ASC');
			return $this->charts[$cate_name];
		}
	}
}