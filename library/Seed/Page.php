<?php
class Seed_Page {
    private $_total;//总记录
    private $_totalpage;//总页码
    private $_perpage;//每页显示数
    private $_pagename;
    private $_request;
    private $_curpage;//当前页
    private $_displaypage;
	
    function __construct($request,$total,$perpage = 10,$curpage=0,$pagename = 'page',$displaypage = 8) {
        $this->_total   = intval($total);
        $this->_perpage = intval($perpage);
        $this->_pagename = $pagename;
        $this->_displaypage = $displaypage;
        $this->_request  = $request;
        if($curpage>0)
        	$this->_curpage = $curpage;
        else
       		$this->_curpage = intval($this->_request->getParam($this->_pagename)); 
        if($this->_curpage==0)$this->_curpage=1;
//        if($this->_total==0)$this->_total=1;
        if($this->_perpage==0)$this->_perpage=10;
        $this->_totalpage=ceil($this->_total/$this->_perpage);
        if($this->_curpage>$this->_totalpage)$this->_curpage=$this->_totalpage;
    }
	
    function getPageArray($baseurl=null){
    	if ($this->_displaypage >= $this->_totalpage) {
			$basepage = 0;
			$this->_displaypage = $this->_totalpage;
		} else {
			$half = ceil($this->_displaypage / 2);
			if ($this->_curpage < $half) $basepage = 0;
			elseif ($this->_displaypage > $this->_totalpage - $half) $basepage = $this->_totalpage - $this->_displaypage;
			else $basepage = $this->_curpage - $half;
		}
    	$startpage = $basepage + 1;
    	$endpage = $basepage + $this->_displaypage;
    	if($endpage>$this->_totalpage)$endpage=$this->_totalpage;
    	$moduleName     = $this->_request->getModuleName();
    	$controllerName = $this->_request->getControllerName();
    	$actionName     = $this->_request->getActionName();
    	$params         = $this->_request->getParams();
    	
    	if($baseurl==null){
    		$baseurl = $this->_request->getBaseUrl();
    		$baseurl =  $baseurl . '/' . $controllerName . '/' . $actionName . '?';
    	
	    	if($params && is_array($params))
	    	{
	    		$params = array_diff_key($params,array_flip(array($this->_request->getModuleKey(),$this->_request->getControllerKey(),$this->_request->getActionKey())));
	    		foreach($params as $key => $value)
	    		{
	    			if($key!=$this->_pagename){
	    				if(is_string($value))
	    					$baseurl.= $key . '=' . urlencode($value) . '&';
	    				elseif (is_array($value)){
	    					foreach ($value as $k=>$v){
	    						if(is_string($v))$baseurl.= $key . '['.$k.']=' . urlencode($v) . '&';
	    					}
	    				}
	    			}
	    		}
	    	}
    	}
    	$baseurl.=$this->_pagename."=";
    	
    	$prepage=$this->_curpage-1;
    	$nextpage=$this->_curpage+1;
    	if($prepage<1)$prepage=1;
    	if($nextpage>$this->_totalpage)$nextpage=$this->_totalpage;
    	
    	$page=array();
    	$page['total']=$this->_total;
    	$page['totalpage']=$this->_totalpage;
    	$page['curpage']=$this->_curpage;
    	$page['prepage']=$prepage;
    	$page['nextpage']=$nextpage;
    	$page['baseurl']=$baseurl;
    	$page['startpage']=$startpage;
    	$page['endpage']=$endpage;
    	return $page;
    }
}

?>