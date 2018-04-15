<?php

class Kyapi_Model_KyDB
{
    /**
     * @var string 接口地址
     */
    private $_url = NULL;
    /**
     * @var string 映射JAVA对象
     */
    private $_option = NULL;

    /**
     * @var result 句柄
     */
    private $_handle = NULL;
    /**
     * @var array 存放单例模式数组
     */
    private static $_objects = array();
    /**
     * 设置URL地址
     * 实例化HessianClient类
     * 参数    : (1) url地址 , 2java映射对象
     *
     * 2.Java调用字段
     * @param string $url
     */
    public function __construct($url,$option)
    {
        $this->setUrl( $url );
        $this->setOptions( $option );
        $handler = new HessianClient ( $this->getUrl (), $this->getOptions () );
        $this->setHandler ( $handler );
    }
    /**
     * @return result $_handle 句柄
     */
    public function getHandler() {
        return $this->_handle;

    }
    /**
     * 设置句柄
     * @param result $_handle
     */
    public function setHandler($_handle) {
        $this->_handle = $_handle;
    }
    /**
     * 获取URL地址
     */
    public function getUrl() {
        return $this->_url;
    }
    /**
     * 设置URL地址
     * @param string $url
     */
    public function setUrl($url) {
        $this->_url = $url;
    }
    /**
     * typeMap映射Java等平台对象
     * @return array
     */
    public function getOptions() {
        return $this->opset;
//        return array (
//            'typeMap'=>array(
//                'requestObject'=>'com.jtec.etrade.api.dto.RequestObject',
//                'contact'=>'com.jtec.jump.contact.dto.Contact',
//                'queryParams'=>'java.util.Map',
//                'account'=>'com.jtec.jump.account.dto.Account',
//                'accountPk'=>'com.jtec.jump.account.dto.AccountPk',
//               // 'bankAccout'=>'com.jtec.jump.financial.common.dto.BankAccount',
//                //'product'=>'com.jtec.jump.product.dto.Product'
//            )
//        );
    }
    public function setOptions($option) {
        $this->opset = $option;
    }
    /**
     * 记录接口调用信息
     * @param string $method 调用的方法
     * @param string $returnMsg 需要记入log的文字信息
     */
 /*   public function resultLog( $method , $returnMsg )
    {
        $logPath = SEED_TEMP_ROOT.'/';
        if( !is_dir( $logPath ) ) {
            mkdir($logPath,0777);
        }
        error_log(date('Ymd H:i:s', time()) . '|' . $method . '|' . $returnMsg."\n", 3, $logPath .'hessianAPI' . date('Ymd', time()) . '.log');
    }*/
    /**
     * 静态工厂方法，生成单个URL的唯一实例
     * @param string $url
     */
    public static function start( $url )
    {
        $key = md5( $url );

        if ( isset(self::$_objects[$key]) ) {
            return self::$_objects[$key];
        }

        self::$_objects[$key] = new HessianApi( $url );
        return self::$_objects[$key];
    }
}
?>