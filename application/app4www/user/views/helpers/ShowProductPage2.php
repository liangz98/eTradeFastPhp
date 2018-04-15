<?php
class Zend_View_Helper_ShowProductPage
{
	function ShowProductPage($page)
	{
		$str="";
		if (is_array($page) && $page['totalpage']>1){
			$str.= '<p>';
			$str.= '<span class="page_top"><a href="'.$page['pageurl'].'-1.html">首页</a></span>';
			$str.= '<span class="page_l"><a href="'.$page['pageurl']."-".$page['prepage'].'.html"><</a></span>';

			for ($i=$page['startpage'];$i<=$page['endpage'];$i++){
				if($page['curpage']==$i){
					$str.= '<a class="crumb">'.$i.'</span>';
				}else{
					$str.= '<a href="'.$page['pageurl']."-".$i.'.html" >'.$i.'</a>';
				}
			}

			$str.= '<span class="page_r"><a href="'.$page['pageurl']."-".$page['nextpage'].'.html">></a></span>';
			$str.= '<span class="page_tail"><a href="'.$page['pageurl']."-".$page['totalpage'].'.html">尾页</a></span>';
			$str.= '<span class="page_left"></span><span class="page_right"></span>';
			return $str;
		}
	}
}