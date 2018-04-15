<?php
class BrandController extends Shop_Controller_Action
{
	public function preDispatch()
	{
		$this->view->cur_pos = $this->_request->getParam('controller');
		
		$cururl = $this->getRequestUri();
		preg_match('/(.*)\.html/',$cururl,$arr);
	        if (isset($arr[1]) && !empty($arr[1])) {
            ///入口列表
            preg_match_all('/^\/brand\/(top)-([\d]+).html/isU', $cururl, $arr);
            if (isset($arr[1][0]) && !empty($arr[1][0])) {
                $this->_request->setParam('mod', $arr[1][0]);
                $this->_request->setParam('brand_id', $arr[2][0]);
                $this->indexAction();
                exit;
            }
            
	       preg_match_all('/^\/brand\/([\w]+)-([\d]+)-([\d]+).html/isU', $cururl, $arr);
            if (is_array($arr) && count($arr)>1) {
                    $this->_request->setParam('mod', $arr[1][0]);
                    $this->_request->setParam('brand_id', $arr[2][0]);
                    $this->_request->setParam('page', $arr[3][0]);
                    $this->indexAction();
                    exit;
                }
            Mobile_Browser::redirect('没有找到相关信息！', $this->view->seed_BaseUrl . "/");
        }
	}

	public function indexAction()
	{
		$goodsM = new Shop_Model_Goods('shop');
		$goodsCateM = new Shop_Model_GoodsCate('shop');
		$goodsStockM = new Shop_Model_GoodsStock('shop');
		$goodsBarandM = new Shop_Model_GoodsBrand('shop');
		$shopM = new Shop_Model_Shop('shop');
		
		$f1 = new Seed_Filter_Alnum();
        $mod = $f1->filter($this->_request->getParam('mod'));
        $brand_id = intval($this->_request->getParam('brand_id'));
        
        $conditions = array();
        $conditions['is_actived'] = '1';
        $conditions['is_on_sale'] = '1';
        $conditions['is_auth'] = '1';
        $conditions['brand_id'] = $brand_id;
        $conditions['is_group']='0';//非套装
        $conditions["IFNULL( goods_list_image, '') != ''"]=null;
        $conditions["IFNULL( goods_detail_image, '') != ''"]=null;
        
        if($brand_id>0){
        	$conditions['brand_id'] = $brand_id;
        }
        $conditions['t2.is_actived'] = '1';
        
	    //判断地区
        // if (isset($this->view->seed_Setting['goods_channel']) && $this->view->seed_Setting['goods_channel']=='BBC'){
        // 	$conditions['goods_channel']='BBC';
        // }elseif (isset($this->view->seed_Setting['goods_channel']) && $this->view->seed_Setting['goods_channel']=='BC'){
        // 	$conditions['goods_channel']='BC';
        // }
        $conditions["t1.goods_channel != 'BBC+BC'"]=null;
	    
        switch ($mod) {
			case "sell"://销量
				$orderby = array('t1.sold DESC', 't1.goods_id DESC');
				break;
			case "hot"://人气
				$orderby = array('t1.click DESC', 't1.goods_id DESC');
				break;
			case "comment":
				$orderby = array('t1.comment_cnt DESC');
				break;
			case "new"://上市时间
				$orderby = array('t1.add_time DESC', 't1.goods_id DESC');
				break;
			case "price"://价格
				$orderby = array('t6.stock_shop_price ASC', 't1.goods_id DESC');
				break;
			case "update":
				$orderby = array('t1.update_time DESC', 't1.goods_id DESC');
				break;
			case "share":
				$orderby = array('t1.share_cnt DESC', 't1.goods_id DESC');
				break;
			case "integral":
				$orderby = array('t1.integral DESC', 't1.goods_id DESC');
				break;
			default:
				$orderby = array('add_time DESC');
		}
		
		$file = "brand/" . $mod . "-" . $brand_id;

        $perpage = 20;
        $page=intval($this->_request->getParam('page'));
        $total = $goodsM->fetchGoodsesCount($conditions);
        $pageObj = new Seed_Page($this->_request, $total, $perpage);
		$this->view->page = $pageObj->getPageArray();
		$this->view->page['pageurl'] = '/' . $file;
		if ($page > $this->view->page['totalpage'])
		$page = $this->view->page['totalpage'];
		if ($page < 1)
		$page = 1;
         
        $goodses = $goodsM->fetchGoodses(array(($page-1)*$perpage,$perpage),$conditions,null,$orderby);
        $goodsStockM = new Shop_Model_GoodsStock('shop');
        foreach ($goodses as $k=>$goods){
        	$stock = $goodsStockM->fetchRow(array('goods_id'=>$goods['goods_id']),"*", 'stock_shop_price ASC');
        	if($stock && $stock['stock_id']>0){
        		$goodses[$k]['shop_price'] = $stock['stock_shop_price'];
        		$goodses[$k]['market_price'] = $stock['stock_market_price'];
        		$goodses[$k]['stock_id'] = $stock['stock_id'];
        	}else{
        		$goodses[$k]['shop_price'] = '0.00';
        		$goodses[$k]['market_price'] = '0.00';
        		$goodses[$k]['stock_id'] = 0;
        	}
        	$goodses[$k]['shop_name'] = $shopM->fetchOne(array('shop_id'=>$goods['agent_id']),'shop_name');
        }
        //品牌
        $brands = $goodsBarandM->fetchBrands(null,array('is_actived'=>'1'),'order_by ASC');
        $brand = $goodsBarandM->fetchRow(array('brand_id'=>$brand_id));
        if ($brand['brand_id']>0){
          $this->view->brand = $brand;
        }

        $this->view->mod = $mod;
        $this->view->brand_id = $brand_id;
        $this->view->goodses = $goodses;
        $this->view->brands = $brands;
        $this->view->brand = $brand;
		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/brand/index.phtml");
			echo $content;
			exit;
		}
	}
}