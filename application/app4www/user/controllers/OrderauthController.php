<?php
class orderauthController extends Kyapi_Controller_Action
{
	public function preDispatch()
	{
		$this->view->cur_pos = 'orderauth';
//		if(empty($this->view->userID)){
//			Mobile_Browser::redirect('请先登录系统！',$this->view->seed_Setting['user_app_server']."/login");
//		}

	}

	public function indexAction()
	{
		$_authCode = $_SERVER['QUERY_STRING'];
		if(empty($_authCode)){
		    //验证失败
            Seed_Browser::redirect($this->view->translate('tip_auth_no'),$this->view->seed_BaseUrl ."/index");
            exit;
        }
		//$_authCode = base64_decode($authCode);
        //强制组装session 数据
        $_requestObject = new Kyapi_Model_requestObject();
        $_requestObject->sessionID = "ssssss2";
        $_requestObject->lang = "zh_CN";
        $_requestObject->userID= null;
        $_requestObject->accountID= null;
        $_requestObject->client = "192.168.5.100";
        $_requestObject->timeZone = "GMT +8:00";

		$_requestOb=$this->_requestObject;
		$_resultKY=$this->json->getOrderViewByAuthCodeApi($_requestObject,$_authCode);
		$userKY=$this->objectToArray(json_decode($_resultKY));


		//将session写入redis
		$_SESSION['rev_session']= array(
			'visitor'=>'1',
			'userID' =>$userKY['userID'],
			'accountID'=>$userKY['accountID']);
		//redis写入对应键值对
		$config=array();
		$config['server'] = $this->view->seed_Setting['KyUrlRedis'];
		$config['port'] = $this->view->seed_Setting['KyUrlRedisPort'];
		$redis=new Kyapi_Model_redisInit();
		$redis->connect($config);
		$redis->set('PHPREDIS_ACTIVE_USERS:'.$userKY['userID'],'PHPREDIS_SESSION:'.session_id(),86400);
		$redis->set('PHPREDIS_ACTIVE_SESSION:'.session_id(),$userKY['userID'],86400);

        //强制重新组装session 数据
        $_requestObject2 = new Kyapi_Model_requestObject();
        if(!empty($_SESSION) ){
            $_requestObject2->sessionID = session_id();
        }else{
            $_requestObject2->sessionID = "ssssss2";
        }
        if(empty($userLangCode))$userLangCode="zh_CN";
        $_requestObject2->lang = $userLangCode;
        $_requestObject2->userID= $userKY['userID'];
        $_requestObject2->accountID= $userKY['accountID'];
        $_requestObject2->client = "192.168.5.100";
        $_requestObject2->timeZone = "GMT +8:00";


        $_resultKY2=$this->json->getOrderViewByAuthCodeApi( $_requestObject2,$_authCode);
        $userKY2=$this->objectToArray(json_decode($_resultKY2));

		$this->view->acctID =$userKY2['accountID'];
        $existDatt=$userKY2['result'];
        //当前返回数据为空时 前端显示为无
        if(!isset($existDatt['packingDesc']))$existDatt['packingDesc']=$this->view->translate('noData');  //包装描述
        if(!isset($existDatt['financingRequest']))$existDatt['financingRequest']=$this->view->translate('noData');  //金融要求
        if(!isset($existDatt['customClearanceRequest']))$existDatt['customClearanceRequest']=$this->view->translate('noData'); //报关要求
        if(!isset($existDatt['shippingRequest']))$existDatt['shippingRequest']=$this->view->translate('noData');   //物流要求

        $this->view->orders=$existDatt;
        //判断是否请求合同签订
        if ($existDatt['vendorExecStatus'] == 1||$existDatt['buyerExecStatus'] == 1){
            $bizType='OD';
            $_resultKY = $this->json->listBizContract($_requestOb, $bizType,$existDatt['orderID']);
            $res_contract= json_decode($_resultKY);
            if($res_contract->result){
                $contractList=$this->objectToArray($res_contract->result);
            }
        }
        $this->view->contractList=empty($contractList)?null:$contractList;

		//处理根据返回的运输方式来判断 起运|卸货|交货查询的缓存目录名称
		if ($userKY2['result']['shippingMethod'] == 'SEA') {
			$this->view->port = "SEA_PORT";
		} elseif ($userKY2['result']['shippingMethod'] == 'AIR') {
			$this->view->port = "AIR_PORT";
		} else {
			$this->view->port = "CITY_ISO_CODE";
		};

		$this->view->vestut=$userKY2['result']['vendorExecStatus'];
		$this->view->bystut=$userKY2['result']['buyerExecStatus'];
		$this->view->veorderID=$userKY2['result']['orderID'];
        //订单进度
        $this->view->plan=$this->planAction($userKY2['result']);

		//判断当前订单是否可以更改
		if($this->view->accountID==$userKY2['result']['client']){
			if ($userKY2['result']['orderStatus']==05||$userKY2['result']['orderStatus']==00||$userKY2['result']['orderStatus']==02){
				$this->view->allowEdit=1;
			}else{
				$this->view->allowEdit=0;
			}
		}else{
			$this->view->allowEdit=0;
		}

//		if(isAllowVendorConfirm==true) {
//			$this->view->button='确认';
//		} elseif (isAllowVendorSign==true) {
//			$this->view->button='提交';
//		} elseif (isAllowVendorReady==true) {
//			$this->view->button='备货';
//		}elseif (isAllowVendorDeliver==true) {
//			$this->view->button='发货';
//		}elseif (isAllowVendorSettle==true) {
//			$this->view->button='结算';
//		}else {
//			$this->view->button='为开始';
//		}

		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/orderauth/index.phtml");
			echo $content;
			exit;
		}
	}

	//确认订单
	public function confirmAction()
	{
		// 请求Hessian服务端方法
		$_orderID = $this->_request->getParam('orderID');
		$_requestOb = $this->_requestObject;
		$_resultData = $this->json->doConfirmOrderApi($_requestOb, $_orderID);
		$existData = json_decode($_resultData);
        echo json_encode($existData->status);

//		if ($existData->status != 1) {
//			echo json_encode("未成功确认" . $existData->errorCode);
//		} else {
//			echo json_encode("已成功确认");
//		}
		exit;
	}

	//签订协议
	public function agreeAction()
	{
		// 请求Hessian服务端方法
        $name = $_POST['name'];
        $nid = $_POST['nid'];
        $size = $_POST['size'];
        $attachType=$_POST['attachType'];
        $_orderID = $_POST['orderID'];

        $_nid = explode("|", $nid);
        $_name = explode("|", $name);
        $_size = explode("|", $size);
        $_attachType = explode("|", $attachType);

        foreach ($_nid as $k => $v) {
            if (!$v)
                unset($_nid[$k]);
        }
        foreach ($_name as $k => $v) {
            if (!$v)
                unset($_name[$k]);
        }
        foreach ($_size as $k => $v) {
            if (!$v)
                unset($_size[$k]);
        }
        foreach ($_attachType as $k => $v) {
            if (!$v)
                unset($_attachType[$k]);
        }

        $attach = array();
        $attach['attachID'] = $_nid;
        $attach['name'] = $_name;
        $attach['size'] = $_size;
        $attach['attachType']=$_attachType;
        $_attach2 = array();

        foreach ($attach as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $_attach2[$k1][$k] = $v1;
            }
        }
        $_attachList = array();
        foreach ($_attach2 as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $_attachList[$k] = new Kyapi_Model_Attachment();
                $_attachList[$k]->attachID = $_attach2[$k]['attachID'];
                $_attachList[$k]->name = $_attach2[$k]['name'];
                $_attachList[$k]->size = (int)$_attach2[$k]['size'];
                $_attachList[$k]->attachType = $_attach2[$k]['attachType'];
                if($_attach2[$k]['attachType']=="CRSE"){
                    $_attachList[$k]->bizType = "CR";
                }else{
                    $_attachList[$k]->bizType = "OD";
                }

//				$_attachList[$k]->bizID=$_attach2[$k]['bizID'];
            }
        }


		$_requestOb = $this->_requestObject;
		$_resultData = $this->json->doAgreeContractApi($_requestOb, $_orderID, $_attachList);
		$existData = json_decode($_resultData);
        echo json_encode($existData->status);

		//		if ($existData->status != 1) {
