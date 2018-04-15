<?php
class BranchController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$branchM = new Home_Model_Branch('home');
		$branchRegionM = new Home_Model_BranchRegion('home');

		$conditions = array();
		$branch_type=trim($this->_request->getParam('branch_type'));
		$branch_region=trim($this->_request->getParam('branch_region'));
		if($branch_type!='-1' && $branch_type!='')
			$conditions['branch_type']=$branch_type;
		if($branch_region!='-1' && $branch_region!='')
			$conditions['branch_region']=$branch_region;

		$branchs = $branchM->fetchRows(null,$conditions,'order_by ASC');
		foreach ($branchs as $k=>$branch){
			$my = $branchRegionM->fetchRow(array('br_id'=>$branch['branch_region']));
			$branchs[$k]['br_name']=$my['br_name'];
		}
		$this->view->branchs = $branchs;

		$this->view->branch_type = $branch_type;
		$this->view->branch_region = $branch_region;
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
				$f4 = new Seed_Filter_Text();

				$insertData = array();
				$insertData['branch_name']=$f3->filter($this->_request->getPost('branch_name'));
				$insertData['branch_mark']=$f1->filter($this->_request->getPost('branch_mark'));
				$insertData['branch_m_image']=$f3->filter($this->_request->getPost('branch_m_image'));
				$insertData['branch_image']=$insertData['branch_m_image'];
				$insertData['branch_telephone']=$f3->filter($this->_request->getPost('branch_telephone'));
				$insertData['branch_address']=$f3->filter($this->_request->getPost('branch_address'));
				$insertData['branch_time']=$f3->filter($this->_request->getPost('branch_time'));

				$insertData['branch_m_desc']=$f4->filter($this->_request->getPost('branch_m_desc'));
				$insertData['branch_desc']=$insertData['branch_m_desc'];

				$branch_m_content = $f4->filter($this->_request->getPost('branch_m_content'));
				if(isset($this->view->seed_Setting['upload_view_server'])){
					$upload_view_server=str_replace("/","\/",$this->view->seed_Setting['upload_view_server']);
					$patterns=array();
					$replacements=array();
					$patterns[]='/'.$upload_view_server.'/';
					$replacements[]='{SEED_UPLOAD_SERVER}';

					$insertData['branch_m_content'] = preg_replace($patterns, $replacements, $branch_m_content);
				}else{
					$insertData['branch_m_content'] = $branch_m_content;
				}
				$insertData['branch_content'] = $insertData['branch_m_content'];

				$location_y = strip_tags(trim($this->getRequest()->getPost('location_y')));
           		$location_x = strip_tags(trim($this->getRequest()->getPost('location_x')));
	            if(!empty($location_x) && !empty($location_y)){
	            	$insertData['location_y'] = $location_y;
		            $insertData['location_x'] = $location_x;
	            }

				$insertData['branch_region']=intval($this->_request->getPost('branch_region'));
				$insertData['order_by']=intval($this->_request->getPost('order_by'));
				$insertData['is_m_actived']=intval($this->_request->getPost('is_m_actived'));
				$insertData['is_actived']=$insertData['is_m_actived'];
				$insertData['add_time']=time();
				
				$insertData['html_title']=$insertData['branch_name'];
				$insertData['html_keywords']=$insertData['branch_name'];
				$insertData['html_description']=$insertData['branch_name'];

				if(empty($insertData['branch_mark']))$insertData['branch_mark']=$insertData['add_time'];

				if(empty($insertData['branch_name'])){
					Seed_Browser::tip_show('分店名称不能为空！');
				}elseif(empty($insertData['branch_mark'])){
					Seed_Browser::tip_show('分店标记不能为空！');
				}else{
					$branchM = new Home_Model_Branch('home');
					$check = $branchM->fetchRow(array('branch_name'=>$insertData['branch_name']));
					if($check['branch_id']>0){
						Seed_Browser::tip_show('分店名称已经存在！');
					}
					$check = $branchM->fetchRow(array('branch_mark'=>$insertData['branch_mark']));
					if($check['branch_id']>0){
						Seed_Browser::tip_show('分店标记已经存在！');
					}
					if($branch_id = $branchM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl."/branch/index");
		    		}else{
		    			Seed_Browser::tip_show('添加失败！');
		    		}
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
		$branchRegionM = new Home_Model_BranchRegion('home');
		$this->view->regionoptions = $branchRegionM->getParentOption(0);
	}

	function updateAction()
	{
		if ($this->_request->isPost()) {
			try{
				$branchM = new Home_Model_Branch('home');
				$f1=new Seed_Filter_Alnum();
				$f2=new Zend_Filter_Int();
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f4 = new Seed_Filter_Text();
				$branch_id=intval($this->_request->getPost('branch_id'));
				$branch = $branchM->fetchRow(array('branch_id'=>$branch_id));
				if($branch['branch_id']<1){
					Seed_Browser::tip_show("关键数据错误！");
				}

				$updateData = array();
				$updateData['branch_name']=$f3->filter($this->_request->getPost('branch_name'));
				$updateData['branch_mark']=$f1->filter($this->_request->getPost('branch_mark'));
				$updateData['branch_m_image']=$f3->filter($this->_request->getPost('branch_m_image'));
				$updateData['branch_telephone']=$f3->filter($this->_request->getPost('branch_telephone'));
				$updateData['branch_address']=$f3->filter($this->_request->getPost('branch_address'));
				$updateData['branch_time']=$f3->filter($this->_request->getPost('branch_time'));

				$updateData['branch_m_desc']=$f4->filter($this->_request->getPost('branch_m_desc'));

				$branch_m_content = $f4->filter($this->_request->getPost('branch_m_content'));
				if(isset($this->view->seed_Setting['upload_view_server'])){
					$upload_view_server=str_replace("/","\/",$this->view->seed_Setting['upload_view_server']);
					$patterns=array();
					$replacements=array();
					$patterns[]='/'.$upload_view_server.'/';
					$replacements[]='{SEED_UPLOAD_SERVER}';

					$updateData['branch_m_content'] = preg_replace($patterns, $replacements, $branch_m_content);
				}else{
					$updateData['branch_m_content'] = $branch_m_content;
				}

				$location_y = strip_tags(trim($this->getRequest()->getPost('location_y')));
                $location_x = strip_tags(trim($this->getRequest()->getPost('location_x')));
	            if(!empty($location_x) && !empty($location_y)){
                    $updateData['location_y'] = $location_y;
		            $updateData['location_x'] = $location_x;
	            }

				$updateData['branch_region']=intval($this->_request->getPost('branch_region'));
				$updateData['order_by']=intval($this->_request->getPost('order_by'));
				$updateData['is_m_actived']=intval($this->_request->getPost('is_m_actived'));

				if(empty($updateData['branch_mark']))$updateData['branch_mark']=$branch['add_time'];

				if(empty($updateData['branch_name'])){
					Seed_Browser::tip_show('分店名称不能为空！');
				}elseif(empty($updateData['branch_mark'])){
					Seed_Browser::tip_show('分店标记不能为空！');
				}else{
					$check = $branchM->fetchRow(array('branch_name'=>$updateData['branch_name']));
					if($check['branch_id']>0 && $check['branch_id']!=$branch_id){
						Seed_Browser::tip_show('分店名称已经存在！');
					}
					$check = $branchM->fetchRow(array('branch_mark'=>$updateData['branch_mark']));
					if($check['branch_id']>0 && $check['branch_id']!=$branch_id){
						Seed_Browser::tip_show('分店标记已经存在！');
					}
					if($branchM->updateRow($updateData,array('branch_id'=>$branch_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/branch/index");
		    		}else{
		    			Seed_Browser::tip_show('修改失败！');
		    		}
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}

		$branchM = new Home_Model_Branch('home');
		$branch_id=intval($this->_request->getParam('branch_id'));
		$branch = $branchM->fetchRow(array('branch_id'=>$branch_id));
		if($branch['branch_id']<1)Seed_Browser::error('没有找到相关数据！');
		$this->view->branch = $branch;
		$branchRegionM = new Home_Model_BranchRegion('home');
		$this->view->regionoptions = $branchRegionM->getParentOption(0);
	}

	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$branch_ids = $this->_request->getPost('branch_id');
				if(count($branch_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}

				$branchM=new Home_Model_Branch('home');
				foreach ($branch_ids as $branch_id){
					$branch_id = intval($branch_id);
					if($branch_id>0){
						$branchM->deleteRow(array('branch_id'=>$branch_id));
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/branch/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}

		$branch_ids = $this->_request->getParam('branch_ids');
	    if(empty($branch_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$branch_ids = explode(',',$branch_ids);
		$f3 = new Zend_Filter_Int();
		$mypages_arr=array();
		foreach ($branch_ids as $branch_id)
		{
			$branch_id = $f3->filter($branch_id);
			if($branch_id>0)
					$mypages_arr[]=$branch_id;
		}
		if(count($mypages_arr)>0){
			$branchM = new Home_Model_Branch('home');
			$branchs = $branchM->fetchRowsByIds('branch_id',$mypages_arr);
	   		$this->view->branchs = $branchs;
		}
	}

	function orderAction() {
		//AJAX POST
		if ($this->_request->isPost()) {
			try{
				$f3 = new Zend_Filter_Alnum();

				$branch_ids = $this->_request->getPost('branch_ids');
				$order_bys = $this->_request->getPost('order_bys');
				$updateData=array();
				$branchM = new Home_Model_Branch('home');
				if(is_array($branch_ids)){
					foreach ($branch_ids as $k=>$branch_id){
						$branch_id = $f3->filter($branch_id);
						$updateData['order_by'] = $f3->filter($order_bys[$k]);
						$branchM->updateRow($updateData,array('branch_id'=>$branch_id));
					}
				}
				Seed_Browser::tip_show('排序成功！',$this->view->seed_BaseUrl.'/branch/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}

	function roleAction() {
		$branchM = new Home_Model_Branch('home');
		$branch_id=intval($this->_request->getParam('branch_id'));
		$branch = $branchM->fetchRow(array('branch_id'=>$branch_id));
		$json_string = json_encode(unserialize($branch['branch_roles']));
        echo $json_string;
        exit;
	}
}
