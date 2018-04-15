<?php
class ImagexhrController extends Shop_Controller_Action
{
    function preDispatch() {
        if($this->view->seed_User['user_id']<1) {
        	$token = $this->_request->getParam('token');
			$fromapi=false;
			if(empty($token)){
				$token = Seed_Auth::getInstance()->getIdentity();
			}else{
				$fromapi=true;
			}
			$this->view->seed_Token = $token;
			$userM = new Seed_Model_User('system');
			if(!empty($token)){
				$my = Seed_Token::decode($token);
				if(!is_array($my)){
					if ($this->_request->isPost())
						exit('关键数据损坏！');
					else
						throw new Exception('关键数据损坏！');
					exit;
				}
				$user = $userM->fetchRow(array('user_id'=>$my['user_id'],'token'=>$my['token'],'is_admin'=>'1','is_actived'=>'1'));
				$user['token']=$token;
				$this->view->seed_User = $user;
			}
        }
    }
    
    function initimgspacetabAction()
    {
        $f1= new zend_filter();
        $f1->addFilter(new Zend_Filter_StripTags())
           ->addFilter(new Zend_Filter_StripNewlines());
        $f2= new Zend_Filter_Int();
        $cateName = $f1->filter($this->_request->getParam('cateid'));
        $width_limit = $f2->filter($this->_request->getParam('width_limit'));
        $height_limit = $f2->filter($this->_request->getParam('height_limit'));

        $imageM = new Seed_Model_Image('system');

		$conditions = array();
        $conditions['user_id'] = $this->view->seed_User['user_id'];
        if( !empty($cateName)) {
            $conditions['cate_name'] = $cateName;
        }
        if( !empty($width_limit)) {
            $conditions['image_width'] = $width_limit;
        }
        if( !empty($height_limit)) {
            $conditions['image_height'] = $height_limit;
        }
		//调整分页
		$perpage = 20;
		$page = intval($this->_request->getParam('page'));
		$total = $imageM->fetchRowsCount($conditions);
		$pageObj = new Seed_Page($this->_request, $total, $perpage);
		$pageList = $pageObj->getPageArray();
		if($page>$pageList['totalpage']) $page=$pageList['totalpage'];
		if($page<1)$page=1;

		$images = $imageM->fetchRows(array(($page-1)*$perpage,$perpage), $conditions,'image_id DESC');
        if( !$images) {$images = array();}
        $detail = array();
        foreach($images as $k => $img) {
            $detail[$k]['cateId'] = $img['cate_name'];
            $detail[$k]['imgHeight'] = $img['image_height'];
            $detail[$k]['imgWidth'] = $img['image_width'];
            $detail[$k]['imgId'] = $img['image_id'];
            $detail[$k]['imgName'] = $img['image_name'];
            $detail[$k]['imgType'] = strtoupper($img['image_type']);
            $detail[$k]['imgUrl'] = '/images/'.$img['cate_path'].'/'.$img['thumb_image_path'];
            $detail[$k]['nolImgUrl'] = '/images/'.$img['cate_path'].'/'.$img['normal_image_path'];
            $detail[$k]['orlImgurl'] = '/images/'.$img['cate_path'].'/'.$img['original_image_path'];
            $detail[$k]['useFlag'] = 1;
            $detail[$k]['venderId'] = $img['user_id'];
        }
        $output = array(
            'page' => array(
                'detail' => $detail,
                'endRow' => $pageList['endpage'],
                'pageIndex' => $pageList['curpage'],
                'pageSize' => $pageList['prepage'],
                'startRow' => $pageList['startpage'],
                'totalItem' => $pageList['total'],
                'totalPage' => $pageList['totalpage']
            ),
            'cookieValue' => $this->view->seed_Token
        );
        exit(Zend_Json::encode($output));
    }

