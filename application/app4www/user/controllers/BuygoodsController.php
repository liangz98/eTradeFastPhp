<?php
class BuygoodsController extends Kyapi_Controller_Action
{
	public function preDispatch()
	{
		$this->view->cur_pos = 'buygoods';
        //清除 免登陆session
        $this->IsAuth($this->view->visitor);

        if(empty($this->view->userID)){
			Mobile_Browser::redirect('请先登录系统！',$this->view->seed_Setting['user_app_server']."/login");
		}
		$this->view->cur_pos = $this->_request->getParam('controller');
		$cururl = $this->getRequestUri();
		if ($cururl == '/buygoods') {
			$this->_request->setParam('all', 'all');
			$this->indexAction();
			exit;
		}

		preg_match('/(.*)\.html/', $cururl, $arr);

		if (isset($arr[1]) && !empty($arr[1])) {


			preg_match_all('/^\/user\/buygoods\/(index|top|price)-([\d]+)-([\d]+).html/isU', $cururl, $arr);

			if (is_array($arr) && count($arr) > 1) {
				$this->_request->setParam('status', $arr[2][0]);
				$this->_request->setParam('page', $arr[3][0]);
				$this->indexAction();
				exit;
			}

			Mobile_Browser::redirect('没有找到相关信息！', $this->view->seed_BaseUrl . "/");

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

			$goodsCount = $userM->countPurProductApi($this->_requestObject);
			//统计所有商品数量
			$clConut=$goodsCount->result;
			$userKY = $userM->listPurProductApi($this->_requestObject,$_queryP, null, $_keyword, $_skip, $_limit);

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

			$file = "user/buygoods/" . $mod . "-" . $_PStatus;
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
			$content = $this->view->render(SEED_WWW_TPL . "/buygoods/index.phtml");
			echo $content;
			exit;
		}
	}

