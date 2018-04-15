<?php
class ProductsController extends Shop_Controller_Action
{
	public $perpage = 20;
	public $goods_total = '';
	public $page_num = '';
	public function preDispatch() {
        $this->view->cur_pos = $this->_request->getParam('controller');

        $cururl = $this->getRequestUri();
        preg_match('/(.*)\.html/', $cururl, $arr);
        if (isset($arr[1]) && !empty($arr[1])) {
            ///入口列表
            preg_match_all('/^\/products\/(top)-([\d]+).html/isU', $cururl, $arr);
            if (isset($arr[1][0]) && !empty($arr[1][0])) {
                $this->_request->setParam('mod', $arr[1][0]);
                $this->_request->setParam('cate_id', $arr[2][0]);
                $this->indexAction();
                exit;
            }
            
            ///入口列表  --products/top-6-0-0.html  
            preg_match_all('/^\/products\/([\w]+)-([\d]+)-([\d]+)-([\d]+).html/isU', $cururl, $arr);
            if (isset($arr[1][0]) && !empty($arr[1][0])) {
                $this->_request->setParam('mod', $arr[1][0]);
                $this->_request->setParam('cate_id', $arr[2][0]);
                $this->_request->setParam('brand_id', $arr[3][0]);
                $this->_request->setParam('page', $arr[4][0]);
                $this->indexAction();
                exit;
            }

            ///库存详细入口
            preg_match('/^\/products\/([\w]+)(-[\w]+)?.html/isU', $cururl, $arr);
            $group_id = 0;
            if (isset($arr[1]) && !empty($arr[1])) {
                if (count($arr) == 3) {
                    if ($arr[2]{0} == '-') {
                        $tmp = explode('-', $arr[2]);
                        $group_id = $tmp[1];
                    }
                }
                $this->_request->setParam('goods_mark', $arr[1]);
                $this->_request->setParam('group_id', $group_id);
                $this->viewAction();
                exit;
            }

            ///商品详细
            preg_match_all('/^\/products\/([\w]+).html/isU', $cururl, $arr);
            if (isset($arr[1][0]) && !empty($arr[1][0])) {
                $this->_request->setParam('goods_mark', $arr[1][0]);
                $this->viewAction();
                exit;
            }

            preg_match_all('/^\/products\/(sold|hot|top|price|new)-([\d]+)-([\d]+)-([-\d]+).html/isU', $cururl, $arr);
            if (isset($arr [4] [0])) {
                //根据分类找出属性
                $cate_id = $arr [2] [0];
                $cateM = new Shop_Model_GoodsCate('shop');
                $cate = $cateM->fetchRow(array('cate_id' => $cate_id));
                $attributeM = new Shop_Model_Attribute('shop');
                $search_attrs = $attributeM->fetchAttributes(null, array("t2.group_name='default'" => null, 'is_search' => '1', 'type_id' => $cate['type_id']), 'order_by ASC');
                ///构建匹配URL
                $myattrs = array_fill(0, count($search_attrs), '([\d]+)');
                $attrstr = implode('-', $myattrs);
                $pregurl = "/^\/products\/(sold|hot|top|price|new)-([\d]+)-([\d]+)-{$attrstr}-([\d]+).html/isU";
                
                preg_match_all($pregurl, $cururl, $arr);
                if (isset($arr[1][0]) && !empty($arr[1][0])) {
                    ///划分获取属性数组，也就是计算出属性开始与结束位置，用来获取属性区间数组
                    $attr_offset = 4;
                    $attr_length = count($myattrs);
                    $page_offset = $attr_offset + $attr_length;
                    $attrs = array_slice($arr, $attr_offset, $attr_length);
                    $myattrs = array();
                    foreach ($attrs as $attr)
                        $myattrs[] = $attr['0'];
                    $page = $arr[$page_offset][0];
                    $this->_request->setParam('mod', $arr[1][0]);
                    $this->_request->setParam('cate_id', $arr[2][0]);
                    $this->_request->setParam('brand_id', $arr[3][0]);
                    $this->_request->setParam('attrs', $myattrs);
                    $this->_request->setParam('page', $page);
                    $this->indexAction();
                    exit;
                }
            }

            Mobile_Browser::redirect('没有找到相关信息！', $this->view->seed_BaseUrl . "/");
        }
    }

