<?php
class Mobile_View_Helper extends Zend_View_Helper_Abstract
{
	protected $models = array();
	
	/**
	 * 获取模型的单一实例
	 * @param string $model_name
	 * @param string $db_name
	 * @return multitype:|mixed|unknown
	 */
	protected function getInstance($model_name,$db_name)
	{
		if(isset($this->models[$model_name]) && is_object($this->models[$model_name])){
			return $this->models[$model_name];
		}else{
			$registry = Zend_Registry::getInstance();
			if($registry->isRegistered('shop_view_helper_models')){
				$models = $registry->get('shop_view_helper_models');
				if(!empty($models)){
					if(isset($models[$model_name]) && is_object($models[$model_name])){
						return $models[$model_name];
					}
				}
			}
			$modelM = new $model_name($db_name);
			if(is_object($modelM)){
				$this->models[$model_name] = $modelM;
				$registry->set('shop_view_helper_models', $this->models);
				return $modelM;
			}
		}
	}
	
	/**
	 * 返回当前相对目录地址。
	 * @return string
	 */
	public function getBaseUrl()
	{
		$fc = Zend_Controller_Front::getInstance();
		return $fc->getBaseUrl();
	}
}