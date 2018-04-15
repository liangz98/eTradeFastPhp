<?php
class FileController extends Shop_Controller_Action 
{
	function indexAction()
	{
		$fileM = new Seed_Model_File('system');
		
		//查询条件
		$conditions = array();
		if($this->_request->getParam('file_id') != '')
		$conditions['file_id'] = trim($this->_request->getParam('file_id'));
		if($this->_request->getParam('user_name') != '')
		$conditions['user_name'] = trim($this->_request->getParam('user_name'));
		if($this->_request->getParam('cate_name') != '')
		$conditions['cate_name'] = trim($this->_request->getParam('cate_name'));
		
		//调整分页
		$perpage = 15;
		$page = intval($this->_request->getParam('page'));
		$total = $fileM->fetchRowsCount($conditions);
		$pageObj = new Seed_Page($this->_request, $total, $perpage);
		$this->view->page = $pageObj->getPageArray();
		if($page>$this->view->page['totalpage']) $page=$this->view->page['totalpage'];
		if($page<1)$page=1;
		
		$this->view->files = $fileM->fetchRows(array(($page-1)*$perpage,$perpage), $conditions, 'upload_time desc');
		$this->view->conditions = $conditions;
		
		$fileCateM = new Seed_Model_FileCategory('system');
		$this->view->cates = $fileCateM->fetchRows();
	}
	
	function deleteAction()
	{
		//AJAX POST
	   if($this->_request->isPost())
	   {
			try 
			{
				$file_ids = $this->_request->getPost('file_id');
				if(count($file_ids)==0){
					Seed_Browser::tip_show('没有选择相关数据！');
				}
				$fileM = new Seed_Model_File('system');
				
				$f3 = new Zend_Filter_Int();
				foreach ($file_ids as $file_id)
				{
					$file_id = $f3->filter($file_id);
					if($file_id>0)
						Seed_Browser::view_page($this->view->seed_Setting['upload_app_server']."/file/delete?file_id=".$file_id."&token=".$this->view->seed_Token);
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/file/index');
			}
			catch (Exception $e)
			{
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
	   $file_ids = $this->_request->getParam('file_ids');
	   if(empty($file_ids))
	    {
			Seed_Browser::tip_show('没有选择相关数据！');
		}
		$file_ids = explode(',',$file_ids);
		$f3 = new Zend_Filter_Int();
		$myfile_arr=array();
		foreach ($file_ids as $file_id)
		{
			$file_id = $f3->filter($file_id);
			if($file_id>0)
					$myfile_arr[]=$file_id;
		}
		$fileM = new Seed_Model_File('system');
		if(count($myfile_arr)>0)
			$files = $fileM->fetchRowsByIds('file_id',$myfile_arr);
	   $this->view->files = $files;
	}
	
	
	function dodeleteAction()
	{
		$file_id = intval($this->_request->getParam('file_id'));
		if($file_id<1)exit('0');
		$fileM = new Seed_Model_File('system');
		Seed_Browser::view_page($this->view->seed_Setting['upload_app_server']."/file/delete?file_id=".$file_id."&token=".$this->view->seed_Token);
		echo('1');
		exit;
	}
	
	function exportflashAction(){
		$cate_id = intval($this->_request->getParam('cate_id'));
		if($cate_id<1)Seed_Browser::error("分类参数不能为空！");
		
		$newsM = new News_Model_News('news');
		$newsM->updateRow(array('export_status'=>'0'),array('cate_id'=>$cate_id));
		
		Seed_Browser::redirect("导出准备中……",$this->view->seed_BaseUrl.'/file/doexportflash?cate_id='.$cate_id);
		exit;
	}
	
	function doexportflashAction(){
		$cate_id = intval($this->_request->getParam('cate_id'));
		if($cate_id<1)Seed_Browser::error("分类参数不能为空！");
		
		$newsM = new News_Model_News('news');
		$news = $newsM->fetchRow(array('cate_id'=>$cate_id,'export_status'=>'0'));
		if($news['news_id']<1)Seed_Browser::error("导出完成！");
		
		$path = realpath(SEED_WWW_ROOT."/../../public4att");
		$source_name = $path.$news['news_flash'];
		$news['news_title'] = @iconv("utf-8","gbk",$news['news_title']);
		$target_name = $path."/files/export/".$cate_id."/".$news['news_title'].".swf";
		copy($source_name,$target_name);
		$newsM->updateRow(array('export_status'=>'1'),array('news_id'=>$news['news_id']));
		Seed_Browser::redirect("导出继续中……",$this->view->seed_BaseUrl.'/file/doexportflash?cate_id='.$cate_id);
		exit;
	}
}
?>