<?php
class LangController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$langM=new Seed_Model_Lang('system');
		$lang_id = $this->_request->getParam('lang_id');
		$this->view->langs = $langM->fetchRows();
		if($lang_id>0){
			$this->view->lang = $langM->fetchRow(array('lang_id'=>$lang_id));
		}
	}

	function addAction() {		
		//AJAX POST
		if ($this->_request->isPost()) { 
			try{
				$insertData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_Alnum());
				
				$insertData['lang_name'] = $f3->filter($this->_request->getPost('lang_name'));	
				$insertData['lang_content'] = $f1->filter($this->_request->getPost('lang_content'));
				$insertData['lang_desc'] = $f1->filter($this->_request->getPost('lang_desc'));
				if (empty($insertData['lang_name'])){
					Seed_Browser::tip_show('请输入名称！');
				}elseif (empty($insertData['lang_content'])){
					Seed_Browser::tip_show('请输入说明！');
				}else{
					$langM=new Seed_Model_Lang('system');
					$lang = $langM->fetchRow(array('lang_name'=>$insertData['lang_name']));
					if ($lang['lang_id']>0){
						Seed_Browser::tip_show('名称已存在,请重新输入');
					}
					if($lang_id = $langM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/lang/index');
					}else{
						Seed_Browser::tip_show('添加失败！');
					}
				}
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
	
	function updateAction() {	
		//AJAX POST
		if ($this->_request->isPost()) { 
			try{
				$langM=new Seed_Model_Lang('system');	
                $updateData = array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_Alnum());
				
				$lang_id = $f3->filter($this->_request->getPost('lang_id'));	
				$updateData['lang_name'] = $f3->filter($this->_request->getPost('lang_name'));	
				$updateData['lang_content'] = $f1->filter($this->_request->getPost('lang_content'));
				$updateData['lang_desc'] = $f1->filter($this->_request->getPost('lang_desc'));
			    if ($lang_id<0){
			    	Seed_Browser::tip_show('关键数据错误！');
			    }elseif (empty($updateData['lang_name'])){
					Seed_Browser::tip_show('请输入名称！');
				}elseif (empty($updateData['lang_content'])){
					Seed_Browser::tip_show('请输入说明！');
				}else{
					$langM=new Seed_Model_Lang('system');
				    $lang = $langM->fetchRow(array('lang_name'=>$updateData['lang_name']));
/*					if ($lang['lang_id']>0){
						Seed_Browser::tip_show('名称已存在,请重新输入');
					}*/
					if($langM->updateRow($updateData,array('lang_id'=>$lang_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/lang/index');
					}else{
						Seed_Browser::tip_show('修改失败！');
					}
				}
				$langM->updateRow($updateData, array('lang_id'=>$lang_id));
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
	
	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Int();
				$lang_ids = $this->_request->getPost('lang_id');				
				if(count($lang_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				
				$f2 = new Zend_Filter( );
				$f2->addFilter(new Zend_Filter_Alnum());	
				$langM = new Seed_Model_Lang('system');
				
				foreach ($lang_ids as $lang_id){
					$lang_id = $f3->filter($lang_id);
					if($lang_id>0){				
						$langM->deleteRow(array('lang_id'=>$lang_id));							
						}
					}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/lang/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		$lang_ids = $this->_request->getParam('lang_ids');
		
	    if(empty($lang_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$lang_ids = explode(',',$lang_ids);
		$f3 = new Zend_Filter_Int();
		$myroles_arr=array();
		foreach ($lang_ids as $lang_id)
		{
			$lang_id = $f3->filter($lang_id);
			if($lang_id>0)
					$myroles_arr[]=$lang_id;
		}
		if(count($myroles_arr)>0){
			$langM = new Seed_Model_Lang('system');
			$langs = $langM->fetchRowsByIds('lang_id',$myroles_arr);
	   		$this->view->langs = $langs;
		}
	}
    
	//
	function cacheAction(){
		$langM = new Seed_Model_Lang('system');
		if ($this->_request->isPost()){
			try{
				//生成缓存
				$langs=$langM->fetchRows();

				$data=array();
				foreach ($langs as $lang){
					$data[$lang['lang_name']]=$lang['lang_content'];
				}
				$cacheM = new Seed_Model_Cache2File();

				$cachefile = "lang";
		
				$cacheM->save($cachefile,$data);
				Seed_Browser::tip_show('设置成功！',$this->view->seed_BaseUrl.'/lang/index');
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
}