    public function indexAction()
	{
        $shopM = new Shop_Model_Shop('shop');
		$goodsM = new Shop_Model_Goods('shop');
		$goodsCateM = new Shop_Model_GoodsCate('shop');
		$goodsStockM = new Shop_Model_GoodsStock('shop');

		$f1 = new Seed_Filter_Alnum();
		$f2 = new Zend_Filter();
		$f2->addFilter(new Zend_Filter_StripTags())->addFilter(new Zend_Filter_StripNewlines());

		$mod = $f1->filter($this->_request->getParam('mod'));
		$cate_id = intval($this->_request->getParam('cate_id'));
        $brand_id = intval($this->_request->getParam('brand_id'));
		$selected_attrs = ($this->_request->getParam('attrs'));

		if (empty($mod))
		$mod = "top";
		if (empty($cate_id)){
			$cate_id = '5';
		}
		$goodscate = $goodsCateM->fetchRow(array('cate_id'=>$cate_id));
		
		//商品分类下的品牌
		$goodsBrandM = new Shop_Model_GoodsBrand('shop');
        $this->view->brands = $goodsBrandM->fetchBrands(null, array('is_actived'=>'1',"t2.type_id = '".$goodscate['type_id']."'"=>null), 'order_by ASC');

		///搜索属性
		$attributeM = new Shop_Model_Attribute('shop');
		$search_attrs = $attributeM->fetchAttributes(null, array("t2.group_name='default'" => null, 'is_search' => '1', 'type_id'=> $goodscate['type_id']), 'order_by ASC');
		$mysearch_attrs = array();
		$myselected_attrs = array();
		
		foreach ($search_attrs as $k => $attr) {
			if ($attr['attr_input_type'] > 1) {
				$attr_values = explode("<br />", nl2br($attr['attr_values']));
				foreach ($attr_values as $m => $value) {
					$value = trim($value);
					if (!empty($value))
					$attr_values[$m] = $value;
				}
				if (count($attr_values) > 0) {
					$mysearch_attrs[] = array('attr_id' => $attr['attr_id'], 'attr_name' => $attr['attr_name'], 'attr_values' => $attr_values, /*'attr_icon' => $attr['attr_icon']*/);
					$myselected_attrs[] = $selected_attrs[$k];
				}
			}
		}
		$conditions = array();
		$conditions['is_actived'] = '1';
		$conditions['is_on_sale'] = '1';
		$conditions['is_auth']='1';
		$conditions['is_group']='0';//非套装
		$conditions['is_buy']='0';
		$conditions["IFNULL( goods_list_image, '') != ''"]=null;
		$conditions["IFNULL( goods_detail_image, '') != ''"]=null;
		
		if($brand_id>0){
			$conditions['brand_id'] = $brand_id;
		}

		if ($cate_id > 0) {
			$childrenids = $goodsCateM->fetchChildrenCateIds($cate_id);
			$conditions['t1.cate_id in (' . implode(',', $childrenids) . ')'] = null;
		}
		
	    $search_mode = 0;
		if (is_array($selected_attrs) && count($selected_attrs) > 0) {
			$search_mode = 1;
			foreach ($selected_attrs as $k => $value) {
				if ($value > 0) {
					$conditions["t5.ext_" . $mysearch_attrs[$k]['attr_id'] . " like '%" . $mysearch_attrs[$k]['attr_values'][$value - 1] . "%'"] = null;
				}
			}
		}
		
	   //判断地区
        // if (isset($this->view->seed_Setting['goods_channel']) && $this->view->seed_Setting['goods_channel']=='BBC'){
        // 	$conditions['goods_channel']='BBC';
        // }elseif (isset($this->view->seed_Setting['goods_channel']) && $this->view->seed_Setting['goods_channel']=='BC'){
        // 	$conditions['goods_channel']='BC';
        // }

        $conditions["t1.goods_channel != 'BBC+BC'"]=null;

		//根据MOD类型选择不同的排序
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
				if ($search_mode == '1'){
				   $orderby = array('t7.stock_shop_price ASC', 't1.goods_id DESC');
				}else{
				   $orderby = array('t6.stock_shop_price ASC', 't1.goods_id DESC');
				}
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

		$file = "products/" . $mod . "-" . $cate_id . '-'. $brand_id . '-';
		for ($i = 0; $i < count($search_attrs); $i++) {
			if ($selected_attrs[$i] > 0)
			$file .= $selected_attrs[$i];
			else
			$file .= '0';
			if ($i != count($search_attrs) - 1)
			$file.='-';
		}
		 
		$perpage = 20;
		$page = intval($this->_request->getParam('page'));
		if ($search_mode == 1) {
			$total = $goodsM->searchGoodsesCount($conditions);
		} else {
			$total = $goodsM->fetchGoodsesCount($conditions);
		}
		$pageObj = new Seed_Page($this->_request, $total, $perpage);
		$this->view->page = $pageObj->getPageArray();
		$this->view->page['pageurl'] = '/' . $file;
		if ($page > $this->view->page['totalpage'])
		$page = $this->view->page['totalpage'];
		if ($page < 1)
		$page = 1;
		if ($search_mode == 1) {
			$goodses = $goodsM->searchGoodses(array(($page - 1) * $perpage, $perpage), $conditions, null, $orderby);
		} else {
			$goodses = $goodsM->fetchGoodses(array(($page - 1) * $perpage, $perpage), $conditions, null, $orderby);
		}
	    foreach ($goodses as $k =>$goods){
			$stock = $goodsStockM->fetchRow(array('goods_id'=>$goods['goods_id']),"*", 'stock_shop_price ASC');
			if($stock['stock_id']>0){
				$goodses[$k]['goods_number'] = $stock['stock_value'];
				$goodses[$k]['shop_price'] = $stock['stock_shop_price'];
				$goodses[$k]['market_price'] = $stock['stock_market_price'];
			}else{
				$goodses[$k]['goods_number'] = '0';
				$goodses[$k]['shop_price'] = '0.00';
				$goodses[$k]['market_price'] = '0.00';
			}
			$goodses[$k]['shop_name'] = $shopM->fetchOne(array('shop_id'=>$goods['agent_id']),'shop_name');
		}

		//上级分类
		$parent_cates= $goodsCateM->getParentNav($cate_id);
		$this->view->parent_cates = $parent_cates;


		$this->view->total = $total;
		$this->view->goodses = $goodses;
		$this->view->search_attrs = $mysearch_attrs;
		$this->view->selected_attrs = $myselected_attrs;
		$this->view->mod = $mod;
		$this->view->cate_id = $cate_id;
        $this->view->brand_id = $brand_id;
		$this->view->cate = $goodsCateM->fetchRow(array('cate_id' => $cate_id));

		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/products/index.phtml");
			echo $content;
			exit;
		}
	}

	public function viewAction()
	{
		// 商品
		$condition = array();
		$goods_mark = trim($this->_request->getParam('goods_mark'));
		$group_id = intval($this->_request->getParam('group_id'));
		$qrcode = trim($this->_request->getParam('qrcode'));				
				
		$shopM = new Shop_Model_Shop('shop');
		$goodsM = new Shop_Model_Goods('shop');
		$goodsCateM = new Shop_Model_GoodsCate('shop');
			
		if(empty($goods_mark))Shop_Browser::redirect('没有找到商品！', $this->view->seed_BaseUrl . "/");
		$condition['goods_mark']=$goods_mark;
		$condition['is_actived']='1';
		$condition['is_auth']='1';
		$condition['is_on_sale'] = '1';
		$condition['is_buy']='0';
		
		$goods = $goodsM->fetchGoods($condition);
		
		if ($goods['goods_id']<1){
			Shop_Browser::redirect('没有找到相关产品/或者产品未上架！', $this->view->seed_BaseUrl . "/");
		}
		
		
		/*
		 * 商品相关的套装在商品详情页面显示
		 */		
		$goodsGroupM = new Shop_Model_GoodsGroup('shop');
		$goodsStockM = new Shop_Model_GoodsStock('shop');
		$goodsStockGroupM = new Shop_Model_GoodsStockGroup('shop');
		$goodsM = new Shop_Model_Goods('shop');
		//通过商品id找出商品在套装组的所有数据

		$goodses = $goodsGroupM->_db->fetchAll("select `t1`.*,`t2`.`is_actived` from 
		".$goodsGroupM->_prefix.$goodsGroupM->_table_name." AS `t1` 
		LEFT JOIN ".$goodsM->_prefix.$goodsM->_table_name." AS `t2` 
		on `t2`.`goods_id` = `t1`.`group_id` 
		where `t1`.`goods_id` = ".$goods['goods_id']." and `t2`.`is_actived` = 1 and `t2`.`is_buy` = 0");

		foreach($goodses as $k => $gid){
		//通过套装组的group_id找到商品组合的各个商品
		$goodsGroupList = $goodsGroupM->fetchRows(NULL, array('group_id' =>$gid['group_id']));
			
			//套装表的group_id等于商品品的goods_id,获取相应数据，方便购物车结算
			$groupstock_id = $goodsM->fetchGroupStockId(array('goods_id' =>$gid['group_id']),array('t6.stock_id'));
				
			//定义初始价格
			if ($goodsGroupList){
				$all_shop_price = '0';
				$all_cost_price = '0';
				$discount_price = '0';	
				if ( !$goodsGroupList) {$goodsGroupList = array();}
				foreach ($goodsGroupList as $key => $value) {
					//遍历每个商品的信息
					$goods_name = $goodsM->fetchOne(array('goods_id' => $value['goods_id']),'goods_name');
					$goods_pic = $goodsM->fetchOne(array('goods_id' => $value['goods_id']),'goods_list_image');
					$goods_mark = $goodsM->fetchOne(array('goods_id' => $value['goods_id']),'goods_mark');
					$group_name = $goodsStockGroupM->fetchOne(array('goods_id'=>$value['goods_id']),'group_name');		
					$goodsstock = $goodsStockM->fetchRow(array('stock_id' => $value['stock_id']));	
					$stock_name = $goodsstock['stock_name'];
					if ($group_name && $group_name != '默认') {$goods_name .= (' ' . $group_name . ' -');}
					if ($stock_name) {$goods_name .= (' ' . $stock_name);}
					//构造groups键值遍历每个商品放进去
					$goodses[$k]['groups'][$key]['goods_name'] = $goods_name;
					$goodses[$k]['groups'][$key]['goods_list_image'] = $goods_pic;
					$goodses[$k]['groups'][$key]['goods_mark'] = $goods_mark;
					$goodses[$k]['groups'][$key]['tax_rate'] = $goodsstock['tax_rate'];;
					$goodses[$k]['groups'][$key]['stock_barcode'] = $goodsstock['stock_barcode'];
					$goodses[$k]['groups'][$key]['shop_price'] = $goodsstock['stock_market_price']; //原价				
					$goodses[$k]['groups'][$key]['cost_price'] = $value['shop_price'];			//现价
					$goodses[$k]['groups'][$key]['goods_number'] = $value['goods_number'];
					$all_shop_price +=($goodsstock['stock_shop_price']*$value['goods_number']);//总的,原价
					$all_cost_price +=($value['shop_price']*$value['goods_number']);		//总的,现价
					$discount_price = $all_shop_price - $all_cost_price ; 					//优惠价
				}
				//每个套装组的总价格		
				$goodses[$k]['all_shop_price']=$all_shop_price;
				$goodses[$k]['all_cost_price']=$all_cost_price;
				$goodses[$k]['discount_price']=$discount_price;
				$goodses[$k]['groupstock_id']=$groupstock_id;	
			}
		
		}
		;
		 // header("Content-type:text/html;charset=utf-8");
		  //echo '<pre/>';
		  // var_dump($goodses);
		$this->view->goodses = $goodses;

		//$this->view->goodsGroupList = $goodsGroupList;

		
		

		if (isset($this->view->seed_Setting['goods_channel']) && $this->view->seed_Setting['goods_channel'] !== $goods['goods_channel']){
		   header('location:'.$this->view->seed_Setting['website'].'/products/'.$goods['goods_mark'].'.html');
		}
		
		$goods_id = $goods['goods_id'];
		
		//上级分类
		$parent_cates= $goodsCateM->getParentNav($goods['cate_id']);
		$this->view->parent_cates = $parent_cates;
		
		//供应商
		$goods['shop_name'] = $shopM->fetchOne(array('shop_id'=>$goods['agent_id']),'shop_name');
        
		//点击自增
		$goodsM->updateRow(array('view_total' => new Zend_Db_Expr("view_total + 1")), array('goods_id' => $goods_id));

		//商品库存开始
		$goodsStockM = new Shop_Model_GoodsStock('shop');
		$goodsStockGroupM = new Shop_Model_GoodsStockGroup('shop');
		$skuM = new Shop_Model_Sku('shop');

		$stock_groups = $goodsStockGroupM->fetchRows(null,array('goods_id'=>$goods_id),'order_by ASC');
		$goods['group_id']=0;
		$goods['stock_id']=0;
		if(count($stock_groups)>0){
			if(isset($stock_groups[$group_id])){
				$stock_group = $stock_groups[$group_id];
			}else{
				$stock_group = $stock_groups[0];
			}
			$this->view->stock_group = $stock_group;

			$goods_stocks = $goodsStockM->fetchRows(null,array('goods_id'=>$goods_id,'group_id'=>$stock_group['group_id']),'order_by ASC');
			if(count($goods_stocks)>0){
				$goods['stock_id']=$goods_stocks[0]['stock_id'];
				$goods['tax_rate']=$goods_stocks[0]['tax_rate'];
				$goods['shop_price']=$goods_stocks[0]['stock_shop_price'];
				$goods['market_price']=$goods_stocks[0]['stock_market_price'];
				$goods['goods_number']=$goodsStockM->fetchSum('stock_value',array('goods_id'=>$goods_id,'group_id'=>$stock_group['group_id']));
				$goods['stock_value'] = $goodsStockM->fetchSum('stock_value',array('goods_id'=>$goods['goods_id']));
				if(count($goods_stocks)>1){
					$this->view->goods_stocks = $goods_stocks;
				}
                        }
		}else{
			$goods_stocks = $goodsStockM->fetchRows(null,array('goods_id'=>$goods_id), 'stock_shop_price ASC');
			if(count($goods_stocks)>0){
				$goods['stock_id']=$goods_stocks[0]['stock_id'];
				$goods['tax_rate']=$goods_stocks[0]['tax_rate'];
				$goods['shop_price']=$goods_stocks[0]['stock_shop_price'];
				$goods['market_price']=$goods_stocks[0]['stock_market_price'];
				$goods['goods_number']=$goodsStockM->fetchSum('stock_value',array('goods_id'=>$goods_id));
				$goods['stock_value'] = $goodsStockM->fetchSum('stock_value',array('goods_id'=>$goods['goods_id']));
				if(count($goods_stocks)>1){
					$this->view->goods_stocks = $goods_stocks;
				}
			}else{
                            $goods['goods_number']=0;
                            $goods['shop_price']=0.00;
                            $goods['market_price']=0.00;
                        }

		}
		$this->view->stock_groups = $stock_groups;
		//商品库存结束

		//产品详细
		$goodsDetailM = new Shop_Model_GoodsDetail('shop');
		$detail = $goodsDetailM->fetchRow(array('goods_id'=>$goods_id));
		if($detail['goods_id']>0){
			$goods['goods_content']=$detail['goods_content'];
			$goods['goods_usemethod']=$detail['goods_usemethod'];
			$goods['goods_detail']=$detail['goods_detail'];
		}else{
			$goods['goods_content']="";
			$goods['goods_usemethod']="";
			$goods['goods_detail']="";
		}
		
		//属性
		$attributeM = new Shop_Model_Attribute('shop');
		$goodsAttrM = new Shop_Model_GoodsAttr('shop');
		$attributes = $attributeM->fetchAttributes(null,array('type_id'=>$goods['type_id']),array('t2.order_by ASC','t1.order_by ASC'));
		if ($attributes){
			$myattrs=array();
			foreach ($attributes as $k=>$attr){
				$goodsattr=$goodsAttrM->fetchRow(array('attr_id'=>$attr['attr_id'],'goods_id'=>$goods_id));
				$attr_value=($goodsattr['attr_value']==null)?"":$goodsattr['attr_value'];
				$group_id = intval($attr['group_id']);
				$myattrs[$group_id]['group_id']=$group_id;
				$myattrs[$group_id]['group_name']=(isset($attr['group_name']) && !empty($attr['group_name']))?$attr['group_name']:"";
				$myattrs[$group_id]['group_desc']=(isset($attr['group_desc']) && !empty($attr['group_desc']))?$attr['group_desc']:"";
				$myattrs[$group_id]['attrs'][]=array('attr_id'=>$attr['attr_id'],'type_id'=>$attr['type_id'],'attr_name'=>$attr['attr_name'],'attr_input_type'=>$attr['attr_input_type'],'attr_values'=>$attr['attr_values'],'attr_value'=>$attr_value);
			}
			$this->view->attrs = $myattrs[$group_id]['attrs'];
		}
		//商品相册
		$photosM = new Shop_Model_GoodsPhoto("shop");
		$this->view->photos = $photosM->fetchRows(array(0,0),array("goods_id"=>$goods['goods_id']),"order_by asc");
		$this->view->hideshare = true;
		
		//收藏状态
		$goods['collect_status']='0';
		if($this->view->seed_User['user_id']>0){
			$collectGoodsM = new Shop_Model_CollectionGoods('shop');
			$check = $collectGoodsM->fetchRow(array('user_id'=>$this->view->seed_User['user_id'],'shop_id'=>'0','goods_id'=>$goods['goods_id']));
			if($check['goods_id']>0)$goods['collect_status']='1';
		}

		$this->view->goods = $goods;

		$condition1=array();
		$condition1['is_auth']='1';
		$condition1['is_actived']='1';
		$condition1['is_recommend']='1';
		$condition1['is_on_sale'] = '1';
		$condition1['is_buy']='0';
		$condition1['t1.goods_id !='.$goods['goods_id']]=null;	
	    $condition1["t1.goods_channel != 'BBC+BC'"]=null;
	    $condition1["IFNULL( goods_list_image, '') != ''"]=null;
		$condition1["IFNULL( goods_detail_image, '') != ''"]=null;
	    //$condition1['cate_id'] = $goods['cate_id'];
	    //判断地区
        // if (isset($this->view->seed_Setting['goods_channel']) && $this->view->seed_Setting['goods_channel']=='BBC'){
        // 	$condition1['goods_channel']='BBC';
        // }elseif (isset($this->view->seed_Setting['goods_channel']) && $this->view->seed_Setting['goods_channel']=='BC'){
        // 	$condition1['goods_channel']='BC';
        // }
        

        $ordery = array('order_by desc','goods_id desc');
        //推荐产品
        $recommend_goodses = $goodsM->fetchGoodses(array(0,3),$condition1,null,$ordery);
        foreach ($recommend_goodses as $k =>$goodses){
        	$stock = $goodsStockM->fetchRow(array('goods_id'=>$goodses['goods_id']),"*", 'stock_shop_price ASC');
        	if($stock['stock_id']>0){
        		$recommend_goodses[$k]['goods_number'] = $stock['stock_value'];
        		$recommend_goodses[$k]['shop_price'] = $stock['stock_shop_price'];
        		$recommend_goodses[$k]['market_price'] = $stock['stock_market_price'];
        	}else{
        		$recommend_goodses[$k]['goods_number'] = '0';
        		$recommend_goodses[$k]['shop_price'] = '0.00';
        		$recommend_goodses[$k]['market_price'] = '0.00';
        	}
        	$recommend_goodses[$k]['shop_name'] = $shopM->fetchOne(array('shop_id'=>$goodses['agent_id']),'shop_name');

        }
                $this->view->recommend_goodses = $recommend_goodses;

        $condition2=array();
		$condition2['is_auth']='1';
		$condition2['is_actived']='1';
		$condition2['is_on_sale'] = '1';
		$condition2['is_buy']='0';
		$condition2['t1.goods_id !='.$goods['goods_id']]=null;
		$condition2["t1.cate_id = ".$goods['cate_id']]=null;
	    $condition2["t1.goods_channel != 'BBC+BC'"]=null;
        $condition2["IFNULL( goods_list_image, '') != ''"]=null;
		$condition2["IFNULL( goods_detail_image, '') != ''"]=null;
        //关联产品
        $same_goodses = $goodsM->fetchGoodses(array(0,3),$condition2,null,$ordery);
        foreach ($same_goodses as $k =>$goods){
        	$stock = $goodsStockM->fetchRow(array('goods_id'=>$goods['goods_id']),"*", 'stock_shop_price ASC');
        	if($stock['stock_id']>0){
        		$same_goodses[$k]['goods_number'] = $stock['stock_value'];
        		$same_goodses[$k]['shop_price'] = $stock['stock_shop_price'];
        		$same_goodses[$k]['market_price'] = $stock['stock_market_price'];
        	}else{
        		$same_goodses[$k]['goods_number'] = '0';
        		$same_goodses[$k]['shop_price'] = '0.00';
        		$same_goodses[$k]['market_price'] = '0.00';
        	}
        	$same_goodses[$k]['shop_name'] = $shopM->fetchOne(array('shop_id'=>$goods['agent_id']),'shop_name');
        }     
        $this->view->same_goodses = $same_goodses;

        $userM  = new Seed_Model_User('system');
        $goodsCommentM = new Shop_Model_GoodsComment('shop');
        $goodsCommentReplyM = new Shop_Model_GoodsCommentReply('shop');
        $f3 = new Zend_Filter();
                $f3->addFilter(new Zend_Filter_Int());
        $page = $f3->filter($this->_request->getParam('page'));
        # 检索评论数
        $commentcondition = array(
            'goods_id' => $goods_id,
            'comment_status' => '1'
        );
        # 分页
        $perpage = 10; //获取的记录数
        $total   = $goodsCommentM->fetchRowsCount($commentcondition);
        $pageObj = new Seed_Page($this->_request, $total, $perpage);
        $this->view->page = $pageObj->getPageArray();
        $pageAry = $pageObj->getPageArray();
        if($page > $pageAry['totalpage']) {$page = $pageAry['totalpage'];}
        if($page < 1) {$page = 1;}
        # 获取数据
        $limit = array(($page-1)*$perpage, $perpage);
        $orderBy = array('comment_time DESC');
        $commentList = $goodsCommentM->fetchRows($limit, $commentcondition, $orderBy);
        if (empty($commentList)) {$commentList = array();}
        foreach ($commentList as $k => $v) {
            # 评论账号头像
            if (empty($v['user_face'])) {
                $user = $userM->fetchRow(array('user_id' => $v['user_id']), array('user_big_face'));
                $uFace = $user['user_big_face'];
                $commentList[$k]['user_face'] = empty($uFace) ? '/images/face50_50.jpg' : $uFace;
            }
            # 评论回复
            $replyOrderBy = array('reply_time DESC');
            $replyList = $goodsCommentReplyM->fetchRows(array(0, 10), array('comment_id' => $v['comment_id']), $replyOrderBy);
            if (empty($replyList)) {$replyList = array();}
            foreach ($replyList as $key => $value) {
                # 回复账号头像
                if (empty($value['user_face'])) {
                    $user = $userM->fetchRow(array('user_id' => $value['user_id']), array('user_big_face'));
                    $uFace = $user['user_big_face'];
                    $replyList[$key]['user_face'] = empty($uFace) ? '/images/face50_50.jpg' : $uFace;
                }
                $replyList[$key]['user_name'] = Tools_String::substr_replace_cn($value['user_name'], '**', 1, 5);
            }
            $commentList[$k]['user_name'] = Tools_String::substr_replace_cn($v['user_name'], '**', 1, 5);
            $commentList[$k]['replyList'] = $replyList;
        }
//                echo "<pre>";var_dump($commentList);exit;
        $this->view->commentList = $commentList;
                
        //规格
        $stock_arr=array();
        $str = '';
        $goodsstocks = $goodsStockM->fetchRows(null,array('goods_id'=>$goods_id));
        foreach ($goodsstocks as $k => $st){
           $stock_arr[]= $st['stock_name'];
        }
        $this->view->stock_str = $str = implode(',', $stock_arr);

        //规格
        $tax_arr=array();
        $tax = '';
        $goodsstocks = $goodsStockM->fetchRows(null,array('goods_id'=>$goods_id));
        foreach ($goodsstocks as $k => $st){
           $tax_arr[]= ($st['tax_rate']*100)."%";
        }
        $this->view->tax_str = $tax = implode(',', $tax_arr);
		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/products/view.phtml");
			echo $content;
			exit;
		}
	}
        
         public function getcommentsAction()
    {
        try {
            # 过滤器
            $f3 = new Zend_Filter();
            $f3->addFilter(new Zend_Filter_Int());
            # 模型
            $userM  = new Seed_Model_User('system');
            $goodsCommentM = new Shop_Model_GoodsComment('shop');
            $goodsCommentReplyM = new Shop_Model_GoodsCommentReply('shop');
            # 参数
            $goods_id = $f3->filter($this->_request->getParam('goods_id'));
            $page     = intval($this->_request->getParam('page'));
            # 检查
            if (empty($goods_id)) {
                $this->output(false, "没有找到相关商品！");
            }
            # 检索条件
            $condition = array(
                'goods_id' => $goods_id,
                'comment_status' => '1'
            );
            # 分页
            $perpage = 10; //获取的记录数
            $total   = $goodsCommentM->fetchRowsCount($condition);
            $pageObj = new Seed_Page($this->_request, $total, $perpage);
            $this->view->page = $pageObj->getPageArray();
            $pageAry = $pageObj->getPageArray();
            if($page > $pageAry['totalpage']) {$page = $pageAry['totalpage'];}
            if($page < 1) {$page = 1;}
            # 获取数据
            $limit = array(($page-1)*$perpage, $perpage);
            $orderBy = array('comment_id DESC');
            $commentList = $goodsCommentM->fetchRows($limit, $condition, $orderBy);
            if (empty($commentList)) {$commentList = array();}
            foreach ($commentList as $k => $v) {
                # 评论账号头像
                if (empty($v['user_face'])) {
                    $user = $userM->fetchRow(array('user_id' => $v['user_id']), array('user_big_face'));
                    $uFace = $user['user_big_face'];
                    $commentList[$k]['user_face'] = empty($uFace) ? '/images/face50_50.jpg' : $uFace;
                }
                # 评论回复
                $replyList = $goodsCommentReplyM->fetchRows(array(0, 10), array('comment_id' => $v['comment_id']));
                if (empty($replyList)) {$replyList = array();}
                foreach ($replyList as $key => $value) {
                    # 回复账号头像
                    if (empty($value['user_face'])) {
                        $user = $userM->fetchRow(array('user_id' => $value['user_id']), array('user_big_face'));
                        $uFace = $user['user_big_face'];
                        $replyList[$key]['user_face'] = empty($uFace) ? '/images/face50_50.jpg' : $uFace;
                    }
                }
                $commentList[$k]['user_name'] = Tools_String::substr_replace_cn($v['user_name'], '**', 1, 5);
                $commentList[$k]['replyList'] = $replyList;
            }
            # 视图赋值
            $this->view->commentList = $commentList;
            # 模板引用
            if(defined('SEED_WWW_TPL')){
                    $content = $this->view->render(SEED_WWW_TPL."/products/commentlist.phtml");
                    echo $content;
                    exit;
            }
        }catch (Exception $e) {
            Mobile_Browser::tip_showMsg($e->getMessage());
        }
    }
