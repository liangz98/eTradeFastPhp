<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/5/31
 * Time: 16:56
 */
class queryPartner
{
    public $partnerStatus;
}
class queryProduct
{
    public $productStatus;
}
class queryProductPur
{
    public $productStatus;
    public $supplierID;

}
class queryAccount
{
    //联系人列表查询条件
   // public $accountID;
    public $contactStatus;
}
class Attachment
{
    public $name;
    public $size;
    public $attachType;
    public $bizType;
    public $attachID;
}
class querySorts
{
    public $createTime;
}

class Kyapi_Controller_Action extends Zend_Controller_Action{
    public $lang;
    public $userID;
    public $sessionID;
    public $json;

    public function init() {
        $this->checkSystem();
        $this->initView();
        $this->initSetting();
        $this->initUser();
        $this->view->seed_BaseUrl = $this->_request->getBaseUrl();
        $this->view->cur_pos = $this->_request->getParam('controller');
        $this->view->addHelperPath(SEED_LIB_ROOT . '/Shop/View/Helper');
        $this->initHelperSetting();
        $this->initCommon();//运行公用的一些代码

        $kyUrl = $this->view->seed_Setting['KyUrl'];
        if (empty($kyUrl) || $kyUrl == 'NULL') {
            $kyUrl = 'https://123.207.120.251:8099';
        }
        // $this->json = new Kyapi_Controller_Json($this->view->seed_Setting['KyUrl']);
        $this->json = new Kyapi_Controller_Json($kyUrl);

        //语言判断
        $_lang=$_SESSION['rev_session']['contactPreference']['langCode'];
        if($_lang=='en_US'){
            $lan='en-us';
        }elseif($_lang=='zh_CN'){
            $lan='zh-cn';
        }else{
            $lan='zh-cn';
//            $lan = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,5);
//            $lan = strtolower($lan);
        }

        $languagesPath='/languages/'.$lan.'.php';
        $locale=$this->getRequest()->getParam('locale');
        //语言选择模块
//        if($locale=='zh-CN'){
//            $datapath='/languages/zh-CN.php';
//        }elseif($locale=='en-US'){
//            $datapath='/languages/en-US.php';
//        }else{
//            $datapath='/languages/zh-CN.php';
//        }
        //加载注册语言模块
        try {
            $adapter = new Zend_Translate('array', (SEED_WWW_ROOT) . $languagesPath, $locale);
        } catch (Zend_Translate_Exception $e) {

        }
        Zend_Registry::set('Zend_Translate', $adapter);

//        // 读取国家地址字段
//        $mod_name='COUNTRY_CODE';
//        $fileM = new Seed_Model_Cache2File();
//        $cachefile = $mod_name . "_setting";
//        $setting = $fileM->get($cachefile);
//
//        if (is_array($setting)){
//            $this->view->COUNTRY=$setting['COUNTRY'];
//            $this->view->PROVINCE=$setting['PROVINCE'];
//            $this->view->CITY=$setting['CITY'];
//        }
    }

    public function IsAuth($e){
        if($e){

            //销毁绑定sessionID的key
            $config=array();
            $config['server'] = $this->view->seed_Setting['KyUrlRedis'];
            $config['port'] = $this->view->seed_Setting['KyUrlRedisPort'];
            $redis=new Kyapi_Model_redisInit();
            $redis->connect($config);
            $redis->delete('PHPREDIS_ACTIVE_USERS:'.$this->view->userID);
            $redis->delete('PHPREDIS_ACTIVE_SESSION:'.session_id());

            //销毁session
            session_unset(session_id());
            session_destroy();
            //$_SESSION=array();
            //请先登录系统
            Mobile_Browser::redirect($this->view->translate('tip_login_please'),$this->view->seed_Setting['user_app_server']."/login");
        }

    }
//
//
//    public function DateTo($e){
//        //时间处理方法start
//        if(!empty($e)){
//            $dateF=	date("Y-m-d H:i:s",strtotime($e));
//        } else{
//            $dateF=date("Y-m-d H:i:s",time());}
//        $dateM=new datetime($dateF);
//        return $dateM;
//        //时间处理方法end
//    }


    //对象转数组
    public function objectToArray($e){
        $e=(array)$e;
        foreach($e as $k=>$v){
            if( gettype($v)=='resource' ) return;
            if( gettype($v)=='object' || gettype($v)=='array' )
                $e[$k]=(array)$this->objectToArray($v);
        }
        return $e;
    }

