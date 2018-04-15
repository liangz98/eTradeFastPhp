<?php
class WechattemplateController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$wechatTempM = new Seed_Model_WechatTemplate('system');
		$temps = $wechatTempM->fetchRows();
		$this->view->temps = $temps;
	}
	
	function addAction()
	{
		if ($this->_request->isPost()) { 
			try{
				$f1=new Seed_Filter_Alnum();
				$f2=new Zend_Filter_Int();
				$f3 = new Zend_Filter();
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f4 = new Seed_Filter_Text();
				$f5 = new Seed_Filter_Mobile();
				
				$insertData = array();
				$insertData['temp_name']=$f1->filter($this->_request->getPost('temp_name'));
				$insertData['temp_desc']=$f3->filter($this->_request->getPost('temp_desc'));
				$insertData['temp_content']=$f3->filter($this->_request->getPost('temp_content'));
				$insertData['is_actived']=intval($this->_request->getPost('is_actived'));
				$insertData['send_to']=intval($this->_request->getPost('send_to'));
				
				if(empty($insertData['temp_name'])){
					Seed_Browser::tip_show('模板名称不能为空！');
				}elseif(empty($insertData['temp_desc'])){
					Seed_Browser::tip_show('模板说明不能为空！');
				}elseif(empty($insertData['temp_content'])){
					Seed_Browser::tip_show('模板内容不能为空！');
				}else{
					$wechatTempM = new Seed_Model_WechatTemplate('system');
					if($temp_id = $wechatTempM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl."/wechattemplate/index");
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
				$wechatTempM = new Seed_Model_WechatTemplate('system');
				$f1=new Seed_Filter_Alnum();
				$f2=new Zend_Filter_Int();
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f4 = new Seed_Filter_Text();
				$f5 = new Seed_Filter_Mobile();
				
				$temp_id=$f2->filter($this->_request->getPost('temp_id'));
				$tempDetail = $wechatTempM->fetchRow(array('temp_id'=>$temp_id));
				if($tempDetail['temp_id']<1){
					Seed_Browser::tip_show("关键数据错误！");
				}
				
				$updateData = array();
				$updateData['temp_name']=$f1->filter($this->_request->getPost('temp_name'));
				$updateData['temp_desc']=$f3->filter($this->_request->getPost('temp_desc'));	
				$updateData['temp_content']=$f3->filter($this->_request->getPost('temp_content'));	
				$updateData['is_actived']=intval($this->_request->getPost('is_actived'));
				$updateData['send_to']=$f5->filter($this->_request->getPost('send_to'));
				
				if($temp_id<1){
					Seed_Browser::tip_show('关键参数错误！');
				}elseif(empty($updateData['temp_name'])){
					Seed_Browser::tip_show('模板名称不能为空！');
				}elseif(empty($updateData['temp_desc'])){
					Seed_Browser::tip_show('模板说明不能为空！');
				}elseif(empty($updateData['temp_content'])){
					Seed_Browser::tip_show('模板内容不能为空！');
				}else{
					if($wechatTempM->updateRow($updateData,array('temp_id'=>$temp_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/wechattemplate/index");
		    		}else{
		    			Seed_Browser::tip_show('修改失败！');
		    		}
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
		$wechatTempM = new Seed_Model_WechatTemplate('system');
		$temp_id=intval($this->_request->getParam('temp_id'));
		$temp = $wechatTempM->fetchRow(array('temp_id'=>$temp_id));
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
				
				$wechatTempM=new Seed_Model_WechatTemplate('system');
				foreach ($temp_ids as $temp_id){
					$temp_id = $f3->filter($temp_id);
					if($temp_id>0){
						$wechatTempM->deleteRow(array('temp_id'=>$temp_id));
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/wechattemplate/index');
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
			$wechatTempM = new Seed_Model_WechatTemplate('system');
			$temps = $wechatTempM->fetchRowsByIds('temp_id',$mytemps_arr);
	   		$this->view->temps = $temps;
		}
	}
	
	function sendAction(){
		$f5 = new Seed_Filter_Mobile();
		$wechatTempM = new Seed_Model_WechatTemplate('system');
		$temp_id=intval($this->_request->getParam('temp_id'));
		$temp = $wechatTempM->fetchRow(array('temp_id'=>$temp_id));
		if($temp['temp_id']<1)Seed_Browser::tip_show('没有找到相关数据！');
		$mobile=$f5->filter($temp['send_to']);
		if(empty($mobile))Seed_Browser::tip_show('发送对象错误！');
		$outboxM = new Seed_Model_MobileOutbox('system');
		$outboxM->mobileSend($mobile,$temp['temp_content'],time());
		Seed_Browser::tip_show('添加发送队列成功！本测试信息只是测试能否发送当前模板短信！');
	}
}
	