<?php
/**
 * 类名        : Goods
 * 继承类        : HessianApi
 * 用途        : 调用server.php方法
 *
 */
class Kyapi_Controller_Countsaleorder extends Kyapi_Model_Curl
{
    /**
     * requestObject请求资源
     *
     * @public
     */
    public $lang;
    public $userID;
    public $sessionID;

    /**
     * 设置接口地址
     * @param string $url
     */
    public function __construct( $url ) {
        parent::__construct($url);
    }
    /**销售订单列表统计接口**/
    public function countSaleOrderStatusApi( $_requestObject)
    {
        $_url=$this->url.'/orderapi/orderApi!countSaleOrderStatus.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
}
?>