    //数组转对象
    public function arrayToObject($e){

        if( gettype($e)!='array' )
            return;
        foreach($e as $k=>$v){
            if( gettype($v)=='array' || getType($v)=='object' )
                $e[$k]=(object)arrayToObject($v);
        }
        return (object)$e;
    }

//    public function array2ToObject($e){
//        if( gettype($e)!='array' ) return;
//        foreach($e as $key=>$value){
//            foreach($value as $k=>$v){
//                if( gettype($v)=='array' || getType($v)=='object' )
//                    $e[$k]=(object)arrayToObject($v);
//            }
//        }
//        return (object)$e;
//    }

//    public function item2object($array) {
//        if (is_array($array)) {
//            $obj = new Kyapi_Model_OrderItem();
//            foreach ($array as $key => $val){
//                $obj->$key = $val;
//            }
//        }
//        else { $obj = $array; }
//        return $obj;
//    }

//    public function itemFJobject($array) {
//        if (is_array($array)) {
//            $obj = new Kyapi_Model_Attachment();
//            foreach ($array as $key => $val){
//                $obj->$key = $val;
//            }
//        }
//        else { $obj = $array; }
//        return $obj;
//    }

    //多维转单一数组
    //多维数组转换方法
//    public function arr_foreach($arr)
//    {
//        static $tmp = array();
//        if (!is_array($arr)) {
//            return false;
//        }
//        foreach ($arr as $k => $val) {
//            if (is_array($val)) {
//                $this->arr_foreach($val);
//            } else {
//                $tmp[$k] = $val;
//            }
//        }
//        return $tmp;
//    }
    //取出地区、国家、省市数据
//    public function arr_one($arr)
//    {
//        static $tmp = array();
//        if (!is_array($arr)) {
//            return false;
//        }
//        foreach ($arr as $k => $val) {
//
//            $tmp[$k] = $val['nameText'];
//        }
//        return $tmp;
//    }




    /**
     * 检查系统完整性
     * @throws Exception
     */
    protected function checkSystem()
    {
        if(!defined('CURRENT_MODULE_NAME') || !defined('CURRENT_MODULE_TYPE'))throw new Exception("CURRENT_MODULE_NAME or CURRENT_MODULE_TYPE not defined");
    }

