<?php
/**
 *  解析文本关键字
 */
class Wechat_Base_ParseContent{
    public static function parse($Content){
        if(empty($Content))exit;
        $w_controller = Wechat_Base_Controller::getInstance();
        $userWechatReplyM = new Wechat_Model_Reply('wechat');
        
        //准确查找
        $replies = $userWechatReplyM->_db->fetchRow('select * from '.$userWechatReplyM->_prefix.$userWechatReplyM->_table_name.'
            where wc_id = "'.$w_controller->_wechat['id'].'"
             and keyword = "'.$Content.'" and reply_used = "1" order by add_time desc');
        if($replies){
            Wechat_Base_ParseReply::parse($replies);
            exit;
        }

        //模糊查找
        $sql = 'select * from '.$userWechatReplyM->_prefix.$userWechatReplyM->_table_name.'
            where wc_id = "'.$w_controller->_wechat['id'].'"
             and "'.$Content.'" like Replace(`keyword`,"*","%")
             and reply_used = "1" order by add_time asc';

        $replies = $userWechatReplyM->_db->fetchAll($sql);
        if($replies){
            $res = array();
            foreach($replies as $k=>$v){
                $v['keyword'] = str_replace(array('*'),array(''),$v['keyword']);
                $res[count($v['keyword'])] = $v;
            }
            krsort($res);
            $res = reset($res);
            Wechat_Base_ParseReply::parse($res);
            exit;
        }
        if($w_controller->_select_priority == '2'){//无回答时查找外部接口
            Wechat_Base_SecondInterface::ping();
        }
        $unknowReply = new Wechat_Base_UnknowReply();
        $unknowReply->reply();//文本消息无回答帮助语
        exit;
    }

   public static function sqlEncode($col_name){
        if(!is_string($col_name)){
            return $col_name;
        }
        if(empty($col_name))return '';
        return "REPLACE(REPLACE(REPLACE(`{$col_name}`,'[','[[]'),'_','[_]'),'%','[%]')";
    }


}