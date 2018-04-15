<?php
class ShippingparamController extends Seed_Controller_Action4Admin
{
	function settingAction()
	{
		$shippingparamM = new Shop_Model_ShippingParam('shop');
		if ($this->_request->isPost()) { 
			try{
				$settings = $this->_request->getPost('settings');
				$shipping_name = $this->_request->getPost('shipping_name');
				if(is_array($settings)){
					$updateData=array();
					foreach ($settings as $k=>$setting){
						$updateData['setting_content']=$setting;
						$shippingparamM->updateRow($updateData,array('shipping_name'=>$shipping_name,'setting_variable'=>$k));
					}
				}
				Seed_Browser::tip_show('设置成功！',$this->view->seed_BaseUrl.'/shippingparam/setting/shipping_name/'.$shipping_name);
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		$shipping_name = $this->_request->getParam('shipping_name');
		$this->view->shipping_name = $shipping_name;
		$this->view->settings = $shippingparamM->fetchRows(null,array('shipping_name'=>$shipping_name),'order_by asc');
	}
	
	function indexAction()
	{
		$shipping_name = $this->_request->getParam('shipping_name');
		$this->view->shipping_name = $shipping_name;
		$shippingparamM=new Shop_Model_ShippingParam('shop');
		$seting_id = $this->_request->getParam('setting_id');
		$this->view->settings = $shippingparamM->fetchRows(null,array('shipping_name'=>$shipping_name),array('order_by asc'));
		if($seting_id>0){
			$this->view->mysetting = $shippingparamM->fetchRow(array('setting_id'=>$seting_id));
		}
	}
	
	function addAction()
	{
		$shipping_name = $this->_request->getPost('shipping_name');
		if ($this->_request->isPost()) { 
			try{
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
				$insertData['shipping_name'] = $shipping_name;	
				$insertData['setting_variable'] = $f3->filter($this->_request->getPost('setting_variable'));	
				
				if (empty($insertData['setting_variable'])){
					Seed_Browser::tip_show('请输入变量！');
				}elseif (empty($insertData['setting_name'])){
					Seed_Browser::tip_show('请输入名称！');
				}else{
					$shippingparamM=new Shop_Model_ShippingParam('shop');
					if($setting_id = $shippingparamM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/shippingparam/index/shipping_name/'.$shipping_name);
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
		$shipping_name = $this->_request->getPost('shipping_name');
		if ($this->_request->isPost()) { 
			try{
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
				$updateData['shipping_name'] = $shipping_name;	
				$updateData['setting_variable'] = $f3->filter($this->_request->getPost('setting_variable'));	
				
				if($setting_id<1){
					Seed_Browser::tip_show('参数数据错误！');
				}elseif (empty($updateData['setting_variable'])){
					Seed_Browser::tip_show('请输入变量！');
				}elseif (empty($updateData['setting_name'])){
					Seed_Browser::tip_show('请输入名称！');
				}else{
					$shippingparamM=new Shop_Model_ShippingParam('shop');
					if($shippingparamM->updateRow($updateData,array('setting_id'=>$setting_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/shippingparam/index/shipping_name/'.$shipping_name);
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
		$shipping_name = $this->_request->getPost('shipping_name');
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Int();
				$setting_id = $f3->filter($this->_request->getPost('setting_id'));				
				
				if($setting_id<1){
					Seed_Browser::tip_show('参数数据错误！');
				}else{
					$shippingparamM=new Shop_Model_ShippingParam('shop');
					if($shippingparamM->deleteRow(array('setting_id'=>$setting_id))){
						Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/shippingparam/index/shipping_name/'.$shipping_name);
					}else{
						Seed_Browser::tip_show('删除失败！');
					}
				}
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
}