//			echo json_encode('未成功签订协议！');
//		} else {
//			echo json_encode('已成功签订协议！');
//		}
		exit;
	}

	//备货
	public function prepareAction()
	{
		$_requestOb = $this->_requestObject;
		// 请求Hessian服务端方法
		$name = $_POST['name'];
		$nid = $_POST['nid'];
		$size = $_POST['size'];
		$_orderID = $_POST['orderID'];
		$_nid = explode("|", $nid);
		$_name = explode("|", $name);
		$_size = explode("|", $size);

		foreach ($_nid as $k => $v) {
			if (!$v)
				unset($_nid[$k]);
		}
		foreach ($_name as $k => $v) {
			if (!$v)
				unset($_name[$k]);
		}
		foreach ($_size as $k => $v) {
			if (!$v)
				unset($_size[$k]);
		}
		$attach = array();
		$attach['nid'] = $_nid;
		$attach['name'] = $_name;
		$attach['size'] = $_size;

		$_attach2 = array();
		foreach ($attach as $k => $v) {
			foreach ($v as $k1 => $v1) {
				$_attach2[$k1][$k] = $v1;
			}
		}
		$_attachList = array();
		foreach ($_attach2 as $k => $v) {
			foreach ($v as $k1 => $v1) {
				$_attachList[$k] = new Kyapi_Model_Attachment();
				$_attachList[$k]->attachID = $_attach2[$k]['nid'];
				$_attachList[$k]->name = $_attach2[$k]['name'];
				$_attachList[$k]->size = (int)$_attach2[$k]['size'];
				$_attachList[$k]->attachType = "ODPG";
				$_attachList[$k]->bizType = "OD";
			}
		}

		$_resultData = $this->json->doPrepareGoodsApi($_requestOb, $_orderID, $_attachList);
		$existData = json_decode($_resultData);
        echo json_encode($existData->status);

//		if ($existData->status != 1) {
//			echo json_encode('未成功备货！' . $existData->error);
//		} else {
//			echo json_encode('已成功备货！');
//		}
		exit;
	}

	//验货
	public function examineAction()
	{
		$_requestOb = $this->_requestObject;
		// 请求Hessian服务端方法
		$name = $_POST['name'];
		$nid = $_POST['nid'];
		$size = $_POST['size'];
		$_orderID = $_POST['orderID'];
		$_nid = explode("|", $nid);
		$_name = explode("|", $name);
		$_size = explode("|", $size);

		foreach ($_nid as $k => $v) {
			if (!$v)
				unset($_nid[$k]);
		}
		foreach ($_name as $k => $v) {
			if (!$v)
				unset($_name[$k]);
		}
		foreach ($_size as $k => $v) {
			if (!$v)
				unset($_size[$k]);
		}

		$attach = array();
		$attach['nid'] = $_nid;
		$attach['name'] = $_name;
		$attach['size'] = $_size;
		$_attach2 = array();

		foreach ($attach as $k => $v) {
			foreach ($v as $k1 => $v1) {
				$_attach2[$k1][$k] = $v1;
			}
		}
		$_attachList = array();
		foreach ($_attach2 as $k => $v) {
			foreach ($v as $k1 => $v1) {
				$_attachList[$k] = new Kyapi_Model_Attachment();
				$_attachList[$k]->attachID = $_attach2[$k]['nid'];
				$_attachList[$k]->name = $_attach2[$k]['name'];
				$_attachList[$k]->size = (int)$_attach2[$k]['size'];
				$_attachList[$k]->attachType = "ODEG";
				$_attachList[$k]->bizType = "OD";
			}
		}
		$_resultData = $this->json->doExamineGoodsApi($_requestOb, $_orderID, $_attachList);
		$existData = json_decode($_resultData);
        echo json_encode($existData->status);

//		if ($existData->status != 1) {
//			echo json_encode('未成功验货！' . $existData->error);
//		} else {
//			echo json_encode('成功验货！' . $existData->status);
//		}
		exit;
	}

	//发货
	public function deliverAction()
	{

		$_requestOb = $this->_requestObject;
		// 请求Hessian服务端方法
		$name = $_POST['name'];
		$nid = $_POST['nid'];
		$size = $_POST['size'];
		$_orderID = $_POST['orderID'];
		$_nid = explode("|", $nid);
		$_name = explode("|", $name);
		$_size = explode("|", $size);

		foreach ($_nid as $k => $v) {
			if (!$v)
				unset($_nid[$k]);
		}
		foreach ($_name as $k => $v) {
			if (!$v)
				unset($_name[$k]);
		}
		foreach ($_size as $k => $v) {
			if (!$v)
				unset($_size[$k]);
		}

		$attach = array();
		$attach['nid'] = $_nid;
		$attach['name'] = $_name;
		$attach['size'] = $_size;
		$_attach2 = array();

		foreach ($attach as $k => $v) {
			foreach ($v as $k1 => $v1) {
				$_attach2[$k1][$k] = $v1;
			}
		}
		$_attachList = array();
		foreach ($_attach2 as $k => $v) {
			foreach ($v as $k1 => $v1) {
				$_attachList[$k] = new Kyapi_Model_Attachment();
				$_attachList[$k]->attachID = $_attach2[$k]['nid'];
				$_attachList[$k]->name = $_attach2[$k]['name'];
				$_attachList[$k]->size = (int)$_attach2[$k]['size'];
				$_attachList[$k]->attachType = "ODDG";
				$_attachList[$k]->bizType = "OD";
			}
		}

		$_resultData = $this->json->doDeliverGoodsApi($_requestOb, $_orderID, $_attachList);
		$existData = json_decode($_resultData);

		echo json_encode($existData->status);
//		if ($existData->status != 1) {
//			echo json_encode('未成功发货！' . $existData->error);
//		} else {
//			echo json_encode('成功发货！' . $existData->status);
//		}
		exit;
	}

	//收货
	public function receiptAction()
	{
		$_requestOb = $this->_requestObject;
		// 请求Hessian服务端方法
		$name = $_POST['name'];
		$nid = $_POST['nid'];
		$size = $_POST['size'];
		$_orderID = $_POST['orderID'];
		$_nid = explode("|", $nid);
		$_name = explode("|", $name);
		$_size = explode("|", $size);
		foreach ($_nid as $k => $v) {
			if (!$v)
				unset($_nid[$k]);
		}
		foreach ($_name as $k => $v) {
			if (!$v)
				unset($_name[$k]);
		}
		foreach ($_size as $k => $v) {
			if (!$v)
				unset($_size[$k]);
		}

		$attach = array();
		$attach['nid'] = $_nid;
		$attach['name'] = $_name;
		$attach['size'] = $_size;
		$_attach2 = array();

		foreach ($attach as $k => $v) {
			foreach ($v as $k1 => $v1) {
				$_attach2[$k1][$k] = $v1;
			}
		}
		$_attachList = array();
		foreach ($_attach2 as $k => $v) {
			foreach ($v as $k1 => $v1) {
				$_attachList[$k] = new Kyapi_Model_Attachment();
				$_attachList[$k]->attachID = $_attach2[$k]['nid'];
				$_attachList[$k]->name = $_attach2[$k]['name'];
				$_attachList[$k]->size = (int)$_attach2[$k]['size'];
				$_attachList[$k]->attachType = "ODRG";
				$_attachList[$k]->bizType = "OD";
			}
		}
		$_resultData = $this->json->doReceiptGoodsApi($_requestOb, $_orderID, $_attachList);
		$existData = json_decode($_resultData);
        echo json_encode($existData->status);
//		if ($existData->status != 1) {
//			echo json_encode('未成功收货！' . $existData->error);
//		} else {
//			echo json_encode('成功收货！' . $existData->status);
//		}
		exit;
	}

	//订单跟踪日志
	public function trackAction()
	{
		// 请求Hessian服务端方法
		$_requestOb = $this->_requestObject;
		//    $_orderID='EB7E79BD-A9B9-42DC-CBB5-D431264ADC25';
		$_orderID = $this->_request->getParam('orderID');
		$_view = $this->_request->getParam('view');
		if (empty($_view)) {
			$_view = 'date';
		}
		$_resultData = $this->json->getOrderEventLogApi($_requestOb, $_orderID, $_view);
		$existData = json_decode($_resultData);
		$trackData = $existData->result;
		$tracklist = $this->objectToArray($trackData);
		$this->view->tracklog = $tracklist;
		$this->view->trackview = $_view;

		if (defined('SEED_WWW_TPL')) {
			$content = $this->view->render(SEED_WWW_TPL . "/orderauth/track.phtml");
			echo $content;
			exit;
		}
	}

	//报关单
	public function declarationAction()
	{
		// 请求Hessian服务端方法
		//	$_orderID='EB7E79BD-A9B9-42DC-CBB5-D431264ADC25';
		$_orderID = $this->_request->getParam('orderID');
		$_requestOb = $this->_requestObject;
		$_resultData = $this->json->listDeclarationApi($_requestOb, $_orderID);

		$bgdData = json_decode($_resultData);
		$bgOb = $bgdData->result;
		$bgd = $this->objectToArray($bgOb);

		$this->view->bgd = $bgd;

		if (defined('SEED_WWW_TPL')) {
			$content = $this->view->render(SEED_WWW_TPL . "/orderauth/declaration.phtml");
			echo $content;
			exit;
		}
	}

