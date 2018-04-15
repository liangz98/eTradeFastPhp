<?php
class Zend_View_Helper_Chart
{
	private $charts = array();
	private $chartM = null;
	
	public function chart()
	{
		if(!is_object($this->chartM)){
			$this->chartM = new Seed_Model_Chart('system');
		}
		return $this;
	}
	
	public function fetchRows($cate_name)
	{
		if(isset($this->charts[$cate_name]) && !empty($this->charts[$cate_name])){
			return $this->charts[$cate_name];
		}else{
			$charts = $this->chartM->getCharts(null,array('cate_name'=>$cate_name,'is_actived'=>'1'),'chart_order ASC');
			if(is_array($charts) && !empty($charts)){
				foreach($charts as $k=>$chart){
					if(!empty($chart['chart_attrs'])){
						$charts[$k]['attrs'] = unserialize($chart['chart_attrs']);
					}else{
						$charts[$k]['attrs'] = array();
					}
				}
			}
			$this->charts[$cate_name] = $charts;
			return $this->charts[$cate_name];
		}
	}
	
	public function fetchRow($cate_name)
	{
		$chart = $this->chartM->getChart(array('cate_name'=>$cate_name,'is_actived'=>'1'),"*",'chart_order ASC');
		if(is_array($chart) && !empty($chart)){
			if(!empty($chart['chart_attrs'])){
				$chart['attrs'] = unserialize($chart['chart_attrs']);
			}else{
				$chart['attrs'] = array();
			}
		}
		return $chart;
	}
}