<?php

class IndexController extends Seed_Controller_Action4Admin
{
    function indexAction()
	{
    $visitsM = new Stat_Model_Visits('stat');
    $conditions = array();
		$perpage=15;
    $page=intval($this->_request->getParam('page'));
    $total = $visitsM->fetchVisitsCount($conditions);
    $pageObj = new Seed_Page($this->_request,$total,$perpage);
    $this->view->page = $pageObj->getPageArray();
    if($page > $this->view->page['totalpage'])$page = $this->view->page['totalpage'];
    if($page < 1)$page = 1;
		$statList = $visitsM->fetchVisits(array(($page-1)*$perpage,$perpage),$conditions,'hits DESC');
		$this->view->statList = $statList;
		$this->view->conditions = $conditions;
	}

    function viewAction()
    {
        $hitsM = new Stat_Model_Hits('stat');
        $user_id = intval($this->_request->getParam('uid'));
        $conditions = array('user_id' => $user_id, 'domain IS NOT NULL' => null);
		$perpage=30;
    	$page=intval($this->_request->getParam('page'));
    	$total = $hitsM->fetchRowsCount($conditions);
    	$pageObj = new Seed_Page($this->_request,$total,$perpage);
    	$this->view->page = $pageObj->getPageArray();
	   	if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
    	if($page<1)$page=1;
		$statList = $hitsM->fetchRows(array(($page-1)*$perpage,$perpage),$conditions,array('yr DESC', 'mo DESC', 'dy DESC', 'hr DESC', 'mi DESC'));
        $output = array();
        foreach($statList as $stat) {
            $dateTime = "{$stat['yr']}-{$stat['mo']}-{$stat['dy']} {$stat['hr']}:{$stat['mi']}";
            $timestamp = strtotime($dateTime);
            $weekNum = date('N', $timestamp);
            if( !$weekNum) {continue;}
            switch ($weekNum) {
                case 1: $weekStr = '一';break;
                case 2: $weekStr = '二';break;
                case 3: $weekStr = '三';break;
                case 4: $weekStr = '四';break;
                case 5: $weekStr = '五';break;
                case 6: $weekStr = '六';break;
                case 7: $weekStr = '日';break;
            }
            $key = "周{$weekStr} " . date("n月j日 - H:i", $timestamp);
            $output[$key][] = $stat;
        }
        $this->view->user_name = $stat['user_name'];
        $this->view->nick_name = $stat['nick_name'];
		$this->view->output = $output;
		$this->view->conditions = $conditions;
    }

    function pageurlAction()
    {
        $hitsM = new Stat_Model_Hits('stat');
        $conditions = array();
		$statList = $hitsM->fetchHitGroup($conditions);
		$this->view->statList = $statList;
		$this->view->conditions = $conditions;
    }
    
    
    function goodsAction(){
       $hitsM = new Stat_Model_Hits('stat');
       $goodsM = new Shop_Model_Goods('shop');
       $goods_arr = array();
       $lists = $hitsM->_db->fetchAll("select substring_index(`resource`,'?',1) as res ,count(*) as total from ".$hitsM->_prefix.$hitsM->_table_name." where `resource` like '%/vmall/products/%' group by res order by total DESC");
       foreach($lists as $k=>$v){
          preg_match("/vmall\/products\/(\d+)\.html/is", $v['res'] , $matches);
          if(isset($matches[1])){
                $goods_id = $matches[1];
                if(isset($goods_arr[$goods_id])){
                   $goods = $goods_arr[$goods_id];
                }else{
                   $goods = $goodsM->fetchRow(array('goods_id'=>$goods_id));
                   $goods_arr[$goods_id] = $goods;
                }
                $lists[$k]['goods'] = $goods;
            }
        }
        
        unset($goods_arr);
        $this->view->goods_list = $lists;
        
    }
    
    
    function newsAction(){
       $hitsM = new Stat_Model_Hits('stat');
       $newsM = new Home_Model_News('home');
       $news_arr = array();
       $lists = $hitsM->_db->fetchAll("select `resource` as res ,count(*) as total from ".$hitsM->_prefix.$hitsM->_table_name." where `resource` like '%/vhome/news/%' group by res order by total DESC");
       foreach($lists as $k=>$v){
          preg_match("/vhome\/news\/detail\?id=(\d+)/is", $v['res'] , $matches);
          if(isset($matches[1])){
                $news_id = $matches[1];
                if(isset($news_arr[$news_id])){
                   $news = $news_arr[$news_id];
                }else{
                   $news = $newsM->fetchRow(array('news_id'=>$news_id));
                   $news_arr[$news_id] = $news;
                }
                $lists[$k]['news'] = $news;
            }
        }
        
        unset($news_arr);
        $this->view->news_list = $lists;
        
    }
    
    function aboutAction(){
       $hitsM = new Stat_Model_Hits('stat');
       $aboutM = new Home_Model_About('home');
       $about_arr = array();
       $lists = $hitsM->_db->fetchAll("select `resource` as res ,count(*) as total from ".$hitsM->_prefix.$hitsM->_table_name." where `resource` like '%/vhome/about/%' group by res order by total DESC");
       foreach($lists as $k=>$v){
          preg_match("/vhome\/about\/detail\?.*name=(\w+)/is", $v['res'] , $matches);
          if(isset($matches[1])){
                $about_name = $matches[1];
                if(isset($about_arr[$about_name])){
                   $about = $about_arr[$about_name];
                }else{
                   $about = $aboutM->fetchRow(array('about_mark'=>$about_name));
                   $about_arr[$about_name] = $about;
                }
                $lists[$k]['about'] = $about;
            }
        }
        unset($about_arr);
        $this->view->about_list = $lists;
        
    }
    
    function helpAction(){
       $hitsM = new Stat_Model_Hits('stat');
       $helpM = new Home_Model_Help('home');
       $help_arr = array();
       $lists = $hitsM->_db->fetchAll("select `resource` as res ,count(*) as total from ".$hitsM->_prefix.$hitsM->_table_name." where `resource` like '%/vhome/help/%' group by res order by total DESC");
       foreach($lists as $k=>$v){
          preg_match("/vhome\/help\/detail\?.*name=(\w+)/is", $v['res'] , $matches);
          if(isset($matches[1])){
                $help_mark = $matches[1];
                if(isset($help_arr[$help_mark])){
                   $help = $help_arr[$help_mark];
                }else{
                   $help = $helpM->fetchRow(array('help_mark'=>$help_mark));
                   $help_arr[$help_mark] = $help;
                }
                $lists[$k]['help'] = $help;
            }
        }
        unset($help_arr);
        $this->view->help_list = $lists;
        
    }
    
}