//派车单
	public function truckingAction()
	{
		// 请求Hessian服务端方法
		$_orderID = $this->_request->getParam('orderID');
		$_requestOb = $this->_requestObject;
		$_resultData = $this->json->listTruckingOrderApi($_requestOb, $_orderID);
		$pcdData = json_decode($_resultData);
		$pcOb = $pcdData->result;
		$pcd = $this->objectToArray($pcOb);
		$this->view->pcd = $pcd;

		if (defined('SEED_WWW_TPL')) {
			$content = $this->view->render(SEED_WWW_TPL . "/orderauth/trucking.phtml");
			echo $content;
			exit;
		}
	}

	// 订仓单
	public function shippingAction()
	{
		// 请求Hessian服务端方法
		$_orderID = $this->_request->getParam('orderID');
		$_requestOb = $this->_requestObject;
		$_resultData = $this->json->listShippingOrderApi($_requestOb, $_orderID);
		$dcdData = json_decode($_resultData);
		$dcOb = $dcdData->result;
		$dcd = $this->objectToArray($dcOb);
		$this->view->dcd = $dcd;

		if (defined('SEED_WWW_TPL')) {
			$content = $this->view->render(SEED_WWW_TPL . "/orderauth/shipping.phtml");
			echo $content;
			exit;
		}
	}
    // 结算 模块交易列表
    public function tradingAction()
    {
        // 请求json服务端方法
        $_orderID = $this->_request->getParam('orderID');
        $_requestOb = $this->_requestObject;
        $_resultData = $this->json->Trading4OrderApi($_requestOb, $_orderID);
        $jsdData = json_decode($_resultData);
        $jsdOb = $jsdData->result;
        $jsd = $this->objectToArray($jsdOb);
        $this->view->jsd = $jsd;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/orderauth/trading.phtml");
            echo $content;
            exit;
        }
    }

    // 订单文档
    public function attachmentAction()
    {
        // 请求Hessian服务端方法
        $_orderID = $this->_request->getParam('orderID');
        $_requestOb = $this->_requestObject;
        $_resultData = $this->json->listOrderAttachment($_requestOb, $_orderID);
        $docData = json_decode($_resultData);
        $docOb = $docData->result;
        $doc = $this->objectToArray($docOb);
        //判断文档下级分类重新组装数据
        $this->view->ODOD=array();//订单附件
        $this->view->ODTA=array();
        $this->view->ODSE=array();
        $this->view->ODEG=array();
        $this->view->ODQS=array();
        $this->view->CRCT=array();
        $this->view->ODBQ=array();
        $this->view->CRSE=array();
        $this->view->ODQA=array();
        $this->view->ODVQ=array();
        $this->view->ODRG=array();
        $this->view->ODPG=array();
        $this->view->ODDG=array();
        $this->view->SOTM=array();//订舱单
        $this->view->TOTM=array();//派车单
        $this->view->EDCT=array();//报关单
        $this->view->PL4C=array();//装箱单


        foreach ($doc as $k=>$v){
            if($v['attachType']=="SOTM"){
                $this->view->SOTM[]=$v;
                //订舱单
                $this->view->SOTM_name=$this->view->translate('booking').'('.$v['attachType'].')';
            }

            if($v['attachType']=="TOTM"){
                $this->view->TOTM[]=$v;
                //派车单
                $this->view->TOTM_name=$this->view->translate('carsbook').'('.$v['attachType'].')';
            }

            if($v['attachType']=="EDCT"){
                $this->view->EDCT[]=$v;
                //报关单
                $this->view->EDCT_name=$this->view->translate('customs').'('.$v['attachType'].')';
            }

            if($v['attachType']=="PL4C"){
                $this->view->PL4C[]=$v;
                //装箱单
                $this->view->PL4C_name=$this->view->translate('packing').'('.$v['attachType'].')';
            }
            if($v['attachType']=="ODOD"){
                $this->view->ODOD[]=$v;
                //订单附件
                $this->view->ODOD_name=$this->view->translate('orderATCH').'('.$v['attachType'].')';
            }
            if($v['attachType']=="ODTA"){
                $this->view->ODTA[]=$v;
                //委托书
                $this->view->ODTA_name=$this->view->translate('proxyNo').'('.$v['attachType'].')';
            }

            if($v['attachType']=="ODSE"){

                $this->view->ODSE[]=$v;
                //盖章委托书
                $this->view->ODSE_name=$this->view->translate('delegation').'('.$v['attachType'].')';
            }

            if($v['attachType']=="CRCT"){

                $this->view->CRCT[]=$v;
                //合同范本
                $this->view->CRCT_name=$this->view->translate('contract_tmp').'('.$v['attachType'].')';
            }

            if($v['attachType']=="CRSE"){
                $this->view->CRSE[]=$v;
                //盖章合同
                $this->view->CRSE_name=$this->view->translate('contract_seal').'('.$v['attachType'].')';
            }

            if($v['attachType']=="ODBQ"){
                $this->view->ODBQ[]=$v;
                //买家计价单
                $this->view->ODBQ_name=$this->view->translate('buyers').$this->view->translate('valuationNo').'('.$v['attachType'].')';
            }
            if($v['attachType']=="ODVQ"){
                $this->view->ODVQ[]=$v;
                //卖家计价单
                $this->view->ODVQ_name=$this->view->translate('seller').$this->view->translate('valuationNo').'('.$v['attachType'].')';
            }

            if($v['attachType']=="ODQA"){
                $this->view->ODQA[]=$v;
                //质保函范本
                $this->view->ODQA_name=$this->view->translate('quality_tmp').'('.$v['attachType'].')';
            }

            if($v['attachType']=="ODQS"){
                $this->view->ODQS[]=$v;
                //盖章质保函
                $this->view->ODQS_name=$this->view->translate('quality_seal').'('.$v['attachType'].')';
            }


            if($v['attachType']=="ODEG"){
                $this->view->ODEG[]=$v;
                //验货相关附件查看
                $this->view->ODEG_name=$this->view->translate('examineVIEW').'('.$v['attachType'].')';
            }

            if($v['attachType']=="ODRG"){
                $this->view->ODRG[]=$v;
                //收货相关附件查看
                $this->view->ODRG_name=$this->view->translate('receivingVIEW').'('.$v['attachType'].')';
            }

            if($v['attachType']=="ODPG"){
                $this->view->ODPG[]=$v;
                //备货相关附件查看
                $this->view->ODPG_name=$this->view->translate('stockVIEW').'('.$v['attachType'].')';
            }

            if($v['attachType']=="ODDG"){
                $this->view->ODDG[]=$v;
                //发货相关附件查看
                $this->view->ODDG_name=$this->view->translate('deliverVIEW').'('.$v['attachType'].')';
            }

        }

        $this->view->doc = $doc;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/orderauth/attachment.phtml");
            echo $content;
            exit;
        }
    }

    public function planAction($data){
        /*处理订单进度说明 逻辑关系
        因为关系复杂 故处理顺序如下，先通过买家，卖家身份 对特殊状态进行赋值，发货，备货，验货，收货，等
        （包括状态7时【验货，收货】第二个状态是否显示和隐藏）
        $data 为订单数据
        $exArray 为返回的数据 包括 plan 进度文字，display是否显示：0隐藏，1显示
        */
        $exArray=array();
        if($data['vendor']== $this->view->accountID){
            $exStatus=$data['vendorExecStatus'];
            $lodingSt=$data['buyerExecStatus'];
            $planing02=$this->view->translate('going002_01v');
            $planing031=$this->view->translate('going003_01');
            $planing032=$this->view->translate('going003_02');
            $planing033=$this->view->translate('going003_03');
            $planing051=$this->view->translate('going005_01');
            $planing052=$this->view->translate('going005_02');
            $display052=1;
            $planing053=$this->view->translate('going005_03');
            $planing07=$this->view->translate('going007v');
            $planing08=$this->view->translate('going008v');
        }elseif ($data['buyer']== $this->view->accountID){
            $exStatus=$data['buyerExecStatus'];
            $lodingSt=$data['vendorExecStatus'];
            $planing02=$this->view->translate('going002_01b');
            $planing031=$this->view->translate('going004_01');
            $planing032=$this->view->translate('going004_02');
            $planing033=$this->view->translate('going004_03');
            $planing051=$this->view->translate('going006_01');
            $planing052=$this->view->translate('going006_02');
            $display052=0;
            $planing053=$this->view->translate('going006_03');
            $planing07=$this->view->translate('going007b');
            $planing08=$this->view->translate('going008b');
        }else{
            $exStatus=null;
            $planing02=null;
            $lodingSt=null;
            $planing033=$planing032=$planing031=null;
            $planing08=$planing07=$planing051=$planing052=$display052=$planing053=null;
        }


        switch ($exStatus)
        {
            case "-1":
                $exArray['plan']= $this->view->translate('going000');
                $exArray['display']=null;
                break;
            case "0":
                $exArray['plan']= $this->view->translate('going001');
                $exArray['display']=null;
                break;
            case "1":

                if($lodingSt=='0'&&$data['orderStatus']=='02') {
                    $exArray['plan'] = $planing02;
                    $exArray['display'] = 0;
                }elseif($lodingSt=='1'&&$data['orderStatus']=='02'){
                    $exArray['plan'] = $this->view->translate('going002_02');
                    $exArray['display'] = 0;
                }else{
                    $exArray['plan'] = $this->view->translate('going002_03');
                    $exArray['display'] = 1;
                }

                break;
            case "3":
                if($lodingSt=='1'&&$data['orderStatus']=='03') {
                    $exArray['plan'] = $planing031;
                    $exArray['display'] = 0;
                }elseif($lodingSt=='3'&&$data['orderStatus']=='03'){
                    $exArray['plan'] = $planing032;
                    $exArray['display'] = 0;
                }else{
                    $exArray['plan'] = $planing033;
                    $exArray['display'] = 1;
                }
                break;
            case "7":
                if($lodingSt=='3'&&$data['orderStatus']=='04') {
                    $exArray['plan'] = $planing051;
                    $exArray['display'] = 0;
                }elseif($lodingSt=='7'&&$data['orderStatus']=='04'){
                    $exArray['plan'] = $planing052;
                    $exArray['display'] = $display052;
                }else{
                    $exArray['plan'] = $planing053;
                    $exArray['display'] = 1;
                }
                break;
            case "15":
                $exArray['plan']= $planing07;
                $exArray['display']=null;
                break;
            case "63":
                $exArray['plan']= $planing08;
                $exArray['display']=null;
                break;
            default:
                $exArray['display']=null;
                $exArray['plan']= null;
        }

        return $exArray;

    }

    public function itemAction(){
        $_ItermArr = $this->_request->getParams();

        //调用orderIterm 接口
        $_requestOb=$this->_requestObject;
      //  $_resultData = $this->json->getOrderItemApi($_requestOb, '6128AB0A-5127-A92B-3CCF-477A5ED17106');
       $_resultData = $this->json->getOrderItemApi($_requestOb, $_ItermArr['ID']);
        $userKY=$this->objectToArray(json_decode($_resultData));

        $existDatt=$userKY['result'];

        $this->view->iterm_crnCode=$_ItermArr['crn'];
        $_resultData=array();
        if($_ItermArr['app']=='edit'){
            $_resultData['unitPrice']=$_ItermArr['unitPrice'];
            $_resultData['purUnitPrice']=$_ItermArr['purUnitPrice'];
            $_resultData['totalPrice']=$_ItermArr['totalPrice'];
            $_resultData['totalPurPrice']=$_ItermArr['totalpurpice'];
            $_resultData['quantity']=$_ItermArr['quantity'];//件数
            $_resultData['totalPackage']=$_ItermArr['totalpackage'];//件数
            $_resultData['packingType']=$_ItermArr['packingType'];//包装类型
            $_resultData['productBrand']=$_ItermArr['productBrand'];//品牌
            $_resultData['productModel']=$_ItermArr['productModel'];//型号
            $_resultData['productMaterial']=$_ItermArr['productMaterial'];//材质
            $_resultData['functionUsage']=$_ItermArr['functionUsage'];//功能用途
            $_resultData['packingLength']=$existDatt['packingLength'];//长
            $_resultData['packingWidth']=$existDatt['packingWidth'];//宽
            $_resultData['packingHeight']=$existDatt['packingHeight'];//高
            $_resultData['purCrnCode']=$existDatt['purCrnCode'];//采购货币
            $_resultData['productName']=$existDatt['productName'];//商品名称
            $_resultData['attachmentList']=array();
            $_resultData['attachmentList']=$existDatt['attachmentList'];//附件

            $this->view->iterm=$_resultData;
        }else{
            $this->view->iterm=$existDatt;
        }

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/orderauth/iterm.phtml");
            echo $content;
            exit;
        }
}
}