	public function addAction()
	{
		if ($this->_request->isPost()) {
			try {
				$kyURL= $this->view->seed_Setting['KyUrl'].'/productApi';
				$kyoption = new HessianOptions();
				$kyoption->typeMap['requestObject'] = 'com.jtec.etrade.api.dto.RequestObject';
				$kyoption->typeMap['product'] = 'com.jtec.jump.product.dto.Product';

				$userM = new Kyapi_Controller_Common($kyURL,$kyoption);
				$_requestOb=$this->_requestObject;
				if($this->_request->getParam('needInspection')==1){
					$needInSP=true;
				}else{
					$needInSP=false;
				}

				/*添加合作伙伴信息*/
				$_goods = array();
				$_goods['productID'] = $this->_request->getParam('productID');//商品ID
				$_goods['hscode'] = $this->_request->getParam('hscode');//
				$_goods['taxRate'] = (double)$this->_request->getParam('taxRate');//增值税率
				$_goods['rebateRate'] = (double)$this->_request->getParam('rebateRate');//退税率
				$_goods['productName'] = $this->_request->getParam('productName');//商品名
				$_goods['productEnName'] = $this->_request->getParam('productEnName');//EN商品名
				$_goods['pricingUnit'] = $this->_request->getParam('pricingUnit');//交易单位
				$_goods['legalPricingUnit'] = $this->_request->getParam('legalPricingUnit');//法定计价单位
				$_goods['productModel'] = $this->_request->getParam('productModel');//商品型号
				$_goods['productBrand'] = $this->_request->getParam('productBrand');//商品品牌
				$_goods['productMaterial'] = $this->_request->getParam('productMaterial');//商品材质
				$_goods['functionUsage'] = $this->_request->getParam('functionUsage');//功能用途
				$_goods['productSize'] = $this->_request->getParam('productSize');//尺寸规格
				$_goods['crnCode'] = $this->_request->getParam('crnCode');//货币代码
				$_goods['needInspection'] = $needInSP;//是否商检
				$_goods['productionMode'] = $this->_request->getParam('productionMode');//hscode
				$_goods['unitPrice'] = (double)$this->_request->getParam('unitPrice');//销售单价
				$_goods['purchaseUnitPrice'] =(double)$this->_request->getParam('purchaseUnitPrice');//采购单价
				$_goods['purchaseCrnCode'] = $this->_request->getParam('purchaseCrnCode');//采购货币
				$_goods['productStatus'] = $this->_request->getParam('productStatus');//商品状态
				$_goods['packingVolume'] = (double)$this->_request->getParam('packingVolume');//包装体积
				$_goods['packingType'] = $this->_request->getParam('packingType');//包装类型
				$_goods['packingType'] = (double)$this->_request->getParam('netWeight');//净重
				$_goods['packingType'] = (double)$this->_request->getParam('grossWeight');//毛重
				$_goods['packingLength'] = (double)$this->_request->getParam('packingLength');//长
				$_goods['packingWidth'] = (double)$this->_request->getParam('packingWidth');//宽
				$_goods['packingHeight'] = (double)$this->_request->getParam('packingHeight');//高

				$_goodsArr=$this->arrayToObject($_goods);
				$resultObject= $userM->addPurProductApi($_requestOb,$_goodsArr);
				//$existData = $resultObject->result;
				if ($resultObject->status != 1) {
					//  echo $resultObject->error, "<br />",
					Seed_Browser::redirect('增加失败！'. $resultObject->error,$this->view->seed_BaseUrl ."/buygoods");
				} else {
					Shop_Browser::redirect('增加成功！',$this->view->seed_BaseUrl ."/buygoods");
				}
			} catch (HttpError $ex) {
				Shop_Browser::redirect($ex->getMessage());
			}
		}
		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/buygoods/add.phtml");
			echo $content;
			exit;
		}
	}
	public function editAction()
	{

		$kyURL= $this->view->seed_Setting['KyUrl'].'/productApi';
		$kyoption = new HessianOptions();
		$kyoption->typeMap['requestObject'] = 'com.jtec.etrade.api.dto.RequestObject';
		//  $kyoption->typeMap['product'] = 'com.jtec.jump.product.dto.Product';

		$userM = new Kyapi_Controller_Common($kyURL,$kyoption);
		// 请求Hessian服务端方法
		$productID=$_SERVER['QUERY_STRING'];
		$_productID =base64_decode($productID);
		$_requestOb=$this->_requestObject;
		$userKY= $userM->getProductApi($_requestOb,$_productID);
		$existData =$userKY->result;
		$existDatt=$this->objectToArray($existData);
		@$this->view->goods=$existDatt;
		if ($this->_request->isPost()) {
			try {
				$kyURL= $this->view->seed_Setting['KyUrl'].'/productApi';
				$kyoption = new HessianOptions();
				$kyoption->typeMap['requestObject'] = 'com.jtec.etrade.api.dto.RequestObject';
				// $kyoption->typeMap['product'] = 'com.jtec.jump.product.dto.Product';
				$userM = new Kyapi_Controller_Common($kyURL,$kyoption);
				// 设置请求数据
				$_requestOb=$this->_requestObject;
				if($this->_request->getParam('needInspection')==1){
					$needInSP=true;
				}else{
					$needInSP=false;
				}

				/*添加合作伙伴信息*/
				$_goods = array();
				$_goods['accountID'] = $_productID;//商品ID
				$_goods['hscode'] = $this->_request->getParam('hscode');//
				$_goods['taxRate'] = (double)$this->_request->getParam('taxRate');//增值税率
				$_goods['rebateRate'] = (double)$this->_request->getParam('rebateRate');//退税率
				$_goods['productName'] = $this->_request->getParam('productName');//商品名
				$_goods['productEnName'] = $this->_request->getParam('productEnName');//EN商品名
				$_goods['pricingUnit'] = $this->_request->getParam('pricingUnit');//交易单位
				$_goods['legalPricingUnit'] = $this->_request->getParam('legalPricingUnit');//法定计价单位
				$_goods['productModel'] = $this->_request->getParam('productModel');//商品型号
				$_goods['productBrand'] = $this->_request->getParam('productBrand');//商品品牌
				$_goods['productMaterial'] = $this->_request->getParam('productMaterial');//商品材质
				$_goods['functionUsage'] = $this->_request->getParam('functionUsage');//功能用途
				$_goods['productSize'] = $this->_request->getParam('productSize');//尺寸规格
				$_goods['crnCode'] = $this->_request->getParam('crnCode');//货币代码
				$_goods['needInspection'] = $needInSP;//是否商检
				$_goods['productionMode'] = $this->_request->getParam('productionMode');//生产方式
				$_goods['unitPrice'] = (double)$this->_request->getParam('unitPrice');//销售单价

				$_goods['purchaseUnitPrice'] =(double)$this->_request->getParam('purchaseUnitPrice');//采购单价
				$_goods['purchaseCrnCode'] = $this->_request->getParam('purchaseCrnCode');//采购货币
				$_goods['productStatus'] = '03';//商品状态
				$_goods['packingVolume'] = (double)$this->_request->getParam('packingVolume');//包装体积
				$_goods['packingType'] = $this->_request->getParam('packingType');//包装类型
				$_goods['netWeight'] = (double)$this->_request->getParam('netWeight');//净重
				$_goods['grossWeight'] = (double)$this->_request->getParam('grossWeight');//毛重

				$_goodsArr=$this->arrayToObject($_goods);
				$resultObject= $userM->editPurProductApi($_requestOb,$_goodsArr);

				$existData = $resultObject->result;
				if ($resultObject->status != 1) {
					echo $resultObject->error, "<br />",
					Shop_Browser::redirect('编辑失败！',$this->view->seed_BaseUrl ."/buygoods");
				} else {
					Shop_Browser::redirect('编辑成功！',$this->view->seed_BaseUrl ."/buygoods");
				}
			} catch (HttpError $ex) {
				echo $ex->getMessage();
				Shop_Browser::redirect($ex->getMessage(),$this->view->seed_BaseUrl ."/buygoods");
			}
		}

		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/buygoods/edit.phtml");
			echo $content;
			exit;
		}
	}

	public function viewAction()
	{
		$kyURL= $this->view->seed_Setting['KyUrl'].'/productApi';
		$kyoption = new HessianOptions();
		$kyoption->typeMap['requestObject'] = 'com.jtec.etrade.api.dto.RequestObject';
		$userM = new Kyapi_Controller_Common($kyURL,$kyoption);
		// 请求Hessian服务端方法
		$productID=$_SERVER['QUERY_STRING'];
		$_productID =base64_decode($productID);
		$_requestOb=$this->_requestObject;
		$userKY= $userM->getProductApi($_requestOb,$_productID);

		$existData =$userKY->result;
		$existDatt=$this->objectToArray($existData);
		$this->view->goods=$existDatt;

		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/buygoods/view.phtml");
			echo $content;
			exit;
		}
	}

	public function invalidAction()
	{
		$kyURL= $this->view->seed_Setting['KyUrl'].'/productApi';
		$kyoption = new HessianOptions();
		$kyoption->typeMap['requestObject'] = 'com.jtec.etrade.api.dto.RequestObject';
		$userM = new Kyapi_Controller_Common($kyURL,$kyoption);
		// 请求Hessian服务端方法
		$productID=$_SERVER['QUERY_STRING'];
		$_productID =base64_decode($productID);
		$_requestOb=$this->_requestObject;

		$existData= $userM->invalidProductApi($_requestOb,$_productID);

		if ($existData->status != 1) {
			echo $existData->error, "<br />",
			Shop_Browser::redirect('禁用失败！',$this->view->seed_BaseUrl ."/buygoods");
		} else {
			Shop_Browser::redirect('禁用成功！',$this->view->seed_BaseUrl ."/buygoods");
		}
	}

	public function validAction()
	{
		$kyURL= $this->view->seed_Setting['KyUrl'].'/productApi';
		$kyoption = new HessianOptions();
		$kyoption->typeMap['requestObject'] = 'com.jtec.etrade.api.dto.RequestObject';
		$userM = new Kyapi_Controller_Common($kyURL,$kyoption);
		// 请求Hessian服务端方法
		$productID=$_SERVER['QUERY_STRING'];
		$_productID =base64_decode($productID);
		$_requestOb=$this->_requestObject;
		$existData= $userM->validProductApi($_requestOb,$_productID);

		if ($existData->status != 1) {
			echo $existData->error, "<br />",
			Shop_Browser::redirect('启用失败！',$this->view->seed_BaseUrl ."/buygoods");
		} else {
			Shop_Browser::redirect('启用成功！',$this->view->seed_BaseUrl ."/buygoods");
		}
	}
	public function confrimAction()
	{
		$kyURL= $this->view->seed_Setting['KyUrl'].'/productApi';
		$kyoption = new HessianOptions();
		$kyoption->typeMap['requestObject'] = 'com.jtec.etrade.api.dto.RequestObject';
		$userM = new Kyapi_Controller_Common($kyURL,$kyoption);
		// 请求Hessian服务端方法
		$productID=$_SERVER['QUERY_STRING'];
		$_productID =base64_decode($productID);
		$_requestOb=$this->_requestObject;
		$existData= $userM->confrimProductApi($_requestOb,$_productID);

		if ($existData->status != 1) {
			echo $existData->error, "<br />",
			Shop_Browser::redirect('确认失败！',$this->view->seed_BaseUrl ."/buygoods");
		} else {
			Shop_Browser::redirect('确认成功！',$this->view->seed_BaseUrl ."/buygoods");
		}
	}
	public function delAction()
	{
		$kyURL= $this->view->seed_Setting['KyUrl'].'/productApi';
		$kyoption = new HessianOptions();
		$kyoption->typeMap['requestObject'] = 'com.jtec.etrade.api.dto.RequestObject';
		$userM = new Kyapi_Controller_Common($kyURL,$kyoption);
		// 请求Hessian服务端方法
		$productID=$_SERVER['QUERY_STRING'];
		$_productID =base64_decode($productID);
		$_requestOb=$this->_requestObject;
		$existData= $userM->delProductApi($_requestOb,$_productID);

		if ($existData->status != 1) {
			echo $existData->error, "<br />",
			Shop_Browser::redirect('删除失败！',$this->view->seed_BaseUrl ."/buygoods");
		} else {
			Shop_Browser::redirect('删除成功！',$this->view->seed_BaseUrl ."/buygoods");
		}
	}
}