<?php
class DataController extends Kyapi_Controller_Action
{
    public function indexAction()
    {
        //开启静态配置文件
        $cacheM = new Seed_Model_Cache2File();
        //获取数据字典（不包括国家地区港口）
        $df_stt = $cacheM->get("dictionary_admin_setting");
        $dateArr=array();
        foreach ($df_stt as $k=>$v){
            $dateArr[]=$v;
        }

        //国家、地区、港口数据字典获取

        // 判断参数的值
        if( $dateArr !=null){
            $userKY= $this->json->findDataDictListApi($this->_requestObject,$dateArr);
            $Arrdata =$this->objectToArray(json_decode($userKY));


            //设置常用单位、货号、类型字典资源文件名称
            if(!empty($Arrdata)){
                $mod_name='abc';
                foreach ($Arrdata['result'] as $k=>$v){
                    $cacheM->save($mod_name."_".$k,$v);
                    echo ($v)?$k."write sucessful!".'<br>':$k."write failed!".'<br>';
                }
            }else{
                echo "TongYong write failed!".'<br>';
            }
        }
        exit;
    }

//    //查询国家本地资源文件
//    public function countryAction()
//    {
////        if ($this->_request->isPost()) {
//        $condition="COUNTRY_ISO_CODE";
//        $lang_code="zh_CN";
//        $cacheM = new Seed_Model_Cache2File();
//        $dic = $cacheM->get($condition);
////        echo "<pre>";
////        print_r($dic);
////        exit;
//        $str="";
//        foreach($dic as $key=>$value){
//            $setArr=$value['baseLangList'];
//            foreach($setArr as $k1 => $v1){
//
//                if (in_array($lang_code, $v1))
//                {
//                    if($v1['langCode']==$lang_code){
//                        //输出当前语言的name
//                        if(!empty($v1['nameText'])){
//                            echo "<option value=".$value['code'].">".$v1['nameText']."</option>";
//                        }else{
//                            if($v1['langCode']=="zh_CN"){
//                                //输出当前语言的name
//                                echo "<option value=".$value['code'].">".$v1['nameText']."</option>";
//                            }
//                        } }
//
//                }
//            }
//
//        }
//
//        exit;
//    }


    //查询港口、城市列表
    public function searchAction()
    {

        $lang_code="zh_CN";
        $condition=$this->_request->getParam('type');
        $keyword=$this->_request->getParam('keyword');
        $cacheM = new Seed_Model_Cache2File();
        $dic = $cacheM->get($condition);
        $str=array();
        foreach($dic as $key=>$value){
            $setArr=$value['baseLangList'];
            foreach($setArr as $k1 => $v1){
                if (in_array($lang_code, $v1))
                {
                    if($v1['langCode']==$lang_code){
                        //输出当前语言的name
                        $str[$key]['code']=$value['code'];
                        $str[$key]['name']=$v1['nameText'];
                        if(empty($str[$key]['name'])){
                            if($v1['langCode']=="zh_CN") $str[$key]['name']=$v1['nameText'];
                        }
                    }
                }
            }

        }

        $reqArr=array();
        foreach($str as $k1 => $map){
            foreach ($map as $k =>$v) {
                if (strpos($v, $keyword) !== false) {
                    $reqArr[$k1]['name']=$map['name'];
                    $reqArr[$k1]['code']=$map['code'];
                }
            }
        }

        echo json_encode($reqArr);
        exit;

    }
    //订单新增 编辑页面 用于查询港口、城市列表数据字典接口方法
    public function portAction()
    {

        $lang_code=$this->view->userLangCode;
//        $condition='SEA_PORT';
//        $keyword='2';
        $condition=$this->_request->getParam('type');
        $keyword=$this->_request->getParam('q');
        $cacheM = new Seed_Model_Cache2File();
        $dic = $cacheM->get($condition);
        $str=array();
        foreach($dic['result'] as $key=>$value){
            $setArr=$value['baseLangList'];
            foreach($setArr as $k1 => $v1){
                if (in_array($lang_code, $v1))
                {
                    if($v1['langCode']==$lang_code){
                        //输出当前语言的name
                        $str[$key]['code']=$value['code'];
                        $str[$key]['name']=$v1['nameText'];
                        if(empty($str[$key]['name'])){
                            if($v1['langCode']=="zh_CN") $str[$key]['name']=$v1['nameText'];
                        }
                    }
                }
            }

        }

        $reqArr=array();
        foreach($str as $k1 => $map){
            foreach ($map as $k =>$v) {
                if (strpos($v, $keyword) !== false) {
                    $reqArr[$k1]['name']=$map['name'];
                    $reqArr[$k1]['id']=$map['code'];
                }
            }
        }
        $qArr=array_values($reqArr);

        echo json_encode($qArr);
        exit;

    }

