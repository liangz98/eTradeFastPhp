<?php
class inbuyController extends Shop_Controller_Action
{
    public $perpage = 20;
	public $goods_total = '';
	public $page_num = '';
	public function preDispatch() {
        $this->view->cur_pos = $this->_request->getParam('controller');
      // if ($this->view->seed_User['user_id']<1) {
       //     Shop_Browser::redirect('请先登录系统！',$this->view->seed_Setting['user_app_server']."/login");
      //  } elseif (($this->view->seed_User['user_id']>1)&&($this->view->seed_User['is_open']<1)) {
       //    Shop_Browser::redirect('不是内部员工无法进入！',$this->view->seed_Setting['user_app_server']."/");
      //  } 
        if($this->view->seed_User['is_open']<1){
            Seed_Browser::redirect('不是内部员工！',$this->view->seed_Setting['www_app_server']);
        }
        
      //  if($this->view->seed_User['is_open']<1){
       //     Shop_Browser::redirect('XX请先登录系统！',$this->view->seed_Setting['user_app_server']."/");
      //  }
        $cururl = $this->getRequestUri();
        preg_match('/(.*)\.html/', $cururl, $arr);
        if (isset($arr[1]) && !empty($arr[1])) {
            ///入口列表
            preg_match_all('/^\/inbuy\/(top)-([\d]+).html/isU', $cururl, $arr);
            if (isset($arr[1][0]) && !empty($arr[1][0])) {
                $this->_request->setParam('mod', $arr[1][0]);
                $this->_request->setParam('cate_id', $arr[2][0]);
                $this->indexAction();
                exit;
            }
            
            ///入口列表 
            preg_match_all('/^\/inbuy\/([\w]+)-([\d]+)-([\d]+).html/isU', $cururl, $arr);
            if (isset($arr[1][0]) && !empty($arr[1][0])) {
                $this->_request->setParam('mod', $arr[1][0]);
                $this->_request->setParam('cate_id', $arr[2][0]);
                $this->_request->setParam('page', $arr[3][0]);
                $this->indexAction();
                exit;
            }

            ///库存详细入口
            preg_match('/^\/inbuy\/([\w]+)(-[\w]+)?.html/isU', $cururl, $arr);
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
            preg_match_all('/^\/inbuy\/([\w]+).html/isU', $cururl, $arr);
            if (isset($arr[1][0]) && !empty($arr[1][0])) {
                $this->_request->setParam('goods_mark', $arr[1][0]);
                $this->viewAction();
                exit;
            }

            preg_match_all('/^\/inbuy\/(sold|hot|top|price|new)-([\d]+)-([\d]+)-([-\d]+).html/isU', $cururl, $arr);
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
		$goodsGroupM = new Shop_Model_GoodsGroup('shop');

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
			$goodscate = $goodsCateM->fetchRow(array('is_actived'=>'1','is_buy'=>'1'));
			$cate_id = $goodscate['cate_id'];
		}
		$goodscate = $goodsCateM->fetchRow(array('cate_id'=>$cate_id,'is_buy'=>'1'));
		
		
		 
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
		$conditions['is_auth'] = '1';
		$conditions['is_buy'] = '1';
	    //判断地区
        /*if (isset($this->view->seed_Setting['goods_channel']) && $this->view->seed_Setting['goods_channel']=='BBC'){
        	$conditions['goods_channel']='BBC';
        }elseif (isset($this->view->seed_Setting['goods_channel']) && $this->view->seed_Setting['goods_channel']=='BC'){
        	$conditions['goods_channel']='BC';
        }*/
        
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

		$file = "inbuy/" . $mod . "-" . $cate_id;
		for ($i = 0; $i < count($search_attrs); $i++) {
			if ($selected_attrs[$i] > 0)
			$file .= $selected_attrs[$i];
			else
			$file .= '0';
			if ($i != count($search_attrs) - 1)
			$file.='-';
		}
		 
		$perpage = 50;
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
		//$fields = array('goods_id','goods_name','agent_id');
		if ($search_mode == 1) {
			$goodses = $goodsM->searchGoodses(array(($page - 1) * $perpage, $perpage), $conditions, null, $orderby);
		} else {
			$goodses = $goodsM->fetchGoodses(array(($page - 1) * $perpage, $perpage), $conditions, null, $orderby);
		}

		foreach ($goodses as $k =>$goods){
			$goodsinbuy = $goodsGroupM->fetchRows(null,array('group_id'=>$goods['goods_id']));
			if ($goodsinbuy){
				$shop_price = '0';
				$cost_price = '0';
				foreach ($goodsinbuy as $gk=>$gv){
					$single_goods = $goodsM->fetchGoods(array('goods_id'=>$gv['goods_id']));
					$stock = $goodsStockM->fetchRow(array('goods_id'=>$single_goods['goods_id'],'stock_id'=>$gv['stock_id']),"*", 'stock_shop_price ASC');
					if($stock['stock_id']>0){
						$goodses[$k]['inbuy'][$gk]['goods_mark'] = $single_goods['goods_mark'];
						$goodses[$k]['inbuy'][$gk]['goods_list_image'] = $single_goods['goods_list_image'];
						$goodses[$k]['inbuy'][$gk]['goods_number'] = $gv['goods_number'];//单个商品的数量
						$goodses[$k]['inbuy'][$gk]['shop_price'] = $gv['shop_price'];//单个商品的现价
						$goodses[$k]['inbuy'][$gk]['cost_price'] = $stock['stock_market_price'];//单个商品的原价
						
						$shop_price +=($gv['shop_price']*$gv['goods_number']);//总的,现价
					    $cost_price +=($stock['stock_shop_price']*$gv['goods_number']);//总的,原价
					}else{
						unset($goodses[$k]['inbuy'][$gk]);
//						$goodses[$k]['inbuy'][$gg]['goods_number'] = '0';
//						$goodses[$k]['inbuy'][$gg]['shop_price'] = '0.00';
//						$goodses[$k]['inbuy'][$gg]['market_price'] = '0.00';
					}
				}
				$goodses[$k]['cost_price']=$cost_price;
				$goodses[$k]['shop_price']=$shop_price;
			}
		}


		$this->view->total = $total;
		$this->view->goodses = $goodses;
		$this->view->search_attrs = $mysearch_attrs;
		$this->view->selected_attrs = $myselected_attrs;
		$this->view->mod = $mod;
		$this->view->cate_id = $cate_id;
        $this->view->brand_id = $brand_id;
		$this->view->cate = $cate = $goodsCateM->fetchRow(array('cate_id' => $cate_id));
		$this->view->cates =$cates = $goodsCateM->getGoodsGroupParentOption();

		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/inbuy/index.phtml");
			echo $content;
			exit;
		}
	}
}