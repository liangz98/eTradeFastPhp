<?php
class Zend_View_Helper_ShowOrderPage
{
	function ShowOrderPage($page,$id)
	{
		$str="";
		if (is_array($page) && $page['totalpage']>1){
			$str.= '<p>';
			//$str.= '<strong>总记录:'.$page['total'].' / 总页码:'.$page['totalpage'].' / 当前页:'.$page['curpage'].'</strong>';
			//$str.= '<a href="'.$page['baseurl'].'1">首页</a>';
//			$str.= '<span class="page_top"><a href="'.$page['pageurl'].'-1.html">首页</a></span>';
			$str.= '<span class="page_l"><a href="'.$page['pageurl'].'-'.$page['prepage'].'.html?id='.$id.'"><</a></span>';
			$str.= '<input id="pagekey" style="width:30px;text-align:center;color:#DF7E20;border: 1px solid #A0A0A0;border-radius:5px;margin:0 5px;" type="text" value='.$page['curpage'].'>';
			$str.= '<input id="pagestr"  type="hidden" value='.$page['pageurl'].' >';
			if(!empty($id)){
			$str.= '<input id="pageid"  type="hidden" value="?id='.$id.'" >';
			}

			$str.= '<a href="'.$page['pageurl']."-".$page['totalpage'].'.html">/'.$page['totalpage'].'</a>';
			$str.= '<a id="gokey" href="#" style="color:#DF7E20;width: 30px;display: inline-block;">Go</a>';

			
			$str.= '<span class="page_r"><a href="'.$page['pageurl']."-".$page['nextpage'].'.html?id='.$id.'">></a></span>';
//			$str.= '<span class="page_tail"><a href="'.$page['pageurl']."-".$page['totalpage'].'.html">尾页</a></span>';
			$str.= '<span class="page_left"></span><span class="page_right"></span>';
			$str.='<script type="text/javascript" src="/ky/js/pageGo.js"></script>';
			return $str;
		}
	}
}