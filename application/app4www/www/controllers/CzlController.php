<?php
class CzlController extends Shop_Controller_Action
{
	function indexAction()
	{
	$goodsStockM = new Shop_Model_GoodsStock('shop');
	$goodes  = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (1721,1930,10,13,211,427) order by field (stock_id,1721,1930,10,13,211,427)");
	$goodes1 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (87,1306,1126,1539) order by field (stock_id,87,1306,1126,1539)");
	$goodes2 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (689,504,663,506,509,752) order by field (stock_id,689,504,663,506,509,752)");
	$goodes3 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (430,497,132,1539,235,1632) order by field (stock_id,430,497,132,1539,235,1632)");
	$goodes4 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (180,184,89,1106,61,69) order by field (stock_id,180,184,89,1106,61,69)");
	$goodes5 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (1656,120,1444,1576,439,1657) order by field (stock_id,1656,120,1444,1576,439,1657)");
	$this->view->goodes=$goodes;
	$this->view->goodes1=$goodes1;
	$this->view->goodes2=$goodes2;
	$this->view->goodes3=$goodes3;
	$this->view->goodes4=$goodes4;
	$this->view->goodes5=$goodes5;

	if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/czl/index.phtml");
			echo $content;
			exit;
		}
	}
}
?>