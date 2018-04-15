<?php
/**
 * 类名        : Goods
 * 继承类        : HessianApi
 * 用途        : 调用server.php方法
 *
 */
class Kyapi_Controller_Common extends Kyapi_Model_KyDB
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
    public function __construct( $url,$option ) {
        parent::__construct( $url,$option );
    }
    /**
     * 获取用户信息
     * 调用JAVA服务端文件中的getContact方法
     * @param string $title 标题
     * @param string $title 价格
     */
    public function getContactApi( $_requestObject , $_contactID )
    {
        // 如果调用java平台的hessian服务 需要指定你传递参数的类型,特别是整形和字符串.
        // $contactID = (string) $contactID;

        $resultObject = $this->getHandler()->getContact( $_requestObject, $_contactID );
        return $resultObject;
    }
    /**
     * 获取用户信息
     * 调用JAVA服务端文件中的reister方法
     */
    public function registerApi( $_requestObject , $_contact )
    {
        $resultObject = $this->getHandler()->register( $_requestObject, $_contact );
        return $resultObject;
    }
    /**
     * 获取用户信息
     * 调用JAVA服务端文件中的login方法
     */
    public function loginApi( $_requestObject , $_loginName,$_password )
    {
        $resultObject = $this->getHandler()->login( $_requestObject,$_loginName,$_password );
        return $resultObject;
    }
    /**
     * 获取用户信息
     * 调用JAVA服务端文件中的 修改密码 changePasswd方法
     */
    public function changePasswordApi( $_requestObject , $_contactID , $_oldPwd, $_newPwd )
    {
        $resultObject = $this->getHandler()->changePassword( $_requestObject, $_contactID, $_oldPwd , $_newPwd);
        return $resultObject;
    }
    /**
     * 获取用户信息
     * 调用JAVA服务端文件中的 验证用户名唯一性 checkLoginName方法
     */
    public function checkLoginNameApi( $_requestObject , $_ecLoginName )
    {
        $resultObject = $this->getHandler()->checkLoginName( $_requestObject, $_ecLoginName);
        return $resultObject;
    }
    /**
     * 获取用户信息
     * 调用JAVA服务端文件中的 找回密码方法 验证code 提交新密码
     */
    public function forgotPasswordApi( $_requestObject , $_ecLoginName )
    {
        $resultObject = $this->getHandler()->forgotPassword( $_requestObject, $_ecLoginName);
        return $resultObject;
    }
    public function checkAuthCodeApi( $_requestObject , $_authCode )
    {
        $resultObject = $this->getHandler()->checkAuthCode( $_requestObject, $_authCode);
        return $resultObject;
    }
    public function changePasswordByAuthCodeApi( $_requestObject ,$_ecLoginName, $_authCode, $_newPwd )
    {
        $resultObject = $this->getHandler()->changePasswordByAuthCode( $_requestObject, $_ecLoginName, $_authCode, $_newPwd);
        return $resultObject;
    }
    /**
     * 获取用户信息-用户列表
     * 调用JAVA服务端文件中的 获取用户列表 listContact方法
     */
    public function listContactApi( $_requestObject , $_queryParams, $_querySorts , $_keyword, $_skip, $_limit )
    {
        $resultObject = $this->getHandler()->listContact( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit);
        return $resultObject;
    }
    /*用户编辑*/
    public function editContactApi( $_requestObject , $_contactID )
    {
        $resultObject = $this->getHandler()->editContact( $_requestObject, $_contactID );
        return $resultObject;
    }
    /*用户新增*/
    public function addContactApi( $_requestObject , $_contactID )
    {
        $resultObject = $this->getHandler()->addContact( $_requestObject, $_contactID );
        return $resultObject;
    }
    /*用户删除*/
    public function deleteContactApi( $_requestObject , $_contactID )
    {
        $resultObject = $this->getHandler()->deleteContact( $_requestObject, $_contactID );
        return $resultObject;
    }
    /**
     * 公司部分 验证公司名是否唯一
     * 调用JAVA服务端文件中的 获取用户列表 listContact方法
     */
    public function checkAccountNameApi( $_requestObject , $_accountName )
    {
        $resultObject = $this->getHandler()->checkAccountName( $_requestObject, $_accountName);
        return $resultObject;
    }
    /**
     * 公司信息部分
     * 公司信息查看、编辑模块方法
     * 调用JAVA服务端文件中的
     */
    public function getAccountApi( $_requestObject , $_accountID )
    {
        $resultObject = $this->getHandler()->getAccount( $_requestObject, $_accountID);
        return $resultObject;
    }
    public function editAccountApi( $_requestObject , $_accountID )
    {
        $resultObject = $this->getHandler()->editAccount( $_requestObject, $_accountID);
        return $resultObject;
    }
    /**公司信息部分结束**/

    /**
     * 公司银行账号部分
     * 银行账号列表、查看、新增、编辑、删除模块方法
     * 调用JAVA服务端文件中的
     */
    public function listBankAccountApi( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        $resultObject = $this->getHandler()->listBankAccount( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit);
        return $resultObject;
    }
    public function getBankAccountApi( $_requestObject , $_bankAcctID )
    {
        $resultObject = $this->getHandler()->getBankAccount( $_requestObject, $_bankAcctID);
        return $resultObject;
    }
    public function addBankAccountApi( $_requestObject , $_bankAccount )
    {
        $resultObject = $this->getHandler()->addBankAccount( $_requestObject, $_bankAccount);
        return $resultObject;
    }
    public function editBankAccountApi( $_requestObject , $_bankAccount )
    {
        $resultObject = $this->getHandler()->editBankAccount( $_requestObject, $_bankAccount);
        return $resultObject;
    }
    public function delBankAccountApi( $_requestObject , $_bankAcctID )
    {
        $resultObject = $this->getHandler()->delBankAccount( $_requestObject, $_bankAcctID);
        return $resultObject;
    }
    //启用银行账号
    public function validBankAccountApi( $_requestObject , $_bankAcctID )
    {
        $resultObject = $this->getHandler()->validBankAccount( $_requestObject, $_bankAcctID);
        return $resultObject;
    }
    //禁用银行账号
    public function invalidBankAccountApi( $_requestObject , $_bankAcctID )
    {
        $resultObject = $this->getHandler()->invalidBankAccount( $_requestObject, $_bankAcctID);
        return $resultObject;
    }
    /**银行账号部分结束**/

    /**
     * 合作伙伴部分
     * 账号列表、添加、编辑、删除->根据买家、卖家、合作方不同角色在前端控制器进行筛选
     * 调用JAVA服务端文件中的
     */
    /*11.3新增接口 合作伙伴公开联系人列表*/
    public function listAccountPublicContactApi( $_requestObject,$_accountID, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        $resultObject = $this->getHandler()->listAccountPublicContact( $_requestObject,$_accountID, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit);
        return $resultObject;
    }

    /**买家合作伙伴**/
    public function listBuyerPartnerApi( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        $resultObject = $this->getHandler()->listBuyerPartner( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit);
        return $resultObject;
    }
    /**卖家合作伙伴**/
    public function listVendorPartnerApi( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        $resultObject = $this->getHandler()->listVendorPartner( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit);
        return $resultObject;
    }
    /**模糊查询已存在合作伙伴**/
    public function listExistAccountApi( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        $resultObject = $this->getHandler()->listExistAccount( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit);
        return $resultObject;
    }
    /**新增全新合作伙伴**/
    public function addPartnerApi( $_requestObject, $_account)
    {
        $resultObject = $this->getHandler()->addPartner( $_requestObject, $_account);
        return $resultObject;
    }
    /**新增已有合作伙伴**/
    public function addPartnerByAccountIDApi($_requestOb,$_accountID,$_roleCode)
    {
        $resultObject = $this->getHandler()->addPartnerByAccountID( $_requestOb,$_accountID,$_roleCode);
        return $resultObject;
    }
    /**接受申请**/
    public function editPartnerAcceptApi( $_requestObject, $_accountNumber,$_roleCode)
    {
        $resultObject = $this->getHandler()->editPartnerAccept( $_requestObject, $_accountNumber,$_roleCode);
        return $resultObject;
    }
    /**拒绝申请**/
    public function editPartnerRejectApi( $_requestObject, $_accountNumber,$_roleCode)
    {
        $resultObject = $this->getHandler()->editPartnerReject( $_requestObject, $_accountNumber,$_roleCode);
        return $resultObject;
    }
    /**编辑合作伙伴**/
    public function editPartnerApi( $_requestObject, $_partnerID)
    {
        $resultObject = $this->getHandler()->editPartner( $_requestObject, $_partnerID);
        return $resultObject;
    }
    /**删除合作伙伴**/
    public function delPartnerApi( $_requestObject, $_partnerID,$_roleCode)
    {
        $resultObject = $this->getHandler()->delPartner( $_requestObject, $_partnerID,$_roleCode);
        return $resultObject;
    }
    /**合作伙伴角色列表**/
    public function listPartnerRoleApi( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        $resultObject = $this->getHandler()->listPartnerRole( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit);
        return $resultObject;
    }
    /**合作伙伴统计BUYER**/
    public function countBuyerPartnerApi( $_requestObject)
    {
        $resultObject = $this->getHandler()->countBuyerPartner( $_requestObject);
        return $resultObject;
    }
    /**合作伙伴统计vendor**/
    public function countVendorPartnerApi( $_requestObject)
    {
        $resultObject = $this->getHandler()->countVendorPartner( $_requestObject);
        return $resultObject;
    }
    /**合作伙伴部分结束**/

    /**
     * 商品模块接口
     * 商品列表（自有、采购）、添加、查看、提交审核、数量统计
     * 调用JAVA服务端文件中的
     */
    /**自有商品列表**/

    public function listSaleProductApi( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        // 如果调用java平台的hessian服务 需要指定你传递参数的类型,特别是整形和字符串.
        $resultObject = $this->getHandler()->listSaleProduct( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit);
        return $resultObject;
    }
    /**新增自有商品**/
    public function addSaleProductApi( $_requestObject, $_product)
    {
        $resultObject = $this->getHandler()->addSaleProduct( $_requestObject, $_product);
        return $resultObject;
    }
    /**编辑自有商品**/
    public function editSaleProductApi( $_requestObject, $_product)
    {
        $resultObject = $this->getHandler()->editSaleProduct( $_requestObject, $_product);
        return $resultObject;
    }
    /**采购商品列表**/
    public function listPurProductApi( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        $resultObject = $this->getHandler()->listPurProduct( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit);
        return $resultObject;
    }
    /**新增采购商品**/
    public function addPurProductApi( $_requestObject, $_product)
    {
        $resultObject = $this->getHandler()->addPurProduct( $_requestObject, $_product);
        return $resultObject;
    }
    /**编辑采购商品**/
    public function editPurProductApi( $_requestObject, $_product)
    {
        $resultObject = $this->getHandler()->editPurProduct( $_requestObject, $_product);
        return $resultObject;
    }
    /**商品信息**/
    public function getProductApi( $_requestObject, $_productID)
    {
        $resultObject = $this->getHandler()->getProduct( $_requestObject, $_productID);
        return $resultObject;
    }
    /**提交商品审核**/
    public function forReviewProductApi( $_requestObject, $_productID)
    {
        $resultObject = $this->getHandler()->forReviewProduct( $_requestObject, $_productID);
        return $resultObject;
    }
    /**确认商品商品**/
    public function confrimProductApi( $_requestObject, $_productID)
    {
        $resultObject = $this->getHandler()->confrimProduct( $_requestObject, $_productID);
        return $resultObject;
    }
    /**启用商品**/
    public function invalidProductApi( $_requestObject, $_productID)
    {
        $resultObject = $this->getHandler()->invalidProduct( $_requestObject, $_productID);
        return $resultObject;
    }
    /**禁用商品**/
    public function validProductApi( $_requestObject, $_productID)
    {
        $resultObject = $this->getHandler()->validProduct( $_requestObject, $_productID);
        return $resultObject;
    }
    /**删除商品**/
    public function delProductApi( $_requestObject, $_productID)
    {
        $resultObject = $this->getHandler()->delProduct( $_requestObject, $_productID);
        return $resultObject;
    }
    /**自有商品数量统计**/
    public function countSaleProductApi( $_requestObject)
    {
        $resultObject = $this->getHandler()->countSaleProduct( $_requestObject);
        return $resultObject;
    }
    /**采购商品数量统计**/
    public function countPurProductApi( $_requestObject)
    {
        $resultObject = $this->getHandler()->countPurProduct( $_requestObject);
        return $resultObject;
    }
    /****HSCODE列表******/
    public function listHSCodeApi($_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        $resultObject = $this->getHandler()->listHSCode( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit);
        return $resultObject;
    }
    /****HSCODE详细信息******/
    public function getHSCodeApi($_requestObject, $_hscode)
    {
        $resultObject = $this->getHandler()->getHSCode( $_requestObject, $_hscode);
        return $resultObject;
    }

    /**通用接口**/
    /**系统参数方法获取**/
    public function getSysParamApi( $_requestObject,$_paramCode)
    {
        $resultObject = $this->getHandler()->getSysParam( $_requestObject,$_paramCode);
        return $resultObject;
    }
    /**数据字典列表方法获取**/
    public function getStaticDataListApi( $_requestObject,$_dataDictCode,$_args,$_parentCode,$_langCode)
    {
        $resultObject = $this->getHandler()->getStaticDataList( $_requestObject,$_dataDictCode,$_args,$_parentCode,$_langCode);
        return $resultObject;
    }
    /**数据字典方法获取**/
    public function getStaticDataApi( $_requestObject,$_dataDictCode,$_valueCode,$_args,$_langCode)
    {
        $resultObject = $this->getHandler()->getStaticData( $_requestObject,$_dataDictCode,$_valueCode,$_args,$_langCode);
        return $resultObject;
    }
    /**数据字典映射方法获取**/
    public function getStaticDataMapApi( $_requestObject,$_dataDictCode,$_valueCode,$_args,$_langCode)
    {
        $resultObject = $this->getHandler()->getStaticDataMap( $_requestObject,$_dataDictCode,$_valueCode,$_args,$_langCode);
        return $resultObject;
    }
    /**语言包列表方法获取**/
    public function getSysLangPacksApi( $_requestObject,$_langPacksID)
    {
        $resultObject = $this->getHandler()->getSysLangPacks( $_requestObject,$_langPacksID);
        return $resultObject;
    }
    /**语言包方法获取**/
    public function getLangPacksApi( $_requestObject,$_langPacksID,$_lang)
    {
        $resultObject = $this->getHandler()->getLangPacks( $_requestObject,$_langPacksID,$_lang);
        return $resultObject;
    }
    /**语言包方法获取**/
    public function uploadApi( $_sid,$_file,$_name)
    {
        $resultObject = $this->getHandler()->upload( $_sid,$_file,$_name);
        return $resultObject;
    }
    /**订单模块的接口**/
    /**销售订单的列表接口**/
    public function listSaleOrderApi( $_requestObject, $_queryParams, $_querySorts,$_orderStatus,$_keyword, $_skip, $_limit)
    {
        // 如果调用java平台的hessian服务 需要指定你传递参数的类型,特别是整形和字符串.
        $resultObject = $this->getHandler()->listSaleOrder( $_requestObject, $_queryParams, $_querySorts,$_orderStatus,$_keyword, $_skip, $_limit);
        return $resultObject;
    }
    /**采购订单的列表接口**/
    public function listPurOrderApi( $_requestObject, $_queryParams, $_querySorts,$_orderStatus,$_keyword, $_skip, $_limit)
    {
        // 如果调用java平台的hessian服务 需要指定你传递参数的类型,特别是整形和字符串.
        $resultObject = $this->getHandler()->listPurOrder( $_requestObject, $_queryParams, $_querySorts,$_orderStatus,$_keyword, $_skip, $_limit);
        return $resultObject;
    }
    /**新增订单接口（保存草稿）**/
    public function addOrderApi( $_requestObject, $_order)
    {
        $resultObject = $this->getHandler()->addOrder( $_requestObject, $_order);
        return $resultObject;
    }
    /**编辑订单基本信息接口**/
    public function editOrderApi( $_requestObject, $_order)
    {
        $resultObject = $this->getHandler()->editOrder( $_requestObject, $_order);
        return $resultObject;
    }
    /**订单基本信息接口**/
    public function getOrderApi( $_requestObject, $_orderID)
    {
        $resultObject = $this->getHandler()->getOrder( $_requestObject, $_orderID);
        return $resultObject;
    }
    /**订单商品信息接口**/
    public function getOrderItemApi( $_requestObject, $_itemID)
    {
        $resultObject = $this->getHandler()->getOrderItem( $_requestObject, $_itemID);
        return $resultObject;
    }
    /**新增订单商品信息接口**/
    public function addOrderItemApi( $_requestObject, $_orderItem)
    {
        $resultObject = $this->getHandler()->addOrderItem(  $_requestObject, $_orderItem);
        return $resultObject;
    }
    /**编辑订单商品信息接口**/
    public function editOrderItemApi(  $_requestObject, $_orderItem)
    {
        $resultObject = $this->getHandler()->editOrderItem(  $_requestObject, $_orderItem);
        return $resultObject;
    }
    /**删除草稿订单接口**/
    public function doRemoveDraftApi(  $_requestObject, $_orderID)
    {
        $resultObject = $this->getHandler()->doRemoveDraft(  $_requestObject, $_orderID);
        return $resultObject;
    }
    /**取消订单接口**/
    public function doCancelOrderApi( $_requestObject, $_orderID, $_operationReason)
    {
        $resultObject = $this->getHandler()->doCancelOrder( $_requestObject, $_orderID, $_operationReason);
        return $resultObject;
    }
    /**提交订单接口**/
    public function doSubmitOrderApi( $_requestObject, $_orderID)
    {
        $resultObject = $this->getHandler()->doSubmitOrder( $_requestObject, $_orderID);
        return $resultObject;
    }
    /**确认订单接口**/
    public function doConfirmOrderApi( $_requestObject, $_orderID)
    {
        $resultObject = $this->getHandler()->doConfirmOrder( $_requestObject, $_orderID);
        return $resultObject;
    }
    /**签订协议接口**/
    public function doAgreeContractApi( $_requestObject, $_orderID,$_attachList)
    {
        $resultObject = $this->getHandler()->doAgreeContract( $_requestObject, $_orderID,$_attachList);
        return $resultObject;
    }
    /**备货接口**/
    public function doPrepareGoodsApi( $_requestObject, $_orderID,$_attachList)
    {
        $resultObject = $this->getHandler()->doPrepareGoods( $_requestObject, $_orderID,$_attachList);
        return $resultObject;
    }
    /**验货接口**/
    public function doExamineGoodsApi( $_requestObject, $_orderID,$_attachList)
    {
        $resultObject = $this->getHandler()->doExamineGoods( $_requestObject, $_orderID,$_attachList);
        return $resultObject;
    }
    /**发货接口**/
    public function doDeliverGoodsApi( $_requestObject, $_orderID,$_attachList)
    {
        $resultObject = $this->getHandler()->doDeliverGoods( $_requestObject, $_orderID,$_attachList);
        return $resultObject;
    }
    /**收货接口**/
    public function doReceiptGoodsApi( $_requestObject, $_orderID,$_attachList)
    {
        $resultObject = $this->getHandler()->doReceiptGoods( $_requestObject, $_orderID,$_attachList);
        return $resultObject;
    }
    /**销售订单列表统计接口**/
    public function countSaleOrderStatusApi( $_requestObject)
    {
        $resultObject = $this->getHandler()->countSaleOrderStatus( $_requestObject);
        return $resultObject;
    }
    /**采购订单列表统计接口**/
    public function countPurOrderStatusApi( $_requestObject)
    {
        $resultObject = $this->getHandler()->countPurOrderStatus( $_requestObject);
        return $resultObject;
    }

    /**最新销售订单接口**/
    public function getQuickSaleOrderApi($_requestObject)
    {
        $resultObject = $this->getHandler()->getQuickSaleOrder($_requestObject);
        return $resultObject;
    }
    /**最新采购订单接口**/
    public function getQuickPruOrderApi($_requestObject)
    {
        $resultObject = $this->getHandler()->getQuickPruOrder($_requestObject);
        return $resultObject;
    }
    /**报关单列表**/
    public function listDeclarationApi($_requestObject, $_orderID)
    {
        $resultObject = $this->getHandler()->listDeclaration($_requestObject, $_orderID);
        return $resultObject;
    }
    /**订舱单列表**/
    public function listShippingOrderApi($_requestObject, $_orderID)
    {
        $resultObject = $this->getHandler()->listShippingOrder($_requestObject, $_orderID);
        return $resultObject;
    }
    /**派车单列表**/
    public function listTruckingOrderApi($_requestObject, $_orderID)
    {
        $resultObject = $this->getHandler()->listTruckingOrder($_requestObject, $_orderID);
        return $resultObject;
    }
    /**订单跟踪日志**/
    public function getOrderEventLogApi($_requestObject, $_orderID,$_view)
    {
        $resultObject = $this->getHandler()->getOrderEventLog($_requestObject,$_orderID,$_view);
        return $resultObject;
    }
    /*系统接口 订单汇率*/
    public function getExchangeRateApi($_requestObject,$_bizType,$_bizID,$_baseCrn,$_contraCrn)
    {
        $resultObject = $this->getHandler()->getExchangeRate($_requestObject,$_bizType,$_bizID,$_baseCrn,$_contraCrn);
        return $resultObject;
    }

}
?>