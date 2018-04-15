<?php
class LinksController extends Shop_Controller_Action
{
	function indexAction()
	{
		$linkM = new Home_Model_Links('home');
    	
		if ($this->_request->isPost()) {
            try {
                $f2 = new Seed_Filter_Text();
                $f3 = new Seed_Filter_Int();
                $f4 = new Zend_Filter();
                $f4->addFilter(new Zend_Filter_StripTags())
                    ->addFilter(new Seed_Filter_EscapeQuotes());
                $insertData = array();
                $insertData['link_name'] = $f4->filter($this->_request->getPost('link_name'));
                $insertData['link_url'] = $f4->filter($this->_request->getPost('link_url')); 
                $insertData['link_way'] = $f4->filter($this->_request->getPost('link_way'));
                $insertData['link_desc'] = $f4->filter($this->_request->getPost('link_desc'));
                $insertData['is_actived'] = '0'; 
                $insertData['add_time']=time();
                if(empty( $insertData['link_name'])){
                    Seed_Browser::tip_show('请填写网站名称！');
                }elseif(empty( $insertData['link_url'])){
                    Seed_Browser::tip_show('请填写网站链接！');
                }elseif(empty( $insertData['link_way'])){
                    Seed_Browser::tip_show('请填写联系方式！');
                }
                $url = $_SERVER["HTTP_REFERER"];
                
                $link_name = $linkM->fetchRow(array('link_name' => $insertData['link_name']));
                 
                if ($link_name['link_id']>0){
                    Seed_Browser::tip_show('网站名称已存在，请修改！');
                }

                if ($linkM->insertRow($insertData)) {
                    Seed_Browser::tip_show('增加成功,请等待审批,谢谢！', $url);
                } else {
                    Seed_Browser::tip_show('增加失败！');
                }
            } catch (Exception $e) {
                Seed_Browser::tip_show($e->getMessage());
            }
            exit;
        }
        $condiction = array();
        //默认显示已审批
        $condiction['is_actived'] = '1'; 
           
        $links= $linkM->fetchRows(null,$condiction,'order_by desc');

        $this->view->links = $links;

	if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/links/index.phtml");
			echo $content;
			exit;
		}
	}
}
?>