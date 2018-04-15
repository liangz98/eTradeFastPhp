<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ChartchannelController extends Seed_Controller_Action4Admin
{
    function indexAction()
    {
        $m_channel = new Seed_Model_ChartChannel('system');
        $perpage=15;
    	$page=intval($this->_request->getParam('page'));
    	$total = $m_channel->fetchRowsCount();
    	$pageObj = new Seed_Page($this->_request,$total,$perpage);
    	$this->view->page = $pageObj->getPageArray();
	   	if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
    	if($page<1)$page=1;
        $this->view->channels = $m_channel->fetchRows(array(($page-1)*$perpage,$perpage), NULL, 'channel_id DESC');
    }

    function addAction()
    {
        if($this->_request->isPost()) {
            try {
                $f3 = new Zend_Filter( );
                $f3->addFilter(new Zend_Filter_StripTags())
                    ->addFilter(new Seed_Filter_EscapeQuotes());
                $f4 = new Seed_Filter_Text();

                $data_set['channel_name'] = $f3->filter($this->_request->getPost('channel_name'));
                $data_set['channel_desc'] = $f4->filter($this->_request->getPost('channel_desc'));
                $data_set['channel_intime'] = time();

                if(empty($data_set['channel_name'])) {
                    Seed_Browser::tip_show('频道名称不能为空！');
                } else {
                    $m_channel = new Seed_Model_ChartChannel('system');
                    $channel = $m_channel->fetchRow(array('channel_name' => $data_set['channel_name']));
                    if( ! empty($channel)) {
                        Seed_Browser::tip_show('频道名称已存在！');
                        exit;
                    }
                    if(FALSE !== $m_channel->insertRow($data_set)) {
                        Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl."/chartchannel/index");
                    } else {
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
        $m_channel = new Seed_Model_ChartChannel('system');
        $channel_id = $this->_request->getParam('channel_id');

        if($this->_request->isPost()) {
            try {
                $f3 = new Zend_Filter( );
                $f3->addFilter(new Zend_Filter_StripTags())
                    ->addFilter(new Seed_Filter_EscapeQuotes());
                $f4 = new Seed_Filter_Text();

                $channel_id = $this->_request->getPost('channel_id');
                if(count($channel_id)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
                $data_set['channel_name'] = $f3->filter($this->_request->getPost('channel_name'));
                $data_set['channel_desc'] = $f4->filter($this->_request->getPost('channel_desc'));
                $data_set['channel_intime'] = time();

                if(empty($data_set['channel_name'])) {
                    Seed_Browser::tip_show('频道名称不能为空！');
                } else {
                    $channel = $m_channel->fetchRows(NULL, array("channel_name = '".$data_set['channel_name']."'" => null, "channel_id != '".$channel_id."'" => null));
                    if( ! empty($channel)) {
                        Seed_Browser::tip_show('频道名称已存在！');
                        exit;
                    }
                    if(FALSE !== $m_channel->updateRow($data_set, array('channel_id' => $channel_id))) {
                        Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/chartchannel/index");
                    } else {
                        Seed_Browser::tip_show('修改失败！');
                    }
                }
            } catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
            exit;
        }
        $this->view->channel = $m_channel->fetchRow(array('channel_id' => $channel_id));
    }

    function deleteAction()
    {
        $m_channel = new Seed_Model_ChartChannel('system');
        $m_category = new Seed_Model_ChartCategory('system');
        $m_chart = new Seed_Model_Chart('system');

        if($this->_request->isPost()) {
            try {
                $channel_ids = $this->_request->getPost('channel_id');
                if(count($channel_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}

                foreach ($channel_ids as $channel_id){
                    $m_chart->deleteRow(array('channel_id' => $channel_id));
                    $m_category->deleteRow(array('channel_id' => $channel_id));
                    $m_channel->deleteRow(array('channel_id' => $channel_id));
                }
                Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/chartchannel/index');
            } catch (Exception $e) {
                    Seed_Browser::tip_show($e->getMessage());
            }
            exit;
        }

        $channel_ids = $this->_request->getParam('channel_ids');
        if(empty($channel_ids)) {
			Seed_Browser::error('找不到相关的数据!');
		}
		$channel_ids = explode(',',$channel_ids);
		$f3 = new Zend_Filter_Int();
		$mychannel_arr = array();
		foreach ($channel_ids as $channel_id)
		{
			$channel_id = $f3->filter($channel_id);
			if($channel_id > 0)
					$mychannel_arr[] = $channel_id;
		}
		if(count($mychannel_arr) > 0){
			$channelall = $m_channel->fetchRowsByIds('channel_id',$mychannel_arr);
	   		$this->view->channelall = $channelall;
		}
    }
}
?>
