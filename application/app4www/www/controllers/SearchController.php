<?php

class SearchController extends Shop_Controller_Action
{

    public $perpage = 20;

    function indexAction()
    {
        $shopM = new Shop_Model_Shop('shop');
        $keyword = strip_tags(trim($this->_request->getParam('keyword')));
        if (empty($keyword)) {
            header("location:/");
            exit();
        }
        $splitKeywords = preg_split("#\s+#", $keyword);
        if ($keyword) {
            // 商品
            $goodsM = new Shop_Model_Goods('shop');
            // 搜索模式标记
            $perpage = $this->perpage;
            $orderby = array(
                't1.order_by ASC',
                't1.goods_id DESC',
                't1.sold DESC'
            );
            
            $conditions = array();
            $orconditions = array();
            $conditions['is_actived'] = '1';
            $conditions['is_on_sale'] = '1';
            $conditions['is_auth'] = '1';
            $conditions['is_group'] = '0';
            $conditions['is_buy']='0';
            $conditions["IFNULL( goods_list_image, '') != ''"]=null;
            $conditions["IFNULL( goods_detail_image, '') != ''"]=null;

            $conditionGoodsName = '(';
            foreach ($splitKeywords as $k => $splitKeyword) {
                $conditionGoodsName = $conditionGoodsName . "t1.goods_name like '%" . addslashes($splitKeyword) . "%' and ";
            }
            $conditionGoodsName = $conditionGoodsName . '1=1)';
            $orconditions[$conditionGoodsName] = null;
            $orconditions["t1.goods_tips like '%" . addslashes($keyword) . "%'"] = null;
            $orconditions["t2.brand_name like '%" . addslashes($keyword) . "%'"] = null;
            $orconditions["t1.goods_sn like '%" . addslashes($keyword) . "%'"] = null;
            $orconditions["t1.goods_barcode like '%" . addslashes($keyword) . "%'"] = null;
            $orconditions["t6.stock_sn like '%" . addslashes($keyword) . "%'"] = null;
            $orconditions["t6.stock_barcode like '%" . addslashes($keyword) . "%'"] = null;
            $conditionGoodsTags = '(';
            foreach ($splitKeywords as $k => $splitKeyword) {
                $conditionGoodsTags = $conditionGoodsTags . "find_in_set('" . addslashes($splitKeyword) . "',goods_tags) > 0 and ";
            }
            $conditionGoodsTags = $conditionGoodsTags . '1=1)';
            $orconditions[$conditionGoodsTags] = null;
            
            // 判断地区
            // if (isset($this->view->seed_Setting['goods_channel']) && $this->view->seed_Setting['goods_channel'] == 'BBC') {
            //     $conditions['goods_channel'] = 'BBC';
            // } elseif (isset($this->view->seed_Setting['goods_channel']) && $this->view->seed_Setting['goods_channel'] == 'BC') {
            //     $conditions['goods_channel'] = 'BC';
            // }
            $conditions["t1.goods_channel != 'BBC+BC'"]=null;
            
            $page = intval($this->_request->getParam('page'));
            $total = $goodsM->fetchGoodsesCount($conditions, $orconditions,false);
            $pageObj = new Seed_Page($this->_request, $total, $perpage);
            $this->view->page = $pageObj->getPageArray();
            if ($page > $this->view->page['totalpage'])
                $page = $this->view->page['totalpage'];
            if ($page < 1)
                $page = 1;
            
            $goodses = $goodsM->fetchGoodses(array(
                ($page - 1) * $perpage,
                $perpage
            ), $conditions, $orconditions, $orderby);
            $goodsStockM = new Shop_Model_GoodsStock('shop');
            foreach ($goodses as $k => $goods) {
                $stock = $goodsStockM->fetchRow(array(
                    'goods_id' => $goods['goods_id']
                ), "*", 'stock_shop_price ASC');
                if ($stock && $stock['stock_id'] > 0) {
                    $goodses[$k]['shop_price'] = $stock['stock_shop_price'];
                    $goodses[$k]['market_price'] = $stock['stock_market_price'];
                    $goodses[$k]['stock_id'] = $stock['stock_id'];
                } else {
                    $goodses[$k]['shop_price'] = '0.00';
                    $goodses[$k]['market_price'] = '0.00';
                    $goodses[$k]['stock_id'] = 0;
                }
                $goodses[$k]['shop_name'] = $shopM->fetchOne(array(
                    'shop_id' => $goods['agent_id']
                ), 'shop_name');
            }
            
            $this->view->goodses = $goodses;
        }
        
        $this->view->keyword = $keyword;
        
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/search/index.phtml");
            echo $content;
            exit();
        }
    }
}