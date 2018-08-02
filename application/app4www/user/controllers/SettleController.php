<?php
class SettleController extends Kyapi_Controller_Action
{
	/**入口文件**/
    public function preDispatch()
    {
        $this->view->cur_pos = 'settle';
        //清除 免登陆session
        $this->IsAuth($this->view->visitor);

        if(empty($this->view->userID)){
            //判断是否免登陆接口数据
            //记录当前url
            $url=$_SERVER['REQUEST_URI'];
            $_SESSION['url']=$url;
            //请先登录系统
            Mobile_Browser::redirect($this->view->translate('tip_login_please'),$this->view->seed_Setting['user_app_server']."/login");
        }
        if(empty($this->view->CompUser)){
            //暂无权限访问
            Mobile_Browser::redirect($this->view->translate('tip_auth_no'),$this->view->seed_Setting['user_app_server']."/");
        }
        $this->view->cur_pos = $this->_request->getParam('controller');
        $cururl = $this->getRequestUri();
        if ($cururl == '/settle') {
            $this->_request->setParam('all', 'all');
            $this->indexAction();
            exit;
        }

        preg_match('/(.*)\.html/', $cururl, $arr);

        if (isset($arr[1]) && !empty($arr[1])) {


            preg_match_all('/^\/user\/settle\/(index|list)-([\d]+)-([\d]+).html/isU', $cururl, $arr);

            if (is_array($arr) && count($arr) > 1) {
                $this->_request->setParam('mod', $arr[1][0]);
                $this->_request->setParam('status', $arr[2][0]);
                $this->_request->setParam('page', $arr[3][0]);
                if($arr[1][0]=='list'){
                    $this->listAction();
                }elseif($arr[1][0]=='lslist'){
                    $this->contactAction();
                }else{
                $this->indexAction();
                }
                exit;
            }

            Mobile_Browser::redirect($this->view->translate('tip_find_no'), $this->view->seed_BaseUrl . "/");

        }
    }


