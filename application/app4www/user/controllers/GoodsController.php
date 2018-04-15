<?php
class GoodsController extends Kyapi_Controller_Action
{
    public function preDispatch()
    {
        $this->view->cur_pos = 'goods';
        //清除 免登陆session
        $this->IsAuth($this->view->visitor);

        if(empty($this->view->userID)){
            // 提示：请先登录系统
            Mobile_Browser::redirect($this->view->translate('tip_login_please'),$this->view->seed_Setting['user_app_server']."/login");
        }
//        if(empty($this->view->CompProductAdmin)){
//            Mobile_Browser::redirect('暂无权限访问！',$this->view->seed_Setting['user_app_server']."/");
//        }
        $this->view->cur_pos = $this->_request->getParam('controller');
        $cururl = $this->getRequestUri();
        if ($cururl == '/goods') {
            $this->_request->setParam('all', 'all');
            $this->indexAction();
            exit;
        }

        preg_match('/(.*)\.html/', $cururl, $arr);

        if (isset($arr[1]) && !empty($arr[1])) {


            preg_match_all('/^\/user\/goods\/(index|orderlist)-([\d]+)-([\d]+).html/isU', $cururl, $arr);

            if (is_array($arr) && count($arr) > 1) {
                $this->_request->setParam('mod', $arr[1][0]);
                $this->_request->setParam('status', $arr[2][0]);
                $this->_request->setParam('page', $arr[3][0]);
                if($arr[1][0]=='orderlist'){
                    $this->orderlistAction();
                }
                $this->indexAction();
                exit;
            }
//没有找到相关信息！
            Mobile_Browser::redirect($this->view->translate('tip_find_no'), $this->view->seed_BaseUrl . "/");

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
            $this->view->keyword=$_keyword;

            $page =intval($this->_request->getParam('page'));
            if($page<1)$page=1;
            $_limit=5;
            $_skip=$_limit*($page-1);

            $_queryP = new queryProduct();
            $_queryP->productStatus = $_PStatus;

            $goodsCount = $this->json->countSaleProductApi($this->_requestObject);
            //统计所有商品数量
            $listConut=json_decode($goodsCount);
            $clConut = $this->objectToArray($listConut);
            $this->view->clConut=$clConut['result'];
            //获取商品列表信息
            $_resultData=$this->json->listSaleProductApi($this->_requestObject,$_queryP, null, $_keyword, $_skip, $_limit);
            $existData = json_decode($_resultData);
            $existDatt = $this->objectToArray($existData);
            $this->view->e = $existDatt['result'];

            //统计正常状态数量、分页
            $existCount = $existDatt['extData'];
            $total = $existCount['totalSize'];
            $page=$existCount['totalPage'];

            //设置视图商品状态
            $this->view->status= $_PStatus;
            $this->view->status=='00'?$linked='edit':$linked='view';
            $this->view->linked=$linked;

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

    public function addAction()
    {
        /**HScode获取**/
//        $_hscodeData=$this->json->listHSCodeApi($this->_requestObject,null, null, null, 0, 0);
//        $hscodeData = json_decode($_hscodeData);
//        $hscodeDatt = $this->objectToArray($hscodeData);
//        $hscodeList = $hscodeDatt['result'];
        if ($this->_request->isPost()) {
            $Atachlist=array();
            $Atachlist["attachID"] =  $this->_request->getParam('attachNid');
            $Atachlist["attachType"] =  $this->_request->getParam('attachType');
//                $Atachlist["bizType"] = $this->_request->getParam("bizType");
            $Atachlist["attachName"] = $this->_request->getParam("attachName");
            $Atachlist["attachSize"] = $this->_request->getParam("attachSize");
            $_attach2=array();
            foreach($Atachlist as $k =>$v){
                foreach($v as $k1=>$v1){
                    $_attach2[$k1][$k]=$v1;
                }
            }
            $_attachList=array();
            foreach($_attach2 as $k=>$v){
                foreach($v as $k1 =>$v1){
                    $_attachList[$k]=new Kyapi_Model_Attachment();
                    $_attachList[$k]->attachID=$_attach2[$k]['attachID'];
                    $_attachList[$k]->attachType=$_attach2[$k]['attachType'];
                    $_attachList[$k]->attachType=$_attach2[$k]['bizType'];
                    $_attachList[$k]->name=$_attach2[$k]['name'];
                    $_attachList[$k]->size=(int)$_attach2[$k]['size'];

                }
            }
            if($this->_request->getParam('needInspection')==1){
                $needInSP=true;
            }else{
                $needInSP=false;
            }


            /*实例化商品类*/
            $_goods=new Kyapi_Model_product();
            /*添加商品信息*/
            //$_goods->productID= $_productID;//商品ID
            $_goods->productName= $this->_request->getParam('productName');//商品名
            $_goods->productEnName= $this->_request->getParam('productEnName');//EN商品名
            $_goods->productBrand= $this->_request->getParam('productBrand');//商品品牌
            $_goods->productModel= $this->_request->getParam('productModel');//商品型号
            $_goods->unitPrice= (double)$this->_request->getParam('unitPrice');//销售单价
            $_goods->crnCode= $this->_request->getParam('crnCode');//货币代码
            $_goods->pricingUnit= $this->_request->getParam('pricingUnit');//交易单位
            $_goods->legalPricingUnit= $this->_request->getParam('legalPricingUnit');//法定计价单位
            $_goods->legalPricingUnit2= $this->_request->getParam('legalPricingUnit2');//法定计价单位2(HScode查询决定是否需要)
            $_goods->hscode= $this->_request->getParam('hscode');//
            $_goods->taxRate= (double)$this->_request->getParam('taxRate');//增值税率
            $_goods->rebateRate= (double)$this->_request->getParam('rebateRate');//退税率
            $_goods->declareElements= $this->_request->getParam('declareElements');//申报要素
            $_goods->functionUsage= $this->_request->getParam('functionUsage');//功能用途
            $_goods->productSize= $this->_request->getParam('productSize');//尺寸规格
            $_goods->productMaterial= $this->_request->getParam('productMaterial');//商品材质
            $_goods->productionMode= $this->_request->getParam('productionMode');//生产方式
            if($_goods->productionMode==02){
                $_goods->supplierID= $this->_request->getParam('supplierID');//供应商ID
                $_goods->purchaseUnitPrice=(double)$this->_request->getParam('purchaseUnitPrice');//采购单价
                $_goods->purchaseCrnCode= $this->_request->getParam('purchaseCrnCode');//采购货币
            }
            $_goods->packingVolume= (double)$this->_request->getParam('packingVolume');//包装体积
            $_goods->packingType= $this->_request->getParam('packingType');//包装类型
            $_goods->netWeight= (double)$this->_request->getParam('netWeight');//净重
            $_goods->grossWeight= (double)$this->_request->getParam('grossWeight');//毛重
            $_goods->needInspection= $needInSP;//是否商检
            $_goods->attachmentList= $_attachList;//商品附件

            $_resultData=$this->json->addSaleProductApi($this->_requestObject,$_goods);
            $existData = json_decode($_resultData);

            if ($existData->status != 1) {
                //增加失败
                Seed_Browser::redirect($this->view->translate('tip_add_fail'). $existData->error,$this->view->seed_BaseUrl ."/goods");
            } else {
                //增加成功
                Shop_Browser::redirect($this->view->translate('tip_add_sucess'),$this->view->seed_BaseUrl ."/goods");
            }

        }
        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/goods/add.phtml");
            echo $content;
            exit;
        }
    }
    public function editAction()
    {
        $productID=$_SERVER['QUERY_STRING'];
        $_productID =base64_decode($productID);
        $_resultData=$this->json->getProductApi($this->_requestObject,$_productID);
        $existData=json_decode($_resultData);
        $existArr=$this->objectToArray($existData);
        $this->view->goods=$existArr['result'];

        if ($this->_request->isPost()) {
            try {
                //获取附件ID
                $Atachlist=array();
                $Atachlist["attachID"] =  $this->_request->getParam('attachNid');
//                $Atachlist["attachType"] =  $this->_request->getParam('attachType');//
//                $Atachlist["bizType"] = $this->_request->getParam("bizType");
                $Atachlist["attachName"] = $this->_request->getParam("attachName");
                $Atachlist["attachSize"] = $this->_request->getParam("attachSize");

                $_attach2=array();
                foreach($Atachlist as $k =>$v){
                    foreach($v as $k1=>$v1){
                        $_attach2[$k1][$k]=$v1;
                    }
                }
                $_attachList=array();
                foreach($_attach2 as $k=>$v){
                    foreach($v as $k1 =>$v1){
                        $_attachList[$k]=new Kyapi_Model_Attachment();
                        $_attachList[$k]->attachID=$_attach2[$k]['attachID'];
                        $_attachList[$k]->attachType="PDPD";
                        $_attachList[$k]->bizType="PD";
                        $_attachList[$k]->name=$_attach2[$k]['attachName'];
                        $_attachList[$k]->size=(int)$_attach2[$k]['attachSize'];

                    }
                }


                if($this->_request->getParam('needInspection')==1){
                    $needInSP=true;
                }else{
                    $needInSP=false;
                }

//                /*实例化商品类*/
                $_goods=new Kyapi_Model_product();
                /*编辑商品信息*/
                $_goods->productID= $_productID;//商品ID
                $_goods->productName= $this->_request->getParam('productName');//商品名
                $_goods->productEnName= $this->_request->getParam('productEnName');//EN商品名
                $_goods->productBrand= $this->_request->getParam('productBrand');//商品品牌
                $_goods->productModel= $this->_request->getParam('productModel');//商品型号
                $_goods->unitPrice= (double)$this->_request->getParam('unitPrice');//销售单价
                $_goods->crnCode= $this->_request->getParam('crnCode');//货币代码
                $_goods->purchaseUnitPrice=(double)$this->_request->getParam('purchaseUnitPrice');//采购单价
                $_goods->purchaseCrnCode= $this->_request->getParam('purchaseCrnCode');//采购货币
                $_goods->pricingUnit= $this->_request->getParam('pricingUnit');//交易单位
                $_goods->legalPricingUnit= $this->_request->getParam('legalPricingUnit');//法定计价单位
                $_goods->legalPricingUnit2= $this->_request->getParam('legalPricingUnit2');//法定计价单位2(HScode查询决定是否需要)
                $_goods->hscode= $this->_request->getParam('hscode');//
                $_goods->taxRate= (double)$this->_request->getParam('taxRate');//增值税率
                $_goods->rebateRate= (double)$this->_request->getParam('rebateRate');//退税率
                $_goods->declareElements= $this->_request->getParam('declareElements');//申报要素
                $_goods->functionUsage= $this->_request->getParam('functionUsage');//功能用途
                $_goods->productSize= $this->_request->getParam('productSize');//尺寸规格
                $_goods->productMaterial= $this->_request->getParam('productMaterial');//商品材质
                $_goods->productionMode= $this->_request->getParam('productionMode');//生产方式
                $_goods->supplierID= $this->_request->getParam('supplierID');//供应商ID
                $_goods->packingVolume= (double)$this->_request->getParam('packingVolume');//包装体积
                $_goods->packingType= $this->_request->getParam('packingType');//包装类型
                $_goods->netWeight= (double)$this->_request->getParam('netWeight');//净重
                $_goods->grossWeight= (double)$this->_request->getParam('grossWeight');//毛重
                $_goods->needInspection= $needInSP;//是否商检
                $_goods->attachmentList= $_attachList;//商品附件

                $_resultData=$this->json->editSaleProductApi($this->_requestObject,$_goods);
                $existData = json_decode($_resultData);
                if ($existData->status != 1) {
                    //编辑失败
                    Shop_Browser::redirect($this->view->translate('tip_edit_fail').$existData->error,$this->view->seed_BaseUrl ."/goods");
                } else {
                    Shop_Browser::redirect($this->view->translate('tip_edit_sucess'),$this->view->seed_BaseUrl ."/goods");
                }
            } catch (HttpError $ex) {
                echo $ex->getMessage();
                Shop_Browser::redirect($ex->getMessage(),$this->view->seed_BaseUrl ."/goods");
            }
        }

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/goods/edit.phtml");
            echo $content;
            exit;
        }
    }

