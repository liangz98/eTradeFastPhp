<?php
class Zend_View_Helper_ShowToRptypeTitle extends Shop_View_Helper
{
	function ShowToRptypeTitle($key,$dic)
	{
		//$rptype 判断 输出类型  ‘R +’ ‘P-’ ‘o 不显示’；
		if($key=='o'){
		    $str="null";
        }else{
		    if($key=='R'){
		        $str=$dic.'_R';
            }elseif ($key='p'){
                $str=$dic.'_P';
            }else{
                $str="null";
            }

        };

		return $this->view->translate($str);
	}
}