    /**
     * 初始化用户信息
     * @throws Exception
     */
    protected function initUser()
    {
      //  print_r($_SESSION['rev_session']);
        if( !empty($_SESSION['rev_session']) ){
            //创建视图变量 传递用户信息
            $this->view->userID = $userID = $_SESSION['rev_session']['userID'];
            $this->view->accountID = $accountID = $_SESSION['rev_session']['accountID'];
            $this->view->accountName = $accountName = $_SESSION['rev_session']['accountName'];
            $this->view->accountStatus = $accountName = $_SESSION['rev_session']['accountStatus'];
            $this->view->crnCode = $accountName = $_SESSION['rev_session']['crnCode'];
            $this->view->userLoginName = $userLoginName = $_SESSION['rev_session']['userName'];
            $this->view->isPersonAccount = $userLoginName = $_SESSION['rev_session']['isPersonAccount'];
            $this->view->ecommloginname = $ecommloginname = $_SESSION['rev_session']['userLoginName'];
            $this->view->userRole = $userRole = $_SESSION['rev_session']['ecommrole'];
            $this->view->submenus = $_SESSION['rev_session']['menus'];//会员中心左侧菜单
            $submenus = $_SESSION['rev_session']['role_nav'];
            $this->view->newUrl = $_SERVER['REQUEST_URI']; //当前 URL 决定菜单高亮
            $this->view->userLangCode = $userLangCode = $_SESSION['rev_session']['contactPreference']['langCode'];
            $this->view->userrgCode = $userrgCode = $_SESSION['rev_session']['contactPreference']['regdCountryCode'];

            //判断登录状态是否authorder  免登陆，visitor==1 免登陆状态
            if(!empty($_SESSION['rev_session']['visitor'])){$this->view->visitor=$_SESSION['rev_session']['visitor'];}

            $this->view->CompUser=strstr($userRole,'CompUser')?true:false;//普通用户
            $this->view->CompAdmin=strstr($userRole,'CompAdmin')?true:false;//公司管理员
            $this->view->SOAdmin=strstr($userRole,'SOAdmin')?true:false;//订舱单管理员
            $this->view->TOAdmin=strstr($userRole,'TOAdmin')?true:false;//派车单管理员
            $this->view->DeclarationAdmin=strstr($userRole,'DeclarationAdmin')?true:false;//报关单管理员
            $this->view->CompPublicInfoAdmin=strstr($userRole,'CompPublicInfoAdmin')?true:false;//公司公告信息管理员
            $this->view->CompSettleAdmin=strstr($userRole,'CompSettleAdmin')?true:false;//结算中心管理员
            $this->view->CompOrderAdmin=strstr($userRole,'CompOrderAdmin')?true:false;//订单管理员
            $this->view->CompClerk=strstr($userRole,'CompClerk')?true:false;//跟单员
            $this->view->CompProductAdmin=strstr($userRole,'CompProductAdmin')?true:false;//商品管理员
            $this->view->CompPartnerAdmin=strstr($userRole,'CompPartnerAdmin')?true:false;//合作伙伴管理员

            //设置控制器视图的查看、新增访问权限
            $this->view->account_isAllowView = Seed_Common::array_search_two($submenus,'link_url','account/view');
            $this->view->account_isAllowAdd = Seed_Common::array_search_two($submenus,'link_url','account/add');
            $this->view->bank_isAllowView = Seed_Common::array_search_two($submenus,'link_url','bank/view');
            $this->view->bank_isAllowAdd = Seed_Common::array_search_two($submenus,'link_url','bank/add');
            $this->view->buyer_isAllowView = Seed_Common::array_search_two($submenus,'link_url','buyer/view');
            $this->view->buyer_isAllowAdd = Seed_Common::array_search_two($submenus,'link_url','buyer/add');
            $this->view->vendor_isAllowView = Seed_Common::array_search_two($submenus,'link_url','vendor/view');
            $this->view->vendor_isAllowAdd = Seed_Common::array_search_two($submenus,'link_url','vendor/add');
            $this->view->goods_isAllowView = Seed_Common::array_search_two($submenus,'link_url','goods/view');
            $this->view->goods_isAllowAdd = Seed_Common::array_search_two($submenus,'link_url','goods/add');
            $this->view->purchase_isAllowView = Seed_Common::array_search_two($submenus,'link_url','purchase/view');
            $this->view->purchase_isAllowAdd = Seed_Common::array_search_two($submenus,'link_url','purchase/add');
            $this->view->ordercg_isAllowView = Seed_Common::array_search_two($submenus,'link_url','ordercg/view');
            $this->view->ordercg_isAllowAdd = Seed_Common::array_search_two($submenus,'link_url','ordercg/add');
            $this->view->orderxs_isAllowView = Seed_Common::array_search_two($submenus,'link_url','orderxs/view');
            $this->view->orderxs_isAllowAdd = Seed_Common::array_search_two($submenus,'link_url','orderxs/add');

        }





        // requestObject 入参条件
        $_requestObject = new Kyapi_Model_requestObject();
        // if(!empty($_SESSION) ){
        //     $_requestObject->sessionID = session_id();
        // }else{
        //     $_requestObject->sessionID = "sessionNull";
        // }

        // 获取sessionID
        $_requestObject->sessionID = session_id();

        $_requestObject->userID = $userID = $this->view->userID;
        $_requestObject->accountID = $accountID = $this->view->accountID;
        if (empty($userLangCode))
            $userLangCode = "zh_CN";
        $this->view->userLangCode = $userLangCode;
        $_requestObject->lang = $userLangCode;
        // $_requestObject->client = "192.168.5.100";
        $_requestObject->client = $_SERVER['REMOTE_ADDR'];
        $_requestObject->timeZone = "GMT +8:00";
        $this->_requestObject = $_requestObject;
        $this->view->reqOb = $_requestObject;
    }