	/**结算列表**/
	public function indexAction()
	{
	    try{

        //获取结算中心详情
//        $_req = new Kyapi_Model_requestObject();
//        $_req->sessionID = "hs2tei8nlt4a50nv57bhr9r0m4";
//        $_req->userID= "D468C09D-9D8F-8D75-FBBC-0537FE6E0B2F";
//        $_req->accountID= "72E13EF4-3A16-6CCE-7B42-1D93CC9D910F";
//        $_req->lang = "zh_CN";
//        $_req->client = "192.168.5.100";
//        $_req->timeZone = "GMT +8:00";

        $_initPWD=$this->json->paymentgetAccountUserApi($this->_requestObject);
        $_initData=$this->objectToArray(json_decode($_initPWD));
        $this->view->init = $_initData['result'];
        if($_initData['result']['userStatus']=='00'){
            Shop_Browser::redirect($this->view->translate('tip_payment_pwd'),$this->view->seed_BaseUrl . "/settle/initpwd");
        }

        $_resultData=$this->json->paymentViewApi($this->_requestObject);
        $existData = json_decode($_resultData);
        $existDatt = $this->objectToArray($existData);
        $this->view->e = $existDatt['result'];

        //获取最近交易记录
        $_resultList=$this->json->listpaymentTradApi($this->_requestObject,null, null, null,0, 10);

        $existList = json_decode($_resultList);
        $dataList = $this->objectToArray($existList);
        $this->view->list = $dataList['result'];

        //获取信用额度百分比
          if($existDatt['result']['creditLimit']=='0'){
             $this->view->jd= '0';}else{
              $this->view->jd=  round($existDatt['result']['creditBal'])/round($existDatt['result']['creditLimit'])>0?round(($existDatt['result']['creditBal'])/round($existDatt['result']['creditLimit'])*100):'0';
          }

        //获取多币种余额 各币种余额组成

         if(is_array($existDatt['result']['paymentAccountBalList'])){
             $item=array();
             $item2=array();
             foreach($existDatt['result']['paymentAccountBalList'] as $k=>$v){

                //计算余额 总金额和货币
                if(!isset($item[$v['crnCode']])){
                     $item[$v['crnCode']]=$v;
                }else
                {
                    $item[$v['crnCode']]['balAmount']+=$v['balAmount'];
                }

                  if($v['crnCode']=='CNY'){
                      //自由余额 balType->F&& balStatus->N+L
                     if($v['balType']=='F'&&($v['balStatus']=='N'||$v['balStatus']=='L')){
                       $this->view->amount_CNY_F+=$v['balAmount'];
                      }
                     //定向余额 balType->D&& balStatus->N+L
                     if($v['balType']=='D'&&($v['balStatus']=='N'||$v['balStatus']=='L')){
                         $this->view->amount_CNY_D+=$v['balAmount'];
                      }
                      //冻结余额 && balStatus->B
                     if($v['balStatus']=='B'){
                       $this->view->amount_CNY_B+=$v['balAmount'];
                     }
                  }
                  if($v['crnCode']=='USD'){
                     //自由余额 balType->F&& balStatus->N+L
                     if($v['balType']=='F'&&($v['balStatus']=='N'||$v['balStatus']=='L')){
                         $this->view->amount_USD_F+=$v['balAmount'];
                     }
                     //定向余额 balType->D&& balStatus->N+L
                     if($v['balType']=='D'&&($v['balStatus']=='N'||$v['balStatus']=='L')){
                         $this->view->amount_USD_D+=$v['balAmount'];
                     }
                     //冻结余额 && balStatus->B
                     if($v['balStatus']=='B'){
                         $this->view->amount_USD_B+=$v['balAmount'];
                     }
                  }
             }



         }

       $this->view->paymentAccountBalList=$item;
       $this->view->paymentAccountBalList2=$existDatt['result']['paymentAccountBalList'];

        //获取余额
        if(is_array($existDatt['result']['paymentTradingBalList'])){
            $item3=array();
            foreach($existDatt['result']['paymentTradingBalList'] as $k=>$v){
                $item3[$v['rptype']][$k]['crnCode']=$v['crnCode'];
                $item3[$v['rptype']][$k]['balAmount']=$v['balAmount'];
            }
        }


       $this->view->paymentTradingBalList=$item3;
        } catch (Exception $e) {
            Shop_Browser::redirect($e->getMessage());
        }

		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/settle/index.phtml");
			echo $content;
			exit;
		}
	}


	/**结算列表**/
	public function listAction()
	{
        try{
//        $_req = new Kyapi_Model_requestObject();
//        $_req->sessionID = "hs2tei8nlt4a50nv57bhr9r0m4";
//        $_req->userID= "D468C09D-9D8F-8D75-FBBC-0537FE6E0B2F";
//        $_req->accountID= "72E13EF4-3A16-6CCE-7B42-1D93CC9D910F";
//        $_req->lang = "zh_CN";
//        $_req->client = "192.168.5.100";
//        $_req->timeZone = "GMT +8:00";


            $f1 = new Seed_Filter_Alnum();
            $mod = $f1->filter($this->_request->getParam('mod'));
            if (empty($mod)) {$mod = "list";}

            $_PStatus =strval($this->_request->getParam('status'));
            if(empty( $_PStatus)){  $_PStatus ='03';}

            $_querySorts=$this->_request->getParam('querySorts');
            if(empty($_querySorts)){ $_querySorts =null;}

            $_keyword=$this->_request->getParam('keyword');
            if(empty($_keyword)){ $_keyword =null;}
            $this->view->keyword=$_keyword;

            $page =intval($this->_request->getParam('page'));
            if($page<1)$page=1;
            $_limit=8;
            $_skip=$_limit*($page-1);
            /*起始时间*/
            $_startTime=empty($this->_request->getParam('startDate'))?null:$this->_request->getParam('startDate');
            if (!empty($_startTime)) {
                $_startDate = date("Y-m-d\TH:i:s", strtotime($_startTime));
            }

            $this->view->startDate=$_startTime;
            $_endTime=empty($this->_request->getParam('endDate'))?null:$this->_request->getParam('endDate');
            if (!empty($_endTime)) {
                $_endDate = date("Y-m-d\TH:i:s", strtotime($_endTime));
            }
            $this->view->endDate=$_endTime;
            /*起始时间Ending*/
            $_lowerAmount=empty($this->_request->getParam('lowerAmount'))?null:$this->_request->getParam('lowerAmount');
            $this->view->lowerAmount=$_lowerAmount;
            $_upperAmount=empty($this->_request->getParam('upperAmount'))?null:$this->_request->getParam('upperAmount');
            $this->view->upperAmount=$_upperAmount;
            $_paymentStatus=empty($this->_request->getParam('paymentStatus'))?null:$this->_request->getParam('paymentStatus');
            $this->view->paymentStatus=$_paymentStatus;
            $_tradingType=empty($this->_request->getParam('tradingType'))?null:$this->_request->getParam('tradingType');
            $this->view->tradingType=$_tradingType;
            $_transNo=empty($this->_request->getParam('transNo'))?null:$this->_request->getParam('transNo');
            $this->view->transNo=$_transNo;
            $_oppCustomerNames=empty($this->_request->getParam('oppCustomerNames'))?null:$this->_request->getParam('oppCustomerNames');
            $this->view->oppCustomerNames=$_oppCustomerNames;
            /*币种*/
            $_crnstring=empty($this->_request->getParam('crnArray'))?null:$this->_request->getParam('crnArray');
            $_crnCodes=empty($this->_request->getParam('crnCode'))?null:$this->_request->getParam('crnCode');
            $this->view->dfcrnCode=$_crnCode=($_crnCodes)?$_crnCodes:$this->view->crnCode;

            $flolist=array();
            if($_crnCodes){
                $flolist['crnCode']=$_crnCodes;
            }
            if($_oppCustomerNames){
                $flolist['oppCustomerDesc']=$_oppCustomerNames;
            }

            $Flowlist=$this->arrayToObject($flolist);


            //获取交易记录列表
            $_resultData=$this->json->listpaymentTradApi($this->_requestObject,$Flowlist, null, $_keyword, $_skip, $_limit,$_startDate,$_endDate,$_lowerAmount,$_upperAmount,$_paymentStatus,$_tradingType,$_transNo);
            $existData = json_decode($_resultData);
            $existDatt = $this->objectToArray($existData);
            $this->view->e = $existDatt['result'];

            //获取货币集合
            if($_crnstring){
                $this->view->crnArr=explode('|',$_crnstring);
                $this->view->crnString =$_crnstring;
            }else{
                $crnArr=array();
                foreach ($existDatt['result'] as $k=>$v){
                    $crnArr[]=$v['crnCode'];
                }
                $this->view->crnArr=array_unique($crnArr);
                $this->view->crnString =implode('|',$this->view->crnArr);
            }


            //统计正常状态数量、分页
            $existCount = $existDatt['extData'];
            $total = $existCount['totalSize'];
            $page=$existCount['totalPage'];

            //设置视图商品状态
            $this->view->status= $_PStatus;


            $file = "user/settle/" . $mod . "-" . $_PStatus;
            $_limit=8;
            $pageObj = new Seed_Page($this->_request,$total,$_limit);
            $this->view->page = $pageObj->getPageArray();
            $this->view->page['pageurl'] = '/' . $file;
            if ($page > $this->view->page['totalpage'])
                $page = $this->view->page['totalpage'];
            if ($page < 1) $page = 1;

        } catch (Exception $e) {
            Shop_Browser::redirect($e->getMessage());
        }

            if(defined('SEED_WWW_TPL')){
                $content = $this->view->render(SEED_WWW_TPL."/settle/list.phtml");
                echo $content;
                exit;
            }
     }

    public function paymentTradingListAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;

        $queryParams = array();

        $querySorts = array();
        // $querySorts['createTime'] = "DESC";

        $keyword = $this->_request->getParam('keyword');
        if (empty($keyword)) {
            $keyword = null;
        }

        $limit = $this->_request->getParam('limit');
        if (empty($limit) || $limit <= 0) {
            $limit = 10;
        }

        $skip = $this->_request->getParam('skip');
        if (empty($limit) || $limit <= 0) {
            $skip = 0;
        }

        if (is_array($queryParams)) {
            $queryParams = $this->arrayToObject($queryParams);
        }

        if (is_array($querySorts)) {
            $querySorts = $this->arrayToObject($querySorts);
        }

        $startDate = $this->_request->getParam('startDate');
        if (empty($startDate)) {
            $startDate = null;
        }
        $endDate = $this->_request->getParam('endDate');
        if (empty($endDate)) {
            $endDate = null;
        }

        $lowerAmount = $this->_request->getParam('lowerAmount');
        $upperAmount = $this->_request->getParam('upperAmount');

        $paymentStatus = strval($this->_request->getParam('paymentStatus'));
        if (empty($paymentStatus)) {
            $paymentStatus = null;
        }

        $tradingType = strval($this->_request->getParam('tradingType'));
        if (empty($tradingType)) {
            $tradingType = null;
        }

        $transNo = strval($this->_request->getParam('transNo'));
        if (empty($transNo)) {
            $transNo = null;
        }

        $resultObject = $this->json->listpaymentTradApi($requestObject, $queryParams, $querySorts, $keyword, $skip, $limit, $startDate, $endDate,
            $lowerAmount, $upperAmount, $paymentStatus, $tradingType, $transNo);
        $msg["total"] = json_decode($resultObject)->extData->totalSize;
        $msg["rows"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    public function lslistAction()
    {
        try{
//        $_req = new Kyapi_Model_requestObject();
//        $_req->sessionID = "hs2tei8nlt4a50nv57bhr9r0m4";
//        $_req->userID= "D468C09D-9D8F-8D75-FBBC-0537FE6E0B2F";
//        $_req->accountID= "72E13EF4-3A16-6CCE-7B42-1D93CC9D910F";
//        $_req->lang = "zh_CN";
//        $_req->client = "192.168.5.100";
//        $_req->timeZone = "GMT +8:00";


            $f1 = new Seed_Filter_Alnum();
            $mod = $f1->filter($this->_request->getParam('mod'));
            if (empty($mod)) {$mod = "list";}

            $_PStatus =strval($this->_request->getParam('status'));
            if(empty( $_PStatus)){  $_PStatus ='03';}

            $_querySorts=$this->_request->getParam('querySorts');
            if(empty($_querySorts)){ $_querySorts =null;}

            $_keyword=$this->_request->getParam('keyword');
            if(empty($_keyword)){ $_keyword =null;}
            $this->view->keyword=$_keyword;

            $page =intval($this->_request->getParam('page'));
            if($page<1)$page=1;
            $_limit=8;
            $_skip=$_limit*($page-1);
            /*起始时间*/
            $_startTime=empty($this->_request->getParam('startDate'))?null:$this->_request->getParam('startDate');
            if (!empty($_startTime)) {
                $_startDate = date("Y-m-d\TH:i:s", strtotime($_startTime));
            }

            $this->view->startDate=$_startTime;
            $_endTime=empty($this->_request->getParam('endDate'))?null:$this->_request->getParam('endDate');
            if (!empty($_endTime)) {
                $_endDate = date("Y-m-d\TH:i:s", strtotime($_endTime));
            }
            $this->view->endDate=$_endTime;
            /*起始时间Ending*/
            /*金额*/
            $_lowerAmount=empty($this->_request->getParam('lowerAmount'))?null:$this->_request->getParam('lowerAmount');
            $this->view->lowerAmount=$_lowerAmount;
            $_upperAmount=empty($this->_request->getParam('upperAmount'))?null:$this->_request->getParam('upperAmount');
            $this->view->upperAmount=$_upperAmount;
            /*状态*/
            $_tradingStatus=empty($this->_request->getParam('tradingStatus'))?null:$this->_request->getParam('tradingStatus');
            $this->view->tradingStatus=$_tradingStatus;
            /*状态*/
            $_tradingType=empty($this->_request->getParam('tradingType'))?null:$this->_request->getParam('tradingType');
            $this->view->tradingType=$_tradingType;
            /*订单号*/
            $_transNo=empty($this->_request->getParam('transNo'))?null:$this->_request->getParam('transNo');
            $this->view->transNo=$_transNo;

            /*币种*/
            $_crnstring=empty($this->_request->getParam('crnArray'))?null:$this->_request->getParam('crnArray');
            $_crnCodes=empty($this->_request->getParam('crnCode'))?null:$this->_request->getParam('crnCode');
            $this->view->dfcrnCode=$_crnCode=($_crnCodes)?$_crnCodes:$this->view->crnCode;
            /*流入*/
            $_debitCredit=empty($this->_request->getParam('debitCredit'))?null:$this->_request->getParam('debitCredit');
            $this->view->debitCredit=$_debitCredit;
            $flolist=array();
            if($_crnCodes){
                $flolist['crnCode']=$_crnCodes;
            }
           if($_debitCredit){
                $flolist['debitCredit']=$_debitCredit;
           }
           $Flowlist=$this->arrayToObject($flolist);
//            $Flowlist=new Kyapi_Model_Flowlist();
//            $Flowlist->crnCode=$_crnCodes;
//            $Flowlist->debitCredit=$_debitCredit;

            //获取交易记录列表
            $_FlowTotal=$this->json->getPaymentFlowTotal($this->_requestObject,$Flowlist, null, $_keyword, $_skip, $_limit,$_startDate,$_endDate,$_lowerAmount,$_upperAmount,$_tradingStatus,$_tradingType,$_transNo);
            $FlowDatt=$this->objectToArray(json_decode($_FlowTotal));
            $this->view->flow=$FlowDatt['result'];
            $_resultData=$this->json->listPaymentFlowApi($this->_requestObject,$Flowlist, null, $_keyword, $_skip, $_limit,$_startDate,$_endDate,$_lowerAmount,$_upperAmount,$_tradingStatus,$_tradingType,$_transNo);
            $existData = json_decode($_resultData);
            $existDatt = $this->objectToArray($existData);
            $this->view->e = $existDatt['result'];
            //获取货币集合
            if($_crnstring){
                $this->view->crnArr=explode('|',$_crnstring);
                $this->view->crnString =$_crnstring;
            }else{
                $crnArr=array();
                foreach ($existDatt['result'] as $k=>$v){
                    $crnArr[]=$v['crnCode'];
                }
                $this->view->crnArr=array_unique($crnArr);
                $this->view->crnString =implode('|',$this->view->crnArr);
            }
            //流入 流出总额计算
            foreach ($FlowDatt['result'] as $k=>$v){
                if($v['crnCode']==$this->view->dfcrnCode){
                    if($v['debitCredit']=='C'){
                         $this->view->debitCredit_c+=$v['totalAmount'];
                    }

                    if($v['debitCredit']=='D'){
                        $this->view->debitCredit_d+=$v['totalAmount'];
                    }
                }
            }


            //统计正常状态数量、分页
            $existCount = $existDatt['extData'];
            $total = $existCount['totalSize'];
            $page=$existCount['totalPage'];

            //设置视图商品状态
            $this->view->status= $_PStatus;


            $file = "user/settle/" . $mod . "-" . $_PStatus;
            $_limit=8;
            $pageObj = new Seed_Page($this->_request,$total,$_limit);
            $this->view->page = $pageObj->getPageArray();
            $this->view->page['pageurl'] = '/' . $file;
            if ($page > $this->view->page['totalpage'])
                $page = $this->view->page['totalpage'];
            if ($page < 1) $page = 1;

        } catch (Exception $e) {
            Shop_Browser::redirect($e->getMessage());
        }

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/settle/lslist.phtml");
            echo $content;
            exit;
        }
    }


    /**结算列表**/
    public function listviewAction()
    {
        try{
//        $_req = new Kyapi_Model_requestObject();
//        $_req->sessionID = "hs2tei8nlt4a50nv57bhr9r0m4";
//        $_req->userID= "D468C09D-9D8F-8D75-FBBC-0537FE6E0B2F";
//        $_req->accountID= "72E13EF4-3A16-6CCE-7B42-1D93CC9D910F";
//        $_req->lang = "zh_CN";
//        $_req->client = "192.168.5.100";
//        $_req->timeZone = "GMT +8:00";

        $tradingID=$_SERVER['QUERY_STRING'];
        $_tradingID =base64_decode($tradingID);
        //获取交易记录详情
        $_resultData=$this->json->getpaymentTradApi($this->_requestObject,$_tradingID);
        $existData = json_decode($_resultData);
        $existDatt = $this->objectToArray($existData);
        $this->view->e = $existDatt['result'];
        if($existDatt['result']['rptype']=="R"){
            $this->view->t1="+";
        }elseif ($existDatt['result']['rptype']=="R"){
            $this->view->t1="-";
        }
        } catch (Exception $e) {
            Shop_Browser::redirect($e->getMessage());
        }


        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/settle/listview.phtml");
            echo $content;
            exit;
        }
    }

	/**查看流水信息**/
	public function viewAction()
	{
        //获取交易记录详情
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
            $this->view->keyword=$_keyword;

            $page =intval($this->_request->getParam('page'));
            if($page<1)$page=1;
            $_limit=5;
            $_skip=$_limit*($page-1);


            //获取交易记录列表
            $_resultData=$this->json->listPaymentFlowApi($this->_requestObject,null, null, $_keyword, $_skip, $_limit);
            $existData = json_decode($_resultData);
            $existDatt = $this->objectToArray($existData);
            $this->view->e = $existDatt['result'];
        } catch (Exception $e) {
            Shop_Browser::redirect($e->getMessage());
        }

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/settle/list.phtml");
            echo $content;
            exit;
        }
	}

    public function paymentAction()
    {
        $_initPWD=$this->json->paymentgetAccountUserApi($this->_requestObject);
        $_initData=$this->objectToArray(json_decode($_initPWD));
        $this->view->init = $_initData['result'];
       //获取订单支付信息
        $tradingID=$_SERVER['QUERY_STRING'];
        $_tradingID =base64_decode($tradingID);
        $_resultData=$this->json->Request4PaymentApi($this->_requestObject,$_tradingID);
        $existData=json_decode($_resultData);
        $existArr=$this->objectToArray($existData);
        $this->view->payment=$existArr['result'];
        $this->view->paymentTrading=$existArr['result']['paymentTrading'];
        $this->view->paymentDetailList=$existArr['result']['paymentDetailList'];
        $this->view->paymentAccountBal=$existArr['result']['paymentAccountBalList'];

        //判断自由余额模块

        if ($this->_request->isPost()) {
            //获取附件ID
            $Atachlist = array();
            $Atachlist["attachID"] = $this->_request->getParam('attachNid');
            $Atachlist["attachType"] = $this->_request->getParam('attachType');
            $Atachlist["bizType"] = $this->_request->getParam("bizType");
            $Atachlist["attachName"] = $this->_request->getParam("attachName");
            $Atachlist["attachSize"] = $this->_request->getParam("attachSize");
            $_attach2 = array();
            foreach ($Atachlist as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $_attach2[$k1][$k] = $v1;
                }
            }
            $_attachList = array();
            foreach ($_attach2 as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $_attachList[$k] = new Kyapi_Model_Attachment();
                    $_attachList[$k]->attachID = $_attach2[$k]['attachID'];
                    $_attachList[$k]->attachType = $_attach2[$k]['attachType'];
                    $_attachList[$k]->bizType = $_attach2[$k]['bizType'];
                    $_attachList[$k]->name = $_attach2[$k]['attachName'];
                    $_attachList[$k]->size = (int)$_attach2[$k]['attachSize'];

                }
            }
            //入参支付请求
            $_paymentRequest=new Kyapi_Model_paymentRequest();//实例化
            $_paymentRequest->crnCode=$existArr['result']['paymentTrading']['crnCode'];
            $_paymentRequest->payAcctID=$existArr['result']['paymentTrading']['payAcctID'];
           $_paymentRequest->balPaymentAmount=empty($this->_request->getParam('balPaymentAmount'))?0:$this->_request->getParam('balPaymentAmount');
            $_paymentRequest->directPaymentAmount=empty($this->_request->getParam('directPaymentAmount'))?0:$this->_request->getParam('directPaymentAmount');
            $_paymentRequest->paymentAmount=empty($this->_request->getParam('paymentAmount'))?0:$this->_request->getParam('paymentAmount');
            $_paymentRequest->paymentPwd=$this->_request->getParam('pwd');
            $dateNow = date("Y-m-d\TH:i:s", time());
            $_paymentRequest->paymentDate=$dateNow;
            $_paymentRequest->tradingID=$existArr['result']['tradingID'];
            $_paymentRequest->explanation=$existArr['result']['PaymentTrading']['remarks'];
            $_paymentRequest->paymentPwd=$this->_request->getParam('paymentPwd');
            $_paymentRequest->attachmentList= $_attachList;//附件集合

            $_resultData=$this->json->addPaymentApi($this->_requestObject,$_paymentRequest);
            $existData=json_decode($_resultData);
            if ($existData->status != 1) {
                //支付失败
                Shop_Browser::redirect($this->view->translate('tip_payment_fail').$existData->error,$this->view->seed_BaseUrl . "/settle");
            } else {
                //支付成功
                Shop_Browser::redirect($this->view->translate('tip_payment_success'),$this->view->seed_BaseUrl . "/settle");
            }

        }

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/settle/payment.phtml");
            echo $content;
            exit;
        }
    }

    //转账页面
    public function transferAction()
    {
        //获取订单支付信息
        $tradingID=$_SERVER['QUERY_STRING'];
        $_tradingID =base64_decode($tradingID);
        $_resultData=$this->json->Request4TransferApi($this->_requestObject,$_tradingID);
        $existData=json_decode($_resultData);
        $existArr=$this->objectToArray($existData);
        $this->view->payment=$existArr['result'];
        $this->view->paymentTrading=$existArr['result']['paymentTrading'];
        //获取自由余额
        foreach ($existArr['result']['paymentAccountBalList'] as $k =>$v){
            if($v['crnCode']==$existArr['result']['crnCode']&&$v['balType']=='F'&&$v['balStatus']=='N'){
                $balfree=$v['balAmount'];
            }
        }
        $this->view->paybal_free=$balfree;

        if($this->_request->isPost()){

            try {
                //获取附件ID
                $Atachlist = array();
                $Atachlist["attachID"] = $this->_request->getParam('attachNid');
                $Atachlist["attachType"] = $this->_request->getParam('attachType');
                $Atachlist["bizType"] = $this->_request->getParam("bizType");
                $Atachlist["attachName"] = $this->_request->getParam("attachName");
                $Atachlist["attachSize"] = $this->_request->getParam("attachSize");
                $_attach2 = array();
                foreach ($Atachlist as $k => $v) {
                    foreach ($v as $k1 => $v1) {
                        $_attach2[$k1][$k] = $v1;
                    }
                }
                $_attachList = array();
                foreach ($_attach2 as $k => $v) {
                    foreach ($v as $k1 => $v1) {
                        $_attachList[$k] = new Kyapi_Model_Attachment();
                        $_attachList[$k]->attachID = $_attach2[$k]['attachID'];
                        $_attachList[$k]->attachType = $_attach2[$k]['attachType'];
                        $_attachList[$k]->bizType = $_attach2[$k]['bizType'];
                        $_attachList[$k]->name = $_attach2[$k]['attachName'];
                        $_attachList[$k]->size = (int)$_attach2[$k]['attachSize'];

                    }
                }
                $_paymentRequest=array();
                $_paymentRequest["payAcctID"]= $existArr['result']['paymentTrading']['payAcctID'];
                $_paymentRequest["tradingID"]= $_tradingID;
                $_paymentRequest["crnCode"]= $existArr['result']['crnCode'];
                $_balPaymentAmount= $this->_request->getParam('balPaymentAmount');
                $_directPaymentAmount= $this->_request->getParam('directPaymentAmount');
                $_banklist= $this->_request->getParam('banklist');
                $_bankArr=explode('|',$_banklist);
                $_paymentRequest["balPaymentAmount"]= (float)$_balPaymentAmount;
                $_paymentRequest["directPaymentAmount"]= (float)$_directPaymentAmount;
                $_paymentRequest["bankAcctID"]= $_bankArr[0];
                $_paymentRequest["bankAcctNo"]=$_bankArr[2];
                $_paymentRequest["bankName"]= $_bankArr[1];
                $dateNow = date("Y-m-d\TH:i:s", time());
                $_paymentRequest["paymentDate"]= $dateNow;
                $_paymentRequest["explanation"]= "转账摘要";
                $_paymentRequest["paymentPwd"]= $this->_request->getParam('paymentPwd');
                $_paymentRequest["attachmentList"]= $_attachList;//附件集合
                $_requestObject=$this->json->transferPaymentApi($this->_requestObject,$_paymentRequest);
                $rebObject=json_decode($_requestObject);
                if($rebObject->status==1){
                    Shop_Browser::redirect($this->view->translate('tip_transfer_success'),$this->view->seed_BaseUrl . "/settle");
                }else{
                    Shop_Browser::redirect($this->view->translate('tip_transfer_fail'),$this->view->seed_BaseUrl . "/settle");
                }

            } catch (HttpError $ex) {
                Shop_Browser::redirect($ex->getMessage());
            }
        }

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/settle/transfer.phtml");
            echo $content;
            exit;
        }
    }

    public function forgetpwdAction()
    {
//       //通过authcode找回密码
//        if ($this->_request->isPost()) {
//            $_timeout=$this->_request->getParam('timeout');
//
//            if($_timeout){
//            $_resultData=$this->json->paymentforgotPasswordApi($this->_requestObject);
//            $_Data=json_decode($_resultData);
//            $time=$_Data->result;
//            $status=$_Data->status;
//
//            }
//        }
        //通过初始化找回密码
        if ($this->_request->isPost()) {
        $_password=$this->_request->getParam('password');
        $_resultData=$this->json->paymentinitPasswordApi($this->_requestObject,$_password);
        $_Data=json_decode($_resultData);
            if ($_Data->status != 1) {
                //编辑失败
                Seed_Browser::autoclose($this->view->translate('tip_edit_fail').$_Data->error,$this->view->seed_BaseUrl . "/settle");
            } else {
                //编辑成功
                Seed_Browser::autoclose($this->view->translate('tip_edit_success'),$this->view->seed_BaseUrl . "/settle");
            }
        }
        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/settle/forgetpwd.phtml");
            echo $content;
            exit;
        }
    }

    public function changepwdAction()
    {
        //忘记密码
       if($this->_request->isPost()){
            $_newPSD=$this->_request->getParam('newPSD');
            $_confirmPSD=$this->_request->getParam('confirmPSD');
           if($_newPSD!=$_confirmPSD){
               Seed_Browser::autoclose($this->view->translate('is_psd'),$this->view->seed_BaseUrl . "/settle/initpwd");
               exit;
           }
            $_loginPwd=$this->_request->getParam('longPSD');
            $_resultData=$this->json->paymentchangePasswordApi($this->_requestObject,$_loginPwd,$_newPSD);
            $_Data=json_decode($_resultData);
           if ($_Data->status != 1) {
               //忘记密码修改
               Seed_Browser::autoclose($this->view->translate('tip_edit_fail').$_Data->error,$this->view->seed_BaseUrl . "/settle");
           } else {
               Seed_Browser::autoclose($this->view->translate('tip_edit_success'),$this->view->seed_BaseUrl . "/settle");
           }
       }
        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/settle/changepwd.phtml");
            echo $content;
            exit;
        }
    }

    public function initpwdAction()
    {
        //初始化密码
        if ($this->_request->isPost()){

            $_password=trim($this->_request->getParam('password'));
            $_password2=trim($this->_request->getParam('password2'));
            if($_password!=$_password2){
                Seed_Browser::autoclose($this->view->translate('is_psd'),$this->view->seed_BaseUrl . "/settle/initpwd");
                exit;
            }
            $_resultData=$this->json->paymentinitPasswordApi($this->_requestObject,$_password);
            $_Data=json_decode($_resultData);
                 if ($_Data->status != 1) {
                     //初始化密码失败
                     Seed_Browser::autoclose($this->view->translate('tip_edit_fail').$_Data->error,$this->view->seed_BaseUrl . "/settle");
                 } else {
                     Seed_Browser::autoclose($this->view->translate('tip_edit_success'),$this->view->seed_BaseUrl . "/settle");
                 }
        }
        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/settle/initpwd.phtml");
            echo $content;
            exit;
        }
    }
    public function mbinitpwdAction()
    {
        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/settle/initpwd_mb.phtml");
            echo $content;
            exit;
        }
    }

    public function authpwdAction()
    {
        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/settle/authpwd.phtml");
            echo $content;
            exit;
        }
    }

    //充值页面
    public function payAction()
    {
        $payAmountView=json_decode($this->json->paymentViewApi($this->_requestObject));
        $payView=$this->objectToArray($payAmountView->result);
        $payAmountbal=json_decode($this->json->paymentgetAccountBal($this->_requestObject));
        $paybal=$this->objectToArray($payAmountbal->result);
        $totalAmount="";
        foreach ($paybal as $k =>$v){
          //  echo $v['balAmount']."<br>";
            if($v['crnCode']==$this->view->crnCode && $v['balType']=='F'&&($v['balStatus']=='N'||$v['balStatus']=='L')){
                $totalAmount+= $v['balAmount'];
            }
        }

        $this->view->totalAmount=$totalAmount;
        $payAmountbank=json_decode($this->json->paymentgetRechargeBank($this->_requestObject));
        $this->view->paybank=$this->objectToArray($payAmountbank->result);
        if($this->_request->isPost()){

            try {
                //获取附件ID
                $Atachlist = array();
                $Atachlist["attachID"] = $this->_request->getParam('attachNid');
                $Atachlist["attachType"] = $this->_request->getParam('attachType');
                $Atachlist["bizType"] = $this->_request->getParam("bizType");
                $Atachlist["attachName"] = $this->_request->getParam("attachName");
                $Atachlist["attachSize"] = $this->_request->getParam("attachSize");
                $_attach2 = array();
                foreach ($Atachlist as $k => $v) {
                    foreach ($v as $k1 => $v1) {
                        $_attach2[$k1][$k] = $v1;
                    }
                }
                $_attachList = array();
                foreach ($_attach2 as $k => $v) {
                    foreach ($v as $k1 => $v1) {
                        $_attachList[$k] = new Kyapi_Model_Attachment();
                        $_attachList[$k]->attachID = $_attach2[$k]['attachID'];
                        $_attachList[$k]->attachType = $_attach2[$k]['attachType'];
                        $_attachList[$k]->bizType = $_attach2[$k]['bizType'];
                        $_attachList[$k]->name = $_attach2[$k]['attachName'];
                        $_attachList[$k]->size = (int)$_attach2[$k]['attachSize'];

                    }
                }
            $_paymentRequest=array();
            $_paymentRequest[ "payAcctID"]= $payView['payAcctID'];
            $_paymentRequest[ "crnCode"]= "CNY";
            $_paymentAmount= $this->_request->getParam('paymentAmount');
            $_paymentRequest[ "paymentAmount"]= (float)$_paymentAmount;
            $_paymentRequest[ "bankAcctID"]= $this->view->paybank['bankAcctID'];
            $_paymentRequest[ "bankAcctNo"]=$this->view->paybank['bankAcctNo'];
            $_paymentRequest[ "bankName"]= $this->view->paybank['bankName'];
            $dateNow = date("Y-m-d\TH:i:s", time());
            $_paymentRequest[ "paymentDate"]= $dateNow;
            $_paymentRequest[ "explanation"]= "充值摘要";
            $_paymentRequest[ "paymentPwd"]= $this->_request->getParam('paymentPwd');
            $_paymentRequest["attachmentList"]= $_attachList;//附件集合
            $_requestObject=$this->json->paymentaddRecharge($this->_requestObject,$_paymentRequest);
            $rebObject=json_decode($_requestObject);
            if($rebObject->status==1){
                Shop_Browser::redirect($this->view->translate('tip_recharge_success'),$this->view->seed_BaseUrl . "/settle");
            }else{
                Shop_Browser::redirect($this->view->translate('tip_recharge_fail'),$this->view->seed_BaseUrl . "/settle/pay");
            }
            } catch (HttpError $ex) {
                Shop_Browser::redirect($ex->getMessage());
            }
        }

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/settle/pay.phtml");
            echo $content;
            exit;
        }
    }

    //提现页面
    public function drawAction()
    {
        //获取当前帐户余额接口
        $payAmountbal=json_decode($this->json->paymentgetAccountBal($this->_requestObject));
        $paybal=$this->objectToArray($payAmountbal->result);
        $paybal_cny=array();
        foreach ($paybal as $k =>$v){
            $paybal_cny[$v['crnCode']][]=$v;
        }
        foreach ($paybal_cny as $k =>$v){
            foreach ($v as $k2 =>$v2){
            if( $v2['balType']=='F'&&($v2['balStatus']=='N'||$v2['balStatus']=='L')){
                $paybal_cny[$k]['balAmount']+= $v2['balAmount'];
            }
            }
        }
        $this->view->paybal_cny=$paybal_cny;

        $payAmountView=json_decode($this->json->paymentViewApi($this->_requestObject));
        $payView=$this->objectToArray($payAmountView->result);
        if($this->_request->isPost()){

            try {
                //获取附件ID
                $Atachlist = array();
                $Atachlist["attachID"] = $this->_request->getParam('attachNid');
                $Atachlist["attachType"] = $this->_request->getParam('attachType');
                $Atachlist["bizType"] = $this->_request->getParam("bizType");
                $Atachlist["attachName"] = $this->_request->getParam("attachName");
                $Atachlist["attachSize"] = $this->_request->getParam("attachSize");
                $_attach2 = array();
                foreach ($Atachlist as $k => $v) {
                    foreach ($v as $k1 => $v1) {
                        $_attach2[$k1][$k] = $v1;
                    }
                }
                $_attachList = array();
                foreach ($_attach2 as $k => $v) {
                    foreach ($v as $k1 => $v1) {
                        $_attachList[$k] = new Kyapi_Model_Attachment();
                        $_attachList[$k]->attachID = $_attach2[$k]['attachID'];
                        $_attachList[$k]->attachType = $_attach2[$k]['attachType'];
                        $_attachList[$k]->bizType = $_attach2[$k]['bizType'];
                        $_attachList[$k]->name = $_attach2[$k]['attachName'];
                        $_attachList[$k]->size = (int)$_attach2[$k]['attachSize'];

                    }
                }
                $_paymentRequest=array();
                $_paymentRequest[ "payAcctID"]= $payView['payAcctID'];
                $_paymentRequest[ "crnCode"]= $payView['crnCode'];
                $_paymentAmount= $this->_request->getParam('paymentAmount');
                $_banklist= $this->_request->getParam('banklist');
                $_bankArr=explode('|',$_banklist);
                $_paymentRequest[ "balPaymentAmount"]= (float)$_paymentAmount;
                $_paymentRequest[ "bankAcctID"]= $_bankArr[0];
                $_paymentRequest[ "bankAcctNo"]=$_bankArr[2];
                $_paymentRequest[ "bankName"]= $_bankArr[1];
                $dateNow = date("Y-m-d\TH:i:s", time());
                $_paymentRequest[ "paymentDate"]= $dateNow;
                $_paymentRequest[ "explanation"]= "提现摘要";
                $_paymentRequest["attachmentList"]= $_attachList;//附件集合
                $_paymentRequest[ "paymentPwd"]= $this->_request->getParam('paymentPwd');

                $_requestObject=$this->json->drawPaymentApi($this->_requestObject,$_paymentRequest);
                $rebObject=json_decode($_requestObject);
                if($rebObject->status==1){
                    Shop_Browser::redirect($this->view->translate('tip_draw_success'),$this->view->seed_BaseUrl . "/settle");
                }else{
                    Shop_Browser::redirect($this->view->translate('tip_draw_fail'),$this->view->seed_BaseUrl . "/settle/draw");
                }

            } catch (HttpError $ex) {
                Shop_Browser::redirect($ex->getMessage());
            }
        }

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/settle/draw.phtml");
            echo $content;
            exit;
        }
    }
    public function drawsettleAction(){
        $this->view->draw_crnCode= $draw_crnCode=$this->_request->getParam('draw_crnCode');
        $draw_amount=$this->_request->getParam('draw_amount');
        $this->view->draw_amount=$draw_amount[$draw_crnCode]['balAmount'];

        if(defined('SEED_WWW_TPL')){
            if($draw_crnCode==$this->view->crnCode){
            $content = $this->view->render(SEED_WWW_TPL."/settle/drawt.phtml");
            }else{
                $content = $this->view->render(SEED_WWW_TPL."/settle/drawh.phtml");
            }
            echo $content;
            exit;
        }
    }

    //结汇页面
    public function exchangeAction()
    {
        //获取当前帐户余额接口
        $payAmountbal=json_decode($this->json->paymentgetAccountBal($this->_requestObject));
        $paybal=$this->objectToArray($payAmountbal->result);
        $paybal_cny=array();
        foreach ($paybal as $k =>$v){
            $paybal_cny[$v['crnCode']][]=$v;
        }
        foreach ($paybal_cny as $k =>$v){
                foreach ($v as $k2 =>$v2){
                    $paybal_cny[$k]['balAmount']+= $v2['balAmount'];
                }
        }
        //过滤当前默认货币
        $this->view->paybal_cny=array();
        $this->view->df_crnCode=array();
        foreach ($paybal_cny as $k=>$v){
            if($this->view->crnCode!=$k){
                $this->view->paybal_cny[$k]=$v;
                $this->view->df_crnCode[]=$k;
            }
        }

        $payAmountView=json_decode($this->json->paymentViewApi($this->_requestObject));
        $payView=$this->objectToArray($payAmountView->result);
        if($this->_request->isPost()){

            try {
                $_paymentAmount= $this->_request->getParam('paymount');
                $dateNow = date("Y-m-d\TH:i:s", time());
                $_paymentRequest=array();
                $_paymentRequest[ "payAcctID"]= $payView['payAcctID'];
                $_paymentRequest[ "crnCode"]= $this->_request->getParam('JhcrnCode');
                $_paymentRequest[ "balPaymentAmount"]= (float)$_paymentAmount;
                $_paymentRequest[ "paymentDate"]= $dateNow;
                $_paymentRequest[ "explanation"]= "结汇摘要";
                $_paymentRequest[ "paymentPwd"]= $this->_request->getParam('paymentPwd');
                $_requestObject=$this->json->exchangePaymentApi($this->_requestObject,$_paymentRequest);
                $rebObject=json_decode($_requestObject);
                if($rebObject->status==1){
                    Shop_Browser::redirect($this->view->translate('tip_exchange_success'),$this->view->seed_BaseUrl . "/settle");
                }else{
                    Shop_Browser::redirect($this->view->translate('tip_exchange_fail'),$this->view->seed_BaseUrl . "/settle/exchange");
                }

            } catch (HttpError $ex) {
                Shop_Browser::redirect($ex->getMessage());
            }
        }

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/settle/exchange.phtml");
            echo $content;
            exit;
        }
    }
    public function banklistAction()
    {

        $_querySorts = new querySorts();
        $_querySorts->createTime= "DESC";
        $userKY= $this->json->listBankAccountApi($this->_requestObject,null,$_querySorts,  null, null, null);
        $existDatt =$this->objectToArray(json_decode($userKY));
        $resultData=array();
        foreach ($existDatt['result'] as $k=>$v){
            if($v['bankAcctType']=='BA'&&$v['bankAcctStatus']=='01'){
                $resultData[]=$v;
            }
        }
        //返回json 数组
       echo json_encode($resultData);
        exit;
    }
}

