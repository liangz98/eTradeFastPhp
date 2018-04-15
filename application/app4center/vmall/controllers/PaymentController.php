<?php
class PaymentController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$paymentM = new Shop_Model_Payment('shop');
		$this->view->payments = $paymentM->fetchRows(null,null,'order_by asc');
	}
	
	function addAction() {		
		//AJAX POST
		if ($this->_request->isPost()) { 
			try{
				$insertData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$insertData['payment_desc'] = $f1->filter($this->_request->getPost('payment_desc'));
				$insertData['payment_logo'] = $f1->filter($this->_request->getPost('payment_logo'));
				$insertData['payment_intro'] = $f1->filter($this->_request->getPost('payment_intro'));
				$insertData['payment_interface'] = $f1->filter($this->_request->getPost('payment_interface'));
				$insertData['payment_fee_percent'] = $f1->filter($this->_request->getPost('payment_fee_percent'));
				$f2 = new Seed_Filter_Alnum();
				$insertData['payment_name'] = $f2->filter($this->_request->getPost('payment_name'));
				
				$f3 = new Zend_Filter_Int();
				$insertData['order_by'] = $f3->filter($this->_request->getPost('order_by'));
				$insertData['is_online'] = intval($this->_request->getPost('is_online'));
				$insertData['is_actived'] = intval($this->_request->getPost('is_actived'));
				$insertData['is_front'] = intval($this->_request->getPost('is_front'));
				$insertData['is_mob'] = intval($this->_request->getPost('is_mob'));
				$insertData['is_cod'] = intval($this->_request->getPost('is_cod'));
				$insertData['is_gift'] = intval($this->_request->getPost('is_gift'));
				$insertData['is_withdraw'] = intval($this->_request->getPost('is_withdraw'));
				$insertData['expire_time'] = $f1->filter($this->_request->getPost('expire_time'));
				$f4 = new Seed_Filter_Float();
				$insertData['payment_fee_percent'] = $f4->filter($this->_request->getPost('payment_fee_percent'));
				
				if (empty($insertData['payment_name'])){
					Seed_Browser::tip_show('请输入名称！');
				}if (empty($insertData['payment_desc'])){
					Seed_Browser::tip_show('请输入说明！');
				}if (empty($insertData['payment_fee_percent'])){
					Seed_Browser::tip_show('请输入手续费用！');
				}else{
					$paymentM=new Shop_Model_Payment('shop');
					if($paymentM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/payment/index');
					}else{
						Seed_Browser::tip_show('添加失败！',$this->view->seed_BaseUrl.'/payment/index');
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
				$updateData['payment_desc'] = $f1->filter($this->_request->getPost('payment_desc'));
				$updateData['payment_logo'] = $f1->filter($this->_request->getPost('payment_logo'));
				$updateData['payment_intro'] = $f1->filter($this->_request->getPost('payment_intro'));
				$updateData['payment_interface'] = $f1->filter($this->_request->getPost('payment_interface'));
				$updateData['payment_fee_percent'] = $f1->filter($this->_request->getPost('payment_fee_percent'));
				$f2 = new Seed_Filter_Alnum();
				$updateData['payment_name'] = $f2->filter($this->_request->getPost('payment_name'));
				
				$f3 = new Zend_Filter_Int();
				$payment_id = $f3->filter($this->_request->getPost('payment_id'));
				$updateData['order_by'] = $f3->filter($this->_request->getPost('order_by'));
				$updateData['is_online'] = intval($this->_request->getPost('is_online'));
				$updateData['is_front'] = intval($this->_request->getPost('is_front'));
				$updateData['is_actived'] = intval($this->_request->getPost('is_actived'));
				$updateData['is_mob'] = intval($this->_request->getPost('is_mob'));
				$updateData['is_cod'] = intval($this->_request->getPost('is_cod'));
				$updateData['is_gift'] = intval($this->_request->getPost('is_gift'));
				$updateData['is_withdraw'] = intval($this->_request->getPost('is_withdraw'));
                                $updateData['expire_time'] = $f1->filter($this->_request->getPost('expire_time'));
				$f4 = new Seed_Filter_Float();
				$updateData['payment_fee_percent'] = $f4->filter($this->_request->getPost('payment_fee_percent'));
				
				if ($payment_id<1){
					Seed_Browser::tip_show('关键数据错误！');
				}elseif (empty($updateData['payment_name'])){
					Seed_Browser::tip_show('请输入名称！');
				}elseif (empty($updateData['payment_desc'])){
					Seed_Browser::tip_show('请输入说明！');
				}elseif (empty($updateData['payment_fee_percent'])){
					Seed_Browser::tip_show('请输入手续费用！');
				}else{
					$paymentM=new Shop_Model_Payment('shop');
					if($paymentM->updateRow($updateData,array('payment_id'=>$payment_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/payment/index');
					}else{
						Seed_Browser::tip_show('修改失败！',$this->view->seed_BaseUrl.'/payment/index');
					}
				}
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		$payment_id = $this->_request->getParam('payment_id');
		$paymentM = new Shop_Model_Payment('shop');
		$this->view->payment = $paymentM->fetchRow(array('payment_id'=>$payment_id));
	}
	
	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Int();
				$payment_ids = $this->_request->getPost('payment_id');				
				if(count($payment_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				
				$paymentM=new Shop_Model_Payment('shop');
				$paymentParamM=new Shop_Model_PaymentParam('shop');
				foreach ($payment_ids as $payment_id){
					$payment_id = intval($payment_id);
					if($payment_id>0){
						$payment = $paymentM->fetchRow(array('payment_id'=>$payment_id));
						if($paymentM->deleteRow(array('payment_id'=>$payment_id))){
							$paymentParamM->deleteRow(array('payment_name'=>$payment['payment_name']));
						}
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/payment/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		$payment_ids = $this->_request->getParam('payment_ids');
	    if(empty($payment_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$payment_ids = explode(',',$payment_ids);
		$f3 = new Zend_Filter_Int();
		$mypayment_arr=array();
		foreach ($payment_ids as $payment_id)
		{
			$payment_id = $f3->filter($payment_id);
			if($payment_id>0)
					$mypayment_arr[]=$payment_id;
		}
		if(count($mypayment_arr)>0){
			$paymentM=new Shop_Model_Payment('shop');
			$payments = $paymentM->fetchRowsByIds('payment_id',$mypayment_arr);
	   		$this->view->payments = $payments;
		}
	}
}