    /**
     * 运行公用的一些代码
     * 注意：如果在Common里添加新文件务必把Autoload.php删掉
     */
    protected function initCommon(){
        $common_dir = SEED_LIB_ROOT.DIRECTORY_SEPARATOR.'Common';
        if(is_dir($common_dir)){
            $Autoload = $common_dir.DIRECTORY_SEPARATOR.'Autoload.php';
            if(file_exists($Autoload)){
                $load_files = include_once $Autoload;
                foreach($load_files as $load_file){
                    include_once SEED_LIB_ROOT.$load_file;
                }
            }else{//没有Autoload.php文件则生成
                $files = Seed_Folder::read($common_dir,1);
                $include_files = array();
                foreach($files as $file){
                    if($file != 'Autoload.php' && substr($file,strrpos($file,'.')) == '.php'){
                        $include_files[$file] = $common_dir.DIRECTORY_SEPARATOR.$file;
                    }
                }
                if($include_files){
                    $data_files = array();
                    natsort($include_files);//自然排序
                    foreach ($include_files as $key=>$include_file) {
                        include_once $include_file;
                        $data_files[$key] = str_replace(SEED_LIB_ROOT, '', $include_file);
                    }
                    if($fp = @fopen($Autoload,"w")){//生成文件
                        flock($fp,LOCK_EX);
                        fwrite($fp, "<?php\r\nreturn ".var_export($data_files, true).';');
                        flock($fp,LOCK_UN);
                        fclose($fp);
                        chmod($Autoload,0666);
                    }
                }
            }
        }
    }


