<?php
/**
 * 类名        : Goods
 * 继承类        : HessianApi
 * 用途        : 调用server.php方法
 *
 */
class Kyapi_Controller_Order extends Kyapi_Model_Curl
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
    /**采购订单的列表接口**/
    public function listPurOrderApi( $_requestObject, $_queryParams, $_querySorts,$_keyword, $_skip, $_limit)
    {
        // 如果调用java平台的hessian服务 需要指定你传递参数的类型,特别是整形和字符串.
        $_url=$this->url.'/orderapi/orderApi!listPurOrder.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderStatus"=> $_queryParams,"querySorts"=> $_querySorts,"keyword"=> $_keyword,"skip"=> $_skip,"limit"=> $_limit));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**新增订单接口（保存草稿）**/
    public function addOrderApi( $_requestObject, $_order)
    {
        $_url=$this->url.'/orderapi/orderApi!addOrder.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"order"=> $_order));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**编辑订单基本信息接口**/
    public function editOrderApi( $_requestObject, $_order)
    {
        $_url=$this->url.'/orderapi/orderApi!editOrder.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"order"=> $_order));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**订单基本信息接口**/
    public function getOrderApi( $_requestObject, $_orderID)
    {
        $_url=$this->url.'/orderapi/orderApi!getOrderView.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**订单商品信息接口**/
    public function getOrderItemApi( $_requestObject, $_itemID)
    {
        $_url=$this->url.'/orderapi/orderApi!getOrderItemView.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"itemID"=> $_itemID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**新增订单商品信息接口**/
    public function addOrderItemApi( $_requestObject, $_orderItem)
    {
        $_url=$this->url.'/orderapi/orderApi!addOrderItem.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderItem"=> $_orderItem));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**编辑订单商品信息接口**/
    public function editOrderItemApi(  $_requestObject, $_orderItem)
    {
        $_url=$this->url.'/orderapi/orderApi!editOrderItem.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderItem"=> $_orderItem));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**删除草稿订单接口**/
    public function doRemoveDraftApi(  $_requestObject, $_orderID)
    {
        $_url=$this->url.'/orderapi/orderApi!doRemoveDraft.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**authCode临时订单接口**/
    public function getOrderViewByAuthCodeApi(  $_requestObject, $_authCode)
    {
        $_url=$this->url.'/orderapi/orderApi!getOrderViewByAuthCode.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"authCode"=> $_authCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**取消订单接口**/
    public function doCancelOrderApi( $_requestObject, $_orderID, $_operationReason)
    {
        $_url=$this->url.'/orderapi/orderApi!doCancelOrder.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID,"operationReason"=>$_operationReason));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**提交订单接口**/
    public function doSubmitOrderApi( $_requestObject, $_orderID)
    {
        $_url=$this->url.'/orderapi/orderApi!doSubmitOrder.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**确认订单接口**/
    public function doConfirmOrderApi( $_requestObject, $_orderID)
    {
        $_url=$this->url.'/orderapi/orderApi!doConfirmOrder.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**签订协议接口**/
    public function doAgreeContractApi( $_requestObject, $_orderID,$_attachList)
    {
        $_url=$this->url.'/orderapi/orderApi!doAgreeContract.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID,"attachList"=> $_attachList));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**备货接口**/
    public function doPrepareGoodsApi( $_requestObject, $_orderID,$_attachList)
    {
        $_url=$this->url.'/orderapi/orderApi!doPrepareGoods.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID,"attachList"=> $_attachList));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**验货接口**/
    public function doExamineGoodsApi( $_requestObject, $_orderID,$_attachList)
    {
        $_url=$this->url.'/orderapi/orderApi!doExamineGoods.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID,"attachList"=> $_attachList));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**发货接口**/
    public function doDeliverGoodsApi( $_requestObject, $_orderID,$_attachList)
    {
        $_url=$this->url.'/orderapi/orderApi!doDeliverGoods.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID,"attachList"=> $_attachList));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**收货接口**/
    public function doReceiptGoodsApi( $_requestObject, $_orderID,$_attachList)
    {
        $_url=$this->url.'/orderapi/orderApi!doReceiptGoods.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID,"attachList"=> $_attachList));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**销售订单列表统计接口**/
    public function countSaleOrderStatusApi( $_requestObject)
    {
        $_url=$this->url.'/orderapi/orderApi!countSaleOrderStatus.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**采购订单列表统计接口**/
    public function countPurOrderStatusApi( $_requestObject)
    {
        $_url=$this->url.'/orderapi/orderApi!countPurOrderStatus.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /**最新销售订单接口**/
    public function getQuickSaleOrderApi($_requestObject)
    {
        $_url=$this->url.'/orderapi/orderApi!getQuickSaleOrder.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**最新采购订单接口**/
    public function getQuickPruOrderApi($_requestObject)
    {
        $_url=$this->url.'/orderapi/orderApi!getQuickPurOrder.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /**报关单列表**/
    public function listDeclarationApi($_requestObject, $_orderID)
    {
        $_url=$this->url.'/orderapi/orderApi!listDeclaration.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**订舱单列表**/
    public function listShippingOrderApi($_requestObject, $_orderID)
    {
        $_url=$this->url.'/orderapi/orderApi!listShippingOrder.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**派车单列表**/
    public function listTruckingOrderApi($_requestObject, $_orderID)
    {
        $_url=$this->url.'/orderapi/orderApi!listTruckingOrder.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /**订单跟踪日志**/
    public function getOrderEventLogApi($_requestObject, $_orderID,$_view)
    {
        $_url=$this->url.'/orderapi/orderApi!getOrderEventLog.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID,"view"=>$_view));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /**所有订单列表状态统计**/
    public function countOrderStatusApi($_requestObject)
    {
        $_url=$this->url.'/orderapi/orderApi!countOrderStatus.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /**订单文档接口**/
    public function listOrderAttachment($_requestObject, $_orderID)
    {
        $_url=$this->url.'/orderapi/orderApi!listOrderAttachment.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
}
?>