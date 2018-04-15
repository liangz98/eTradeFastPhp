<?php
/**
 * 位置消息
 */
class Wechat_Run_Location{
    const EARTH_RADIUS = 6378.137;
    protected $_wechat_controller = null;

    function  __construct() {
        if(null === $this->_wechat_controller){
            $this->_wechat_controller = Wechat_Base_Controller::getInstance();
        }
    }
    function run(){
            $Location_X =  $this->_wechat_controller->_wechat_base_params->Location_X;
            $Location_Y = $this->_wechat_controller->_wechat_base_params->Location_Y;
            $Scale = $this->_wechat_controller->_wechat_base_params->Scale;
            $Label = strip_tags($this->_wechat_controller->_wechat_base_params->Label);

            $view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;

            $arr = array();
            $arr['location_x'] = $Location_X;
            $arr['location_y'] = $Location_Y;
            $arr['scale'] = $Scale;
            $arr['label'] = $Label;
            
            $latitude = $Location_X;
            $longitude = $Location_Y;
            
//            $latitude = $Location_Y;
//            $longitude = $Location_X;
            
           //返回最近店铺
            $branchM = new Home_Model_Branch('home');
            $branch_count = $branchM->fetchRowsCount(array('is_actived = ?'=>'1','branch_name != ?'=>''));
            if($branch_count > 0){
                $geohashC = new Wechat_Geohash();
//                $geohash = $geohashC->encode($Location_X, $Location_Y);
                $branches_arr = array();
//                if($geohash){
                if(1){
//                    for($i= 10;$i > 0;--$i){
//                        $branches = $branchM->fetchRows(null,array('is_actived = ?'=>'1','branch_name != ?'=>'','geohash like CONCAT(?,"%")'=>substr($geohash, 0, $i)),array('geohash asc'));
//                        if($branches){
//                            foreach($branches as $bra){
//                                if(!isset($branches_arr[$bra['branch_id']])){
//                                    $branches_arr[$bra['branch_id']] = $bra;
//                                }
//                            }
//                        }
//                        if(count($branches_arr) > 7 || count($branches_arr) == $branch_count)break;
//                    }
                    $order_by = array('distance ASC','order_by ASC');
                    $field = array('*');
                    
                    $distance = 500;
                    $dlng =  2 * asin(sin($distance / (2 * self::EARTH_RADIUS)) / cos(deg2rad($latitude)));
                    $dlng = rad2deg($dlng);
                    $dlat = $distance/self::EARTH_RADIUS;
                    $dlat = rad2deg($dlat);
                    $squares = array(
                        'left-top'=>array('lat'=>$latitude + $dlat,'lng'=>$longitude-$dlng),
                        'right-top'=>array('lat'=>$latitude + $dlat, 'lng'=>$longitude + $dlng),
                        'left-bottom'=>array('lat'=>$latitude - $dlat, 'lng'=>$longitude - $dlng),
                        'right-bottom'=>array('lat'=>$latitude - $dlat, 'lng'=>$longitude + $dlng)
                    );
                    
                    $condition['is_actived = ?'] = '1';
                    $condition['branch_name != ?'] = '';
//                    $condition["location_x  > {$squares['right-bottom']['lat']}"] = NULL;
//                    $condition["location_x  < {$squares['left-top']['lat']}"] = NULL;
//                    $condition["location_y > {$squares['left-top']['lng']}"] = NULL;
//                    $condition["location_y < {$squares['right-bottom']['lng']}"] = NULL;
                    $field[] = "2*".self::EARTH_RADIUS."*ASIN(SQRT(POW(SIN(PI()*({$latitude}-location_y)/360),2)+COS(PI()*{$latitude}/180)"
                     ."*COS(location_y*PI()/180)*POW(SIN(PI()*({$longitude}-location_x)/360),2))) AS distance";
                    
                    $branches_arr = $branchM->fetchRows(array(0,9), $condition, $order_by,$field);
                    
//                    $aaa = '';
//                    foreach ($branches_arr as $key => $value) {
//                        $aaa .= $value['distance'].''.$value['branch_name'].'  :: ';
//                    }
//                    $responseM = new Wechat_Base_Response();
//                    $responseM->responseTextMsg($aaa);
                    
                    $news = array();
                    $i = 0;
                    foreach($branches_arr as $bra_k=>$bra_v){
                        $title_str = '';
                        $branch_adress = '';
                        if($bra_v['branch_address']){
                            $branch_adress = '
地址：'.$bra_v['branch_address'];
                        }
                        $branch_telephone = '';
                        if($bra_v['branch_telephone']){
                            $branch_telephone = '
电话：'.$bra_v['branch_telephone'];
                        }
                        $title_str .= $bra_v['branch_name'].$branch_adress.$branch_telephone;
                        $news[] = array('title'=>$title_str,
                                        'description'=>$bra_v['branch_desc'],
                                        'picurl'=>$bra_v['branch_m_image'] ? $bra_v['branch_m_image'] : ($bra_v['branch_image'] ? $bra_v['branch_image'] : ''),
                                        'url'=>$view->seed_Setting['wechat_app_server'].'/vhome/map?x='.$Location_X.'&y='.$Location_Y.'&bid='.$bra_v['branch_id']);
                        ++$i;
//                        if($i > 7)break;
                     }
                     if(!empty($news)){
                        $branchParamM = new Home_Model_BranchParam('home');
                        $bp = $branchParamM->fetchRow(array('bp_id'=>1));
                        if($bp && isset($bp['title']) && $bp['title']){//添加位置标题
                            array_unshift($news, array('title'=>$bp['title'],
                                        'description'=>'',
                                        'picurl'=>$bp['image'] ? $view->WechatShowImage($bp['image']) : '',
                                        'url'=>isset($bp['url'])?$bp['url']:''));
                        }
                        $record_title = '[图文]';
                        foreach($news as $k=>$v){
                             $record_title .= ($record_title ? "\r\n" : "").$v['title'];
                        }
                        Wechat_Base_RecordReply::record($record_title);
                        $response = new Wechat_Base_Response();
                        $response->responseNewsMsg($news);
                     }
                }
            }
            Wechat_Base_RecordReply::record('你的地址为：'.$Label.'。');
            $response = new Wechat_Base_Response();
            $response->responseTextMsg('你的地址为：'.$Label.'。');
            exit;
    }
}
