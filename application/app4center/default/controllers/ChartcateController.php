<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ChartcateController extends Seed_Controller_Action4Admin
{
    function indexAction()
    {
        $m_category = new Seed_Model_ChartCategory('system');

        $conditions = array();
		//查询条件
		if($this->_request->getParam('channel_id') != '-1' && trim($this->_request->getParam('channel_id')) != '')
			$conditions['channel_id'] = $this->_request->getParam('channel_id');

        $perpage=15;
    	$page=intval($this->_request->getParam('page'));
    	$total = $m_category->fetchRowsCount($conditions);
    	$pageObj = new Seed_Page($this->_request,$total,$perpage);
    	$this->view->page = $pageObj->getPageArray();
	   	if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
    	if($page<1)$page=1;
        $this->view->categorys = $m_category->getCates(array(($page-1)*$perpage,$perpage), $conditions, 'cate_id DESC');
        $this->view->conditions = $conditions;

        $m_channel = new Seed_Model_ChartChannel('system');
        $this->view->channeloptions = $m_channel->getParentOption();
    }

    function addAction()
    {
        if($this->_request->isPost()) {
            try {
                $f3 = new Zend_Filter( );
                $f3->addFilter(new Zend_Filter_StripTags())
                    ->addFilter(new Seed_Filter_EscapeQuotes());
                $f4 = new Seed_Filter_Text();

                $data_set['cate_name'] = $f3->filter($this->_request->getPost('cate_name'));
                $data_set['cate_title'] = $f3->filter($this->_request->getPost('cate_title'));
                $data_set['cate_desc'] = $f4->filter($this->_request->getPost('cate_desc'));
                $data_set['cate_limit'] = intval($this->_request->getPost('cate_limit'));
                $data_set['order_by'] = intval($this->_request->getPost('order_by'));
                $data_set['channel_id'] = intval($this->_request->getPost('channel_id'));
                $data_set['cate_intime'] = time();

                if(empty($data_set['cate_name'])) {
                    Seed_Browser::tip_show('榜单名称不能为空！');
                } elseif(empty($data_set['cate_title'])) {
                    Seed_Browser::tip_show('保存名称不能为空！');
                } elseif(empty($data_set['cate_limit'])) {
                    Seed_Browser::tip_show('榜单限量不能为空！');
                } else {
                    $m_category = new Seed_Model_ChartCategory('system');
                    $category = $m_category->fetchRow(array('cate_name' => $data_set['cate_name']));
                    if( ! empty($category)) {
                        Seed_Browser::tip_show('保存名称已存在！');
                        exit;
                    }
//                   $category = $m_category->fetchRow(array('cate_title' => $data_set['cate_title'], 'channel_id' => $data_set['channel_id']));
//                   if( ! empty($category)) {
//                      Seed_Browser::tip_show('所在频道中该榜单名称已存在！');
//                       exit;
//                   }
                    if(FALSE !== $m_category->insertRow($data_set)) {
                        Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl."/chartcate/index");
                    } else {
                        Seed_Browser::tip_show('添加失败！');
                    }
                }
            } catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
            exit;
        }
        $m_channel = new Seed_Model_ChartChannel('system');
        $this->view->channeloptions = $m_channel->getParentOption();
    }

    function updateAction()
    {
        $m_category = new Seed_Model_ChartCategory('system');
        $cate_id = $this->_request->getParam('cate_id');

        if($this->_request->isPost()) {
            try {
                $f3 = new Zend_Filter( );
                $f3->addFilter(new Zend_Filter_StripTags())
                    ->addFilter(new Seed_Filter_EscapeQuotes());
                $f4 = new Seed_Filter_Text();

                $cate_id = $this->_request->getPost('cate_id');
                if(count($cate_id)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
                $data_set['cate_name'] = $f3->filter($this->_request->getPost('cate_name'));
                $data_set['cate_title'] = $f3->filter($this->_request->getPost('cate_title'));
                $data_set['cate_desc'] = $f4->filter($this->_request->getPost('cate_desc'));
                $data_set['cate_limit'] = intval($this->_request->getPost('cate_limit'));
                $data_set['order_by'] = intval($this->_request->getPost('order_by'));
                $data_set['channel_id'] = intval($this->_request->getPost('channel_id'));
                $data_set['cate_intime'] = time();

                if(empty($data_set['cate_name'])) {
                    Seed_Browser::tip_show('榜单名称不能为空！');
                } elseif(empty($data_set['cate_title'])) {
                    Seed_Browser::tip_show('保存名称不能为空！');
                } elseif(empty($data_set['cate_limit'])) {
                    Seed_Browser::tip_show('榜单限量不能为空！');
                } else {
                    $category = $m_category->fetchRows(NULL, array('cate_name' => $data_set['cate_name'], 'cate_id != '.$cate_id => null));
                    if( ! empty($category)) {
                        Seed_Browser::tip_show('保存名称已存在！');
                        exit;
                    }
                    $category = $m_category->fetchRows(NULL, array('cate_title' => $data_set['cate_title'], 'channel_id' => $data_set['channel_id'], 'cate_id != ?' => $cate_id));
                    if( ! empty($category)) {
                        Seed_Browser::tip_show('所在频道中该榜单名称已存在！');
                        exit;
                    }
                    $m_chart = new Seed_Model_Chart('system');
                    $count = $m_chart->fetchRowsCount(array('cate_id' => $cate_id));
                    if($count > $data_set['cate_limit']) {
                        Seed_Browser::tip_show('所设定的记录上限小于该分类的记录数！');
                        exit;
                    }
                    if(FALSE !== $m_category->updateRow($data_set, array('cate_id' => $cate_id))) {
                        Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/chartcate/index");
                    } else {
                        Seed_Browser::tip_show('修改失败！');
                    }
                }
            } catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
            exit;
        }
        $m_channel = new Seed_Model_ChartChannel('system');
        $this->view->channeloptions = $m_channel->getParentOption();
        $this->view->category = $m_category->fetchRow(array('cate_id' => $cate_id));
    }

    function deleteAction()
    {
        $m_category = new Seed_Model_ChartCategory('system');
        $m_chart = new Seed_Model_Chart('system');

        if($this->_request->isPost()) {
            try {
                $cate_ids = $this->_request->getPost('cate_id');
                if(count($cate_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}

                foreach ($cate_ids as $cate_id){
                    $m_chart->deleteRow(array('cate_id' => $cate_id));
                    $m_category->deleteRow(array('cate_id' => $cate_id));
                }
                Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/chartcate/index');
            } catch (Exception $e) {
                    Seed_Browser::tip_show($e->getMessage());
            }
            exit;
        }

        $cate_ids = $this->_request->getParam('cate_ids');
        if(empty($cate_ids)) {
			Seed_Browser::error('找不到相关的数据!');
		}
		$cate_ids = explode(',',$cate_ids);
		$f3 = new Zend_Filter_Int();
		$mycate_arr = array();
		foreach ($cate_ids as $cate_id)
		{
			$cate_id = $f3->filter($cate_id);
			if($cate_id > 0)
					$mycate_arr[] = $cate_id;
		}
		if(count($mycate_arr) > 0){
			$categoryall = $m_category->fetchRowsByIds('cate_id',$mycate_arr);
	   		$this->view->categoryall = $categoryall;
		}
    }
}
?>
