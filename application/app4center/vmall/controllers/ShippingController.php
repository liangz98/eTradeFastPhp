<?php
class ShippingController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$shippingM = new Shop_Model_Shipping('shop');
		$this->view->shippings = $shippingM->fetchRows(null,null,'order_by asc');
	}
	
	function ajaxAction()
	{
		$shippingM = new Shop_Model_Shipping('shop');
		$shippings = $shippingM->fetchRows(null,null,'order_by asc');
		$json_string = json_encode($shippings);
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
				$insertData['inquiry_name'] = $f1->filter($this->_request->getPost('inquiry_name'));
				$insertData['shipping_desc'] = $f1->filter($this->_request->getPost('shipping_desc'));
				$insertData['shipping_logo'] = $f1->filter($this->_request->getPost('shipping_logo'));
				$insertData['shipping_intro'] = $f1->filter($this->_request->getPost('shipping_intro'));
				$insertData['shipping_svalue'] = $f1->filter($this->_request->getPost('shipping_svalue'));
				
				$f2 = new Seed_Filter_Alnum();
				$insertData['shipping_name'] = $f2->filter($this->_request->getPost('shipping_name'));
				
				$f3 = new Zend_Filter_Int();
				$insertData['order_by'] = intval($this->_request->getPost('order_by'));
				$insertData['is_actived'] = intval($this->_request->getPost('is_actived'));
				$insertData['is_m_actived'] = intval($this->_request->getPost('is_m_actived'));
				$insertData['is_default'] = intval($this->_request->getPost('is_default'));
                
				if (empty($insertData['shipping_name'])){
					Seed_Browser::tip_show('请输入名称！');
				}else if (empty($insertData['shipping_desc'])){
					Seed_Browser::tip_show('请输入说明！');
				}else{
					$shippingM=new Shop_Model_Shipping('shop');
					$shipping_id = $shippingM->insertRow($insertData);
					if($shipping_id){
						if ($insertData['is_default']>0){
							$cond = array();
							$cond['shipping_id <>'.$shipping_id]=null;
							$shippingM->updateRow(array('is_default'=>0),$cond);
						}
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/shipping/index');
					}else{
						Seed_Browser::tip_show('添加失败！',$this->view->seed_BaseUrl.'/shipping/index');
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
				$updateData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$updateData['inquiry_name'] = $f1->filter($this->_request->getPost('inquiry_name'));
				$updateData['shipping_desc'] = $f1->filter($this->_request->getPost('shipping_desc'));
				$updateData['shipping_logo'] = $f1->filter($this->_request->getPost('shipping_logo'));
				$updateData['shipping_intro'] = $f1->filter($this->_request->getPost('shipping_intro'));
				$updateData['shipping_svalue'] = $f1->filter($this->_request->getPost('shipping_svalue'));
				
				$f2 = new Seed_Filter_Alnum();
				$updateData['shipping_name'] = $f2->filter($this->_request->getPost('shipping_name'));
				
				$f3 = new Zend_Filter_Int();
				$shipping_id = $f3->filter($this->_request->getPost('shipping_id'));
				$updateData['order_by'] = intval($this->_request->getPost('order_by'));
				$updateData['is_actived'] = intval($this->_request->getPost('is_actived'));
				$updateData['is_m_actived'] = intval($this->_request->getPost('is_m_actived'));
				$updateData['is_default'] = intval($this->_request->getPost('is_default'));
				
				if ($shipping_id<1){
					Seed_Browser::tip_show('关键数据错误！');
				}elseif (empty($updateData['shipping_name'])){
					Seed_Browser::tip_show('请输入名称！');
				}elseif (empty($updateData['shipping_desc'])){
					Seed_Browser::tip_show('请输入说明！');
				}else{
					$shippingM=new Shop_Model_Shipping('shop');
					if($shippingM->updateRow($updateData,array('shipping_id'=>$shipping_id))){
					   if ($updateData['is_default']>0){
							$cond = array();
							$cond['shipping_id <>'.$shipping_id]=null;
							$shippingM->updateRow(array('is_default'=>0),$cond);
						}
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/shipping/index');
					}else{
						Seed_Browser::tip_show('修改失败！',$this->view->seed_BaseUrl.'/shipping/index');
					}
				}
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		$shipping_id = $this->_request->getParam('shipping_id');
		$shippingM = new Shop_Model_Shipping('shop');
		$this->view->shipping = $shippingM->fetchRow(array('shipping_id'=>$shipping_id));
	}
	
	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Int();
				$shipping_ids = $this->_request->getPost('shipping_id');				
				if(count($shipping_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				
				$shippingM=new Shop_Model_Shipping('shop');
				$shippingParamM=new Shop_Model_ShippingParam('shop');
				$shippingRegionM=new Shop_Model_ShippingRegion('shop');
				$shippingRegionDetailM=new Shop_Model_ShippingRegionDetail('shop');
				foreach ($shipping_ids as $shipping_id){
					$shipping_id = intval($shipping_id);
					if($shipping_id>0){
						$shipping = $shippingM->fetchRow(array('shipping_id'=>$shipping_id));
						if($shippingM->deleteRow(array('shipping_id'=>$shipping_id))){
							$shippingParamM->deleteRow(array('shipping_name'=>$shipping['shipping_name']));
							
							$regions = $shippingRegionM->fetchRows(null,array('shipping_name'=>$shipping['shipping_name']));
							foreach ($regions as $region){
								$shippingRegionDetailM->deleteRow(array('sr_id'=>$region['sr_id']));
								$shippingRegionM->deleteRow(array('sr_id'=>$region['sr_id']));
							}
						}
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/shipping/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		$shipping_ids = $this->_request->getParam('shipping_ids');
	    if(empty($shipping_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$shipping_ids = explode(',',$shipping_ids);
		$f3 = new Zend_Filter_Int();
		$myshipping_arr=array();
		foreach ($shipping_ids as $shipping_id)
		{
			$shipping_id = $f3->filter($shipping_id);
			if($shipping_id>0)
					$myshipping_arr[]=$shipping_id;
		}
		if(count($myshipping_arr)>0){
			$shippingM=new Shop_Model_Shipping('shop');
			$shippings = $shippingM->fetchRowsByIds('shipping_id',$myshipping_arr);
	   		$this->view->shippings = $shippings;
		}
	}
}