    protected function initSetting()
    {
        // 获取系统设置
        $fileM = new Seed_Model_Cache2File();
        $mod_name = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;
        if(defined('SEED_HOST_NAME')){
            $seed_host_name = str_replace(".","_",SEED_HOST_NAME);
            $cachefile = $mod_name."_".strtolower($seed_host_name)."_setting";
        }else{
            $cachefile = $mod_name."_setting";
        }

        $setting = $fileM->get($cachefile);
        if(isset($setting['mobile_send_ecode']) && isset($setting['mobile_send_username']) && isset($setting['mobile_send_password'])){
            if(!defined('MOBILE_SEND_ECODE'))define('MOBILE_SEND_ECODE',$setting['mobile_send_ecode']);
            if(!defined('MOBILE_SEND_USERNAME'))define('MOBILE_SEND_USERNAME',$setting['mobile_send_username']);
            if(!defined('MOBILE_SEND_PASSWORD'))define('MOBILE_SEND_PASSWORD',$setting['mobile_send_password']);
        }
        if(!isset($setting['website_domain']))$setting['website_domain']="/";
        if(!isset($setting['upload_app_server']))$setting['upload_app_server']="/uploadapi";
        if(!isset($setting['upload_view_server']))$setting['upload_view_server']="/upload_files";
        if(!isset($setting['upload_return_server']))$setting['upload_return_server']="";
        if(!isset($setting['www_app_server']))$setting['www_app_server']="/";
        if(!isset($setting['v_app_server']))$setting['v_app_server']="/";
        if(!isset($setting['union_app_server']))$setting['union_app_server']="/";
        if(!isset($setting['wechat_app_server']))$setting['wechat_app_server']="/";
        if(!isset($setting['vuser_app_server']))$setting['vuser_app_server']="/vuser";
        if(!isset($setting['user_app_server']))$setting['user_app_server']="/user";
        if(!isset($setting['vmall_app_server']))$setting['vmall_app_server']="/vmall";
        if(!isset($setting['vhome_app_server']))$setting['vhome_app_server']="/vhome";
        if(!isset($setting['vservice_app_server']))$setting['vservice_app_server']="/vservice";
        if(!isset($setting['vmarketing_app_server']))$setting['vmarketing_app_server']="/vmarketing";
        if(!isset($setting['vplug_app_server']))$setting['vplug_app_server']="/vplug";
        if(!isset($setting['mobunion_app_server']))$setting['mobunion_app_server']="/mobunion";
        if(!isset($setting['agent_app_server']))$setting['agent_app_server']="/agent";
        if(!isset($setting['static_app_server']))$setting['static_app_server']="/static";
        if(!isset($setting['KyUrl']))$setting['KyUrl']="NULL";
        if(!isset($setting['KyUrlRedis']))$setting['KyUrlRedis']="NULL";
        if(!isset($setting['KyUrlRedisPort']))$setting['KyUrlRedisPort']="NULL";
        if(!isset($setting['PRODUCT']))$setting['PRODUCT']="NULL";
        //初始化数据字典变量
        $setting1 = $fileM->get("dictionary_admin_setting");
        if(!isset($setting1['SYS_LANG']))$setting1['SYS_LANG']="NULL";
        if(!isset($setting1['TIME_ZONE']))$setting1['TIME_ZONE']="NULL";
        if(!isset($setting1['SEX']))$setting1['SEX']="NULL";
        if(!isset($setting1['PARTNER_STATUS']))$setting1['PARTNER_STATUS']="NULL";
        if(!isset($setting1['BANK_ACCOUNT_TYPE']))$setting1['BANK_ACCOUNT_TYPE']="NULL";
        if(!isset($setting1['CURRENCY']))$setting1['CURRENCY']="NULL";
        if(!isset($setting1['ECOMM_STATUS']))$setting1['ECOMM_STATUS']="NULL";
        if(!isset($setting1['COMMON_RECORD_STATUS']))$setting1['COMMON_RECORD_STATUS']="NULL";
        if(!isset($setting1['ECOMM_ROLE']))$setting1['ECOMM_ROLE']="NULL";
        if(!isset($setting1['ACCOUNT_LEGALFORM']))$setting1['ACCOUNT_LEGALFORM']="NULL";
        if(!isset($setting1['PRODUCT_PRICING_UNIT']))$setting1['PRODUCT_PRICING_UNIT']="NULL";
        if(!isset($setting1['PRODUCT_PACKING_TYPE']))$setting1['PRODUCT_PACKING_TYPE']="NULL";
        if(!isset($setting1['PRODUCT_PRODUCTION_MODE']))$setting1['PRODUCT_PRODUCTION_MODE']="NULL";
        if(!isset($setting1['ACTION_LOG_EVENT_TYPE']))$setting1['ACTION_LOG_EVENT_TYPE']="NULL";
        if(!isset($setting1['PAYMENT_TERM']))$setting1['PAYMENT_TERM']="NULL";
        if(!isset($setting1['PAYMENT_PERIOD']))$setting1['PAYMENT_PERIOD']="NULL";
        if(!isset($setting1['ORDER_QUOTATION_MODE']))$setting1['ORDER_QUOTATION_MODE']="NULL";
        if(!isset($setting1['PRICE_TERM']))$setting1['PRICE_TERM']="NULL";
        if(!isset($setting1['ORDER_PACKING_MODE']))$setting1['ORDER_PACKING_MODE']="NULL";
        if(!isset($setting1['packingDesc']))$setting1['packingDesc']="NULL";
        if(!isset($setting1['SHIPPING_METHOD']))$setting1['SHIPPING_METHOD']="NULL";
        if(!isset($setting1['SHIPPING_SERVICE_TYPE']))$setting1['SHIPPING_SERVICE_TYPE']="NULL";
        if(!isset($setting1['ORDER_VENDOR_EXECUTION_STATUE']))$setting1['ORDER_VENDOR_EXECUTION_STATUE']="NULL";
        if(!isset($setting1['ORDER_BUYER_EXECUTION_STATUE']))$setting1['ORDER_BUYER_EXECUTION_STATUE']="NULL";
        if(!isset($setting1['BIZ_TYPE']))$setting1['BIZ_TYPE']="NULL";
        if(!isset($setting1['ISIC_INDUSTRY_TYPE']))$setting1['ISIC_INDUSTRY_TYPE']="NULL";
        if(!isset($setting1['EXPORT_POINTS']))$setting1['EXPORT_POINTS']="NULL";
        if(!isset($setting1['AIR_PORT']))$setting1['AIR_PORT']="NULL";
        if(!isset($setting1['SEA_PORT']))$setting1['SEA_PORT']="NULL";
        if(!isset($setting1['COUNTRY_ISO_CODE']))$setting1['COUNTRY_ISO_CODE']="NULL";
        if(!isset($setting1['PROVINCE_CODE']))$setting1['PROVINCE_CODE']="NULL";
        if(!isset($setting1['CITY_ISO_CODE']))$setting1['CITY_ISO_CODE']="NULL";
        if(!isset($setting1['AYMENT_ACCOUNT_STATUS']))$setting1['AYMENT_ACCOUNT_STATUS']="NULL";
        if(!isset($setting1['PAYMENT_BAL_TYPE']))$setting1['PAYMENT_BAL_TYPE']="NULL";
        if(!isset($setting1['AR_AP_TYPE']))$setting1['AR_AP_TYPE']="NULL";
        if(!isset($setting1['PAYMENT_CUSTOMER_TYPE']))$setting1['PAYMENT_CUSTOMER_TYPE']="NULL";
        if(!isset($setting1['PAYMENT_ADVANCE_TYPE']))$setting1['PAYMENT_ADVANCE_TYPE']="NULL";
        if(!isset($setting1['PAYMENT_TRADING_STATUS']))$setting1['PAYMENT_TRADING_STATUS']="NULL";
        if(!isset($setting1['PAYMENT_STATUS']))$setting1['PAYMENT_STATUS']="NULL";
        if(!isset($setting1['ACCRUED_ITEM_TYPE']))$setting1['ACCRUED_ITEM_TYPE']="NULL";
        if(!isset($setting1['PAYMENT_TRADING_TYPE']))$setting1['PAYMENT_TRADING_TYPE']="NULL";
        if(!isset($setting1['INVOICE_TYPE']))$setting1['INVOICE_TYPE']="NULL";
        if(!isset($setting1['PAYMENT_REQUEST_STATUS']))$setting1['PAYMENT_REQUEST_STATUS']="NULL";
        if(!isset($setting1['PAYMENT_FLOW_TYPE']))$setting1['PAYMENT_FLOW_TYPE']="NULL";
        if(!isset($setting1['DR_CR_TYPE']))$setting1['DR_CR_TYPE']="NULL";
        if(!isset($setting1['PAYMENT_TYPE']))$setting1['PAYMENT_TYPE']="NULL";
        if(!isset($setting1['RECORD_STATUS']))$setting1['RECORD_STATUS']="NULL";
        if(!isset($setting1['ORDER_STATUS']))$setting1['ORDER_STATUS']="NULL";
        if(!isset($setting1['TRUCKING_ORDER_STATUS']))$setting1['TRUCKING_ORDER_STATUS']="NULL";
        if(!isset($setting1['TRUCKING_SERVICE_TYPE']))$setting1['TRUCKING_SERVICE_TYPE']="NULL";


        //初始化 附件业务类型变量
        $setting2 = $fileM->get("biztype_front_setting");
        if(!isset($setting2['ACCOUNT']))$setting2['ACCOUNT']="NULL";//人员账号
        if(!isset($setting2['CONTACT']))$setting2['CONTACT']="NULL";//联系人管理
        if(!isset($setting2['CONTRACT']))$setting2['CONTRACT']="NULL";//合同
        if(!isset($setting2['ORDER']))$setting2['ORDER']="NULL";//订单
        if(!isset($setting2['ORDER_ITEM']))$setting2['ORDER_ITEM']="NULL";//订单商品
        if(!isset($setting2['PRODUCT']))$setting2['PRODUCT']="NULL";//商品
        if(!isset($setting2['CREDIT_INSURANCE']))$setting2['CREDIT_INSURANCE']="NULL";//信用保险
        if(!isset($setting2['CREDIT_INSURANCE_DECLARE']))$setting2['CREDIT_INSURANCE_DECLARE']="NULL";//信用保险申报
        if(!isset($setting2['DECLARATION']))$setting2['DECLARATION']="NULL";//报关单
        if(!isset($setting2['SHIPPING_ORDER']))$setting2['SHIPPING_ORDER']="NULL";//订舱单
        if(!isset($setting2['TRUCKING_ORDER']))$setting2['TRUCKING_ORDER']="NULL";//派车单
        if(!isset($setting2['PACKING_LIST']))$setting2['PACKING_LIST']="NULL";//装箱单
        if(!isset($setting2['VALUATION_LIST']))$setting2['VALUATION_LIST']="NULL"; //计价单
        if(!isset($setting2['INVOICE']))$setting2['INVOICE']="NULL"; //销售发票
        if(!isset($setting2['BANK_ACCOUNT']))$setting2['BANK_ACCOUNT']="NULL"; //银行账户

        //初始化附件attachtype 类型
        $setting3 = $fileM->get("attachtype_front_setting");
        if(!isset($setting3['0000']))$setting3['0000']="NULL"; //缺省值
        if(!isset($setting3['PDPD']))$setting3['PDPD']="NULL"; //商品
        if(!isset($setting3['ODTA']))$setting3['ODTA']="NULL"; //委托书
        if(!isset($setting3['ODSE']))$setting3['ODSE']="NULL"; //盖章委托书
        if(!isset($setting3['ODVQ']))$setting3['ODVQ']="NULL"; //卖家计价单
        if(!isset($setting3['ODBQ']))$setting3['ODBQ']="NULL"; //买家计价单
        if(!isset($setting3['ODQA']))$setting3['ODQA']="NULL"; //质保函
        if(!isset($setting3['ODQS']))$setting3['ODQS']="NULL"; //质保函盖章
        if(!isset($setting3['CRCT']))$setting3['CRCT']="NULL"; //合同范本
        if(!isset($setting3['CRSE']))$setting3['CRSE']="NULL"; //合同盖章
        if(!isset($setting3['ODPG']))$setting3['ODPG']="NULL"; //备货
        if(!isset($setting3['ODEG']))$setting3['ODEG']="NULL"; //验货
        if(!isset($setting3['ODRG']))$setting3['ODRG']="NULL"; //收货
        if(!isset($setting3['ODDG']))$setting3['ODDG']="NULL"; //发货


        $this->view->seed_Setting = $setting;
        $this->view->dic_Setting = $setting1;
        $this->view->biz_Setting = $setting2;
        $this->view->attach_Setting = $setting3;
        $this->view->controllerName = $this->getRequest()->getControllerName();
        $this->view->actionName = $this->getRequest()->getActionName();
        $this->view->seed_BaseUrl = $this->getRequest()->getBaseUrl();


        $seed_vhome_tpl = empty($_GET['tpl']) ? '' : trim($_GET['tpl']);
        if(!empty($seed_vhome_tpl)){
            define('SEED_MALL_TPL',$seed_vhome_tpl);
        }else{
            if(defined('SEED_HOST_NAME')){
                $seed_host_name = str_replace(".","_",SEED_HOST_NAME);
                $tplfile = SEED_CONF_ROOT."/".strtolower($seed_host_name)."_tpl.php";
                if(file_exists($tplfile)){
                    require_once($tplfile);
                }else{
                    define('SEED_MALL_TPL','default');
                }
            }else{
                $tplfile = SEED_CONF_ROOT."/tpl.php";
                if(file_exists($tplfile)){
                    require_once($tplfile);
                }else{
                    define('SEED_MALL_TPL','default');
                }
            }
        }
    }

