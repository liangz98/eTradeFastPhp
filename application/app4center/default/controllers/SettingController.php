<?php
class SettingController extends Seed_Controller_Action4Admin
{
	function settingAction()
	{
		if ($this->_request->isPost()) { 
			try{
				$modname = $this->_request->getPost('modname');
				$modtype = $this->_request->getPost('modtype');
				$mod_name = $modname."_".$modtype;
				
				$moduleM = new Seed_Model_Module('system');
				$module = $moduleM->fetchRow(array('mod_name'=>$modname,'mod_type'=>$modtype));
				if($module['mod_id']<1){
					Seed_Browser::tip_show('请先添加模块：'.$mod_name);
					exit;
				}
		
				$settings = $this->_request->getPost('settings');
				$settingM = new Seed_Model_Setting('system');
				if(is_array($settings)){
					$updateData=array();
					$f1 = new Seed_Filter_EscapeQuotes();
					foreach ($settings as $k=>$setting){
						$updateData['setting_content']=$f1->filter($setting);
						$settingM->updateRow($updateData,array('mod_name'=>$mod_name,'setting_variable'=>$k));
					}
				}
				
				//生成缓存
				$settings=$settingM->fetchRows(null,array('mod_name'=>$mod_name),'order_by asc');
				$data=array();
				foreach ($settings as $setting){
					$data[$setting['setting_variable']]=$setting['setting_content'];
				}
				$cacheM = new Seed_Model_Cache2File();
				
				if(defined('SEED_HOST_NAME')){
					$seed_host_name = str_replace(".","_",SEED_HOST_NAME);
					$cachefile = $mod_name."_".strtolower($seed_host_name)."_setting";
				}else{ 
					$cachefile = $mod_name."_setting";
				}
		
				$cacheM->save($cachefile,$data);
		
                                //调用接口
				$moduleapiM = new Seed_Model_ModuleApi('system');
				$apis = $moduleapiM->fetchRows(null,array('mod_name'=>$modname,'mod_type'=>$modtype,'api_name'=>'setting_cache'));
				
				if(count($apis)>0){
					$rs="";
					$i=0;
					foreach ($apis as $api){
						if ($api['api_id']>0 && !empty($api['api_url'])){
							$url = $api['api_url']."&token=".$this->view->seed_Token;
							$rs.= ++$i.":".Seed_Browser::view_page($url)."<br>";
						}
					}
					Seed_Browser::tip_show($rs);
				}
                                
				Seed_Browser::tip_show('设置成功！',$this->view->seed_BaseUrl.'/setting/setting?modname='.$modname.'&modtype='.$modtype);
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		$modname = $this->_request->getParam('modname');
		$modtype = $this->_request->getParam('modtype');
		$mod_name = $modname."_".$modtype;
		
		$moduleM = new Seed_Model_Module('system');
		$module = $moduleM->fetchRow(array('mod_name'=>$modname,'mod_type'=>$modtype));
		if($module['mod_id']<1){
			Seed_Browser::error('请先添加模块：'.$mod_name);
			exit;
		}
		$this->view->module = $module;
		$this->view->modname = $modname;
		$this->view->modtype = $modtype;
		$settingM = new Seed_Model_Setting('system');
		$this->view->settings = $settingM->fetchRows(null,array('mod_name'=>$mod_name),'order_by asc');
	}
	
	function indexAction()
	{
		$modname = $this->_request->getParam('modname');
		$modtype = $this->_request->getParam('modtype');
		$mod_name = $modname."_".$modtype;
		
		$moduleM = new Seed_Model_Module('system');
		$module = $moduleM->fetchRow(array('mod_name'=>$modname,'mod_type'=>$modtype));
		if($module['mod_id']<1){
			Seed_Browser::error('请先添加模块：'.$mod_name);
			exit;
		}
		$this->view->module = $module;
		
		$settingM=new Seed_Model_Setting('system');
		$seting_id = $this->_request->getParam('setting_id');
		$this->view->settings = $settingM->fetchRows(null,array('mod_name'=>$mod_name),array('order_by asc'));
		if($seting_id>0){
			$this->view->mysetting = $settingM->fetchRow(array('setting_id'=>$seting_id));
		}
		$this->view->modname = $modname;
		$this->view->modtype = $modtype;
	}
	
	function addAction()
	{
		if ($this->_request->isPost()) { 
			try{
				$modname = $this->_request->getPost('modname');
				$modtype = $this->_request->getPost('modtype');
				$mod_name = $modname."_".$modtype;
				
				$moduleM = new Seed_Model_Module('system');
				$module = $moduleM->fetchRow(array('mod_name'=>$modname,'mod_type'=>$modtype));
				if($module['mod_id']<1){
					Seed_Browser::tip_show('请先添加模块：'.$mod_name);
					exit;
				}
				
				$insertData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$insertData['setting_name'] = $f1->filter($this->_request->getPost('setting_name'));
				$insertData['setting_desc'] = $f1->filter($this->_request->getPost('setting_desc'));
				
				$f2 = new Zend_Filter_Int();
				$insertData['order_by'] = $f2->filter($this->_request->getPost('order_by'));	
				$insertData['setting_input'] = $f2->filter($this->_request->getPost('setting_input'));	
				
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Seed_Filter_Alnum());
				$insertData['mod_name'] = $mod_name;	
				$insertData['setting_variable'] = $f3->filter($this->_request->getPost('setting_variable'));	
				
				if (empty($insertData['setting_variable'])){
					Seed_Browser::tip_show('请输入变量！');
				}elseif (empty($insertData['setting_name'])){
					Seed_Browser::tip_show('请输入名称！');
				}else{
					$settingM=new Seed_Model_Setting('system');
					if($setting_id = $settingM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/setting/index?modname='.$modname.'&modtype='.$modtype);
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
				$modname = $this->_request->getPost('modname');
				$modtype = $this->_request->getPost('modtype');
				$mod_name = $modname."_".$modtype;
				
				$moduleM = new Seed_Model_Module('system');
				$module = $moduleM->fetchRow(array('mod_name'=>$modname,'mod_type'=>$modtype));
				if($module['mod_id']<1){
					Seed_Browser::tip_show('请先添加模块：'.$mod_name);
					exit;
				}
				
				$updateData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$updateData['setting_name'] = $f1->filter($this->_request->getPost('setting_name'));
				$updateData['setting_desc'] = $f1->filter($this->_request->getPost('setting_desc'));
				
				$f2 = new Zend_Filter_Int();
				$updateData['order_by'] = $f2->filter($this->_request->getPost('order_by'));	
				$updateData['setting_input'] = $f2->filter($this->_request->getPost('setting_input'));	
				$setting_id = $f2->filter($this->_request->getPost('setting_id'));	
				
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Seed_Filter_Alnum());
				$updateData['mod_name'] = $mod_name;	
				$updateData['setting_variable'] = $f3->filter($this->_request->getPost('setting_variable'));	
				
				if($setting_id<1){
					Seed_Browser::tip_show('参数数据错误！');
				}elseif (empty($updateData['setting_variable'])){
					Seed_Browser::tip_show('请输入变量！');
				}elseif (empty($updateData['setting_name'])){
					Seed_Browser::tip_show('请输入名称！');
				}else{
					$settingM=new Seed_Model_Setting('system');
					if($settingM->updateRow($updateData,array('setting_id'=>$setting_id,'mod_name'=>$mod_name))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/setting/index?modname='.$modname.'&modtype='.$modtype);
					}else{
						Seed_Browser::tip_show('修改失败！');
					}
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
	}
	
	function deleteAction() {
		if ($this->_request->isPost()){
			try{
				$modname = $this->_request->getPost('modname');
				$modtype = $this->_request->getPost('modtype');
				$mod_name = $modname."_".$modtype;
				
				$moduleM = new Seed_Model_Module('system');
				$module = $moduleM->fetchRow(array('mod_name'=>$modname,'mod_type'=>$modtype));
				if($module['mod_id']<1){
					Seed_Browser::tip_show('请先添加模块：'.$mod_name);
					exit;
				}
				
				$f3 = new Zend_Filter_Int();
				$setting_ids = $this->_request->getPost('setting_id');				
				if(count($setting_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				
				$settingM=new Seed_Model_Setting('system');
				foreach ($setting_ids as $setting_id){
					$setting_id = $f3->filter($setting_id);
					if($setting_id>0){
						$settingM->deleteRow(array('setting_id'=>$setting_id,'mod_name'=>$mod_name));
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/setting/index?modname='.$modname.'&modtype='.$modtype);
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		$modname = $this->_request->getParam('modname');
		$modtype = $this->_request->getParam('modtype');
		$mod_name = $modname."_".$modtype;
		
		$moduleM = new Seed_Model_Module('system');
		$module = $moduleM->fetchRow(array('mod_name'=>$modname,'mod_type'=>$modtype));
		if($module['mod_id']<1){
			Seed_Browser::error('请先添加模块：'.$mod_name);
			exit;
		}
		$this->view->module = $module;
		
		$setting_ids = $this->_request->getParam('setting_ids');
	    if(empty($setting_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$setting_ids = explode(',',$setting_ids);
		$f3 = new Zend_Filter_Int();
		$mysettings_arr=array();
		foreach ($setting_ids as $setting_id)
		{
			$setting_id = $f3->filter($setting_id);
			if($setting_id>0)
					$mysettings_arr[]=$setting_id;
		}
		if(count($mysettings_arr)>0){
			$settingM = new Seed_Model_Setting('system');
			$settings = $settingM->fetchRowsByIds('setting_id',$mysettings_arr);
	   		$this->view->settings = $settings;
		}
		$this->view->modname = $modname;
		$this->view->modtype = $modtype;
	}
	
	function orderAction() {
		//AJAX POST
		if ($this->_request->isPost()) {
			try{
				$modname = $this->_request->getPost('modname');
				$modtype = $this->_request->getPost('modtype');
				$mod_name = $modname."_".$modtype;
				
				$moduleM = new Seed_Model_Module('system');
				$module = $moduleM->fetchRow(array('mod_name'=>$modname,'mod_type'=>$modtype));
				if($module['mod_id']<1){
					Seed_Browser::tip_show('请先添加模块：'.$mod_name);
					exit;
				}
				
				$f3 = new Zend_Filter_Alnum();
				
				$setting_ids = $this->_request->getPost('setting_ids');
				$order_bys = $this->_request->getPost('order_bys');
				$updateData=array();
				$settingM = new Seed_Model_Setting('system');
				if(is_array($setting_ids)){
					foreach ($setting_ids as $k=>$setting_id){
						$setting_id = $f3->filter($setting_id);
						$updateData['order_by'] = $f3->filter($order_bys[$k]);
						$settingM->updateRow($updateData,array('setting_id'=>$setting_id,'mod_name'=>$mod_name));
					}
				}
				Seed_Browser::tip_show('排序成功！',$this->view->seed_BaseUrl.'/setting/index?modname='.$modname.'&modtype='.$modtype);
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
	function upAction()
    {
        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/setting/up.phtml");
            echo $content;
            exit;
        }
    }
}