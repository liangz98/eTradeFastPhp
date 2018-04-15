<?php
class AboutController extends Commerce_Controller_Action4Commerce
{
    function indexAction()
    {
        $aboutM = new Home_Model_About('home');

        $conditions = array();
        $f1=new Zend_Filter_Int();
        //查询条件
        if($this->_request->getParam('is_m_actived')!='-1' && trim($this->_request->getParam('is_m_actived'))!='')
            $conditions['is_m_actived']=$this->_request->getParam('is_m_actived');

        $perpage=10;
        $page=intval($this->_request->getParam('page'));
        $total = $aboutM->fetchRowsCount($conditions);
        $pageObj = new Seed_Page($this->_request,$total,$perpage);
        $this->view->page = $pageObj->getPageArray();
        if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
        if($page<1)$page=1;
        $abouts = $aboutM->fetchRows(array(($page-1)*$perpage,$perpage),$conditions,'order_by ASC');
        $this->view->abouts = $abouts;
        $this->view->conditions = $conditions;
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
                $insertData['about_title']=$f3->filter($this->_request->getPost('about_title'));

                $insertData['about_mark']=$f1->filter($this->_request->getPost('about_mark'));
                $insertData['about_m_image']=$f3->filter($this->_request->getPost('about_m_image'));
                $insertData['about_image']=$insertData['about_m_image'];
                $insertData['about_m_desc'] = $f4->filter($this->_request->getPost('about_m_desc'));
                $insertData['about_desc'] = $insertData['about_m_desc'];

                $about_m_content = $this->_request->getPost('about_m_content');
                if(isset($this->view->seed_Setting['upload_view_server'])){
                    $upload_view_server=str_replace("/","\/",$this->view->seed_Setting['upload_view_server']);
                    $patterns=array();
                    $replacements=array();
                    $patterns[]='/'.$upload_view_server.'/';
                    $replacements[]='{SEED_UPLOAD_SERVER}';

                    $insertData['about_m_content'] = preg_replace($patterns, $replacements, $about_m_content);
                }else{
                    $insertData['about_m_content'] = $about_m_content;
                }

                $insertData['about_source']=intval($this->_request->getPost('about_source'));
                $insertData['about_material_id']=intval($this->_request->getPost('about_material_id'));
                if($insertData['about_source']=='0'){
                    $insertData['about_material_id']=0;
                }elseif($insertData['about_source']=='1'){
                    $insertData['about_m_content']="";
                }

                $insertData['about_content'] = $insertData['about_m_content'];

                $insertData['is_m_actived']=intval($this->_request->getPost('is_m_actived'));
                $insertData['is_actived']=$insertData['is_m_actived'];


                $insertData['html_title']=$f3->filter($this->_request->getPost('html_title'));
                $insertData['html_keywords']=$f3->filter($this->_request->getPost('html_keywords'));
                $insertData['html_description']=$f3->filter($this->_request->getPost('html_description'));
                if(empty($insertData['html_title']))$insertData['html_title']=$insertData['about_title'];
                if(empty($insertData['html_keywords']))$insertData['html_keywords']=$insertData['about_title'];
                if(empty($insertData['html_description']))$insertData['html_description']=$insertData['about_m_desc'];

                $insertData['add_time']=time();

                if(empty($insertData['about_title'])){
                    Seed_Browser::tip_show('标题不能为空！');
                }elseif(empty($insertData['about_mark'])){
                    Seed_Browser::tip_show('标识不能为空！');
                }elseif($insertData['about_source']=='0' && empty($insertData['about_m_content'])){
                    Seed_Browser::tip_show('内容不能为空！');
                }elseif($insertData['about_source']=='1' && $insertData['about_material_id']<1){
                    Seed_Browser::tip_show('没选择素材！');
                }else{
                    $aboutM = new Home_Model_About('home');
                    if(!empty($insertData['about_mark'])){
                        $check = $aboutM->fetchRow(array('about_mark'=>$insertData['about_mark']));
                        if($check['about_id']>0){
                            Seed_Browser::tip_show('标识已经存在！');
                        }
                    }
                    if($about_id = $aboutM->insertRow($insertData)){
                        Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl."/about/index");
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
        if ($this->_request->isPost()) {
            try{
                $aboutM = new Home_Model_About('home');
                $f1=new Seed_Filter_Alnum();
                $f2=new Zend_Filter_Int();
                $f3 = new Zend_Filter( );
                $f3->addFilter(new Zend_Filter_StripTags())
                    ->addFilter(new Seed_Filter_EscapeQuotes());
                $f4 = new Seed_Filter_Text();
                $about_id=intval($this->_request->getPost('about_id'));
                $aboutDetail = $aboutM->fetchRow(array('about_id'=>$about_id));
                if($aboutDetail['about_id']<1){
                    Seed_Browser::tip_show("关键数据错误！");
                }

                $updateData = array();
                $updateData['about_title']=$f3->filter($this->_request->getPost('about_title'));
                $updateData['about_mark']=$f1->filter($this->_request->getPost('about_mark'));
                $updateData['about_m_image']=$f3->filter($this->_request->getPost('about_m_image'));
                $updateData['about_m_desc'] = $f4->filter($this->_request->getPost('about_m_desc'));

                $about_m_content = $this->_request->getPost('about_m_content');
                if(isset($this->view->seed_Setting['upload_view_server'])){
                    $upload_view_server=str_replace("/","\/",$this->view->seed_Setting['upload_view_server']);
                    $patterns=array();
                    $replacements=array();
                    $patterns[]='/'.$upload_view_server.'/';
                    $replacements[]='{SEED_UPLOAD_SERVER}';

                    $updateData['about_m_content'] = preg_replace($patterns, $replacements, $about_m_content);
                }else{
                    $updateData['about_m_content'] = $about_m_content;
                }

                $updateData['about_source']=$aboutDetail['about_source'];
                $updateData['about_material_id']=intval($this->_request->getPost('about_material_id'));
                if($updateData['about_source']=='0'){
                    $updateData['about_material_id']=0;
                }elseif($updateData['about_source']=='1'){
                    $updateData['about_m_content']="";
                    $updateData['about_content']="";
                }

                $updateData['is_m_actived']=intval($this->_request->getPost('is_m_actived'));
                $updateData['order_by']=intval($this->_request->getPost('order_by'));

                $about_id=intval($this->_request->getPost('about_id'));

                if($about_id<1){
                    Seed_Browser::tip_show('关键参数错误！');
                }elseif(empty($updateData['about_title'])){
                    Seed_Browser::tip_show('标题不能为空！');
                }elseif(empty($updateData['about_mark'])){
                    Seed_Browser::tip_show('标识不能为空！');
                }elseif($updateData['about_source']=='0' && empty($updateData['about_m_content'])){
                    Seed_Browser::tip_show('内容不能为空！');
                }elseif($updateData['about_source']=='1' && $updateData['about_material_id']<1){
                    Seed_Browser::tip_show('没选择素材！');
                }else{
                    $check = $aboutM->fetchRow(array('about_mark'=>$updateData['about_mark']));
                    if($check['about_id']>0 && $check['about_id']!=$about_id){
                        Seed_Browser::tip_show('保存文件名已经存在！');
                    }
                    if($aboutM->updateRow($updateData,array('about_id'=>$about_id))){
                        Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/about/update?about_id=".$about_id);
                    }else{
                        Seed_Browser::tip_show('修改失败！');
                    }
                }
            } catch (Exception $e) {
                Seed_Browser::tip_show($e->getMessage());
            }
            exit;
        }

        $aboutM = new Home_Model_About('home');
        $about_id=intval($this->_request->getParam('about_id'));
        $about = $aboutM->fetchRow(array('about_id'=>$about_id));
        if($about['about_id']<1)Seed_Browser::error('没有找到相关数据！');

        if($about['about_source']=='1' && $about['about_material_id']>0){
            $materialM = new Home_Model_Material('home');
            $material = $materialM->fetchRow(array('material_id'=>$about['about_material_id']));
            if($material['material_id']>0){
                $about['about_material_title'] = $material['material_title'];
            }else{
                $about['about_material_title'] = "";
            }
        }else{
            $about['about_material_title'] = "";
        }

        $this->view->about = $about;
    }


    function updatepcAction()
    {
        if ($this->_request->isPost()) {
            try{
                $aboutM = new Home_Model_About('home');
                $f1=new Seed_Filter_Alnum();
                $f2=new Zend_Filter_Int();
                $f3 = new Zend_Filter( );
                $f3->addFilter(new Zend_Filter_StripTags())
                    ->addFilter(new Seed_Filter_EscapeQuotes());
                $f4 = new Seed_Filter_Text();
                $about_id=intval($this->_request->getPost('about_id'));
                $aboutDetail = $aboutM->fetchRow(array('about_id'=>$about_id));
                if($aboutDetail['about_id']<1){
                    Seed_Browser::tip_show("关键数据错误！");
                }

                $updateData = array();
                $updateData['about_title']=$f3->filter($this->_request->getPost('about_title'));
                $updateData['about_mark']=$f1->filter($this->_request->getPost('about_mark'));
                $updateData['about_image']=$f3->filter($this->_request->getPost('about_image'));
                $updateData['about_desc'] = $f4->filter($this->_request->getPost('about_desc'));

                $about_content = $this->_request->getPost('about_content');
                if(isset($this->view->seed_Setting['upload_view_server'])){
                    $upload_view_server=str_replace("/","\/",$this->view->seed_Setting['upload_view_server']);
                    $patterns=array();
                    $replacements=array();
                    $patterns[]='/'.$upload_view_server.'/';
                    $replacements[]='{SEED_UPLOAD_SERVER}';

                    $updateData['about_content'] = preg_replace($patterns, $replacements, $about_content);
                }else{
                    $updateData['about_content'] = $about_content;
                }

                $updateData['about_source']=$aboutDetail['about_source'];
                $updateData['about_material_id']=intval($this->_request->getPost('about_material_id'));
                if($updateData['about_source']=='0'){
                    $updateData['about_material_id']=0;
                }elseif($updateData['about_source']=='1'){
                    $updateData['about_content']="";
                    $updateData['about_m_content']="";
                }

                $updateData['is_actived']=intval($this->_request->getPost('is_actived'));
                $updateData['order_by']=intval($this->_request->getPost('order_by'));


                $updateData['html_title']=$f3->filter($this->_request->getPost('html_title'));
                $updateData['html_keywords']=$f3->filter($this->_request->getPost('html_keywords'));
                $updateData['html_description']=$f3->filter($this->_request->getPost('html_description'));
                $updateData['html_title'] = $updateData['html_title']?$updateData['html_title']:($updateData['about_title']);
                $updateData['html_keywords'] = $updateData['html_keywords']?$updateData['html_keywords']:($updateData['about_title']);
                $updateData['html_description'] = $updateData['html_description']?$updateData['html_description']:($updateData['about_desc']);

                $about_id=intval($this->_request->getPost('about_id'));

                if($about_id<1){
                    Seed_Browser::tip_show('关键参数错误！');
                }elseif(empty($updateData['about_title'])){
                    Seed_Browser::tip_show('标题不能为空！');
                }elseif(empty($updateData['about_mark'])){
                    Seed_Browser::tip_show('标识不能为空！');
                }elseif($updateData['about_source']=='0' && empty($updateData['about_content'])){
                    Seed_Browser::tip_show('内容不能为空！');
                }elseif($updateData['about_source']=='1' && $updateData['about_material_id']<1){
                    Seed_Browser::tip_show('没选择素材！');
                }else{
                    $check = $aboutM->fetchRow(array('about_mark'=>$updateData['about_mark']));
                    if($check['about_id']>0 && $check['about_id']!=$about_id){
                        Seed_Browser::tip_show('保存文件名已经存在！');
                    }
                    if($aboutM->updateRow($updateData,array('about_id'=>$about_id))){
                        Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/about/updatepc?about_id=".$about_id);
                    }else{
                        Seed_Browser::tip_show('修改失败！');
                    }
                }
            } catch (Exception $e) {
                Seed_Browser::tip_show($e->getMessage());
            }
            exit;
        }

        $aboutM = new Home_Model_About('home');
        $about_id=intval($this->_request->getParam('about_id'));
        $about = $aboutM->fetchRow(array('about_id'=>$about_id));
        if($about['about_id']<1)Seed_Browser::error('没有找到相关数据！');

        if($about['about_source']=='1' && $about['about_material_id']>0){
            $materialM = new Home_Model_Material('home');
            $material = $materialM->fetchRow(array('material_id'=>$about['about_material_id']));
            if($material['material_id']>0){
                $about['about_material_title'] = $material['material_title'];
            }else{
                $about['about_material_title'] = "";
            }
        }else{
            $about['about_material_title'] = "";
        }

        $this->view->about = $about;
    }

    function deleteAction() {
        //AJAX POST
        if ($this->_request->isPost()){
            try{
                $f3 = new Zend_Filter_Int();
                $about_ids = $this->_request->getPost('about_id');
                if(count($about_ids)==0){
                    Seed_Browser::tip_show('找不到相关的数据!');
                }

                $aboutM=new Home_Model_About('home');
                foreach ($about_ids as $about_id){
                    $about_id = $f3->filter($about_id);
                    if($about_id>0){
                        $aboutM->deleteRow(array('about_id'=>$about_id));
                    }
                }
                Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/about/index');
                exit;
            } catch (Exception $e) {
                Seed_Browser::tip_show($e->getMessage());
            }
        }

        $about_ids = $this->_request->getParam('about_ids');
        if(empty($about_ids))
        {
            Seed_Browser::error('找不到相关的数据!');
        }
        $about_ids = explode(',',$about_ids);
        $f3 = new Zend_Filter_Int();
        $mypages_arr=array();
        foreach ($about_ids as $about_id)
        {
            $about_id = $f3->filter($about_id);
            if($about_id>0)
                $mypages_arr[]=$about_id;
        }
        if(count($mypages_arr)>0){
            $aboutM = new Home_Model_About('home');
            $abouts = $aboutM->fetchRowsByIds('about_id',$mypages_arr);
            $this->view->abouts = $abouts;
        }
    }

    function orderAction() {
        //AJAX POST
        if ($this->_request->isPost()) {
            try{
                $f3 = new Zend_Filter_Alnum();

                $about_ids = $this->_request->getPost('about_ids');
                $order_bys = $this->_request->getPost('order_bys');
                $updateData=array();
                $aboutM = new Home_Model_About('home');
                if(is_array($about_ids)){
                    foreach ($about_ids as $k=>$about_id){
                        $about_id = $f3->filter($about_id);
                        $updateData['order_by'] = $f3->filter($order_bys[$k]);
                        $aboutM->updateRow($updateData,array('about_id'=>$about_id));
                    }
                }
                Seed_Browser::tip_show('排序成功！',$this->view->seed_BaseUrl.'/about/index');
                exit;
            } catch (Exception $e) {
                Seed_Browser::tip_show($e->getMessage());
            }
        }
    }
}