    public function dictoAction(){
        //此方法主要用于产品页面通过hscode 实现法定单位
        //$files  缓存文件目录
        //$lang_code   语言版本
        //$e  缓存路径-》数据字典代码
        //$t 查询key
        $lang_code=$this->view->userLangCode;
        $files=$this->_request->getParam('files');
        $e=$this->_request->getParam('e');
        $t=$this->_request->getParam('t');

        //测试数据
//        $files="datatest_setting";
//        $lang_code="en_US";
//        $e="ECOMM_ROLE";
//        $t="001";

        $cacheM = new Seed_Model_Cache2File();
        $datalist = $cacheM->get($files);
        $dall=$datalist['result'];
        if(!empty($dall[$e])){
            $dic=$dall[$e];
        }else{
            $dic=$dall;
        }

        $str=array();
        foreach($dic as $key=>$value){
            if($value['baseLangList']){
                $setArr=$value['baseLangList'];
                foreach($setArr as $k1 => $v1){

                        if($v1['langCode']==$lang_code){
                            //输出当前语言的name
                            $str[$key]['code']=$value['code'];
                            $str[$key]['name']=$v1['nameText'];
                            //设置缺省
                            if(empty($str[$key]['name'])){
                                if($v1['langCode']=="zh_CN") $str[$key]['name']=$v1['nameText'];
                            }
                        }
                }
            }else{
                $str=$dic;
            }
        }
        $kval="";

        foreach($str as $k=>$v){
            if(!empty($t)){
                if($t==$v['code']){
                    $kval=$v['name'];

                }
            }else{
                $kval=$t;
            }
        }
        echo json_encode($kval);
        exit;
    }

    //查询地区本地资源文件
    public function countryAction()
    {
        if ($this->_request->isPost()) {
            $cacheM = new Seed_Model_Cache2File();
            $dic = $cacheM->get("COUNTRY_ISO_CODE");
            $provinceID = $this->_request->getParam('type');
            $prov=array();
            foreach($dic as $k=>$v){
                if($v['parentCode']==$provinceID){

                    $prov[$k]=$v;
                }
            }
            $msg = json_encode($prov);
            echo $msg;
        }else{
            echo 'badRequest';
        }
        exit;
    }
    //查询地区本地资源文件
    public function provinceAction()
    {
        if ($this->_request->isPost()) {
            $cacheM = new Seed_Model_Cache2File();
            $dic = $cacheM->get("AP");
            $provinceID = $this->_request->getParam('provinceID');
            $prov=array();
            foreach($dic as $k=>$v){
                if($v['parentCode']==$provinceID){

                    $prov[$k]=$v;
                }
            }
            $msg = json_encode($prov);
            echo $msg;
        }else{
            echo 'badRequest';
        }
        exit;
    }
    //查询城市本地资源文件
    public function cityAction()
    {
        if ($this->_request->isPost()) {
            $cacheM = new Seed_Model_Cache2File();
            $dic = $cacheM->get("AT");
            //  $city = $this->_request->getParam('type');
            $cityID = $this->_request->getParam('cityID');
            $_city=array();
            foreach($dic as $k=>$v){
                if($v['parentCode']==$cityID){
                    $_city[$k]=$v;
                }
            }
            $msg = json_encode($_city);
            echo $msg;
        }else{
            echo 'badRequest';
        }
        exit;
    }

