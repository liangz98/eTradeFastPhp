<?php
/**
 * Seed_Shipping_Yto
 * 
 * @author Biaoest (biaoest@gmail.com)
 * @link http://www.gzseed.com
 * @version 1.0.1
 * @copyright guangzhou seed studio
 * 
 **/
class Seed_Shipping_Yto
{
	public $_configure=array();
	
	public function __construct($cfg){
		$cfg_list=array();
		$cfg_list[]='base_fee';//1KG以内费用
		$cfg_list[]='step_fee';//续重每1KG或其零数
		$cfg_list[]='free_money';//免费额度

		
		//验证字段是否齐全
		if(!is_array($cfg))throw new Exception('Invalid Configure Params!');
		foreach ($cfg_list as $v){
			if(!isset($cfg[$v]))throw new Exception('Invalid Configure Params!');
		}
		
		$this->_configure=$cfg;
	}
	
	public function calculate($goods_weight, $goods_amount)
    {
        if ($this->_configure['free_money'] > 0 && $goods_amount >= $this->_configure['free_money'])
        {
            return 0;
        }
        else
        {
            $fee = $this->_configure['base_fee'];

            if ($goods_weight > 1)
            {
                $fee += (ceil(($goods_weight - 1) / 1)) * $this->_configure['step_fee'];
            }
            return $fee;
        }
    }
}