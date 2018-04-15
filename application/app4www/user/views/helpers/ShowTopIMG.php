<?php
class Zend_View_Helper_ShowTopIMG extends Shop_View_Helper
{
	function ShowTopIMG($Arr,$accountID,$key,$client)
	{
		//$accountID;
	    //$client;
	    //$Arr; 附件数组
	    //$key; 查询key
       $IMG='';

		foreach( $Arr as $k=>$v){
			if($v['attachType']==$key){
				if(!empty($client)&&$accountID!=$client){continue;}
				$IMG.='<li style="width: 100%;float: left;"><em class="new_upicon"></em>';
				$IMG.= '<a class="new_uptitle"';
				$IMG.= ' href="'. $this->view->seed_Setting['KyUrlex'];
				$IMG.= '/doc/download.action?sid='.session_id();
				$IMG.=	'&nid='.$v['attachID'].'&vid='.$v['verifyID'];
				$IMG.=	'" download>'.$v['name'].'</a>';
                $IMG.='<input type="hidden" value="'.$v['attachType'].'">';
				$IMG.=	'<span  class="fr">'.$this->view->translate('creatdate').date('Y-m-d H:i:s',strtotime($v['createTime']));
				$IMG.= '</span></li>';
			}
		}


		return $IMG;
	}
}