    function loadimgspacedataAction()
    {
        $f1= new zend_filter();
        $f1->addFilter(new Zend_Filter_StripTags())
           ->addFilter(new Zend_Filter_StripNewlines());
        $f2= new Zend_Filter_Int();

		$cateName = $f1->filter($this->_request->getParam('cateid'));
        $queryType = $f2->filter($this->_request->getParam('query_type'));
        $queryKey = $f1->filter($this->_request->getParam('query_key'));
        $width_limit = $f2->filter($this->_request->getParam('width_limit'));
        $height_limit = $f2->filter($this->_request->getParam('height_limit'));
        $page = $f2->filter($this->_request->getParam('page'));

        $imageM = new Seed_Model_Image('system');

		$conditions = array();
        $conditions['user_id'] = $this->view->seed_User['user_id'];
        if( !empty($width_limit)) {
            $conditions['image_width'] = $width_limit;
        }
        if( !empty($height_limit)) {
            $conditions['image_height'] = $height_limit;
        }
        if( !empty($cateName)) {
            $conditions['cate_name'] = $cateName;
        }
        if( !empty($queryKey)) {
            if($queryType == 0) {
                $conditions["image_name LIKE '%{$queryKey}%'"] = NULL;
            } elseif($queryType == 1) {
                $conditions["user_name LIKE '%{$queryKey}%'"] = NULL;
            }
        }
		//调整分页
		$perpage = 20;
		$total = $imageM->fetchRowsCount($conditions);
		$pageObj = new Seed_Page($this->_request, $total, $perpage);
		$pageList = $pageObj->getPageArray();
		if($page>$pageList['totalpage']) $page=$pageList['totalpage'];
		if($page<1)$page=1;

		$imageList = $imageM->fetchRows(array(($page-1)*$perpage,$perpage), $conditions,'image_id DESC');
        if( !$imageList) {$imageList = array();}
        $detail = array();
        foreach($imageList as $k => $img) {
            $detail[$k]['cateId'] = $img['cate_name'];
            $detail[$k]['imgHeight'] = $img['image_height'];
            $detail[$k]['imgWidth'] = $img['image_width'];
            $detail[$k]['imgId'] = $img['image_id'];
            $detail[$k]['imgName'] = $img['image_name'];
            $detail[$k]['imgType'] = strtoupper($img['image_type']);
            $detail[$k]['imgUrl'] = '/images/'.$img['cate_path'].'/'.$img['thumb_image_path'];
            $detail[$k]['nolImgUrl'] = '/images/'.$img['cate_path'].'/'.$img['normal_image_path'];
            $detail[$k]['orlImgurl'] = '/images/'.$img['cate_path'].'/'.$img['original_image_path'];
            $detail[$k]['useFlag'] = 1;
            $detail[$k]['venderId'] = $img['user_id'];
        }
        $output = array(
            'page' => array(
                'detail' => $detail,
                'endRow' => $pageList['endpage'],
                'pageIndex' => $pageList['curpage'],
                'pageSize' => $pageList['prepage'],
                'startRow' => $pageList['startpage'],
                'totalItem' => $pageList['total'],
                'totalPage' => $pageList['totalpage']
            )
        );
        exit(Zend_Json::encode($output));
    }

    function initimagepageAction()
    {
        $f1= new zend_filter();
        $f1->addFilter(new Zend_Filter_StripTags())
           ->addFilter(new Zend_Filter_StripNewlines());
        $f2= new Zend_Filter_Int();

        $img_cate = $f1->filter($this->_request->getParam('img_cate'));
        $page = $f2->filter($this->_request->getParam('page'));
        $type = $f2->filter($this->_request->getParam('type'));

        $categoryM = new Seed_Model_ImageCategory('system');
        $imageM = new Seed_Model_Image('system');

        $imgCate = $categoryM->fetchRow(array('cate_name' => $img_cate));

		$conditions = array();
        $conditions['user_id'] = $this->view->seed_User['user_id'];
        if( !empty($img_cate)) {
            $conditions['cate_name'] = $img_cate;
        }
        if( !empty($imgCate['width_limit'])) {
            $conditions['image_width'] = $imgCate['width_limit'];
        }
        if( !empty($imgCate['height_limit'])) {
            $conditions['image_height'] = $imgCate['image_height'];
        }
		//调整分页
		$perpage = 16;
		$total = $imageM->fetchRowsCount($conditions);
		$pageObj = new Seed_Page($this->_request, $total, $perpage, 0, 'page', 5);
		$this->view->page = $pageObj->getPageArray();
	   	if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
    	if($page<1)$page=1;

		$images = $imageM->fetchRows(array(($page-1)*$perpage,$perpage), $conditions,'image_id DESC');
        if( !$images) {$images = array();}
        $imageList = array();
        foreach($images as $k => $img) {
            $imageList[$k]['cateId'] = $img['cate_name'];
            $imageList[$k]['imgHeight'] = $img['image_height'];
            $imageList[$k]['imgWidth'] = $img['image_width'];
            $imageList[$k]['imgId'] = $img['image_id'];
            $imageList[$k]['imgName'] = $img['image_name'];
            $imageList[$k]['imgType'] = strtoupper($img['image_type']);
            $imageList[$k]['imgUrl'] = '/images/'.$img['cate_path'].'/'.$img['thumb_image_path'];
            $imageList[$k]['nolImgUrl'] = '/images/'.$img['cate_path'].'/'.$img['normal_image_path'];
            $imageList[$k]['orlImgurl'] = '/images/'.$img['cate_path'].'/'.$img['original_image_path'];
        }
        $this->view->imageList = $imageList;
        $this->view->imgCateName = $img_cate;
        $this->view->type = empty($type) ? 1 : $type;
    }

