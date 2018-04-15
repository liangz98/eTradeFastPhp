<?php
class ChartattrController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$chartAttrM = new Seed_Model_ChartAttr('system');
		$attrs = $chartAttrM->fetchRows(null,array(),'order_by ASC');
		$this->view->attrs = $attrs;
	}

	function addAction()
	{
		if ($this->_request->isPost())
		{
			try
			{
				$f1 = new Zend_Filter();
				$f1->addFilter(new Zend_Filter_StripTags())
				   ->addFilter(new Zend_Filter_StripNewlines());
				$f2 = new zend_filter();
				$f2->addFilter(new Zend_Filter_Int());
				$f3 = new Zend_Filter();
				$f3->addFilter(new Zend_Filter_StripTags());

				$insertData = array();
				$insertData['attr_name'] = $f1->filter($this->_request->getPost('attr_name'));
				$insertData['field_name'] = $f1->filter($this->_request->getPost('field_name'));
				$insertData['field_type'] = $f1->filter($this->_request->getPost('field_type'));
				$insertData['field_length'] = $f2->filter($this->_request->getPost('field_length'));
				$insertData['attr_input_type'] = $f2->filter($this->_request->getPost('attr_input_type'));
				$insertData['attr_values'] = $f3->filter($this->_request->getPost('attr_values'));
				$insertData['attr_displays'] = $this->_request->getPost('attr_displays');

				if (empty($insertData['attr_name']))
				{
					Seed_Browser::tip_show('属性名称不能为空！');
				}else{
				    if ($insertData['attr_input_type'] == 0 || $insertData['attr_input_type']== 1)
				        $insertData['attr_values'] = '' ;
					$chartAttrM = new Seed_Model_ChartAttr('system');
					$attr_id = $chartAttrM->insertRow($insertData);
					if ($attr_id>0)
					{
						Seed_Browser::tip_show("添加数据成功!", $this->view->baseUrl().'/chartattr/index');
					}
					else
					{
						Seed_Browser::tip_show('数据错误！！');
					}
				}
			}
			catch (Exception $e)
			{
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
	}


	function updateAction()
	{
		if ($this->_request->isPost()) {
			try{
				$f1 = new Zend_Filter();
				$f1->addFilter(new Zend_Filter_StripTags())
				   ->addFilter(new Zend_Filter_StripNewlines());
				$f2 = new zend_filter();
				$f2->addFilter(new Zend_Filter_Int());
				$f3 = new Zend_Filter();
				$f3->addFilter(new Zend_Filter_StripTags());

				$updateData = array();
				$updateData['attr_name'] = $f1->filter($this->_request->getPost('attr_name'));
				$updateData['attr_input_type'] = $f2->filter($this->_request->getPost('attr_input_type'));
				$updateData['attr_values'] = $f3->filter($this->_request->getPost('attr_values'));
				$updateData['attr_displays'] = $this->_request->getPost('attr_displays');

				$is_ext = $f2->filter($this->_request->getPost('is_ext'));
				$attr_id = $f2->filter($this->_request->getPost('attr_id'));

				if (empty($updateData['attr_name']))
				{
					Seed_Browser::tip_show('属性名称不能为空！');
				}elseif ($attr_id<1){
					Seed_Browser::tip_show('参数错误！');
				}else{
				    if ($updateData['attr_input_type'] == 0 || $updateData['attr_input_type']== 1) $updateData['attr_values'] = '' ;

					$chartAttrM = new Seed_Model_ChartAttr('system');
					$rs = $chartAttrM->updateRow($updateData, array('attr_id' => $attr_id));
					if ($rs)
					{
						Seed_Browser::tip_show("编辑数据成功！", $this->view->baseUrl().'/chartattr/index');
					}
					else
					{
						Seed_Browser::tip_show('编辑数据失败！');
					}
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
		$attr_id=intval($this->_request->getParam('attr_id'));
		if($attr_id<1)Seed_Browser::error('参数错误！');
		$chartAttrM = new Seed_Model_ChartAttr('system');
		$attribute = $chartAttrM->fetchRow(array('attr_id'=>$attr_id));
		if($attribute['attr_id']<1)Seed_Browser::error('没有找到相关数据！');
		$this->view->attribute = $attribute;
	}

	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Int();
				$attr_ids = $this->_request->getPost('attr_id');
				if(count($attr_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}

				$chartAttrM = new Seed_Model_ChartAttr('system');
				foreach ($attr_ids as $attr_id){
					$attr_id = $f3->filter($attr_id);
					if($attr_id>0){
						$chartAttrM->deleteRow(array('attr_id'=>$attr_id));
					}
				}

				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/chartattr/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}

		$attr_ids = $this->_request->getParam('attr_ids');
	    if(empty($attr_ids))
	    {
			Seed_Browser::error('找不到相关的数据！');
		}
		$attr_ids = explode(',',$attr_ids);
		$f3 = new Zend_Filter_Int();
		$myattrs_arr=array();
		foreach ($attr_ids as $attr_id)
		{
			$attr_id = $f3->filter($attr_id);
			if($attr_id>0)
					$myattrs_arr[]=$attr_id;
		}
		if(count($myattrs_arr)>0){
			$chartAttrM = new Seed_Model_ChartAttr('system');
			$attributes = $chartAttrM->fetchRowsByIds('attr_id',$myattrs_arr);
	   		$this->view->attributes = $attributes;
		}
	}

	function orderAction() {
		//AJAX POST
		if ($this->_request->isPost()) {
			try{
				$f3 = new Zend_Filter_Alnum();

				$attr_ids = $this->_request->getPost('attr_ids');
				$order_bys = $this->_request->getPost('order_bys');
				$updateData=array();
				$chartAttrM = new Seed_Model_ChartAttr('system');
				if(is_array($attr_ids)){
					foreach ($attr_ids as $k=>$attr_id){
						$attr_id = $f3->filter($attr_id);
						$updateData['order_by'] = $f3->filter($order_bys[$k]);
						$chartAttrM->updateRow($updateData,array('attr_id'=>$attr_id));
					}
				}
				Seed_Browser::tip_show('排序成功！',$this->view->seed_BaseUrl.'/chartattr/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
}
?>