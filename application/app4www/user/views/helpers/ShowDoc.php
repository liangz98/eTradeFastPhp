<?php
class Zend_View_Helper_ShowDoc extends Shop_View_Helper
{
    //订单模块 文档 展示判断
	function showDoc($attArr)
	{
        //$attArr 附件数组
        //$this->view->seed_Setting['KyUrlex'] 是服务端 附件地址路径
        //通过附件 ext 扩展名判断文件类型  图片 || 文件

        $str="";
        if(is_array($attArr) && count($attArr)>0){
            foreach($attArr as $k=>$attlist){
                if ($attlist['ext']!="jpeg" && $attlist['ext']!="png" && $attlist['ext']!="jpg" && $attlist['ext']!="gif" && $attlist['ext']!="GIF" && $attlist['ext']!="JPG" && $attlist['ext']!="PNG"){
                    $str.='<li style="margin: 0 10px;width: 130px;float: left;"><a class="new_uptitle" href="';
                    $str.=$this->view->seed_Setting['KyUrlex'].'/doc/download.action?sid='.session_id().'&nid='.$attlist['attachID'].'&vid='.$attlist['verifyID'];
                    $str.='" download><img width="125px" height="125px"  src="/ky/ico/'.strtolower($attlist['ext']).'.png"';
                    $str.='alt='.$attlist['attachType'].'><span style="width:120px;line-height:20px;display:block;word-wrap: break-word">'.$attlist['name'].'</span></a>';
                    $str.='<input type="hidden" name="attachNid[]" value="'.$attlist['attachID'].'">';
                    $str.='<input type="hidden" name="attachVid[]" value="'.$attlist['verifyID'].'">';
                    $str.='<input type="hidden" name="attachType[]" value="'.$attlist['attachType'].'">';
                    $str.='<input type="hidden" name="attachName[]" value="'.$attlist['name'].'">';
                    $str.='<input type="hidden" name="attachSize[]" value="'.$attlist['size'].'"></li>';

                }else{
                $str.='<li style="margin: 0 10px;width: 130px;float: left;"><a class="new_uptitle" href="';
                $str.=$this->view->seed_Setting['KyUrlex'].'/doc/download.action?sid='.session_id().'&nid='.$attlist['attachID'].'&vid='.$attlist['verifyID'];
                $str.='"download><img width="125px" height="125px"  src="'. $this->view->seed_Setting['KyUrlex'];
                $str.='/doc/download.action?sid='.session_id();
                $str.='&nid='.$attlist['attachID'];
                $str.='&vid='.$attlist['verifyID'];
                $str.='&size=MIDDLE" name='.$attlist['name'];
                $str.='alt='.$attlist['attachType'].'><span style="width:120px;line-height:20px;display:block;word-wrap: break-word">'.$attlist['name'].'</span></a>';
                $str.='<input type="hidden" name="attachNid[]" value="'.$attlist['attachID'].'">';
                $str.='<input type="hidden" name="attachVid[]" value="'.$attlist['verifyID'].'">';
                $str.='<input type="hidden" name="attachType[]" value="'.$attlist['attachType'].'">';
                $str.='<input type="hidden" name="attachName[]" value="'.$attlist['name'].'">';
                $str.='<input type="hidden" name="attachSize[]" value="'.$attlist['size'].'"></li>';
                }
            }
        }else{
            $str="";
        }

        return $str;
	}
}