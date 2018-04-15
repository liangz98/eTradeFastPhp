<?php
class ArticleController extends Commerce_Controller_Action4Commerce
{
	function indexAction()
	{
		$newsM = new Home_Model_News('home');
		$newsCateM = new Home_Model_NewsCate('home');

		$conditions = array();
		$myconditions = array();
		//查询条件
		if($this->_request->getParam('cate_id')!='-1' && trim($this->_request->getParam('cate_id'))!=''){
			$conditions['cate_id']=$this->_request->getParam('cate_id');
			$childrenids = $newsCateM->fetchChildrenCateIds($conditions['cate_id']);
			$myconditions['t1.cate_id in ('.implode(',',$childrenids).')']=null;
		}
		if($this->_request->getParam('is_actived')!='-1' && trim($this->_request->getParam('is_actived'))!=''){
			$conditions['is_actived']=$this->_request->getParam('is_actived');
			$myconditions['is_actived']=$conditions['is_actived'];
		}
		if($this->_request->getParam('is_new')!='-1' && trim($this->_request->getParam('is_new'))!=''){
			$conditions['is_new']=$this->_request->getParam('is_new');
			$myconditions['is_new']=$conditions['is_new'];
		}
		if($this->_request->getParam('is_top')!='-1' && trim($this->_request->getParam('is_top'))!=''){
			$conditions['is_top']=$this->_request->getParam('is_top');
			$myconditions['is_top']=$conditions['is_top'];
		}
		if($this->_request->getParam('is_recommend')!='-1' && trim($this->_request->getParam('is_recommend'))!=''){
			$conditions['is_recommend']=$this->_request->getParam('is_recommend');
			$myconditions['is_recommend']=$conditions['is_recommend'];
		}
		if($this->_request->getParam('news_type')!='-1' && trim($this->_request->getParam('news_type'))!=''){
			$conditions['news_type']=$this->_request->getParam('news_type');
			$myconditions['news_type']=$conditions['news_type'];
		}

		$perpage=15;
    	$page=intval($this->_request->getParam('page'));
    	$total = $newsM->fetchNewsesCount($myconditions);
    	$pageObj = new Seed_Page($this->_request,$total,$perpage);
    	$this->view->page = $pageObj->getPageArray();
	   	if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
    	if($page<1)$page=1;
		$newsall = $newsM->fetchNewses(array(($page-1)*$perpage,$perpage),$myconditions,'news_id DESC');
		$this->view->newsall = $newsall;
		$this->view->conditions = $conditions;
		$this->view->cateoptions = $newsCateM->getParentOption(0);
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
                $f5 = new Zend_Filter();
				$f5->addFilter(new Zend_Filter_StripTags())
				   ->addFilter(new Zend_Filter_StripNewlines());

				$insertData = array();
				$insertData['news_title']=$f3->filter($this->_request->getPost('news_title'));
				$insertData['news_m_image']=$f3->filter($this->_request->getPost('news_m_image'));
				$insertData['news_image']=$insertData['news_m_image'];
				$insertData['news_m_desc']=$f3->filter($this->_request->getPost('news_m_desc'));
				$insertData['news_desc']=$insertData['news_m_desc'];
				$insertData['news_type']=intval($this->_request->getPost('news_type'));

				$image_ids_arr = $this->_request->getPost('album_image_ids');
				if($insertData['news_type']=='1'){//图册类
					$insertData['news_m_content']="";
				}else{//文章类
					$insertData['news_type']='0';

					//内容替换对应上传图片路径
					$news_m_content = $this->_request->getPost('news_m_content');
					if(isset($this->view->seed_Setting['upload_view_server'])){
						$upload_view_server=str_replace("/","\/",$this->view->seed_Setting['upload_view_server']);
						$patterns=array();
						$replacements=array();
						$patterns[]='/'.$upload_view_server.'/';
						$replacements[]='{SEED_UPLOAD_SERVER}';

						$insertData['news_m_content'] = preg_replace($patterns, $replacements, $news_m_content);
					}else{
						$insertData['news_m_content'] = $news_m_content;
					}

					$image_ids_arr=array();
				}
                $insertData['news_content'] = $insertData['news_m_content'];

				$insertData['news_source']=intval($this->_request->getPost('news_source'));
				$insertData['news_material_id']=intval($this->_request->getPost('news_material_id'));
				if($insertData['news_source']=='0'){
					$insertData['news_material_id']=0;
				}elseif($insertData['news_source']=='1'){
					$insertData['news_m_content']="";
				}
				$insertData['news_content']=$insertData['news_m_content'];

				$insertData['cate_id']=intval($this->_request->getPost('cate_id'));
				$insertData['is_m_actived']=intval($this->_request->getPost('is_m_actived'));
				$insertData['add_time']=time();
				
				$insertData['html_title']=$f3->filter($this->_request->getPost('html_title'));
				$insertData['html_keywords']=$f3->filter($this->_request->getPost('html_keywords'));
				$insertData['html_description']=$f3->filter($this->_request->getPost('html_description'));		
				if(empty($insertData['html_title']))$insertData['html_title']=$insertData['news_title'];
				if(empty($insertData['html_keywords']))$insertData['html_keywords']=$insertData['news_title'];
				if(empty($insertData['html_description']))$insertData['html_description']=$insertData['news_m_desc'];

				if(empty($insertData['news_title'])){
					Seed_Browser::tip_show('标题不能为空！');
				}elseif($insertData['cate_id']<1){
					Seed_Browser::tip_show('分类不能为空！');
				}elseif($insertData['news_source']=='0' && $insertData['news_type']=='0' && empty($insertData['news_m_content'])){
					Seed_Browser::tip_show('内容不能为空！');
				}elseif($insertData['news_source']=='0' && $insertData['news_type']=='1' && count($image_ids_arr)==0){
					Seed_Browser::tip_show('图册不能为空！');
				}elseif($insertData['news_source']=='1' && $insertData['news_material_id']<1){
					Seed_Browser::tip_show('没选择素材！');
				}else{
					$newsM = new Home_Model_News('home');

					if($news_id = $newsM->insertRow($insertData)){
						$albumM = new Home_Model_NewsAlbum('home');
						$mAlbumM = new Home_Model_NewsMAlbum('home');
						$thumb_images_arr = $this->_request->getPost('album_thumb_images');
						$normal_images_arr = $this->_request->getPost('album_normal_images');
						$original_images_arr = $this->_request->getPost('album_original_images');
						$descs_arr = $this->_request->getPost('album_image_descs');
						$order_by_arr = $this->_request->getPost('album_order_bys');
						if(is_array($image_ids_arr)){
							foreach ($image_ids_arr as $k=>$image_id){
								$image_id = intval($image_id);
								$thumb_image = trim($thumb_images_arr[$k]);
								$normal_image = trim($normal_images_arr[$k]);
								$original_image = trim($original_images_arr[$k]);
								$image_desc = trim($f3->filter($descs_arr[$k]));
								$order_by = intval($order_by_arr[$k]);

								if($image_id>0){
									$albumData = array(
														'image_id'=>$image_id,
														'thumb_image'=>$thumb_image,
														'normal_image'=>$normal_image,
														'original_image'=>$original_image,
														'image_desc'=>$image_desc,
														'order_by'=>$order_by,
														'news_id'=>$news_id);
									$albumM->insertRow($albumData);
									$mAlbumM->insertRow($albumData);
								}
							}
						}
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl."/article/index");
		    		}else{
		    			Seed_Browser::tip_show('添加失败！');
		    		}
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
		$newsCateM = new Home_Model_NewsCate('home');
		$this->view->cateoptions = $newsCateM->getParentOption(0);
	}

