<?php
class QitaiController extends Shop_Controller_Action
{
	function indexAction()
	{
	
	$goodsStockM = new Shop_Model_GoodsStock('shop');
	$goodes  = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." WHERE stock_id in (524,516,517,518,519,520,521,530) order by field (stock_id,524,516,517,518,519,520,521,530)");
	$goodes1 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." WHERE stock_id in (1935,1936,1937,1938,1939,1940) order by field (stock_id,1935,1936,1937,1938,1939,1940)");
	$goodes2 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." WHERE stock_id in (513,514,515) order by field (stock_id,513,514,515)");
	$goodes3 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." WHERE stock_id in (525,526,527,528) order by field (stock_id,525,526,527,528)");
	$goodes4 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." WHERE stock_id in (1931,1932,1933) order by field (stock_id,1931,1932,1933)");
	$goodes5 = $goodsStockM->_db->fetchAll("select stock_id,stock_market_price,stock_shop_price from ".$goodsStockM->_prefix.$goodsStockM->_table_name." WHERE stock_id in (522,523,529,1934,531,533) order by field (stock_id,522,523,529,1934,531,533)");
	$this->view->goodes=$goodes;
	$this->view->goodes1=$goodes1;
	$this->view->goodes2=$goodes2;
	$this->view->goodes3=$goodes3;
	$this->view->goodes4=$goodes4;
	$this->view->goodes5=$goodes5;

	if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/qitai/index.phtml");
			echo $content;
			exit;
		}
	}
}
?>