    function queryimagepageAction()
    {
        $f1= new zend_filter();
        $f1->addFilter(new Zend_Filter_StripTags())
           ->addFilter(new Zend_Filter_StripNewlines());
        $f2= new Zend_Filter_Int();

        $img_cate = $f1->filter($this->_request->getParam('img_cate'));
        $queryType = $f2->filter($this->_request->getParam('query_type'));
        $queryKey = $f1->filter($this->_request->getParam('query_key'));
        $page = $f2->filter($this->_request->getParam('page'));
        $type = $f2->filter($this->_request->getParam('type'));

        $categoryM = new Seed_Model_ImageCategory('system');
        $imageM = new Seed_Model_Image('system');

        $imgCate = $categoryM->fetchRow(array('cate_name' => $img_cate));

		$conditions = array();
        $conditions['user_id'] = $this->view->seed_User['user_id'];
        if( !empty($img_cate)) {
            $conditions['cate_name'] = $img_cate;
        }
        if( !empty($imgCate['width_limit'])) {
            $conditions['image_width'] = $imgCate['width_limit'];
        }
        if( !empty($imgCate['height_limit'])) {
            $conditions['image_height'] = $imgCate['image_height'];
        }
        if( !empty($queryKey)) {
            if($queryType == 0) {
                $conditions["image_name LIKE '%{$queryKey}%'"] = NULL;
            } elseif($queryType == 1) {
                $conditions["user_name LIKE '%{$queryKey}%'"] = NULL;
            }
        }
		//调整分页
		$perpage = 16;
		$total = $imageM->fetchRowsCount($conditions);
		$pageObj = new Seed_Page($this->_request, $total, $perpage, 0, 'page', 5);
		$this->view->page = $pageObj->getPageArray();
	   	if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
    	if($page<1)$page=1;

		$images = $imageM->fetchRows(array(($page-1)*$perpage,$perpage), $conditions,'image_id DESC');
        if( !$images) {$images = array();}
        $imageList = array();
        foreach($images as $k => $img) {
            $imageList[$k]['cateId'] = $img['cate_name'];
            $imageList[$k]['imgHeight'] = $img['image_height'];
            $imageList[$k]['imgWidth'] = $img['image_width'];
            $imageList[$k]['imgId'] = $img['image_id'];
            $imageList[$k]['imgName'] = $img['image_name'];
            $imageList[$k]['imgType'] = strtoupper($img['image_type']);
            $imageList[$k]['imgUrl'] = '/images/'.$img['cate_path'].'/'.$img['thumb_image_path'];
            $imageList[$k]['nolImgUrl'] = '/images/'.$img['cate_path'].'/'.$img['normal_image_path'];
            $imageList[$k]['orlImgurl'] = '/images/'.$img['cate_path'].'/'.$img['original_image_path'];
        }
        $this->view->imageList = $imageList;
        $this->view->queryType = $queryType;
        $this->view->queryKey = $queryKey;
        $this->view->imgCateName = $img_cate;
        $this->view->type = empty($type) ? 1 : $type;
    }

    function uploadinitAction()
    {
        $f1= new zend_filter();
        $f1->addFilter(new Zend_Filter_StripTags())
           ->addFilter(new Zend_Filter_StripNewlines());
        $f2= new Zend_Filter_Int();

        $img_cate = $f1->filter($this->_request->getParam('img_cate'));
        $type = $f2->filter($this->_request->getParam('type'));

        $categoryM = new Seed_Model_ImageCategory('system');

		$category = $categoryM->fetchRows();
		if( !$category) {$category = array();}

        $catelogs = array();
        foreach($category as $key => $cate) {
            $catelogs[$key]['cateId'] = $cate['cate_name'];
            $catelogs[$key]['cateName'] = $cate['cate_desc'];
            $catelogs[$key]['subList'] = array();
        }

        $imgCate = $this->getCategory($img_cate);
        $this->view->catelogs = $catelogs;
        $this->view->imgCateName = $img_cate;
        $this->view->imgCate = $imgCate;
        $this->view->type = empty($type) ? 1 : $type;
    }

	function indexAction()
	{
		exit;
	}

    private function getCategory($cate_name) {
        $categoryM=new Seed_Model_ImageCategory('system');
        $category = $categoryM->fetchRow(array('cate_name'=>$cate_name));
        if( !empty($category['cate_ext'])) {
            $file_type = '';
            $cate_exts = explode(",",$category['cate_ext']);
            foreach ($cate_exts as $my){
                $file_type .= "*.{$my};";
            }
        }
        $category['file_type'] = empty($file_type) ? '*.jpg;*.png;*.jpeg;' : $file_type;
        if(empty($category['size_limit'])) {
            $category['size_limit'] = '2MB';
        } else {
            $category['size_limit'] .= 'KB';
        }
        return $category;
    }
}