	function updateAction()
	{
		if ($this->_request->isPost()) {
			try{
				$newsM = new Home_Model_News('home');
				$f1=new Seed_Filter_Alnum();
				$f2=new Zend_Filter_Int();
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f4 = new Seed_Filter_Text();
                $f5 = new Zend_Filter();
				$f5->addFilter(new Zend_Filter_StripTags())
				   ->addFilter(new Zend_Filter_StripNewlines());
				   
				$news_id=intval($this->_request->getPost('news_id'));
				$newsDetail = $newsM->fetchRow(array('news_id'=>$news_id));
				if($newsDetail['news_id']<1){
					Seed_Browser::tip_show("关键数据错误！");
				}

				$add_time=$this->_request->getPost('add_time');
				$add_time = mktime(substr($add_time,11,2),substr($add_time,14,2),substr($add_time,17,2),substr($add_time,5,2),substr($add_time,8,2),substr($add_time,0,4));

				$updateData = array();
				$updateData['news_title']=$f3->filter($this->_request->getPost('news_title'));
				$updateData['news_m_image']=$f3->filter($this->_request->getPost('news_m_image'));
				$updateData['news_m_desc']=$f3->filter($this->_request->getPost('news_m_desc'));
				$updateData['news_type']=$newsDetail['news_type'];
				$updateData['add_time']=$add_time;

				$image_ids_arr = $this->_request->getPost('album_image_ids');
				if($updateData['news_type']=='1'){//图册类
					$updateData['news_m_content']="";
				}else{//文章类
					//内容替换对应上传图片路径
					$news_m_content = $this->_request->getPost('news_m_content');
					if(isset($this->view->seed_Setting['upload_view_server'])){
						$upload_view_server=str_replace("/","\/",$this->view->seed_Setting['upload_view_server']);
						$patterns=array();
						$replacements=array();
						$patterns[]='/'.$upload_view_server.'/';
						$replacements[]='{SEED_UPLOAD_SERVER}';

						$updateData['news_m_content'] = preg_replace($patterns, $replacements, $news_m_content);
					}else{
						$updateData['news_m_content'] = $news_m_content;
					}
					$image_ids_arr=array();
				}
				
				$updateData['news_source']=$newsDetail['news_source'];
				$updateData['news_material_id']=intval($this->_request->getPost('news_material_id'));
				if($updateData['news_source']=='0'){
					$updateData['news_material_id']=0;
				}elseif($updateData['news_source']=='1'){
					$updateData['news_m_content']="";
					$updateData['news_content']="";
				}


				$updateData['cate_id']=intval($this->_request->getPost('cate_id'));
				$updateData['is_m_actived']=intval($this->_request->getPost('is_m_actived'));
				$updateData['view_cnt']=intval($this->_request->getPost('view_cnt'));

				if(empty($updateData['news_title'])){
					Seed_Browser::tip_show('标题不能为空！');
				}elseif($updateData['cate_id']<1){
					Seed_Browser::tip_show('分类不能为空！');
				}elseif($updateData['news_source']=='0' && $updateData['news_type']=='0' && empty($updateData['news_m_content'])){
					Seed_Browser::tip_show('内容不能为空！');
				}elseif($updateData['news_source']=='0' && $updateData['news_type']=='1' && count($image_ids_arr)==0){
					Seed_Browser::tip_show('图册不能为空！');
				}elseif($updateData['news_source']=='1' && $updateData['news_material_id']<1){
					Seed_Browser::tip_show('没选择素材！');
				}else{
					if($newsM->updateRow($updateData,array('news_id'=>$news_id))){
						$albumM = new Home_Model_NewsMAlbum('home');
						$albumM->deleteRow(array('news_id'=>$news_id));
						$thumb_images_arr = $this->_request->getPost('album_thumb_images');
						$normal_images_arr = $this->_request->getPost('album_normal_images');
						$original_images_arr = $this->_request->getPost('album_original_images');
						$descs_arr = $this->_request->getPost('album_image_descs');
						$order_by_arr = $this->_request->getPost('album_order_bys');
						if(is_array($image_ids_arr)){
							foreach ($image_ids_arr as $k=>$image_id){
								$image_id = intval($image_id);
								$thumb_image = trim($thumb_images_arr[$k]);
								$normal_image = trim($normal_images_arr[$k]);
								$original_image = trim($original_images_arr[$k]);
								$image_desc = trim($f3->filter($descs_arr[$k]));
								$order_by = intval($order_by_arr[$k]);

								if($image_id>0){
									$albumData = array(
														'image_id'=>$image_id,
														'thumb_image'=>$thumb_image,
														'normal_image'=>$normal_image,
														'original_image'=>$original_image,
														'image_desc'=>$image_desc,
														'order_by'=>$order_by,
														'news_id'=>$news_id);
									$albumM->insertRow($albumData);
								}
							}
						}
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/article/update?news_id=".$news_id);
		    		}else{
		    			Seed_Browser::tip_show('修改失败！');
		    		}
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
		$newsM = new Home_Model_News('home');
		$news_id=intval($this->_request->getParam('news_id'));
		$news = $newsM->fetchRow(array('news_id'=>$news_id));
		if($news['news_id']<1)throw new Exception('没有找到相关数据！');
		$albumM = new Home_Model_NewsMAlbum('home');
		$news['news_m_images'] = $albumM->fetchRows(null,array('news_id'=>$news_id),'order_by ASC');

		if($news['news_source']=='1' && $news['news_material_id']>0){
			$materialM = new Home_Model_Material('home');
			$material = $materialM->fetchRow(array('material_id'=>$news['news_material_id']));
			if($material['material_id']>0){
				$news['news_material_title'] = $material['material_title'];
			}else{
				$news['news_material_title'] = "";
			}
		}else{
			$news['news_material_title'] = "";
		}
		
		$this->view->news = $news;
		$newsCateM = new Home_Model_NewsCate('home');
		$this->view->cateoptions = $newsCateM->getParentOption(0);
	}
	
	function updatepcAction()
	{
		if ($this->_request->isPost()) {
			try{
				$newsM = new Home_Model_News('home');
				$f1=new Seed_Filter_Alnum();
				$f2=new Zend_Filter_Int();
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f4 = new Seed_Filter_Text();
                $f5 = new Zend_Filter();
				$f5->addFilter(new Zend_Filter_StripTags())
				   ->addFilter(new Zend_Filter_StripNewlines());
				   
				$news_id=intval($this->_request->getPost('news_id'));
				$newsDetail = $newsM->fetchRow(array('news_id'=>$news_id));
				if($newsDetail['news_id']<1){
					Seed_Browser::tip_show("关键数据错误！");
				}

				$add_time=$this->_request->getPost('add_time');
				$add_time = mktime(substr($add_time,11,2),substr($add_time,14,2),substr($add_time,17,2),substr($add_time,5,2),substr($add_time,8,2),substr($add_time,0,4));

				$updateData = array();
				$updateData['news_title']=$f3->filter($this->_request->getPost('news_title'));
				$updateData['news_image']=$f3->filter($this->_request->getPost('news_image'));
				$updateData['news_desc']=$f3->filter($this->_request->getPost('news_desc'));
				$updateData['news_type']=$newsDetail['news_type'];
				$updateData['add_time']=$add_time;

				$image_ids_arr = $this->_request->getPost('album_image_ids');
				if($updateData['news_type']=='1'){//图册类
					$updateData['news_content']="";
				}else{//文章类
					//内容替换对应上传图片路径
					$news_content = $this->_request->getPost('news_content');
					if(isset($this->view->seed_Setting['upload_view_server'])){
						$upload_view_server=str_replace("/","\/",$this->view->seed_Setting['upload_view_server']);
						$patterns=array();
						$replacements=array();
						$patterns[]='/'.$upload_view_server.'/';
						$replacements[]='{SEED_UPLOAD_SERVER}';

						$updateData['news_content'] = preg_replace($patterns, $replacements, $news_content);
					}else{
						$updateData['news_content'] = $news_content;
					}
					$image_ids_arr=array();
				}
				
				$updateData['news_source']=$newsDetail['news_source'];
				$updateData['news_material_id']=intval($this->_request->getPost('news_material_id'));
				if($updateData['news_source']=='0'){
					$updateData['news_material_id']=0;
				}elseif($updateData['news_source']=='1'){
					$updateData['news_content']="";
					$updateData['news_m_content']="";
				}


				$updateData['cate_id']=intval($this->_request->getPost('cate_id'));
				$updateData['is_actived']=intval($this->_request->getPost('is_actived'));
				$updateData['view_cnt']=intval($this->_request->getPost('view_cnt'));
				$updateData['html_title']=$f1->filter($this->_request->getPost('html_title'));
				$updateData['html_keywords']=$f1->filter($this->_request->getPost('html_keywords'));
				$updateData['html_description']=$f1->filter($this->_request->getPost('html_description'));
                $updateData['html_title'] = $updateData['html_title']?$updateData['html_title']:($updateData['news_title']);
                $updateData['html_keywords'] = $updateData['html_keywords']?$updateData['html_keywords']:($updateData['news_title']);
                $updateData['html_description'] = $updateData['html_description']?$updateData['html_description']:($updateData['news_desc']);
                

				if(empty($updateData['news_title'])){
					Seed_Browser::tip_show('标题不能为空！');
				}elseif($updateData['cate_id']<1){
					Seed_Browser::tip_show('分类不能为空！');
				}elseif($updateData['news_source']=='0' && $updateData['news_type']=='0' && empty($updateData['news_content'])){
					Seed_Browser::tip_show('内容不能为空！');
				}elseif($updateData['news_source']=='0' && $updateData['news_type']=='1' && count($image_ids_arr)==0){
					Seed_Browser::tip_show('图册不能为空！');
				}elseif($updateData['news_source']=='1' && $updateData['news_material_id']<1){
					Seed_Browser::tip_show('没选择素材！');
				}else{
					if($newsM->updateRow($updateData,array('news_id'=>$news_id))){
						$albumM = new Home_Model_NewsAlbum('home');
						$albumM->deleteRow(array('news_id'=>$news_id));
						$thumb_images_arr = $this->_request->getPost('album_thumb_images');
						$normal_images_arr = $this->_request->getPost('album_normal_images');
						$original_images_arr = $this->_request->getPost('album_original_images');
						$descs_arr = $this->_request->getPost('album_image_descs');
						$order_by_arr = $this->_request->getPost('album_order_bys');
						if(is_array($image_ids_arr)){
							foreach ($image_ids_arr as $k=>$image_id){
								$image_id = intval($image_id);
								$thumb_image = trim($thumb_images_arr[$k]);
								$normal_image = trim($normal_images_arr[$k]);
								$original_image = trim($original_images_arr[$k]);
								$image_desc = trim($f3->filter($descs_arr[$k]));
								$order_by = intval($order_by_arr[$k]);

								if($image_id>0){
									$albumData = array(
														'image_id'=>$image_id,
														'thumb_image'=>$thumb_image,
														'normal_image'=>$normal_image,
														'original_image'=>$original_image,
														'image_desc'=>$image_desc,
														'order_by'=>$order_by,
														'news_id'=>$news_id);
									$albumM->insertRow($albumData);
								}
							}
						}
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/article/updatepc?news_id=".$news_id);
		    		}else{
		    			Seed_Browser::tip_show('修改失败！');
		    		}
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
		$newsM = new Home_Model_News('home');
		$news_id=intval($this->_request->getParam('news_id'));
		$news = $newsM->fetchRow(array('news_id'=>$news_id));
		if($news['news_id']<1)throw new Exception('没有找到相关数据！');
		$albumM = new Home_Model_NewsAlbum('home');
		$news['news_images'] = $albumM->fetchRows(null,array('news_id'=>$news_id),'order_by ASC');

		if($news['news_source']=='1' && $news['news_material_id']>0){
			$materialM = new Home_Model_Material('home');
			$material = $materialM->fetchRow(array('material_id'=>$news['news_material_id']));
			if($material['material_id']>0){
				$news['news_material_title'] = $material['material_title'];
			}else{
				$news['news_material_title'] = "";
			}
		}else{
			$news['news_material_title'] = "";
		}
		$this->view->news = $news;
		$newsCateM = new Home_Model_NewsCate('home');
		$this->view->cateoptions = $newsCateM->getParentOption(0);
	}

	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Int();
				$news_ids = $this->_request->getPost('news_id');
				if(count($news_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}

				$newsM=new Home_Model_News('home');
				$albumM=new Home_Model_NewsAlbum('home');
				$malbumM=new Home_Model_NewsMAlbum('home');
				foreach ($news_ids as $news_id){
					$news_id = $f3->filter($news_id);
					if($news_id>0){
						$newsM->deleteRow(array('news_id'=>$news_id));
						$albumM->deleteRow(array('news_id'=>$news_id));
						$malbumM->deleteRow(array('news_id'=>$news_id));
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/article/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}

		$news_ids = $this->_request->getParam('news_ids');
	    if(empty($news_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$news_ids = explode(',',$news_ids);
		$f3 = new Zend_Filter_Int();
		$mynewss_arr=array();
		foreach ($news_ids as $news_id)
		{
			$news_id = $f3->filter($news_id);
			if($news_id>0)
					$mynewss_arr[]=$news_id;
		}
		if(count($mynewss_arr)>0){
			$newsM = new Home_Model_News('home');
			$newsall = $newsM->fetchRowsByIds('news_id',$mynewss_arr);
	   		$this->view->newsall = $newsall;
		}
	}
}
