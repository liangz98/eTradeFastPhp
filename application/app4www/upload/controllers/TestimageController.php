<?php
class TestimageController extends Shop_Controller_Action 
{
	function indexAction()
	{
		$f1=new Seed_Filter_Alnum();
		$f2=new Zend_Filter_Int();
		$name=$f1->filter($this->_request->getParam('name'));
		if(empty($name))Seed_Browser::error('参数“name”出错');
		$categoryM = new Seed_Model_ImageCategory('system');
		$category = $categoryM->fetchRow(array('cate_name'=>$name));
		if($category['cate_id']<1)Seed_Browser::error('参数“name”出错');
		
		$imageM = new Seed_Model_Image('system');
		
		$conditions = array();
		$conditions['cate_name'] = $name;
		if($category['is_pub']=='0')
			$conditions['user_id'] = $this->view->seed_User['user_id'];
		
		//调整分页
		$perpage = 8;
		$page = intval($this->_request->getParam('page'));
		$total = $imageM->fetchRowsCount($conditions);
		$pageObj = new Seed_Page($this->_request, $total, $perpage);
		$this->view->page = $pageObj->getPageArray();
		if($page>$this->view->page['totalpage']) $page=$this->view->page['totalpage'];
		if($page<1)$page=1;
		
		
		$images = $imageM->fetchRows(array(($page-1)*$perpage,$perpage), $conditions,'image_id DESC');
		
		if(count($images)==0){
			Seed_Browser::redirect("没有图片选择",$this->view->seed_BaseUrl."/testimage/upload?&name=".$name);
		}
		$this->view->name=$name;
		$this->view->images=$images;
	}
	
	function uploadAction(){
		$f1=new Seed_Filter_Alnum();
		$name=$f1->filter($this->_request->getParam('name'));
		$categoryM = new Seed_Model_ImageCategory('system');
		$category = $categoryM->fetchRow(array('cate_name'=>$name));
		if($category['cate_id']<1)Seed_Browser::error('参数“name”出错');
		$cate_exts = explode(",",$category['cate_ext']);
		if(count($cate_exts)==0){
			Seed_Browser::error('没有设置上传图片格式！');
		}
		$mycate_exts="";
		foreach ($cate_exts as $my){
			$mycate_exts.="*.".$my.";";
		}
		$mycate_exts=substr($mycate_exts,0,-1);
		$this->view->file_types = $mycate_exts;
		$this->view->file_size_limit = Seed_Common::returnBytes(ini_get('upload_max_filesize'));
		$this->view->name=$name;
	}
}