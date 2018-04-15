<?php
class RegionController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$parent = $this->_request->getParam('parent');
		if (empty($parent))
			$parent = 0;
		$regionM = new Seed_Model_Region('system');
		$this->view->regions = $regionM->fetchRows(null,array('parent'=>$parent),array('order_by ASC',"reg_name ASC"));
		$this->view->parent_nav = $regionM->getParentNav($parent);
		$this->view->parent = $parent;
	}
	
	function ajaxAction()
	{
		$parent = $this->_request->getParam('parent');
		if (empty($parent))
			$parent = 0;
		$regionM = new Seed_Model_Region('system');
		$regions = $regionM->fetchRows(null,array('parent'=>$parent),array('order_by ASC',"reg_name ASC"));
		$json_string = json_encode($regions);
		echo $json_string;
		exit;
	}
	
	function addAction() {		
		//AJAX POST
		if ($this->_request->isPost()) { 
			try{
				$insertData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
					
				$reg_names = $this->_request->getPost('reg_names');
				if(empty($reg_names)) $myregs = explode("<br />",nl2br($reg_names));
				if(is_array($myregs) && count($myregs)>0){
					
				}else{
					$myregs=array();
					$myregs[] = $f1->filter($this->_request->getPost('reg_name'));
				}
				if (count($myregs)==0){
					Seed_Browser::tip_show('请输入名称！');
				}
				
				
				$pinyinM = new Seed_Pinyin();
				$regionM=new Seed_Model_Region('system');
				$insertData['parent'] = intval($this->_request->getPost('parent'));	
				foreach ($myregs as $reg_name){
					$insertData['reg_name'] = $reg_name;
					$insertData['reg_ename'] = $pinyinM->cn2pinyin($insertData['reg_name']);
					if($regionM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/region/index?parent='.$insertData ['parent']);
					}else{
						Seed_Browser::tip_show('添加失败！',$this->view->seed_BaseUrl.'/region/index?parent='.$insertData ['parent']);
					}
				}
				exit;			
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		//GET VIEW
		$f3 = new Zend_Filter_Alnum();
		$parent=$f3->filter($this->_request->getParam('parent'));
		if($parent>0){
			$regionM=new Seed_Model_Region('system');
			$parent_nav = $regionM->getParentNav($parent);
			$this->view->parent = $regionM->fetchRow(array('reg_id'=>$parent));
		}else{
			$this->view->parent = array('reg_id'=>0,'reg_name'=>'最顶级');
		}			
		
	}
	
	function updateAction() {
		//AJAX POST
		if ($this->_request->isPost()) {
			try{
				$updateData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$updateData['reg_name'] = $f1->filter($this->_request->getPost('reg_name'));
				$pinyinM = new Seed_Pinyin();
				$updateData['reg_ename'] = $pinyinM->cn2pinyin($updateData['reg_name']);
				
				$f3 = new Zend_Filter_Alnum();
				$reg_id = $f3->filter($this->_request->getPost('reg_id'));
				
				if (empty($updateData['reg_name'])){
					Seed_Browser::tip_show('请输入名称！');
				}else{
					$regionM=new Seed_Model_Region('system');
					$my = $regionM->fetchRow(array('reg_id'=>$reg_id));
					if($regionM->updateRow($updateData,array('reg_id'=>$reg_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/region/index?parent='.$my ['parent']);
					}else{
						Seed_Browser::tip_show('修改失败！',$this->view->seed_BaseUrl.'/region/index?parent='.$my ['parent']);
					}
				}
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		//GET VIEW
		$f3 = new Zend_Filter_Alnum();
		$reg_id=$f3->filter($this->_request->getParam('reg_id'));
		if($reg_id<1){
			Seed_Browser::error('关键参数错误');
		}
		$regionM=new Seed_Model_Region('system');
		$region = $regionM->fetchRow(array('reg_id'=>$reg_id));		
		$this->view->region = $region;
	}
	
	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Alnum();
				$reg_id = $f3->filter($this->_request->getPost('reg_id'));
				if($reg_id<1){
					Seed_Browser::tip_show('参数数据错误！');
				}else{
					$regionM=new Seed_Model_Region('system');
					$my = $regionM->fetchRow(array('reg_id'=>$reg_id));
					if($regionM->deleteRow(array('reg_id'=>$reg_id))){
						Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/region/index?parent='.$my ['parent']);
					}else{
						Seed_Browser::tip_show('删除失败！',$this->view->seed_BaseUrl.'/region/index?parent='.$my ['parent']);
					}
				}
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		//GET VIEW
		$f3 = new Zend_Filter_Alnum();
		$reg_id=$f3->filter($this->_request->getParam('reg_id'));
		if($reg_id<1){
			Seed_Browser::error('关键参数错误');
		}
		$regionM=new Seed_Model_Region('system');
		$region = $regionM->fetchRow(array('reg_id'=>$reg_id));		
		$this->view->region = $region;
	}
	
	function orderAction() {
		//AJAX POST
		if ($this->_request->isPost()) {
			try{
				$f3 = new Zend_Filter_Alnum();
				
				$reg_ids = $this->_request->getPost('reg_ids');
				$order_bys = $this->_request->getPost('order_bys');
				$updateData=array();
				$regionM=new Seed_Model_Region('system');
				if(is_array($reg_ids)){
					foreach ($reg_ids as $k=>$reg_id){
						$reg_id = $f3->filter($reg_id);
						$updateData['order_by'] = $f3->filter($order_bys[$k]);
						$regionM->updateRow($updateData,array('reg_id'=>$reg_id));
					}
				}else{
					$reg_id = $f3->filter($reg_ids);
					$updateData['order_by'] = $f3->filter($order_bys);
					$regionM->updateRow($updateData,array('reg_id'=>$reg_id));
				}
				$my = $regionM->fetchRow(array('reg_id'=>$reg_id));
				Seed_Browser::tip_show('排序成功！',$this->view->seed_BaseUrl.'/region/index?parent='.$my ['parent']);
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
	
	function docacheAction(){
		$regionM = new Seed_Model_Region('system');
		$regions = $regionM->fetchChildrenNav(0);
		
		$this->regions2str(array(0),$regions);
		$mydata="";
		for($i=1;$i<5;$i++){
			if(count($this->level_data[$i])==0)break;
			foreach ($this->level_data[$i] as $level_str){
				$mydata.=$level_str.",";
			}
		}
		$mydata=substr($mydata,0,-1);
		$data="var data	= {";
		$data.=$mydata;
		$data.="}";
		//生成缓存
		$cacheM = new Seed_Cache2Js();
		$cacheM->cache4region($data);
		Seed_Browser::tip_show('缓存成功！',$this->view->seed_BaseUrl.'/region/index');
	}
	
	public $level_data=array();
	function regions2str($parent,$regions){
		$myparent="";
		foreach ($parent as $v) {
			$myparent.=$v.",";
		}
		$myparent=substr($myparent,0,-1);
		
		$mylevel="";
		foreach ($regions as $k=>$region){
			$mylevel.=$region['reg_id'].":'".$region['reg_name']."',";
			if(count($region['sub'])>0){
				$subparent=$parent;
				$subparent[]=$region['reg_id'];
				$this->regions2str($subparent,$region['sub']);
			}
		}
		$mylevel=substr($mylevel,0,-1);
		
		$str="'".$myparent."':{".$mylevel."}";
		$this->level_data[count($parent)][]=$str;
	}
}