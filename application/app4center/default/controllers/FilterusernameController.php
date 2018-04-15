<?php
class FilterusernameController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$filterUsernameM = new Seed_Model_FilterUsername('system');

		$conditions = array();
		//查询条件
		if($this->_request->getParam('is_actived')!='-1' && trim($this->_request->getParam('is_actived'))!='')
			$conditions['is_actived']=$this->_request->getParam('is_actived');
		if(trim($this->_request->getParam('keyword'))!='')
			$conditions['keyword']=trim($this->_request->getParam('keyword'));
	   		
		$perpage=15;
    	$page=intval($this->_request->getParam('page'));
    	$total = $filterUsernameM->fetchRowsCount($conditions);
    	$pageObj = new Seed_Page($this->_request,$total,$perpage);
    	$this->view->page = $pageObj->getPageArray();
	   	if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
    	if($page<1)$page=1;
		$filters = $filterUsernameM->fetchRows(array(($page-1)*$perpage,$perpage),$conditions,'filter_id DESC');
		$this->view->filters = $filters;
		$this->view->conditions = $conditions;
	}
	
	function addAction()
	{
		if ($this->_request->isPost()) { 
			try{
				$f2=new Zend_Filter_Int();
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
					
				$updateData = array();
				$insertData['keyword']=$f3->filter($this->_request->getPost('keyword'));
				$insertData['filter_reason']=$f3->filter($this->_request->getPost('filter_reason'));
				$insertData['is_actived']=intval($this->_request->getPost('is_actived'));
				
				if(empty($insertData['keyword'])){
					Seed_Browser::tip_show('关键词不能为空！');
				}elseif(empty($insertData['filter_reason'])){
					Seed_Browser::tip_show('过滤原因不能为空！');
				}else{
					$filterUsernameM = new Seed_Model_FilterUsername('system');
					$check = $filterUsernameM->fetchRow(array('keyword'=>$insertData['keyword']));
					if($check['filter_id']>0){
						Seed_Browser::tip_show('关键词已经存在！');
					}
					if($filterUsernameM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl."/filterusername/index");
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
		$filterUsernameM = new Seed_Model_FilterUsername('system');
		if ($this->_request->isPost()) { 
			try{
				$f4 = new Zend_Filter( );
				$f4->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$filter_id=intval($this->_request->getPost('filter_id'));
				$filterDetail = $filterUsernameM->fetchRow(array('filter_id'=>$filter_id));
				if($filterDetail['filter_id']<1){
					Seed_Browser::tip_show("关键数据错误！");
				}
				
				$updateData = array();
				$updateData['keyword']=$f4->filter($this->_request->getPost('keyword'));
				$updateData['filter_reason']=$f4->filter($this->_request->getPost('filter_reason'));
				$updateData['is_actived']=intval($this->_request->getPost('is_actived'));
				
				if($filterUsernameM->updateRow($updateData,array('filter_id'=>$filter_id))){
					Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/filterusername/update?filter_id=".$filter_id);
	    		}else{
	    			Seed_Browser::tip_show('修改失败！');
	    		}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
		
		$filter_id=intval($this->_request->getParam('filter_id'));
		$filter = $filterUsernameM->fetchRow(array('filter_id'=>$filter_id));
		if($filter['filter_id']<1)Seed_Browser::error('没有找到相关数据！');
		$this->view->filter = $filter;
	}
	
	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Int();
				$filter_ids = $this->_request->getPost('filter_id');				
				if(count($filter_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				
				$filterUsernameM=new Seed_Model_FilterUsername('system');
				foreach ($filter_ids as $filter_id){
					$filter_id = $f3->filter($filter_id);
					if($filter_id>0){
						$filterUsernameM->deleteRow(array('filter_id'=>$filter_id));
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/filterusername/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		$filter_ids = $this->_request->getParam('filter_ids');
	    if(empty($filter_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$filter_ids = explode(',',$filter_ids);
		$f3 = new Zend_Filter_Int();
		$myfilters_arr=array();
		foreach ($filter_ids as $filter_id)
		{
			$filter_id = $f3->filter($filter_id);
			if($filter_id>0)
					$myfilters_arr[]=$filter_id;
		}
		if(count($myfilters_arr)>0){
			$filterUsernameM = new Seed_Model_FilterUsername('system');
			$filters = $filterUsernameM->fetchRowsByIds('filter_id',$myfilters_arr);
	   		$this->view->filters = $filters;
		}
	}
	
	function importAction(){
		try
		{
			if ($this->_request->isPost()) 
			{
				$myfileM = new Seed_File();
				$myfileM->setPostfix('xls');
				if(empty($_FILES['file']))
				{
					Seed_Browser::error('文件不能为空！');
				}
				$rs = $myfileM->upload($_FILES['file'],SEED_TEMP_ROOT);//上传文件
				if($rs)
				{
					$filePath = SEED_TEMP_ROOT."/".$rs;
					set_include_path(SEED_LIB_ROOT.'/Plugin/'
			    	. PATH_SEPARATOR . get_include_path());
			    	/** PHPExcel */
					require_once SEED_LIB_ROOT.'/Plugin/PHPExcel.php';
					
					/** PHPExcel_IOFactory */
					require_once SEED_LIB_ROOT.'/Plugin/PHPExcel/IOFactory.php';
					
					// Create new PHPExcel object
					$PHPExcel = new PHPExcel();
					
					$PHPReader = new PHPExcel_Reader_Excel2007();                  
					if(!$PHPReader->canRead($filePath)){
					    $PHPReader = new PHPExcel_Reader_Excel5(); 
					    if(!$PHPReader->canRead($filePath)){
					        Seed_Browser::tip_show("ERROR:".$filePath);
					    }
					}
					$PHPExcel = $PHPReader->load($filePath);
					$currentSheet = $PHPExcel->getSheet(0);
					/**取得一共有多少列*/
					$allColumn = $currentSheet->getHighestColumn();
					/**取得一共有多少行*/
					$allRow = $currentSheet->getHighestRow();
					$myresult="";	
					
					$f1 = new Zend_Filter( );
					$f1->addFilter(new Zend_Filter_StripTags())
						->addFilter(new Seed_Filter_EscapeQuotes());
					
					$filterUsernameM = new Seed_Model_FilterUsername('system');
					for($currentRow = 2;$currentRow<=$allRow;$currentRow++)
					{
						$myerror=0;
						$filterData = array();
						$filterData['is_actived']='1';
						$filterData['keyword'] = $f1->filter(trim($currentSheet->getCell('A'.$currentRow)->getValue()));
						if(empty($filterData['keyword'])){
							$myerror=1;
							$myresult.="<p style=\"color:#ff0000;\">第".$currentRow."行禁用用户名输入为空或输入有误！</p>";
							continue;
						}
						$filterData['filter_reason'] = $f1->filter(trim($currentSheet->getCell('B'.$currentRow)->getValue()));
						if(empty($filterData['filter_reason'])){
							$myerror=1;
							$myresult.="<p style=\"color:#ff0000;\">第".$currentRow."行禁用原因输入为空或输入有误！</p>";
							continue;
						}
						
						if($myerror==0){
							$check = $filterUsernameM->fetchRow(array('keyword'=>$filterData['keyword']));
							if($check['filter_id']>0){
								$filterUsernameM->updateRow($filterData,array('filter_id'=>$check['filter_id']));
							}else{
								$filterUsernameM->insertRow($filterData);
							}
						}
					}
					$this->view->myresult = $myresult;	
				}
			}
		}
		catch (Exception $e) 
		{
			Seed_Browser::tip_show($e->getMessage());
		}
	}
}
	