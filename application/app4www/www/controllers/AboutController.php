<?php
class AboutController extends Shop_Controller_Action
{ 
    function preDispatch(){
		$this->view->cur_pos = $this->_request->getParam('controller');
		
		$cateM = new Shop_Model_GoodsCate('shop');
		$cates = $cateM->fetchRows(null,array('parent'=>'0'),'order_by ASC');
		foreach($cates as $k=>$v){
            $sub_cates = $cateM->fetchRows(NULL,array('parent'=>$v['cate_id']),'order_by ASC');
            $cates[$k]['sub_cates'] = $sub_cates;
        }
        $this->view->header_cates = $cates;
        
        $cururl = $_SERVER['REQUEST_URI'];
		preg_match('/(.*)\.html/',$cururl,$arr);
		if(isset($arr[1]) && !empty($arr[1]))
		{
			preg_match('/^\/about\/([\w\-]+).html/isU',$cururl,$arr);
			if(isset($arr[1]) && !empty($arr[1])){
				$this->_request->setParam('about_mark',$arr[1]);
				$this->indexAction();
				exit;
			}
			Shop_Browser::redirect('没有找到相关信息！',$this->view->seed_BaseUrl."/");
		}
    }
    
    
    function indexAction(){
        $about_mark = trim($this->getRequest()->getParam('about_mark'));
		if(empty($about_mark))$about_mark="index";
		$aboutM = new Home_Model_About('home');
		$about = $aboutM->fetchRow(array('about_mark'=>$about_mark));
		if($about['about_id']<1)$this->_redirect('/');
		
		if($about['about_source']=='1' && $about['about_material_id']>0){
			$materialM = new Home_Model_Material('home');
			$material = $materialM->fetchRow(array('material_id'=>$about['about_material_id']));
			if($material['material_id']>0){
				$about['about_content'] = $material['material_content'];
			}
		}
		
		$this->view->about = $about;
		$this->view->about_mark = $about_mark;
        $this->view->abouts = $aboutM->fetchRows(null,array('is_actived'=>'1'),"order_by ASC");
        		
		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/about/index.phtml");
			echo $content;
			exit;
		}
    }

	function detailAction()
	{
        $about_mark = trim($this->getRequest()->getParam('about_mark'));
		if(empty($about_mark))$about_mark="index";
		$aboutM = new Home_Model_About('home');
		$about = $aboutM->fetchRow(array('about_mark'=>$about_mark));
		if($about['about_id']<1)$this->_redirect('/');
		
		if($about['about_source']=='1' && $about['about_material_id']>0){
			$materialM = new Home_Model_Material('home');
			$material = $materialM->fetchRow(array('material_id'=>$about['about_material_id']));
			if($material['material_id']>0){
				$about['about_content'] = $material['material_content'];
			}
		}
		
		$this->view->about = $about;
        $this->view->abouts = $aboutM->fetchRows(null,array('is_actived'=>'1'),"order_by ASC");
        		
		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/about/index.phtml");
			echo $content;
			exit;
		}
	}
}