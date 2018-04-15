<?php
class Wechat_GpsConvert{
    /**
     *百度坐标转换
     * @param <type> $Location_x 经度
     * @param <type> $location_y 纬度
     * @param <type> $from
     * @param <type> $to
     * $form,$to 0:wgs84坐标(gps坐标)，2：google坐标等，4：百度坐标
     */
    function convert($Location_x,$location_y,$from = 2,$to = 4){
        $filter_Lat = new Seed_Filter_Latitude();
        $filter_Lng = new Seed_Filter_Longitude();
        $Location_x = $filter_Lng->filter($Location_x);
        $location_y = $filter_Lat->filter($location_y);
        $coord = array('x'=>'','y'=>'');
        if(empty($Location_x) || empty($location_y))return $coord;
        //百度坐标系转换api
        $url = 'http://api.map.baidu.com/ag/coord/convert?from='.intval($from).'&to='.intval($to).'&x='.$Location_x.'&y='.$location_y.'&rnd='.time();
        $res = @file_get_contents($url);
        $res = @json_decode($res);
        if(isset($res->x) && isset($res->y)){
           $coord['x'] = base64_decode($res->x);
           $coord['y'] = base64_decode($res->y);
        }
        return $coord;
    }
}