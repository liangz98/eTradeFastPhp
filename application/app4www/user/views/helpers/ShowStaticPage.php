<?php
class Zend_View_Helper_ShowStaticPage
{
	function showStaticPage($page , $first = true)
	{
		$str="";
		if (is_array($page) && $page['totalpage']>1){
			//$str.= '<div class="page">';
			//if($first)
			//	$str.= '<a href="'.$page['pageurl'].'-1.html">首页</a>';
			//else 
			//	$str.= '<a href="'.$page['pageurl'].'.html">首页</a>';
			
			if(!$first && $page['prepage']=='1')
				$str.= '<a href="'.$page['pageurl'].'.html">&lt;</a>';
			else
				$str.= '<a href="'.$page['pageurl'].'.html">&lt;</a>';
				
			for ($i=$page['startpage'];$i<=$page['endpage'];$i++){
				if($page['curpage']==$i){
					$str.= '<a class="crumb">'.$i.'</span>';
				}else{
					if(!$first && $i=='1')
						$str.= '<a href="'.$page['pageurl'].'.html" >'.$i.'</a>';
					else
						$str.= '<a href="'.$page['pageurl'].'.html">&lt;</a>';
				}
			}

			$str.= '<a href="'.$page['pageurl'].'.html">&lt;</a>';
			//$str.= '<a href="'.$page['pageurl']."-".$page['totalpage'].'.html">尾页</a>';
			//$str.= '<span class="page_left"></span><span class="page_right"></span></div>';
			return $str;
		}
	}
}