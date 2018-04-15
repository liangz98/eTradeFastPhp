<?php
class Zend_View_Helper_ShowContent extends Mobile_View_Helper
{
	/**
	 * 处理并显示描述
	 * @param string $description
	 * @return mixed|NULL
	 */
	public function ShowContent($description,$lazy=1)
	{
		if(isset($this->view->seed_Setting['upload_view_server'])){
			$upload_view_server=$this->view->seed_Setting['upload_view_server'];
			$patterns=array();
			$replacements=array();
			$patterns[]='/{SEED_UPLOAD_SERVER}/';
			$replacements[]=$upload_view_server;
			$description = preg_replace($patterns, $replacements, $description);
			return $description;
		}
		return null;
	}
}