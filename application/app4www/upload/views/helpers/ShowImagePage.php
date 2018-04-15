<?php
class Zend_View_Helper_ShowImagePage
{
	function ShowImagePage($page)
	{
		$str="";
		if (is_array($page)){
			$str.= '<span class="text">共'.$page['total'].'条记录</span>';
            if($page['curpage'] > 1) {
                $str.= '<a class="prev" href="'.$page['baseurl'].$page['prepage'].'">上一页<b></b></a>';
            }
			if($page['startpage'] - 1 > 1) {
                $str.='<a class="prev" href="'.$page['baseurl'].'1">1</a>';
                $str.='<span class="text">...</span>';
            }
			for ($i=$page['startpage'];$i<=$page['endpage'];$i++){
				if($page['curpage']==$i){
					$str.= '<a class="current">'.$i.'</a>';
				}else{
					$str.= '<a class="prev" href="'.$page['baseurl'].$i.'">'.$i.'</a>';
				}
			}
			if($page['totalpage'] - $page['endpage'] > 1) {
                $str.='<span class="text">...</span>';
                $str.='<a class="next" href="'.$page['baseurl'].$page['totalpage'].'">'.$page['totalpage'].'</a>';
            }
            if($page['curpage'] < $page['totalpage']) {
                $str.= '<a href="'.$page['baseurl'].$page['nextpage'].'">下一页</a>';
            }
			return $str;
		}
	}
}