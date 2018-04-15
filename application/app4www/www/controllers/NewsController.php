<?php
class NewsController extends Kyapi_Controller_Action
{
//    function preDispatch(){
//        $this->view->showhead = true;
//        $this->view->showfoot = true;
//        $this->view->cur_pos = 'news';
//    }

    function indexAction(){
//        $wc_cur_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//        $expiretime = time()+$this->view->seed_Setting['cookie_expire'];
//        Seed_Cookie::setCookie('seed_VmallLastUrl',$wc_cur_url,$expiretime, $this->view->seed_Setting['cookie_path'], $this->view->seed_Setting['cookie_host']);

        $newsM = new Home_Model_News('home');
        $newsCateM = new Home_Model_NewsCate('home');
        $f1 = new Zend_Filter();
        $f1->addFilter(new Zend_Filter_StripTags())
            ->addFilter(new Seed_Filter_EscapeQuotes());
       // $parent = intval($this->_request->getParam('parent'));
        if(empty($parent)){$parent=0;}
        $this->view->cates = $newsCateM->fetchRows(null,array('parent'=>$parent),"order_by asc");

        $cate_mark = $f1->filter($this->_request->getParam('cate_mark'));
        $newsCate = $newsCateM->fetchRow(array('cate_mark'=>$cate_mark));
        $this->view->cate = $newsCate;

        $conditions= array();
        $conditions['is_m_actived']='1';
        if($newsCate['cate_id'] > 0){
            $cate_id = $newsCate['cate_id'];
            $cate_ids = $newsCateM->fetchChildrenCateIds($cate_id);
            if(is_array($cate_ids)){
                $conditions['t2.cate_id in ('.implode(',',$cate_ids).')']=null;
            }
            $this->view->cate_id = $cate_id;
        }
        $perpage = 8;
        $page = intval($this->_request->getParam('page'));
        $total = $newsM->fetchNewsesCount($conditions);
        $pageObj = new Seed_Page($this->_request,$total,$perpage);
        $this->view->page = $pageObj->getPageArray();
        if($page > $this->view->page['totalpage'])$page = $this->view->page['totalpage'];
        if($page < 1)$page = 1;
        $newses = $newsM->fetchNewses(array(($page-1)*$perpage,$perpage),$conditions,'news_id DESC');
        $this->view->newses = $newses;

        if(defined('SEED_V_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/news/index.phtml");
            echo $content;
            exit;
        }
    }

    function getnewsAction(){
        $newsM = new Home_Model_News('home');
        $newsCateM = new Home_Model_NewsCate('home');

        $cate_id = intval($this->_request->getParam('cate'));
        $conditions = array();
        if($cate_id>0){
            $newsCate = $newsCateM->fetchRow(array('cate_id'=>$cate_id));
            if($newsCate['cate_id']>0){
                $conditions['cate_id']=$newsCate['cate_id'];
                $this->view->cate = $newsCate;
            }

        }

        $perpage = 8;
        $page = intval($this->_request->getParam('page'));
        $newses = $newsM->fetchNewses(array(($page-1)*$perpage,$perpage),$conditions,'news_id DESC');
        $this->view->newses = $newses;

        if(defined('SEED_V_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/news/newslist.phtml");
            echo $content;
            exit;
        }
    }

    function detailAction(){

//        $wc_cur_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//        $expiretime = time()+$this->view->seed_Setting['cookie_expire'];
//        Seed_Cookie::setCookie('seed_VmallLastUrl',$wc_cur_url,$expiretime, $this->view->seed_Setting['cookie_path'], $this->view->seed_Setting['cookie_host']);

        $news_id = intval($this->getRequest()->getParam('id'));
        $newsCateM = new Home_Model_NewsCate('home');
        $newsM = new Home_Model_News('home');
        $news = $newsM->fetchNews(array('news_id'=>$news_id));
        if(empty($news))$this->_redirect('/');

        if($news['news_source']=='1' && $news['news_material_id']>0){
            $materialM = new Home_Model_Material('home');
            $material = $materialM->fetchRow(array('material_id'=>$news['news_material_id']));
            if($material['material_id']>0){
                $news['news_m_content'] = $material['material_m_content'];
            }
        }

        $newsM->updateRow(array('view_cnt' => ($news['view_cnt'] + 1)),  array('news_id' => $news_id));
        $this->view->news = $news;
        $this->view->hideshare = true;


        if(defined('SEED_V_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/news/detail.phtml");
            echo $content;
            exit;
        }
    }
}