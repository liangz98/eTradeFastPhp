<?php
class FollowimageController extends Seed_Controller_Action4Admin
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
                $this->view->thumb = $thumb = intval($this->getRequest()->getParam('thumb'));
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
			Seed_Browser::redirect("没有图片选择",$this->view->seed_BaseUrl."/singleimage/upload?&name=".$name);
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
		$this->view->cate = $category;
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

		if($category['keep_orgname']=='1'){
			$catePathM = new Seed_Model_ImageCatePath('system');
			$this->view->paths = $catePathM->fetchRows(null,array('cate_name'=>$category['cate_name']),'path_name ASC');

			$path=$f1->filter($this->_request->getParam('path'));
			$this->view->path = $path;
		}

	}

	function addpathAction()
	{
		if ($this->_request->isPost()) {
			try{
				$f1= new zend_filter();
		        $f1->addFilter(new Zend_Filter_StripTags());
				$f2= new Zend_Filter_Int();
				$f3 = new Zend_Filter_Alnum();

				$insertData = array();
				$insertData['cate_name'] = $f3->filter($this->_request->getPost('cate_name'));
				$insertData['path_name'] = $f3->filter($this->_request->getPost('path_name'));
				$insertData['path_desc'] = $f1->filter($this->_request->getPost('path_desc'));
				if(empty($insertData['path_desc']))$insertData['path_desc'] = $insertData['path_name'];

				if (empty($insertData['cate_name'])){
					Seed_Browser::error('分类选择错误！');
				}elseif (empty($insertData['path_name'])){
					Seed_Browser::error('请输入目录名称！');
				}else{
					$catepathM = new Seed_Model_ImageCatePath('system');
					$check = $catepathM->fetchRow(array('cate_name'=>$insertData['cate_name'],'path_name'=>$insertData['path_name']));
					if($check['path_id']>0){
						Seed_Browser::redirect('目录已经存在！',$this->view->seed_BaseUrl.'/singleimage/upload?name='.$insertData['cate_name'].'&path='.$insertData['path_name']);
					}
					if($path_id = $catepathM->insertRow($insertData)){
						Seed_Browser::redirect('添加目录成功！',$this->view->seed_BaseUrl.'/singleimage/upload?name='.$insertData['cate_name'].'&path='.$insertData['path_name']);
					}else{
						Seed_Browser::error('添加失败！');
					}
				}
			} catch (Exception $e) {
				Seed_Browser::error($e->getMessage());
			}
			exit;
		}
	}
}