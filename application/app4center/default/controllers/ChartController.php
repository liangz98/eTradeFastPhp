<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ChartController extends Seed_Controller_Action4Admin
{
    function indexAction()
    {
        $m_chart = new Seed_Model_Chart('system');

        $conditions = array();
		//查询条件
		if($this->_request->getParam('channel_id') != '-1' && trim($this->_request->getParam('channel_id')) != '')
			$conditions['channel_id'] = $this->_request->getParam('channel_id');
		if($this->_request->getParam('cate_id') != '-1' && trim($this->_request->getParam('cate_id')) != '')
			$conditions['cate_id'] = $this->_request->getParam('cate_id');

        $perpage=30;
    	$page=intval($this->_request->getParam('page'));
    	$total = $m_chart->fetchRowsCount($conditions);
    	$pageObj = new Seed_Page($this->_request,$total,$perpage);
    	$this->view->page = $pageObj->getPageArray();
	   	if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
    	if($page<1)$page=1;
        $this->view->charts = $m_chart->getCharts(array(($page-1)*$perpage,$perpage), $conditions, 'chart_order ASC');
        $this->view->conditions = $conditions;
        
        $m_channel = new Seed_Model_ChartChannel('system');
        $m_category = new Seed_Model_ChartCategory('system');
        $this->view->channeloptions = $m_channel->getParentOption();
        $first_channel_id = isset($conditions['channel_id']) ? $conditions['channel_id']:0;
        $this->view->cateoptions = $m_category->getParentOption(array('channel_id' => $first_channel_id));
    }

    function addAction()
    {
        if($this->_request->isPost()) {
            try {
                $f3 = new Zend_Filter( );
                $f3->addFilter(new Zend_Filter_StripTags())
                    ->addFilter(new Seed_Filter_EscapeQuotes());
                $f4 = new Seed_Filter_Text();

                $data_set['chart_name'] = $f3->filter($this->_request->getPost('chart_name'));
                $data_set['chart_href'] = $f3->filter($this->_request->getPost('chart_href'));
                $data_set['chart_image'] = $f3->filter($this->_request->getPost('chart_image'));
                $data_set['chart_m_image'] = $f3->filter($this->_request->getPost('chart_m_image'));
                $data_set['chart_desc'] = $f4->filter($this->_request->getPost('chart_desc'));
                $data_set['chart_remark'] = $f4->filter($this->_request->getPost('chart_remark'));
                $data_set['chart_order'] = intval($this->_request->getPost('chart_order'));
                $data_set['channel_id'] = intval($this->_request->getPost('channel_id'));
                $data_set['cate_id'] = intval($this->_request->getPost('cate_id'));
                $data_set['chart_intime'] = time();
                $data_set['chart_status'] = intval($this->_request->getPost('chart_status'));

                if(empty($data_set['chart_name'])) {
                    Seed_Browser::tip_show('信息名称不能为空！');
                } elseif(empty($data_set['channel_id'])) {
                    Seed_Browser::tip_show('频道ID不能为空！');
                } elseif(empty($data_set['cate_id'])) {
                    Seed_Browser::tip_show('榜单ID不能为空！');
                } else {
                    $m_chart = new Seed_Model_Chart('system');
                    $count = $m_chart->fetchRowsCount(array('cate_id' => $data_set['cate_id']));
                    $m_category = new Seed_Model_ChartCategory('system');
                    $category = $m_category->fetchRow(array('cate_id' => $data_set['cate_id']));
                    if(isset($category['cate_limit'])) {
                        if($count < $category['cate_limit']) {
                        	$attrData=array();
							foreach ($_POST as $k=>$v){
								if (preg_match("/^attr_([\w]+)$/i",$k,$matches)){
									$attrData[$matches[1]]=$v;
								}
							}
							$data_set['chart_attrs'] = serialize($attrData);
                        	if(FALSE !== $m_chart->insertRow($data_set)) {
                            	Seed_Browser::tip_show('添加成功！', $this->view->seed_BaseUrl."/chart/index/channel_id/{$data_set['channel_id']}/cate_id/{$data_set['cate_id']}");
                            } else {
                                Seed_Browser::tip_show('添加失败！');
                            }
                        } else {
                            Seed_Browser::tip_show('已达到设定记录上限！');
                        }
                    } else {
                        Seed_Browser::tip_show('参数传递错误！');
                    }
                }
            } catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
            exit;
        }
        
        $channel_id = intval($this->_request->getParam('channel_id'));
        $cate_id = intval($this->_request->getParam('cate_id'));
        
        $m_channel = new Seed_Model_ChartChannel('system');
        $m_category = new Seed_Model_ChartCategory('system');
        $chartAttrM = new Seed_Model_ChartAttr('system');
        $this->view->channeloptions = $m_channel->getParentOption();
        if($channel_id>0){
	        $this->view->cateoptions = $m_category->getParentOption(array('channel_id' => $channel_id));
        }else{
	        $first_channel_id = isset($this->view->channeloptions[0]['channel_id']) ? $this->view->channeloptions[0]['channel_id'] : 0;
	        $this->view->cateoptions = $m_category->getParentOption(array('channel_id' => $first_channel_id));
        }
        $this->view->cate_id = $cate_id;
        $this->view->channel_id = $channel_id;
        $attrs = $chartAttrM->fetchRows(null,array(),'order_by asc');
		$this->view->attrs = $attrs;
    }

    function updateAction()
    {
        $m_chart = new Seed_Model_Chart('system');
        $chart_id = $this->_request->getParam('chart_id');

        if($this->_request->isPost()) {
            try {
                $f3 = new Zend_Filter( );
                $f3->addFilter(new Zend_Filter_StripTags())
                    ->addFilter(new Seed_Filter_EscapeQuotes());
                $f4 = new Seed_Filter_Text();

                $chart_id = $this->_request->getPost('chart_id');
                if(count($chart_id)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
                $data_set['chart_name'] = $f3->filter($this->_request->getPost('chart_name'));
                $data_set['chart_href'] = $f3->filter($this->_request->getPost('chart_href'));
                $data_set['chart_image'] = $f3->filter($this->_request->getPost('chart_image'));
                $data_set['chart_m_image'] = $f3->filter($this->_request->getPost('chart_m_image'));
                $data_set['chart_desc'] = $f4->filter($this->_request->getPost('chart_desc'));
                $data_set['chart_remark'] = $f4->filter($this->_request->getPost('chart_remark'));
                $data_set['chart_order'] = intval($this->_request->getPost('chart_order'));
                $data_set['channel_id'] = intval($this->_request->getPost('channel_id'));
                $data_set['cate_id'] = intval($this->_request->getPost('cate_id'));
                $data_set['chart_intime'] = time();
                $data_set['chart_status'] = intval($this->_request->getPost('chart_status'));

                if(empty($data_set['chart_name'])) {
                    Seed_Browser::tip_show('信息名称不能为空！');
                } elseif(empty($data_set['channel_id'])) {
                    Seed_Browser::tip_show('频道ID不能为空！');
                } elseif(empty($data_set['cate_id'])) {
                    Seed_Browser::tip_show('榜单ID不能为空！');
                } else {
                    $attrData=array();
					foreach ($_POST as $k=>$v){
						if (preg_match("/^attr_([\w]+)$/i",$k,$matches)){
							$attrData[$matches[1]]=$v;
						}
					}
					$data_set['chart_attrs'] = serialize($attrData);
                	if(FALSE !== $m_chart->updateRow($data_set, array('chart_id' => $chart_id))) {
                        Seed_Browser::tip_show('修改成功！', $this->view->seed_BaseUrl."/chart/index/channel_id/{$data_set['channel_id']}/cate_id/{$data_set['cate_id']}");
                    } else {
                        Seed_Browser::tip_show('修改失败！');
                    }
                }
            } catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
            exit;
        }
        $chart = $m_chart->fetchRow(array('chart_id' => $chart_id));
        if(is_array($chart) && !empty($chart)){
        	if(!empty($chart['chart_attrs'])){
        		$chart['attrs'] = unserialize($chart['chart_attrs']);
        	}else{
        		$chart['attrs'] = null;
        	}
        }
        $this->view->chart = $chart;
        $m_channel = new Seed_Model_ChartChannel('system');
        $m_category = new Seed_Model_ChartCategory('system');
        $chartAttrM = new Seed_Model_ChartAttr('system');
        $this->view->channeloptions = $m_channel->getParentOption();
        $this->view->cateoptions = $m_category->getParentOption(array('channel_id' => $this->view->chart['channel_id']));
        $attrs = $chartAttrM->fetchRows(null,array(),'order_by asc');
		$this->view->attrs = $attrs;
    }
    
	function orderAction() {
		//AJAX POST
		if ($this->_request->isPost()) {
			try{
				$f3 = new Zend_Filter_Alnum();
				
				$chart_ids = $this->_request->getPost('chart_ids');
				$order_bys = $this->_request->getPost('order_bys');
				$channel_id = $this->_request->getPost('channel_id');
                $cate_id = $this->_request->getPost('cate_id');
				$updateData=array();
				$chartM = new Seed_Model_Chart('system');
				if(is_array($chart_ids)){
					foreach ($chart_ids as $k=>$chart_id){
						$chart_id = $f3->filter($chart_id);
						$updateData['chart_order'] = $f3->filter($order_bys[$k]);
						$chartM->updateRow($updateData,array('chart_id'=>$chart_id));
					}
				}
				Seed_Browser::tip_show('排序成功！',$this->view->seed_BaseUrl."/chart/index/channel_id/$channel_id/cate_id/$cate_id");
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}

    function deleteAction()
    {
        $m_chart = new Seed_Model_Chart('system');

        if($this->_request->isPost()) {
            try {
                $chart_ids = $this->_request->getPost('chart_id');
                $channel_id = $this->_request->getPost('channel_id');
                $cate_id = $this->_request->getPost('cate_id');
                if(count($chart_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
                foreach ($chart_ids as $chart_id){
                    $m_chart->deleteRow(array('chart_id' => $chart_id));
                }
                Seed_Browser::tip_show('删除成功！', $this->view->seed_BaseUrl."/chart/index/channel_id/$channel_id/cate_id/$cate_id");
            } catch (Exception $e) {
                    Seed_Browser::tip_show($e->getMessage());
            }
            exit;
        }

        $chart_ids = $this->_request->getParam('chart_ids');
        if(empty($chart_ids)) {
			Seed_Browser::error('找不到相关的数据!');
		}
		$chart_ids = explode(',',$chart_ids);
		$f3 = new Zend_Filter_Int();
		$mychart_arr = array();
		foreach ($chart_ids as $chart_id)
		{
			$chart_id = $f3->filter($chart_id);
			if($chart_id > 0)
					$mychart_arr[] = $chart_id;
		}
		if(count($mychart_arr) > 0){
			$chartall = $m_chart->fetchRowsByIds('chart_id',$mychart_arr);
	   		$this->view->chartall = $chartall;
		}
    }

    function ajaxchangeAction()
    {
        $channel_id = intval($this->_request->getParam('channel_id'));
        $output_data = '';
        $m_category = new Seed_Model_ChartCategory('system');
        if(0 != $channel_id) {
            $m_channel = new Seed_Model_ChartChannel('system');
            $cateoptions = $m_category->getParentOption(array('channel_id' => $channel_id));
            if(is_array($cateoptions)) {
                foreach($cateoptions as $key => $catemenu) {
                    $output_data .= '<option value="' . $catemenu['cate_id'] . '">' . $catemenu['cate_title'] . "</option>\n";
                }
            } else {
                $output_data .= '<option value="0">&nbsp;</option>';
            }
            echo $output_data;
        } else {
            echo 'null';
        }
        exit;
    }
}
?>
