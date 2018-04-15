<?php
class Zend_View_Helper_ShowGoodsPosition extends Shop_View_Helper
{
	/**
	 * 显示商品导航
	 * @param int $cate_id 分类ID
	 * @param int $goods_id 商品ID
	 * @return string
	 */
	public function showGoodsPosition($cate_id, $goods_id = 0)
	{
		$position = array();
		$position[] = "<a href='/'>{$this->view->helper_Setting['position_index_name']}</a>";
		$position[] = "<a href='/{$this->view->goodsSearchAttr()->getInitUrl()}'>{$this->view->helper_Setting['goods_position_name']}</a>";
		if($cate_id > 0){
			$goodsCateM = new Shop_Model_GoodsCate('shop');
			$goodsCate = $goodsCateM->fetchRow(array('cate_id'=>$cate_id));
			if($goodsCate['cate_id']>0){
				$navs = $goodsCateM->getParentnav($goodsCate['cate_id']);
				if(is_array($navs)){
					$i = 0;
					foreach ($navs as $nav){
						if($i == count($navs)-1 && !($goods_id > 0)){
							$position[] = "<em>{$nav['cate_name']}</em>";
						}else{
							$position[] = "<a href='/{$this->view->goodsSearchAttr()->getCateUrl($nav['cate_id'])}'>{$nav['cate_name']}</a>";
						}
						$i++;
					}
				}
			}
			if($goods_id > 0){
				$goodsM = new Shop_Model_Goods('shop');
				$goods = $goodsM->fetchRow(array('goods_id'=>$goods_id),array('goods_id','goods_name'));
				if($goods['goods_id'] > 0){
					$position[] = "<em>{$goods['goods_name']}</em>";
				}
			}
			return implode($this->view->helper_Setting['position_implode_str'], $position);
		}else{
			return implode($this->view->helper_Setting['position_implode_str'], $position);
		}
	}
}