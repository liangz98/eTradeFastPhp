<?php
class MessagetemplateController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$msgTempM = new Seed_Model_MessageTemplate('system');
		$temps = $msgTempM->fetchRows();
		$this->view->temps = $temps;
	}
	
	function addAction()
	{
		if ($this->_request->isPost()) { 
			try{
				$f1=new Seed_Filter_Alnum();
				$f2=new Zend_Filter_Int();
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f4 = new Seed_Filter_EscapeQuotes();
				
				$insertData = array();
				$insertData['temp_name']=$f1->filter($this->_request->getPost('temp_name'));
				$insertData['temp_title']=$f3->filter($this->_request->getPost('temp_title'));
				$insertData['temp_content']=$f4->filter($this->_request->getPost('temp_content'));
				$insertData['is_actived']=intval($this->_request->getPost('is_actived'));
				
				if(empty($insertData['temp_name'])){
					Seed_Browser::tip_show('模板名称不能为空！');
				}elseif(empty($insertData['temp_content'])){
					Seed_Browser::tip_show('模板内容不能为空！');
				}else{
					$msgTempM = new Seed_Model_MessageTemplate('system');
					if($temp_id = $msgTempM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl."/messagetemplate/index");
		    		}else{
		    			Seed_Browser::tip_show('添加失败！');
		    		}
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
	}
	
	function updateAction()
	{
		if ($this->_request->isPost()) { 
			try{
				$msgTempM = new Seed_Model_MessageTemplate('system');
				$f1=new Seed_Filter_Alnum();
				$f2=new Zend_Filter_Int();
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f4 = new Seed_Filter_EscapeQuotes();
				$temp_id=$f2->filter($this->_request->getPost('temp_id'));
				$tempDetail = $msgTempM->fetchRow(array('temp_id'=>$temp_id));
				if($tempDetail['temp_id']<1){
					Seed_Browser::tip_show("关键数据错误！");
				}
				
				$updateData = array();
				$updateData['temp_name']=$f1->filter($this->_request->getPost('temp_name'));
				$updateData['temp_title']=$f3->filter($this->_request->getPost('temp_title'));
				$updateData['temp_content']=$f4->filter($this->_request->getPost('temp_content'));
				$updateData['is_actived']=intval($this->_request->getPost('is_actived'));	
				
				if($temp_id<1){
					Seed_Browser::tip_show('关键参数错误！');
				}elseif(empty($updateData['temp_name'])){
					Seed_Browser::tip_show('模板名称不能为空！');
				}elseif(empty($updateData['temp_content'])){
					Seed_Browser::tip_show('模板内容不能为空！');
				}else{
					if($msgTempM->updateRow($updateData,array('temp_id'=>$temp_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/messagetemplate/index");
		    		}else{
		    			Seed_Browser::tip_show('修改失败！');
		    		}
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
		$msgTempM = new Seed_Model_MessageTemplate('system');
		$temp_id=intval($this->_request->getParam('temp_id'));
		$temp = $msgTempM->fetchRow(array('temp_id'=>$temp_id));
		if($temp['temp_id']<1)Seed_Browser::error('没有找到相关数据！');
		$this->view->temp = $temp;
	}
	
	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Int();
				$temp_ids = $this->_request->getPost('temp_id');				
				if(count($temp_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				
				$msgTempM=new Seed_Model_MessageTemplate('system');
				foreach ($temp_ids as $temp_id){
					$temp_id = $f3->filter($temp_id);
					if($temp_id>0){
						$msgTempM->deleteRow(array('temp_id'=>$temp_id));
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/messagetemplate/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		$temp_ids = $this->_request->getParam('temp_ids');
	    if(empty($temp_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$temp_ids = explode(',',$temp_ids);
		$f3 = new Zend_Filter_Int();
		$mytemps_arr=array();
		foreach ($temp_ids as $temp_id)
		{
			$temp_id = $f3->filter($temp_id);
			if($temp_id>0)
					$mytemps_arr[]=$temp_id;
		}
		if(count($mytemps_arr)>0){
			$msgTempM = new Seed_Model_MessageTemplate('system');
			$temps = $msgTempM->fetchRowsByIds('temp_id',$mytemps_arr);
	   		$this->view->temps = $temps;
		}
	}
}
	