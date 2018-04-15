<?php
class InfoController extends Kyapi_Controller_Action
{
	public function preDispatch()
	{
		$this->view->cur_pos = 'info';
        //清除 免登陆session
        $this->IsAuth($this->view->visitor);

        if(empty($this->view->userID)){
			Mobile_Browser::redirect('请先登录系统！',$this->view->seed_Setting['user_app_server']."/login");
		}
	}


	public function indexAction()
	{
		try{
			$f1 = new Seed_Filter_Alnum();
			$mod = $f1->filter($this->_request->getParam('mod'));
			if (empty($mod)) {$mod = "index";}

			$_PStatus =strval($this->_request->getParam('status'));
			if(empty( $_PStatus)){  $_PStatus ='03';}

			$_querySorts=$this->_request->getParam('querySorts');
			if(empty($_querySorts)){ $_querySorts =null;}

			$_keyword=$this->_request->getParam('keyword');
			if(empty($_keyword)){ $_keyword =null;}

			$page =intval($this->_request->getParam('page'));
			if($page<1)$page=1;
			$_limit=5;
			$_skip=$_limit*($page-1);

			$kyURL= $this->view->seed_Setting['KyUrl'].'/productApi';
			$kyoption = new HessianOptions();
			$kyoption->typeMap['requestObject'] = 'com.jtec.etrade.api.dto.RequestObject';
			$userM = new Kyapi_Controller_Common($kyURL,$kyoption);

			$_queryParams = array();
			$_queryParams['productStatus'] = $_PStatus;
			$_queryP = $this->arrayToObject($_queryParams);

			$goodsCount = $userM->countSaleProductApi($this->_requestObject);
			//统计所有商品数量
			$clConut=$goodsCount->result;
			$userKY = $userM->listSaleProductApi($this->_requestObject,$_queryP, null, $_keyword, $_skip, $_limit);

			//统计正常状态数量、分页
			$existCount = $userKY->extData;
			$total = $existCount['totalSize'];
			$page=$existCount['totalPage'];
			//获取商品数据
			$existData = $userKY->result;
			$existDatt = $this->objectToArray($existData);
			$this->view->clConut=$clConut;
			$this->view->e = $existDatt;
			$this->view->status= $_PStatus;

			$file = "user/goods/" . $mod . "-" . $_PStatus;
			$_limit=5;
			$pageObj = new Seed_Page($this->_request,$total,$_limit);
			$this->view->page = $pageObj->getPageArray();
			$this->view->page['pageurl'] = '/' . $file;
			if ($page > $this->view->page['totalpage'])
				$page = $this->view->page['totalpage'];
			if ($page < 1) $page = 1;
		} catch (Exception $e) {
			Shop_Browser::redirect($e->getMessage());
		}
		if (defined('SEED_WWW_TPL')) {
			$content = $this->view->render(SEED_WWW_TPL . "/goods/index.phtml");
			echo $content;
			exit;
		}
	}



}
