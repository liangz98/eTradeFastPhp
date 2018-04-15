<?php
class GdyfController extends Shop_Controller_Action
{
	function indexAction()
	{
		$goodsStockM = new Shop_Model_GoodsStock('shop');
		$goodes  = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (500,502,2738,2737,506,511,2716,2715,524,117,1983,1981) order by field (stock_id,500,502,2738,2737,506,511,2716,2715,524,117,1983,1981)");
		$goodes1 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (290,1745,291,1721,1614,1604,2130,1719,1550,1549,2734,1738,1708,1707) order by field (stock_id,290,1745,291,1721,1614,1604,2130,1719,1550,1549,2734,1738,1708,1707)");
		$goodes2 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (2169,1559,2162,1545,2376,1540,1616,1568,2739,1610,2571,1588,549,1734,1733) order by field (stock_id,2169,1559,2162,1545,2376,1540,1616,1568,2739,1610,2571,1588,549,1734,1733)");
		$goodes3 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." 
		WHERE stock_id in (1035,1368,1634,2701,1306,665,609,1438,2378) order by field (stock_id,1035,1368,1634,2701,1306,665,609,1438,2378)");
		$this->view->goodes=$goodes;
		$this->view->goodes1=$goodes1;
		$this->view->goodes2=$goodes2;
		$this->view->goodes3=$goodes3;
	if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/gdyf/index.phtml");
			echo $content;
			exit;
		}
	}
}
?>