    //查询本地资源文件 单一字典结果
    public  function onedAction(){
        //$e  缓存路径-》数据字典代码
        //$lang_code   语言版本
        //$t 查询key'ACTION_LOG_EVENT_TYPE',$k,'zh_CN'
        $e='ACTION_LOG_EVENT_TYPE';
        $t='ORD';
        $lang_code='zh_CN';
        $cacheM = new Seed_Model_Cache2File();
        $dall = $cacheM->get('datatest_setting');
        $dic=$dall[$e];
        $str=array();
        foreach($dic as $key=>$value){
            $setArr=$value['baseLangList'];
            foreach($setArr as $k1 => $v1){
                if (in_array($lang_code, $v1))
                {
                    if($v1['langCode']==$lang_code){
                        //输出当前语言的name
                        $str[$key]['code']=$value['code'];
                        $str[$key]['name']=$v1['nameText'];
                        //设置缺省
                        if(empty($str[$key]['name'])){
                            if($v1['langCode']=="zh_CN") $str[$key]['name']=$v1['nameText'];
                        }
                    }
                }
            }
        }
        $kval="";
        foreach($str as $k=>$v){
            if($t==$v['code']){
                $kval=$v['name'];
            }
        }
        echo  $kval;
        exit;
    }
    //查询本地资源文件 单一字典结果
    public  function tolistAction(){
        //$e  缓存路径-》数据字典代码
        //$lang_code   语言版本
        //$t 查询key'ACTION_LOG_EVENT_TYPE',$k,'zh_CN'

        $e='PAYMENT_PERIOD';
        $t="0";
        $lang_code='zh_CN';
        $cacheM = new Seed_Model_Cache2File();
        $datalist = $cacheM->get('datatest_setting');
//        $dic=$dall[$e];
//        $str=array();

        //$files_name 缓存文件名称
        //$lang_code   语言版本
        //$e  数据字典代码
        //$t 查询key
//        $cacheM = new Seed_Model_Cache2File();
//        $datalist = $cacheM->get($files_name);
        $dall=$datalist['result'];
        if(!empty($dall[$e])){
            $dic=$dall[$e];
        }else{
            $dic=$dall;
        }

        $str=array();
        foreach($dic as $key=>$value){
            if($value['baseLangList']){
                $setArr=$value['baseLangList'];
                foreach($setArr as $k1 => $v1){
//                if (in_array($lang_code, $v1))
//                {
                    if($v1['langCode']==$lang_code){
                        //输出当前语言的name
                        $str[$key]['code']=$value['code'];
                        $str[$key]['name']=$v1['nameText'];
                        //设置缺省
                        if(empty($str[$key]['name'])){
                            if($v1['langCode']=="zh_CN") $str[$key]['name']=$v1['nameText'];
                        }
                    }
//                }
                }
            }else{
                $str=$dic;
            }
        }
        echo $t;
        echo "<pre>";
        var_dump($str);
        $dclist="";
        if(!empty($t)){
          foreach($str as $k=>$v){


                    echo $v['name'];


          }

        }
        //echo $dclist;

        exit;
    }

    public function showtestAction(){
        //$files  缓存文件目录
        //$lang_code   语言版本
        //$e  缓存路径-》数据字典代码
        //$t 查询key
        $files_name="abc_SHIPPING_SERVICE_TYPE";
        $lang_code='en_US';
        $e='ACCOUNT_LEGALFORM';
     //   $t="NV";
        $cacheM = new Seed_Model_Cache2File();
        $dic = $cacheM->get($files_name);

        echo '<pre>';
        var_dump($dic);

        $str=array();
        foreach($dic as $key=>$value){
            if($value['baseLangList']){
                $setArr=$value['baseLangList'];
                foreach($setArr as $k1 => $v1){
//                if (in_array($lang_code, $v1))
//                {
                    if($v1['langCode']==$lang_code){
                        //输出当前语言的name
                        $str[$key]['code']=$value['code'];
                        $str[$key]['name']=$v1['nameText'];
                        //设置缺省
                        if(empty($str[$key]['name'])){
                            if($v1['langCode']=="zh_CN") $str[$key]['name']=$v1['nameText'];
                        }
                    }
//                }
                }
            }else{
                $str=$dic;
            }
        }
        $dclist="";
        foreach($str as $k=>$v){
            if($t==$v['code']){
                $dclist.= '<option selected="selected" value='.$v['code'].'>'.$v['name'].'</option>';
            }else{
                $dclist.= '<option value='.$v['code'].'>'.$v['name'].'</option>';
            }

        }
        echo  $dclist;

       exit;
    }
    public function findNoAction(){
        //此方法主要用于产品页面通过hscode 实现法定单位
        //$files  缓存文件目录
        //$lang_code   语言版本
        //$e  缓存路径-》数据字典代码
        //$t 查询key
//        $lang_code=$this->view->userLangCode;
//        $files=$this->_request->getParam('files');
//        $e=$this->_request->getParam('e');
//        $t=$this->_request->getParam('t');

        //测试数据
        $files="datatest_setting";
        $lang_code="en_US";
//        $e="ECOMM_ROLE";
//        $t="001";

        $cacheM = new Seed_Model_Cache2File();
        $datalist = $cacheM->get($files);
        $dall=$datalist['result'];
        if(!empty($dall[$e])){
            $dic=$dall[$e];
        }else{
            $dic=$dall;
        }

        $str=array();
        foreach($dic as $key=>$value){
            if($value['baseLangList']){
                $setArr=$value['baseLangList'];
                foreach($setArr as $k1 => $v1){

                    if($v1['langCode']==$lang_code){
                        //输出当前语言的name
                        $str[$key]['code']=$value['code'];
                        $str[$key]['name']=$v1['nameText'];
                        //设置缺省
                        if(empty($str[$key]['name'])){
                            if($v1['langCode']=="zh_CN") $str[$key]['name']=$v1['nameText'];
                        }
                    }
                }
            }else{
                $str=$dic;
            }
        }
        $kval="";

        foreach($str as $k=>$v){
            if(!empty($t)){
                if($t==$v['code']){
                    $kval=$v['name'];

                }
            }else{
                $kval=$t;
            }
        }
        echo json_encode($kval);
        exit;
    }
}
