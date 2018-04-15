<?php
/**
 * 类名        : Goods
 * 继承类        : HessianApi
 * 用途        : 调用server.php方法
 *
 */
class Kyapi_Controller_Listsaleorder extends Kyapi_Model_Curl
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

/**订单模块的接口**/
/**销售订单的列表接口**/
    public function listSaleOrderApi( $_requestObject, $_queryParams, $_querySorts,$_keyword, $_skip, $_limit)
    {
    // 如果调用java平台的hessian服务 需要指定你传递参数的类型,特别是整形和字符串.
    $_url=$this->url.'/orderapi/orderApi!listSaleOrder.action';
    $_params =json_encode(array("requestObject"=>$_requestObject,"orderStatus"=> $_queryParams,"querySorts"=> $_querySorts,"keyword"=> $_keyword,"skip"=> $_skip,"limit"=> $_limit));
    $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
    return $resultObject;
    }
}
?>