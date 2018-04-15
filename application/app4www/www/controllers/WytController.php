<?php
class WytController extends Shop_Controller_Action
{
	function indexAction()
	{
	//header("Location:http://www.sendtou.com");exit; 
	$goodsStockM = new Shop_Model_GoodsStock('shop');
	$goodes  = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (2168,2174,2162,2166) order by field (stock_id,2168,2174,2162,2166)");
	$goodes1 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (2168,2178,2172,2185,2187) order by field (stock_id,2168,2178,2172,2185,2187)");
	$goodes2 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (2169,2174,2175,2176,2167) order by field (stock_id,2169,2174,2175,2176,2167)");
	$goodes3 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (2166,2179,2180,2202,2201) order by field (stock_id,2166,2179,2180,2202,2201)");
	$goodes4 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (2162,2199,2163,2196,2251) order by field (stock_id,2162,2199,2163,2196,2251)");
	$this->view->goodes=$goodes;
	$this->view->goodes1=$goodes1;
	$this->view->goodes2=$goodes2;
	$this->view->goodes3=$goodes3;
	$this->view->goodes4=$goodes4;

	if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/wyt/index.phtml");
			echo $content;
			exit;
		}
	}
}
?>