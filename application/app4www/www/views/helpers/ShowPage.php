<?php
class Zend_View_Helper_ShowPage
{
	function showPage($page)
	{
		$str="";
		if (is_array($page)){
			//$str.= '<strong>总记录:'.$page['total'].' / 总页码:'.$page['totalpage'].' / 当前页:'.$page['curpage'].'</strong>';
			//$str.= '<a href="'.$page['baseurl'].'1">首页</a>';
			$str.= '<a href="'.$page['baseurl'].$page['prepage'].'">&lt;</a>';
				
			for ($i=$page['startpage'];$i<=$page['endpage'];$i++){
				$str.= '<a href="'.$page['baseurl'].$i.'">';
				if($page['curpage']==$i){
					$str.= '<span style="color:#7CBE28;">'.$i.'</span>';
				}else{
					$str.= $i;
				}
				$str.= '</a>';
			}
				
			$str.= '<a href="'.$page['baseurl'].$page['nextpage'].'">&gt;</a>';
			//$str.= '<a href="'.$page['baseurl'].$page['totalpage'].'">尾页</a>';
			return $str;
		}
	}
}