<?php
class ImageController extends Shop_Controller_Action 
{
	function indexAction()
	{
		$imageM = new Seed_Model_Image('system');
		
		//查询条件
		$conditions = array();
		if($this->_request->getParam('image_id') != '')
		$conditions['image_id'] = trim($this->_request->getParam('image_id'));
		if($this->_request->getParam('user_name') != '')
		$conditions['user_name'] = trim($this->_request->getParam('user_name'));
		if($this->_request->getParam('cate_name') != '')
		$conditions['cate_name'] = trim($this->_request->getParam('cate_name'));
		
		//调整分页
		$perpage = 15;
		$page = intval($this->_request->getParam('page'));
		$total = $imageM->fetchRowsCount($conditions);
		$pageObj = new Seed_Page($this->_request, $total, $perpage);
		$this->view->page = $pageObj->getPageArray();
		if($page>$this->view->page['totalpage']) $page=$this->view->page['totalpage'];
		if($page<1)$page=1;
		
		$this->view->images = $imageM->fetchRows(array(($page-1)*$perpage,$perpage), $conditions, 'upload_time desc');
		$this->view->conditions = $conditions;
		
		$imageCateM = new Seed_Model_ImageCategory('system');
		$this->view->cates = $imageCateM->fetchRows();
	}
	
	function dodeleteAction()
	{
		$image_id = intval($this->_request->getParam('image_id'));
		if($image_id<1)exit('0');
		$imageM = new Seed_Model_Image('system');
		$url = '';
		if(!preg_match("/http:\/\//isU", $this->view->seed_Setting['upload_app_server'])){
			$url = "http://".$_SERVER['HTTP_HOST'].$this->view->seed_Setting['upload_app_server'];
		}else{
			$url = $this->view->seed_Setting['upload_app_server'];
		}
		$url = $url . "/image/delete?img_id=".$image_id."&token=".$this->view->seed_Token;
		Seed_Browser::view_page($url);
		echo('1');
		exit;
	}
	
	function deleteAction()
	{
		//AJAX POST
	   if($this->_request->isPost())
	   {
			try 
			{
				$image_ids = $this->_request->getPost('image_id');
				if(count($image_ids)==0){
					Seed_Browser::tip_show('没有选择相关数据！');
				}
				$imageM = new Seed_Model_Image('system');
				
				$f3 = new Zend_Filter_Int();
				foreach ($image_ids as $image_id)
				{
					$url = '';
					if(!preg_match("/http:\/\//isU", $this->view->seed_Setting['upload_app_server'])){
						$url = "http://".$_SERVER['HTTP_HOST'].$this->view->seed_Setting['upload_app_server'];
					}else{
						$url = $this->view->seed_Setting['upload_app_server'];
					}
					$url = $url . "/image/delete?img_id=".$image_id."&token=".$this->view->seed_Token;
					Seed_Browser::view_page($url);
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/image/index');
			}
			catch (Exception $e)
			{
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
	   $image_ids = $this->_request->getParam('image_ids');
	   if(empty($image_ids))
	    {
			Seed_Browser::tip_show('没有选择相关数据！');
		}
		$image_ids = explode(',',$image_ids);
		$f3 = new Zend_Filter_Int();
		$myimage_arr=array();
		foreach ($image_ids as $image_id)
		{
			$image_id = $f3->filter($image_id);
			if($image_id>0)
					$myimage_arr[]=$image_id;
		}
		$imageM = new Seed_Model_Image('system');
		if(count($myimage_arr)>0)
			$images = $imageM->fetchRowsByIds('image_id',$myimage_arr);
	   $this->view->images = $images;
	}
}
?>