    public function copyAction()
    {
        $productID=$_SERVER['QUERY_STRING'];
        $_productID =base64_decode($productID);
        $_resultData=$this->json->getProductApi($this->_requestObject,$_productID);
        $existData=json_decode($_resultData);
        $existArr=$this->objectToArray($existData);
        $this->view->goods=$existArr['result'];

        if ($this->_request->isPost()) {
            try {
                //获取附件ID
                $Atachlist=array();
                $Atachlist["attachID"] =  $this->_request->getParam('attachNid');
                $Atachlist["attachType"] =  $this->_request->getParam('attachType');
//                $Atachlist["bizType"] = $this->_request->getParam("bizType");
                $Atachlist["attachName"] = $this->_request->getParam("attachName");
                $Atachlist["attachSize"] = $this->_request->getParam("attachSize");
                $_attach2=array();
                foreach($Atachlist as $k =>$v){
                    foreach($v as $k1=>$v1){
                        $_attach2[$k1][$k]=$v1;
                    }
                }
                $_attachList=array();
                foreach($_attach2 as $k=>$v){
                    foreach($v as $k1 =>$v1){
                        $_attachList[$k]=new Kyapi_Model_Attachment();
                        $_attachList[$k]->attachID=$_attach2[$k]['attachID'];
                        $_attachList[$k]->attachType=$_attach2[$k]['attachType'];
                        $_attachList[$k]->attachType=$_attach2[$k]['bizType'];
                        $_attachList[$k]->name=$_attach2[$k]['name'];
                        $_attachList[$k]->size=(int)$_attach2[$k]['size'];

                    }
                }

                if($this->_request->getParam('needInspection')==1){
                    $needInSP=true;
                }else{
                    $needInSP=false;
                }

//                /*实例化商品类*/
                $_goods=new Kyapi_Model_product();
                /*编辑商品信息*/
//                $_goods->productID= $_productID;//商品ID(复制新增不需要商品ID)
                $_goods->productName= $this->_request->getParam('productName');//商品名
                $_goods->productEnName= $this->_request->getParam('productEnName');//EN商品名
                $_goods->productBrand= $this->_request->getParam('productBrand');//商品品牌
                $_goods->productModel= $this->_request->getParam('productModel');//商品型号
                $_goods->unitPrice= (double)$this->_request->getParam('unitPrice');//销售单价
                $_goods->crnCode= $this->_request->getParam('crnCode');//货币代码
                $_goods->purchaseUnitPrice=(double)$this->_request->getParam('purchaseUnitPrice');//采购单价
                $_goods->purchaseCrnCode= $this->_request->getParam('purchaseCrnCode');//采购货币
                $_goods->pricingUnit= $this->_request->getParam('pricingUnit');//交易单位
                $_goods->legalPricingUnit= $this->_request->getParam('legalPricingUnit');//法定计价单位
                $_goods->legalPricingUnit2= $this->_request->getParam('legalPricingUnit2');//法定计价单位2(HScode查询决定是否需要)
                $_goods->hscode= $this->_request->getParam('hscode');//
                $_goods->taxRate= (double)$this->_request->getParam('taxRate');//增值税率
                $_goods->rebateRate= (double)$this->_request->getParam('rebateRate');//退税率
                $_goods->declareElements= $this->_request->getParam('declareElements');//申报要素
                $_goods->functionUsage= $this->_request->getParam('functionUsage');//功能用途
                $_goods->productSize= $this->_request->getParam('productSize');//尺寸规格
                $_goods->productMaterial= $this->_request->getParam('productMaterial');//商品材质
                $_goods->productionMode= $this->_request->getParam('productionMode');//生产方式
                $_goods->supplierID= $this->_request->getParam('supplierID');//供应商ID
                $_goods->packingVolume= (double)$this->_request->getParam('packingVolume');//包装体积
                $_goods->packingType= $this->_request->getParam('packingType');//包装类型
                $_goods->netWeight= (double)$this->_request->getParam('netWeight');//净重
                $_goods->grossWeight= (double)$this->_request->getParam('grossWeight');//毛重
                $_goods->needInspection= $needInSP;//是否商检
                $_goods->attachmentList= $_attachList;//商品附件

                $_resultData=$this->json->addSaleProductApi($this->_requestObject,$_goods);
                $existData = json_decode($_resultData);
                if ($existData->status != 1) {
                    //复制：新增失败
                    Shop_Browser::redirect($this->view->translate('tip_add_fail').$existData->error,$this->view->seed_BaseUrl ."/goods");
                } else {
                    //复制：新增成功
                    Shop_Browser::redirect($this->view->translate('tip_add_sucess'),$this->view->seed_BaseUrl ."/goods");
                }
            } catch (HttpError $ex) {
                echo $ex->getMessage();
                Shop_Browser::redirect($ex->getMessage(),$this->view->seed_BaseUrl ."/goods");
            }
        }

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/goods/copy.phtml");
            echo $content;
            exit;
        }
    }

    public function viewAction()
    {
        $productID=$_SERVER['QUERY_STRING'];
        $_productID =base64_decode($productID);
        $_resultData=$this->json->getProductApi($this->_requestObject,$_productID);
        $existData=json_decode($_resultData);
        $existArr=$this->objectToArray($existData);

        $this->view->goods=$existArr['result'];

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/goods/view.phtml");
            echo $content;
            exit;
        }
    }

    public function forreviewAction()
    {
        //提示：提交审核
        if ($this->_request->isPost()) {
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
            $opData= $this->json->forReviewProductApi($_requestOb,$_objID);
            echo $opData;
        }
        exit;

    }

    public function invalidAction()
    {
        //禁用
        if ($this->_request->isPost()) {
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
            $opData= $this->json->invalidProductApi($_requestOb,$_objID);
            echo $opData;
        }
        exit;

    }

    public function validAction()
    {
        //启用
        if ($this->_request->isPost()) {
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
            $opData= $this->json->validProductApi($_requestOb,$_objID);
            echo $opData;
        }
        exit;
    }

    public function confrimAction()
    {
        //确认
        if ($this->_request->isPost()) {
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
            $opData= $this->json->confrimProductApi($_requestOb,$_objID);
            echo $opData;
        }
        exit;
    }

    public function delAction()
    {
        //删除
        if ($this->_request->isPost()) {
            // 请求Hessian服务端方法bankAcctID
            $_objID = $this->_request->getPost('delID');
            $_requestOb=$this->_requestObject;
            $opData= $this->json->delProductApi($_requestOb,$_objID);
            echo $opData;
        }
        exit;
    }

    public function orderlistAction()
    {

        try{
            $f1 = new Seed_Filter_Alnum();


            $mod = $f1->filter($this->_request->getParam('mod'));
            if (empty($mod)) {$mod = "orderlist";}

            $_PStatus =strval($this->_request->getParam('status'));
            if(empty( $_PStatus)){  $_PStatus ='03';}

            $_querySorts=$this->_request->getParam('querySorts');
            if(empty($_querySorts)){ $_querySorts =null;}

            $_keyword=$this->_request->getParam('keyword');
            if(empty($_keyword)){ $_keyword =null;}
            $this->view->keyword=$_keyword;

            $_id=$this->_request->getParam('id');
            if(empty($_id)){ $_id =null;}
            $idArr=explode("|",$_id);

            $page =intval($this->_request->getParam('page'));
            if($page<1)$page=1;
            $_limit=6;
            $_skip=$_limit*($page-1);

            $_queryP = new queryProduct();
            $_queryP->productStatus = $_PStatus;


            //获取商品列表信息
            $_goodsData = $this->json->listSaleProductApi($this->_requestObject,$_queryP, null, $_keyword, $_skip, $_limit);
            $goodsData=json_decode($_goodsData);
            $existDatt = $this->objectToArray($goodsData);

            foreach ($existDatt['result']  as $k=>$v){
                if(in_array($v['productID'],$idArr)){
                    $existDatt['result'][$k]['isArr']='1';
                }else{
                    $existDatt['result'][$k]['isArr']=null;
                }
            }
            $this->view->e = $existDatt['result'];
            $this->view->gid = $_id;

            //统计正常状态数量、分页
            $existCount = $existDatt['extData'];
            $total = $existCount['totalSize'];
            $page=$existCount['totalPage'];
            //设置视图商品状态
            $this->view->status= $_PStatus;
            $this->view->status=='00'?$linked='edit':$linked='view';
            $this->view->linked=$linked;

            $file = "user/goods/" . $mod . "-" . $_PStatus;
            $_limit=6;
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
            $content = $this->view->render(SEED_WWW_TPL . "/goods/orderlist.phtml");
            echo $content;
            exit;
        }
    }
}