    protected function initHelperSetting()
    {
        if(is_object($this->view) && empty($this->view->helper_Setting)){
            try {
                $config_file = SEED_CONF_ROOT . '/helper.xml';
                $setting = new Zend_Config_Xml ($config_file);
                $this->view->helper_Setting = $setting->toArray();
            }catch (Exception $e){
                throw $e;
            }
        }
    }

    /**
     * 获取请求的URI
     * @return Ambigous <NULL, unknown>
     */
    protected function getRequestUri()
    {
        $requestUri = null;
        if(isset($_SERVER['HTTP_X_REWRITE_URL']) && !empty($_SERVER['HTTP_X_REWRITE_URL'])){
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        }else{
            $requestUri = $_SERVER["REQUEST_URI"];
        }
        return $requestUri;
    }

    /**
     * 判断当前操作的用户是否已登录
     * @return boolean
     */
    protected function isLoggedIn()
    {
        if($this->view->seed_User['user_id']>0){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 判断用户行为是否存在，若存在则反正真，否则返回假
     * @param string $action_mark 行为标识
     * @param int $user_id 用户ID
     * @return boolean
     */
    protected function hasUserAction($action_mark, $user_id)
    {
        $userActionM = new Seed_Model_UserAction('system');
        $userAction = $userActionM->fetchRow(array('user_id'=>$user_id,'action_mark'=>$action_mark));
        if($userAction['action_id'] > 0)return true;
        return false;
    }

    /**
     * 添加用户行为
     * @param string $action_mark 行为标识
     * @param int $user_id 用户ID
     * @return boolean
     */
    protected function addUserAction($action_mark, $user_id)
    {
        $userActionM = new Seed_Model_UserAction('system');
        $userActionData = array();
        $userActionData['user_id'] = $user_id;
        $userActionData['action_mark'] = $action_mark;
        $userAction_id = $userActionM->insertRow($userActionData);
        if($userAction_id)return true;
        return false;
    }




}