//
//	public function previewAction()
//	{
//		// 商品
//		$condition = array();
//		$goods_id = trim($this->_request->getParam('goods_id'));
//		$group_id = intval($this->_request->getParam('group_id'));
//
//		$condition['goods_id']=$goods_id;
//		$goodsM = new Shop_Model_Goods('shop');
//		$goods = $goodsM->fetchGoods($condition);
//
//		if ($goods['goods_id']<1){
//			Mobile_Browser::error('没有找到相关产品！');
//		}
//		$goods_id = $goods['goods_id'];
//
//		///商品库存开始
//		$goodsStockM = new Shop_Model_GoodsStock('shop');
//		$goodsStockGroupM = new Shop_Model_GoodsStockGroup('shop');
//		$stock_groups = $goodsStockGroupM->fetchRows(null,array('goods_id'=>$goods_id),'order_by ASC');
//		$goods['group_id']=0;
//		$goods['stock_id']=0;
//		if(count($stock_groups)>0){
//			if(isset($stock_groups[$group_id])){
//				$stock_group = $stock_groups[$group_id];
//			}else{
//				$stock_group = $stock_groups[0];
//			}
//			$this->view->stock_group = $stock_group;
//
//			$goods_stocks = $goodsStockM->fetchRows(null,array('goods_id'=>$goods_id),array('group_id','order_by ASC'));
//			if(count($goods_stocks)>0){
//				$goods['stock_id']=$goods_stocks[0]['stock_id'];
//				$goods['shop_price']=$goods_stocks[0]['stock_shop_price'];
//				$goods['market_price']=$goods_stocks[0]['stock_market_price'];
//				$goods['goods_number']=$goodsStockM->fetchSum('stock_value',array('goods_id'=>$goods_id,'group_id'=>$stock_group['group_id']));
//
//				if(count($goods_stocks)>1){
//					$this->view->goods_stocks = $goods_stocks;
//				}
//			}
//		}else{
//			$goods_stocks = $goodsStockM->fetchRows(null,array('goods_id'=>$goods_id),'order_by ASC');
//			if(count($goods_stocks)>0){
//				$goods['stock_id']=$goods_stocks[0]['stock_id'];
//				$goods['shop_price']=$goods_stocks[0]['stock_shop_price'];
//				$goods['market_price']=$goods_stocks[0]['stock_market_price'];
//				$goods['goods_number']=$goodsStockM->fetchSum('stock_value',array('goods_id'=>$goods_id));
//
//				if(count($goods_stocks)>1){
//					$this->view->goods_stocks = $goods_stocks;
//				}
//			}
//
//		}
//
//		$this->view->stock_groups = $stock_groups;
//		///商品库存结束
//
//		//产品详细
//		$goodsDetailM = new Shop_Model_GoodsDetail('shop');
//		$detail = $goodsDetailM->fetchRow(array('goods_id'=>$goods_id));
//		if($detail['goods_id']>0)
//			$goods['goods_m_content']=$detail['goods_m_content'];
//		else
//			$goods['goods_m_content']="";
//
//        //属性
//        $attributeM = new Shop_Model_Attribute('shop');
//		$goodsAttrM = new Shop_Model_GoodsAttr('shop');
//        $attributes = $attributeM->fetchAttributes(null,array('type_id'=>$goods['type_id']),array('t2.order_by ASC','t1.order_by ASC'));
//		$myattrs=array();
//		foreach ($attributes as $k=>$attr){
//			$goodsattr=$goodsAttrM->fetchRow(array('attr_id'=>$attr['attr_id'],'goods_id'=>$goods_id));
//			$attr_value=($goodsattr['attr_value']==null)?"":$goodsattr['attr_value'];
//			$group_id = intval($attr['group_id']);
//			$myattrs[$group_id]['group_id']=$group_id;
//			$myattrs[$group_id]['group_name']=(isset($attr['group_name']) && !empty($attr['group_name']))?$attr['group_name']:"";
//			$myattrs[$group_id]['group_desc']=(isset($attr['group_desc']) && !empty($attr['group_desc']))?$attr['group_desc']:"";
//			$myattrs[$group_id]['attrs'][]=array('attr_id'=>$attr['attr_id'],'type_id'=>$attr['type_id'],'attr_name'=>$attr['attr_name'],'attr_input_type'=>$attr['attr_input_type'],'attr_values'=>$attr['attr_values'],'attr_value'=>$attr_value);
//		}
//        $this->view->attrs = $myattrs;
//
//		//商品相册
//		$photosM = new Shop_Model_GoodsMPhoto("shop");
//		$this->view->photos = $photosM->fetchRows(array(0,0),array("goods_id"=>$goods['goods_id']),"order_by asc");
//		$this->view->hideshare = true;
//
//		$this->view->goods = $goods;
//
//		if(defined('SEED_V_TPL')){
//			$content = $this->view->render(SEED_V_TPL."/products/preview.phtml");
//			echo $content;
//			exit;
//		}
//	}
//
//	//查看更多商品
//	function getgoodsAction()
//	{
//		$perpage=$this->perpage;
//		$page=intval($this->_request->getParam('page'));
//		$other = trim($this->_request->getParam('other'));
//		$sort = intval($this->_request->getParam('sort'));
//		$cate_id = intval($this->_request->getParam('cate'));
//		$brand_id = intval($this->_request->getParam('brand'));
//
//		// 搜索模式标记
//		$orderby=array('t1.order_by ASC','t1.goods_id DESC','t1.sold DESC');
//		$search_mode=0;
//		$conditions = array();
//		$conditions['is_m_actived']='1';
//		$conditions['agent_id']='0';
//		$conditions['is_auth']='1';
//		if($cate_id>0){
//			$cateM = new Shop_Model_GoodsCate('shop');
//			$cate_ids = $cateM->fetchChildrenCateIds($cate_id);
//			if(!empty($cate_ids)){
//				$cate_ids = implode(',', $cate_ids);
//				$cate_ids = $cate_ids.",".$cate_id;
//				$conditions["t1.cate_id in (".$cate_ids.")"] = null;
//			}else{
//				$conditions['cate_id'] = $cate_id;
//			}
//		}
//		if($brand_id>0){
//			$conditions['brand_id'] = $brand_id;
//		}
//		
//		if(!empty($other)){
//			if($sort ==0){
//				$order_by = 'DESC';
//			}else{
//				$order_by = 'ASC';
//			}
//			if($other == 'view'){
//				$orderby=array('t1.click '.$order_by);
//			}elseif($other == 'sale'){
//				$orderby=array('t1.sold '.$order_by);
//			}elseif($other == 'hot'){
//				$orderby=array('t1.is_hot DESC','t1.click '.$order_by);
//			}elseif($other == 'new'){
//				$orderby=array('t1.is_new DESC','t1.add_time '.$order_by);
//			}elseif($other == 'recommend'){
//				$orderby=array('t1.is_recommend DESC','t1.click '.$order_by);
//			}
//		}
//			
//
//		$goodsM = new Shop_Model_Goods('shop');
//		$goodses = $goodsM->fetchGoodses(array(($page-1)*$perpage,$perpage),$conditions,null,$orderby,array('goods_id','type_id','goods_name','goods_tips','goods_sn','goods_m_list_image','goods_m_detail_image','goods_mark','goods_m_desc','is_on_sale','sold'));
//
//		$goodsStockM = new Shop_Model_GoodsStock('shop');
//		foreach ($goodses as $k=>$goods){
//			$stock = $goodsStockM->fetchRow(array('goods_id'=>$goods['goods_id']),"*","order_by ASC");
//			if($stock['stock_id']>0){
//				$goodses[$k]['shop_price'] = $stock['stock_shop_price'];
//				$goodses[$k]['market_price'] = $stock['stock_market_price'];
//				$goodses[$k]['stock_id'] = $stock['stock_id'];
//			}else{
//				$goodses[$k]['shop_price'] = '0.00';
//				$goodses[$k]['market_price'] = '0.00';
//				$goodses[$k]['stock_id'] = 0;
//			}
//		}
//
//		$this->view->goodses = $goodses;
//
//		if(defined('SEED_V_TPL')){
//			$content = $this->view->render(SEED_V_TPL."/products/productslist.phtml");
//			echo $content;
//			exit;
//		}
//	}
//
//	// 加入收藏
//	public function addcollectAction()
//	{
//		$collectionM = new Shop_Model_Collection('shop');
//		$goodsM = new Shop_Model_Goods('shop');
//		$goods_id = (int)$this->_request->getPost('goods_id');
//		if ($goods_id<1){
//			Mobile_Browser::tip_show('无法找到相关数据！');
//		}
//		$goods = $goodsM->fetchGoods(array('goods_id'=>$goods_id));
//		if ($goods['goods_id']<1){
//			Mobile_Browser::tip_show('无法找到相关数据！');
//		}
//		$conditions = array();
//		$conditions['goods_id'] = $goods_id;
//		$conditions['user_id'] = $this->view->seed_User['user_id'];
//		$collection_cnt = $collectionM->fetchRowsCount($conditions);
//		$url = $_SERVER["HTTP_REFERER"];
//		if($this->view->seed_User['user_id']<1){
//			Mobile_Browser::tip_show("您尚未登录，不能进行收藏操作，请先登录！", $url);
//			exit;
//		}
//		if($collection_cnt > 0){
//			Mobile_Browser::tip_show("收藏已存在，无需再次收藏！", $url);
//			exit;
//		}else{
//			$affectRowsCount=$collectionM->insertRow(array('goods_id'=>$goods_id, 'user_id'=>$this->view->seed_User['user_id']));
//			if($affectRowsCount > 0){
//				Mobile_Browser::tip_show("收藏成功！", $url);
//				exit;
//			}else{
//				Mobile_Browser::tip_show("收藏失败，出现未知错误！", $url);
//				exit;
//			}
//		}
//		exit;
//	}
//
	///Ajax读取库存信息
	function ajaxstockAction()
	{
		$goodsM = new Shop_Model_Goods('shop');
		$goodsStockM = new Shop_Model_GoodsStock('shop');
		$goods_id = intval($this->_request->getPost('goods_id'));

		$goods = $goodsM->fetchRow(array('goods_id'=>$goods_id));
		if($goods['goods_id']<1)exit('没有找到相关产品！');
		$this->view->goods = $goods;

		$stock_id = intval($this->_request->getPost('stock_id'));

		$goodsStock = $goodsStockM->fetchRow(array('stock_id'=>$stock_id));
		if($goodsStock['stock_id']<1)exit('没有找到相关库存！');

		if(empty($goodsStock['stock_image']))$goodsStock['stock_image'] = $goods['goods_list_image'];
		$goodsStock['stock_image_realpath'] = $this->view->showImage($goodsStock['stock_image']);
		$goodsStock['stock_market_price_format'] = '¥ '.$goodsStock['stock_market_price'];
		$goodsStock['stock_shop_price_format'] = '¥ '.$goodsStock['stock_shop_price'];
		$goodsStock['tax_rate'] = ($goodsStock['tax_rate']*100).'%';
		$mydata = $goodsStock;
		echo Zend_Json::encode($mydata);
		exit;
	}
	
    //查询分类
    function getcateAction(){ 
        $other = trim($this->_request->getParam('other'));
        $cate_id = intval($this->_request->getParam('cate'));
        $cateM = new Shop_Model_GoodsCate('shop');
        $cates = $cateM->fetchRows(NULL,array('parent'=>$cate_id));
        foreach($cates as $k=>$v){
            $go = $cateM->fetchRows(NULL,array('parent'=>$v['cate_id']));
            $cates[$k]['go'] = $go?true:false;
        }
            
        $this->view->cate_id = $cate_id;
        $this->view->cates =$cates;
		if(defined('SEED_V_TPL')){
			$content = $this->view->render(SEED_V_TPL."/products/getcate.phtml");
			echo $content;
			exit;
		}
    }
    
	//修改数量
    function addgoodsAction()
    {
        $goods_id = intval($this->_request->getParam('goods_id'));
        $stock_id = $this->_request->getParam('stock_id');
        $number = intval($this->_request->getParam('number'));
        $cartM = new Shop_Model_Cart('shop');
        $check = array();
        if ($stock_id)
            $check = $cartM->fetchRow(array('stock_id' => $stock_id, 'goods_id' => $goods_id, 'user_id' => $this->view->seed_User['user_id']));
        else
            $check = $cartM->fetchRow(array('goods_id' => $goods_id, 'user_id' => $this->view->seed_User['user_id']));
        if ($goods_id > 0 || $stock_id > 0) {
            $goodsM = new Shop_Model_Goods('shop');
            $goodsStockM = new Shop_Model_GoodsStock('shop');
            $goods = $goodsM->fetchRow(array('goods_id' => $goods_id, 'is_m_actived' => '1', 'is_on_sale' => '1'));
            $flag = 0;
            if ($stock_id > 0) {
                $stock = $goodsStockM->fetchRow(array('stock_id' => $stock_id));
                if ($stock['stock_value'] <= $number) {
                    $flag = 1;
                    echo json_encode(array('flag' => $flag, 'aa' => $stock_id));
                    exit;
                } else {
                    $flag = 0;
                    echo json_encode(array('flag' => $flag));
                    exit;
                }
            }
            if ($goods_id > 0) {
                if ($goods['goods_number'] <= $number) {
                    $flag = 1;
                    echo json_encode(array('flag' => $flag));
                    exit;
                } else {
                    $flag = 0;
                    echo json_encode(array('flag' => $flag));
                    exit;
                }
            }
        } else {
            $flag = 2;
            echo json_encode(array('flag' => $flag));
        }
        exit;
    }
}