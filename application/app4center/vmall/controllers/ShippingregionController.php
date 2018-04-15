<?php
class ShippingregionController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$shipping_name = $this->_request->getParam('shipping_name');
		$shippingM = new Shop_Model_Shipping('shop');
		$shipping = $shippingM->fetchRow(array('shipping_name'=>$shipping_name));
		if($shipping['shipping_id']<1)throw new Exception('配送方式参数错误！');
		$this->view->shipping = $shipping;
		$this->view->shipping_name = $shipping_name;
		$shippingregionM = new Shop_Model_ShippingRegion('shop');
		$this->view->shippingregions = $shippingregionM->fetchRows(null,array('shipping_name'=>$shipping_name));
	}
	
	function addAction()
	{
		if ($this->_request->isPost()) { 
			try{
				$insertData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$insertData['sr_name'] = $f1->filter($this->_request->getPost('sr_name'));
				$insertData['sr_desc'] = $f1->filter($this->_request->getPost('sr_desc'));
				$insertData['shipping_name'] = $f1->filter($this->_request->getPost('shipping_name'));
				$shipping_name = $insertData['shipping_name'];
				
				$f2 = new Zend_Filter_Int();
				
				$reg_ids = $this->_request->getPost('reg_ids');
				
				if(empty($insertData['shipping_name'])){
					Seed_Browser::tip_show('配送方式参数错误！');
				}elseif(empty($insertData['sr_name'])){
					Seed_Browser::tip_show('请输入名称！');
				}elseif(empty($insertData['sr_desc'])){
					Seed_Browser::tip_show('请输入说明！');
				}elseif(!is_array($reg_ids) || count($reg_ids)==0){
					Seed_Browser::tip_show('没有选择地区！');
				}else{
					//序列号参数
					$shippingparamM = new Shop_Model_ShippingParam('shop');
					$shippingparams = $shippingparamM->fetchRows(null,array('shipping_name'=>$insertData['shipping_name']),'order_by asc');
					$myparams=array();
					foreach ($shippingparams as $param){
						$myparams[$param['setting_variable']] = trim($this->_request->getPost($param['setting_variable']));
					}
					$insertData['sr_configure'] = serialize($myparams);
					
					$shippingregionM = new Shop_Model_ShippingRegion('shop');
					if($sr_id = $shippingregionM->insertRow($insertData)){
						//过滤重复的地区
						$myregions = array();
						$regionM = new Seed_Model_Region('system');
						foreach ($reg_ids as $reg_id){
							$reg_id = $f2->filter($reg_id);
							$myregions[]=$reg_id;
						}
						$myregions = array_unique($myregions);
						
						$insertData2 = array();
						$insertData2['sr_id']=$sr_id;
						$shippingregiondetailM = new Shop_Model_ShippingRegionDetail('shop');
						foreach ($myregions as $k=>$reg_id){
							$regionM->_parent_nav = array();
							$parents = $regionM->getParentNav($reg_id);
							$flag=true;
							foreach ($parents as $row){
								if($row['reg_id']!= $reg_id && in_array($row['reg_id'],$myregions)){
									$flag=false;
								}
							}
							if($flag){
								$insertData2['reg_id']=$reg_id;
								$shippingregiondetailM->insertRow($insertData2);
							}
						}
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/shippingregion/update/shipping_name/'.$shipping_name.'/sr_id/'.$sr_id);
					}else{
						Seed_Browser::tip_show('添加失败！');
					}
				}
				
				exit;
				
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
		$shipping_name = $this->_request->getParam('shipping_name');
		$shippingM = new Shop_Model_Shipping('shop');
		$shipping = $shippingM->fetchRow(array('shipping_name'=>$shipping_name));
		if($shipping['shipping_id']<1)throw new Exception('配送方式参数错误！');
		$this->view->shipping = $shipping;
		$this->view->shipping_name = $shipping_name;
		$shippingparamM = new Shop_Model_ShippingParam('shop');
		$this->view->shippingparams = $shippingparamM->fetchRows(null,array('shipping_name'=>$shipping_name),'order_by asc');
	}
	
	function updateAction()
	{
		if ($this->_request->isPost()) { 
			try{
				$updateData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$updateData['sr_name'] = $f1->filter($this->_request->getPost('sr_name'));
				$updateData['sr_desc'] = $f1->filter($this->_request->getPost('sr_desc'));
				$shipping_name = $f1->filter($this->_request->getPost('shipping_name'));
				
				$f2 = new Zend_Filter_Int();
				$sr_id = $f2->filter($this->_request->getPost('sr_id'));
				
				$reg_ids = $this->_request->getPost('reg_ids');
				
				if($sr_id<1){
					Seed_Browser::tip_show('配送地区参数错误！');
				}elseif(empty($updateData['sr_name'])){
					Seed_Browser::tip_show('请输入名称！');
				}elseif(empty($updateData['sr_desc'])){
					Seed_Browser::tip_show('请输入说明！');
				}elseif(!is_array($reg_ids) || count($reg_ids)==0){
					Seed_Browser::tip_show('没有选择地区！');
				}else{
					//序列号参数
					$shippingparamM = new Shop_Model_ShippingParam('shop');
					$shippingparams = $shippingparamM->fetchRows(null,array('shipping_name'=>$shipping_name),'order_by asc');
					$myparams=array();
					foreach ($shippingparams as $param){
						$myparams[$param['setting_variable']] = trim($this->_request->getPost($param['setting_variable']));
					}
					$updateData['sr_configure'] = serialize($myparams);
					
					$shippingregionM = new Shop_Model_ShippingRegion('shop');
					if($shippingregionM->updateRow($updateData,array('sr_id'=>$sr_id))){
						//过滤重复的地区
						$myregions = array();
						$regionM = new Seed_Model_Region('system');
						foreach ($reg_ids as $reg_id){
							$reg_id = $f2->filter($reg_id);
							$myregions[]=$reg_id;
						}
						$myregions = array_unique($myregions);
						
						$insertData2 = array();
						$insertData2['sr_id']=$sr_id;
						$shippingregiondetailM = new Shop_Model_ShippingRegionDetail('shop');
						$shippingregiondetailM->deleteRow(array('sr_id'=>$sr_id));
						foreach ($myregions as $k=>$reg_id){
							$regionM->_parent_nav = array();
							$parents = $regionM->getParentNav($reg_id);
							$flag=true;
							foreach ($parents as $row){
								if($row['reg_id']!= $reg_id && in_array($row['reg_id'],$myregions)){
									$flag=false;
								}
							}
							if($flag){
								$insertData2['reg_id']=$reg_id;
								$shippingregiondetailM->insertRow($insertData2);
							}
						}
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/shippingregion/update/shipping_name/'.$shipping_name.'/sr_id/'.$sr_id);
					}else{
						Seed_Browser::tip_show('修改失败！');
					}
				}
				
				exit;
				
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
		
		$sr_id = $this->_request->getParam('sr_id');
		$shippingregionM = new Shop_Model_ShippingRegion('shop');
		$shippingregion = $shippingregionM->fetchRow(array('sr_id'=>$sr_id));
		if($shippingregion['sr_id']<1)throw new Exception('配送地区参数错误！');
		$this->view->shippingregion = $shippingregion;
		$this->view->shippingregionconfigure = unserialize($shippingregion['sr_configure']);
		
		$shippingregiondetailM = new Shop_Model_ShippingRegionDetail('shop');
		$regionM = new Seed_Model_Region('system');
		$srs = $shippingregiondetailM->fetchrows(null,array('sr_id'=>$sr_id));
		$shippingregiondetails=array();
		foreach ($srs as $k=>$sr){
			$shippingregiondetails[$k]=$regionM->fetchRow(array('reg_id'=>$sr['reg_id']));
			$shippingregiondetails[$k]['sr_id']=$sr_id;
		}
		$this->view->shippingregiondetails = $shippingregiondetails;
		
		$shipping_name = $this->_request->getParam('shipping_name');
		
		$shippingM = new Shop_Model_Shipping('shop');
		$shipping = $shippingM->fetchRow(array('shipping_name'=>$shipping_name));
		if($shipping['shipping_id']<1)throw new Exception('配送方式参数错误！');
		$this->view->shipping = $shipping;
		$shippingparamM = new Shop_Model_ShippingParam('shop');
		$this->view->shippingparams = $shippingparamM->fetchRows(null,array('shipping_name'=>$shipping_name),'order_by asc');
		
	}
	
	function deleteAction()
	{
		if ($this->_request->isPost()) { 
			try{
				$f2 = new Zend_Filter_Int();
				$sr_id = $f2->filter($this->_request->getPost('sr_id'));
				if($sr_id<1){
					Seed_Browser::tip_show('配送地区参数错误！');
				}else{
					$shippingregionM = new Shop_Model_ShippingRegion('shop');
					$my = $shippingregionM->fetchRow(array('sr_id'=>$sr_id));
					if($shippingregionM->deleteRow(array('sr_id'=>$sr_id))){
						$shippingregiondetailM = new Shop_Model_ShippingRegionDetail('shop');
						$shippingregiondetailM->deleteRow(array('sr_id'=>$sr_id));
						Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/shippingregion/index/shipping_name/'.$my['shipping_name']);
					}else{
						Seed_Browser::tip_show('删除失败！');
					}
				}
				
				exit;
				
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
		$sr_id = $this->_request->getParam('sr_id');
		$shippingregionM = new Shop_Model_ShippingRegion('shop');
		$shippingregion = $shippingregionM->fetchRow(array('sr_id'=>$sr_id));
		if($shippingregion['sr_id']<1)throw new Exception('配送地区参数错误！');
		$this->view->shippingregion = $shippingregion;
		$this->view->shippingregionconfigure = unserialize($shippingregion['sr_configure']);
		
		$shippingregiondetailM = new Shop_Model_ShippingRegionDetail('shop');
		$regionM = new Seed_Model_Region('system');
		$srs = $shippingregiondetailM->fetchrows(null,array('sr_id'=>$sr_id));
		$shippingregiondetails=array();
		foreach ($srs as $k=>$sr){
			$shippingregiondetails[$k]=$regionM->fetchRow(array('reg_id'=>$sr['reg_id']));
			$shippingregiondetails[$k]['sr_id']=$sr_id;
		}
		$this->view->shippingregiondetails = $shippingregiondetails;
		
		$shipping_name = $this->_request->getParam('shipping_name');
		
		$shippingM = new Shop_Model_Shipping('shop');
		$shipping = $shippingM->fetchRow(array('shipping_name'=>$shipping_name));
		if($shipping['shipping_id']<1)throw new Exception('配送方式参数错误！');
		$this->view->shipping = $shipping;
		$shippingparamM = new Shop_Model_ShippingParam('shop');
		$this->view->shippingparams = $shippingparamM->fetchRows(null,array('shipping_name'=>$shipping_name),'order_by asc');
		
	}
}