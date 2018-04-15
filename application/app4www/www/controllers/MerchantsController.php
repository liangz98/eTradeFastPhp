<?php

class MerchantsController extends Shop_Controller_Action
{
    public $perpage = 20;
    public $goods_total = '';
    public $page_num = '';

    public function preDispatch()
    {
        $this->view->cur_pos = $this->_request->getParam('controller');

        $cururl = $this->getRequestUri();
        if ($cururl == '/merchants') {
            $this->_request->setParam('all', 'all');
            $this->indexAction();
            exit;
        }
        preg_match('/(.*)\.html/', $cururl, $arr);
        if (isset($arr[1]) && !empty($arr[1])) {
            ///入口列表
            preg_match_all('/^\/merchants\/(top)-([\d]+).html/isU', $cururl, $arr);
            if (isset($arr[1][0]) && !empty($arr[1][0])) {
                $this->_request->setParam('mod', $arr[1][0]);
                $this->_request->setParam('cate_id', $arr[2][0]);
                $this->indexAction();
                exit;
            }

            ///库存详细入口
            preg_match('/^\/merchants\/([\w]+)(-[\w]+)?.html/isU', $cururl, $arr);
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
            preg_match_all('/^\/merchants\/([\w]+).html/isU', $cururl, $arr);
            if (isset($arr[1][0]) && !empty($arr[1][0])) {
                $this->_request->setParam('goods_mark', $arr[1][0]);
                $this->viewAction();
                exit;
            }

            preg_match_all('/^\/merchants\/(sold|hot|top|price|new)-([\d]+)-([\d]+)-([\d]+).html/isU', $cururl, $arr);
            if (is_array($arr) && count($arr) > 1) {
                $this->_request->setParam('mod', $arr[1][0]);
                $this->_request->setParam('shop_id', $arr[2][0]);
                $this->_request->setParam('brand_id', $arr[3][0]);
                $this->_request->setParam('page', $arr[4][0]);
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
        $shopM = new Shop_Model_Shop('shop');
        $brandM = new Shop_Model_GoodsBrand('shop');
        $shopM = new Shop_Model_Shop('shop');

        $shops = $shopM->fetchRows(null, array('is_actived' => 1));
        foreach ($shops as $sk => $shop) {
            $brands = $brandM->fetchRows(null, array('is_actived' => '1', 'shop_id' => $shop['shop_id']), 'order_by ASC');
            $shops[$sk]['brands'] = $brands;
        }

        $this->view->shops = $shops;
        $f1 = new Seed_Filter_Alnum();
        $f2 = new Zend_Filter();
        $f2->addFilter(new Zend_Filter_StripTags())->addFilter(new Zend_Filter_StripNewlines());

        $mod = $f1->filter($this->_request->getParam('mod'));
        $shop_id = intval($this->_request->getParam('shop_id'));
        $brand_id = intval($this->_request->getParam('brand_id'));
        if (empty($mod)) {
            $mod = "top";
        }
       
        // if (empty($shop_id) && count($shops)>0) {
        //     $shop_id = $shops[0]['shop_id'];
        // }

        $conditions = array();
        $conditions['is_actived'] = '1';
        $conditions['is_on_sale'] = '1';
        $conditions['is_auth'] = '1';
        $conditions['is_group']='0';//非套装
        $conditions["IFNULL( goods_list_image, '') != ''"]=null;
        $conditions["IFNULL( goods_detail_image, '') != ''"]=null;   
        $conditions["t1.goods_channel != 'BBC+BC'"]=null;
  
        if ($shop_id > 0) {
            $conditions["t1.agent_id = ".$shop_id]=null;
            $shop = $shopM->fetchRow(array('shop_id' => $shop_id));
            $this->view->shop_name = $shop['shop_name'];
        }

        
        if ($brand_id > 0) {
            $conditions['brand_id'] = $brand_id;
            $brand = $brandM->fetchRow(array('brand_id' => $brand_id));
            $this->view->brand_name = $brand['brand_name'];
        }



        // if ($shop_id > 0 ) {
        //     if($brand_id == 0 ){
        //         $conditions["t1.agent_id = ".$shop_id]=null;
        //         $shop = $shopM->fetchRow(array('shop_id' => $shop_id));
        //         $this->view->shop_name = $shop['shop_name'];
        //     }
        // }

        // if ($brand_id > 0) {
        //     $conditions['brand_id'] = $brand_id;
        //     $goodsM = new Shop_Model_Goods('shop'); 
        //     $shop_ids = $goodsM->fetchOne(array('brand_id'=>$brand_id),'agent_id');
        //     $shop_name = $shopM->fetchOne(array('shop_id'=>$shop_ids),'shop_name');             
        //     $conditions["t1.agent_id = ".$shop_ids]=null;
        //     $brand = $brandM->fetchRow(array('brand_id' => $brand_id));
        //     $this->view->shop_ids = $shop_ids;
        //     $this->view->shop_name = $shop_name;
        //     $this->view->brand_name = $brand['brand_name'];
        // }

        //判断地区
        // if (isset($this->view->seed_Setting['goods_channel']) && $this->view->seed_Setting['goods_channel']=='BBC'){
        // 	$conditions['goods_channel']='BBC';
        // }elseif (isset($this->view->seed_Setting['goods_channel']) && $this->view->seed_Setting['goods_channel']=='BC'){
        // 	$conditions['goods_channel']='BC';
        // }
        

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

        $search_mode = 0;

        $file = "merchants/" . $mod . "-" . $shop_id . '-' . $brand_id;

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
        foreach ($goodses as $k => $goods) {
            $stock = $goodsStockM->fetchRow(array('goods_id' => $goods['goods_id']), "*", 'stock_shop_price ASC');
            if ($stock['stock_id'] > 0) {
                $goodses[$k]['goods_number'] = $stock['stock_value'];
                $goodses[$k]['shop_price'] = $stock['stock_shop_price'];
                $goodses[$k]['market_price'] = $stock['stock_market_price'];
            } else {
                $goodses[$k]['goods_number'] = '0';
                $goodses[$k]['shop_price'] = '0.00';
                $goodses[$k]['market_price'] = '0.00';
            }
            $goodses[$k]['shop_name'] = $shopM->fetchOne(array('shop_id'=>$goods['agent_id']),'shop_name');
        }

        $this->view->total = $total;
        $this->view->goodses = $goodses;
        $this->view->mod = $mod;
        $this->view->shop_id = $shop_id;
        $this->view->brand_id = $brand_id;

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/merchants/index.phtml");
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

        $goodsM = new Shop_Model_Goods('shop');
        $goodsCateM = new Shop_Model_GoodsCate('shop');

        if (empty($goods_mark)) Shop_Browser::redirect('没有找到商品！', $this->view->seed_BaseUrl . "/");
        $condition['goods_mark'] = $goods_mark;
        $condition['is_actived'] = '1';
        $condition['is_auth'] = '1';

        $goods = $goodsM->fetchGoods($condition);
        if ($goods['goods_id'] < 1) {
            Shop_Browser::redirect('没有找到相关产品/或者产品未上架！', $this->view->seed_BaseUrl . "/");
        }
        $goods_id = $goods['goods_id'];

        //上级分类
        $parent_cates = $goodsCateM->getParentNav($goods['cate_id']);
        $this->view->parent_cates = $parent_cates;

        //点击自增
        $goodsM->updateRow(array('view_total' => new Zend_Db_Expr("view_total + 1")), array('goods_id' => $goods_id));

        //商品库存开始
        $goodsStockM = new Shop_Model_GoodsStock('shop');
        $goodsStockGroupM = new Shop_Model_GoodsStockGroup('shop');
        $skuM = new Shop_Model_Sku('shop');

        $stock_groups = $goodsStockGroupM->fetchRows(null, array('goods_id' => $goods_id), 'order_by ASC');
        $goods['group_id'] = 0;
        $goods['stock_id'] = 0;
        if (count($stock_groups) > 0) {
            if (isset($stock_groups[$group_id])) {
                $stock_group = $stock_groups[$group_id];
            } else {
                $stock_group = $stock_groups[0];
            }
            $this->view->stock_group = $stock_group;

            $goods_stocks = $goodsStockM->fetchRows(null, array('goods_id' => $goods_id, 'group_id' => $stock_group['group_id']), 'order_by ASC');
            if (count($goods_stocks) > 0) {
                $goods['stock_id'] = $goods_stocks[0]['stock_id'];
                $goods['shop_price'] = $goods_stocks[0]['stock_shop_price'];
                $goods['market_price'] = $goods_stocks[0]['stock_market_price'];
                $goods['goods_number'] = $goodsStockM->fetchSum('stock_value', array('goods_id' => $goods_id, 'group_id' => $stock_group['group_id']));
                $goods['stock_value'] = $goodsStockM->fetchSum('stock_value', array('goods_id' => $goods['goods_id']));
                if (count($goods_stocks) > 1) {
                    $this->view->goods_stocks = $goods_stocks;
                }
            }
        } else {
            $goods_stocks = $goodsStockM->fetchRows(null, array('goods_id' => $goods_id), 'order_by ASC');
            if (count($goods_stocks) > 0) {
                $goods['stock_id'] = $goods_stocks[0]['stock_id'];
                $goods['shop_price'] = $goods_stocks[0]['stock_shop_price'];
                $goods['market_price'] = $goods_stocks[0]['stock_market_price'];
                $goods['goods_number'] = $goodsStockM->fetchSum('stock_value', array('goods_id' => $goods_id));
                $goods['stock_value'] = $goodsStockM->fetchSum('stock_value', array('goods_id' => $goods['goods_id']));
                if (count($goods_stocks) > 1) {
                    $this->view->goods_stocks = $goods_stocks;
                }
            }

        }
        $this->view->stock_groups = $stock_groups;
        //商品库存结束

        //产品详细
        $goodsDetailM = new Shop_Model_GoodsDetail('shop');
        $detail = $goodsDetailM->fetchRow(array('goods_id' => $goods_id));
        if ($detail['goods_id'] > 0) {
            $goods['goods_content'] = $detail['goods_content'];
            $goods['goods_usemethod'] = $detail['goods_usemethod'];
        } else {
            $goods['goods_content'] = "";
            $goods['goods_usemethod'] = "";
        }

        //属性
        $attributeM = new Shop_Model_Attribute('shop');
        $goodsAttrM = new Shop_Model_GoodsAttr('shop');
        $attributes = $attributeM->fetchAttributes(null, array('type_id' => $goods['type_id']), array('t2.order_by ASC', 't1.order_by ASC'));
        $myattrs = array();
        foreach ($attributes as $k => $attr) {
            $goodsattr = $goodsAttrM->fetchRow(array('attr_id' => $attr['attr_id'], 'goods_id' => $goods_id));
            $attr_value = ($goodsattr['attr_value'] == null) ? "" : $goodsattr['attr_value'];
            $group_id = intval($attr['group_id']);
            $myattrs[$group_id]['group_id'] = $group_id;
            $myattrs[$group_id]['group_name'] = (isset($attr['group_name']) && !empty($attr['group_name'])) ? $attr['group_name'] : "";
            $myattrs[$group_id]['group_desc'] = (isset($attr['group_desc']) && !empty($attr['group_desc'])) ? $attr['group_desc'] : "";
            $myattrs[$group_id]['attrs'][] = array('attr_id' => $attr['attr_id'], 'type_id' => $attr['type_id'], 'attr_name' => $attr['attr_name'], 'attr_input_type' => $attr['attr_input_type'], 'attr_values' => $attr['attr_values'], 'attr_value' => $attr_value);
        }
        $this->view->attrs = $myattrs[$group_id]['attrs'];

        //商品相册
        $photosM = new Shop_Model_GoodsMPhoto("shop");
        $this->view->photos = $photosM->fetchRows(array(0, 0), array("goods_id" => $goods['goods_id']), "order_by asc");
        $this->view->hideshare = true;

        //收藏状态
        $goods['collect_status'] = '0';
        if ($this->view->seed_User['user_id'] > 0) {
            $collectGoodsM = new Shop_Model_CollectionGoods('shop');
            $check = $collectGoodsM->fetchRow(array('user_id' => $this->view->seed_User['user_id'], 'shop_id' => '0', 'goods_id' => $goods['goods_id']));
            if ($check['goods_id'] > 0) $goods['collect_status'] = '1';
        }

        $this->view->goods = $goods;

        /*if(!empty($qrcode)){
            $goodsQrcodeM = new Shop_Model_GoodsQrcode('shop');
            $this->view->goods_qrcode = $goodsQrcodeM->fetchRow(array('goods_id'=>$goods_id,'shop_id'=>'0','gq_mark'=>$qrcode));
        }
        $this->view->qrcode = $qrcode;*/

        //推荐产品
        $recommend_goodses = $goodsM->fetchGoodses(array(0, 5), array('is_recommend' => '1', 'goods_id !=' . $goods['goods_id'] => null));
        foreach ($recommend_goodses as $k => $goods) {
            $stock = $goodsStockM->fetchRow(array('goods_id' => $goods['goods_id']), "*");
            if ($stock['stock_id'] > 0) {
                $recommend_goodses[$k]['goods_number'] = $stock['stock_value'];
                $recommend_goodses[$k]['shop_price'] = $stock['stock_shop_price'];
                $recommend_goodses[$k]['market_price'] = $stock['stock_market_price'];
            } else {
                $recommend_goodses[$k]['goods_number'] = '0';
                $recommend_goodses[$k]['shop_price'] = '0.00';
                $recommend_goodses[$k]['market_price'] = '0.00';
            }
        }
        $this->view->recommend_goodses = $recommend_goodses;

        $userM = new Seed_Model_User('system');
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
        $total = $goodsCommentM->fetchRowsCount($commentcondition);
        $pageObj = new Seed_Page($this->_request, $total, $perpage);
        $this->view->page = $pageObj->getPageArray();
        $pageAry = $pageObj->getPageArray();
        if ($page > $pageAry['totalpage']) {
            $page = $pageAry['totalpage'];
        }
        if ($page < 1) {
            $page = 1;
        }
        # 获取数据
        $limit = array(($page - 1) * $perpage, $perpage);
        $orderBy = array('comment_time DESC');
        $commentList = $goodsCommentM->fetchRows($limit, $commentcondition, $orderBy);
        if (empty($commentList)) {
            $commentList = array();
        }
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
            if (empty($replyList)) {
                $replyList = array();
            }
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
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/products/view.phtml");
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
            $userM = new Seed_Model_User('system');
            $goodsCommentM = new Shop_Model_GoodsComment('shop');
            $goodsCommentReplyM = new Shop_Model_GoodsCommentReply('shop');
            # 参数
            $goods_id = $f3->filter($this->_request->getParam('goods_id'));
            $page = intval($this->_request->getParam('page'));
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
            $total = $goodsCommentM->fetchRowsCount($condition);
            $pageObj = new Seed_Page($this->_request, $total, $perpage);
            $this->view->page = $pageObj->getPageArray();
            $pageAry = $pageObj->getPageArray();
            if ($page > $pageAry['totalpage']) {
                $page = $pageAry['totalpage'];
            }
            if ($page < 1) {
                $page = 1;
            }
            # 获取数据
            $limit = array(($page - 1) * $perpage, $perpage);
            $orderBy = array('comment_id DESC');
            $commentList = $goodsCommentM->fetchRows($limit, $condition, $orderBy);
            if (empty($commentList)) {
                $commentList = array();
            }
            foreach ($commentList as $k => $v) {
                # 评论账号头像
                if (empty($v['user_face'])) {
                    $user = $userM->fetchRow(array('user_id' => $v['user_id']), array('user_big_face'));
                    $uFace = $user['user_big_face'];
                    $commentList[$k]['user_face'] = empty($uFace) ? '/images/face50_50.jpg' : $uFace;
                }
                # 评论回复
                $replyList = $goodsCommentReplyM->fetchRows(array(0, 10), array('comment_id' => $v['comment_id']));
                if (empty($replyList)) {
                    $replyList = array();
                }
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
            if (defined('SEED_WWW_TPL')) {
                $content = $this->view->render(SEED_WWW_TPL . "/products/commentlist.phtml");
                echo $content;
                exit;
            }
        } catch (Exception $e) {
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

        $goods = $goodsM->fetchRow(array('goods_id' => $goods_id));
        if ($goods['goods_id'] < 1) exit('没有找到相关产品！');
        $this->view->goods = $goods;

        $stock_id = intval($this->_request->getPost('stock_id'));

        $goodsStock = $goodsStockM->fetchRow(array('stock_id' => $stock_id));
        if ($goodsStock['stock_id'] < 1) exit('没有找到相关库存！');

        if (empty($goodsStock['stock_image'])) $goodsStock['stock_image'] = $goods['goods_list_image'];
        $goodsStock['stock_image_realpath'] = $this->view->showImage($goodsStock['stock_image']);
        $goodsStock['stock_market_price_format'] = '¥ ' . $goodsStock['stock_market_price'];
        $goodsStock['stock_shop_price_format'] = '¥ ' . $goodsStock['stock_shop_price'];
        $mydata = $goodsStock;
        echo Zend_Json::encode($mydata);
        exit;
    }

    //查询分类
    function getcateAction()
    {
        $other = trim($this->_request->getParam('other'));
        $cate_id = intval($this->_request->getParam('cate'));
        $cateM = new Shop_Model_GoodsCate('shop');
        $cates = $cateM->fetchRows(NULL, array('parent' => $cate_id));
        foreach ($cates as $k => $v) {
            $go = $cateM->fetchRows(NULL, array('parent' => $v['cate_id']));
            $cates[$k]['go'] = $go ? true : false;
        }

        $this->view->cate_id = $cate_id;
        $this->view->cates = $cates;
        if (defined('SEED_V_TPL')) {
            $content = $this->view->render(SEED_V_TPL . "/products/getcate.phtml");
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