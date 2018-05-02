<?php
/**
 * 类名        : Goods
 * 继承类        : HessianApi
 * 用途        : 调用server.php方法
 *
 */
class Kyapi_Controller_Json extends Kyapi_Model_Curl
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
    /**
     * 附件列表获取接口
     */
    public function findDataDictApi($_requestObject, $_codes,$_valueCode)
    {
        $_url=$this->url.'/commonapi/commonApi!findDataDict.action';
        $_params =json_encode(array('requestObject'=> $_requestObject,'codes'=> $_codes,'valueCode'=>$_valueCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    public function findDataDictListApi($_requestObject, $_codes)
    {
        $_url=$this->url.'/commonapi/commonApi!findDataDictList.action';
        $_params =json_encode(array('requestObject'=> $_requestObject,'codes'=> $_codes));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /*系统接口 订单汇率*/
    public function getExchangeRateApi($_requestObject,$_bizType,$_bizID,$_baseCrn,$_contraCrn)
    {
        $_url=$this->url.'/commonapi/commonApi!getExchangeRate.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"bizType"=> $_bizType,"bizID"=>$_bizID,"baseCrn"=>$_baseCrn,"contraCrn"=>$_contraCrn));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /*汇率接口2*/
    public function listExchangeRateApi($_requestObject,$_bizType,$_bizID,$_baseCrn,$_contraCrn)
    {
        $_url=$this->url.'/commonapi/commonApi!listExchangeRate.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"bizType"=> $_bizType,"bizID"=>$_bizID,"baseCrn"=>$_baseCrn,"contraCrn"=>$_contraCrn));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /**
     * 获取注册用户接口
     * 调用JAVA服务端文件中的reister方法
     */
    public function registerApi( $_requestObject , $_contact )
    {
        $_url=$this->url.'/contactapi/contactApi!register.action';
        $_params =json_encode(array('requestObject'=> $_requestObject,'contact'=> $_contact));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**
     * 获取用户登陆信息
     * 调用JAVA服务端文件中的login方法
     */
    public function loginApi( $_requestObject , $_loginName,$_password,$_authCode )
    {

        $_url=$this->url.'/contactapi/contactApi!login.action';
        $_params =json_encode(array('requestObject'=> $_requestObject,'loginName'=> $_loginName,'password'=> $_password,'authCode'=>$_authCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*登录验证码接口 04.10*/
    public function getLoginAuthCodeApi($_requestObject)
    {
        $_url=$this->url.'/contactapi/contactApi!getLoginAuthCode.action';
        $_params =json_encode(array('requestObject'=> $_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**
     * 获取用户信息
     * 调用JAVA服务端文件中的 修改密码 changePasswd方法
     */
    public function changePasswordApi( $_requestObject , $_oldPwd, $_password )
    {
        $_url=$this->url.'/contactapi/contactApi!changePassword.action';
        $_params =json_encode(array('requestObject'=> $_requestObject,'oldPwd'=> $_oldPwd,'password'=> $_password));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }


    /**
     * 获取用户信息
     * 调用JAVA服务端文件中的 找回密码方法 验证code 提交新密码
     */
    //合作伙伴邀请 回调地址
    public function getAccountViewByAuthCodeApi( $_authcode )
    {
        $_url=$this->url.'/contactapi/contactApi!getAccountViewByAuthCode.action';
        $_params =json_encode(array("authCode"=> $_authcode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    //员工contact邀请 回调地址
    public function getContactViewByAuthCodeApi( $_authcode )
    {
        $_url=$this->url.'/contactapi/contactApi!getContactViewByAuthCode.action';
        $_params =json_encode(array("authCode"=> $_authcode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    public function registerByAuthCodeApi( $_requestObject ,$_contact, $_authcode )
    {
        $_url=$this->url.'/contactapi/contactApi!registerByAuthCode.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"contact"=>$_contact,"authCode"=> $_authcode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    public function contactRegisterByAuthCodeApi( $_requestObject ,$_contact, $_authcode )
    {
        $_url=$this->url.'/contactapi/contactApi!contactRegisterByAuthCode.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"contact"=>$_contact,"authCode"=> $_authcode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**
     * 获取用户信息
     * 调用JAVA服务端文件中的 找回密码方法 验证code 提交新密码
     */
    public function forgotPasswordApi( $_requestObject , $_loginName ,$_contactName)
    {
        $_url=$this->url.'/contactapi/contactApi!forgotPassword.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"loginName"=>$_loginName,"name"=>$_contactName));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    //验证登录密码
    public function checkPasswordApi( $_requestObject , $_password )
    {
        $_url=$this->url.'/contactapi/contactApi!checkPassword.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"password"=>$_password));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    public function checkAuthCodeApi( $_requestObject , $_authCode )
    {
        $_url=$this->url.'/contactapi/contactApi!checkAuthCode.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"authCode"=>$_authCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    public function changePasswordByAuthCodeApi( $_requestObject , $_authCode, $_password )
    {
        $_url=$this->url.'/contactapi/contactApi!changePasswordByAuthCode.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"authCode"=>$_authCode,"password"=>$_password));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**
     * 获取用户信息-用户列表
     * 调用JAVA服务端文件中的 获取用户列表 listContact方法
     */
    public function listContactApi( $_requestObject,$_queryP, $_querySorts, $_keyword, $_skip, $_limit)
    {
        $_url=$this->url.'/contactapi/contactApi!listContact.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"queryParams"=>$_queryP,"querySorts"=>$_querySorts,"keyword"=>$_keyword,"skip"=>$_skip,"limit"=>$_limit));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**
     * 获取用户信息
     * 调用JAVA服务端文件中的getContact方法
     */
    public function getContactApi( $_requestObject , $_contactID )
    {
        // 如果调用java平台的hessian服务 需要指定你传递参数的类型,特别是整形和字符串.

        $_url=$this->url.'/contactapi/contactApi!getContactView.action';
        $_params =json_encode(array('requestObject'=> $_requestObject,'contactID'=> $_contactID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*用户编辑*/
    public function editContactApi($_requestObject ,$_contact )
    {
        $_url=$this->url.'/contactapi/contactApi!editContact.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'contact'=>$_contact));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*用户新增*/
    public function addContactApi( $_requestObject , $_contact )
    {
        $_url=$this->url.'/contactapi/contactApi!addContact.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'contact'=>$_contact));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*用户启用*/
    public function editContactValidApi( $_requestObject , $_contactID )
    {
        $_url=$this->url.'/contactapi/contactApi!editContactValid.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'contactID'=>$_contactID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*用户禁用*/
    public function editContactInvalidApi( $_requestObject , $_contactID )
    {
        $_url=$this->url.'/contactapi/contactApi!editContactInvalid.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'contactID'=>$_contactID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*用户删除*/
    public function deleteContactApi( $_requestObject , $_contactID )
    {
        $_url=$this->url.'/contactapi/contactApi!deleteContact.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'contactID'=>$_contactID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*用户设为默认账户
    */
    public function setDefaultContactApi( $_requestObject , $_contactID )
    {
        $_url=$this->url.'/contactapi/contactApi!setDefaultContact.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'contactID'=>$_contactID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }


    /*查看公司信息 验证公司电子邮箱 发送验证邮件*/
    public function accountEmailNotice( $_requestObject)
    {
        $_url=$this->url.'/accountapi/accountApi!sendVerifyAccountEmailNotice.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    public function verifyAccountEmail($_authCode)
    {
        $_url=$this->url.'/contactapi/contactApi!verifyAccountEmailByAuthCode.action';
        $_params =json_encode(array("authCode"=>$_authCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }


    /*邀请员工注册 发送验证邮件*/
    public function contactInviteEmailNotice( $_requestObject,$_contactID)
    {
        $_url=$this->url.'/contactapi/contactApi!sendEcommContactInviteEmailNotice.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"contactID"=>$_contactID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /*用户邮箱 发送验证邮件*/
    public function sendVerifyContactEmailNotice( $_requestObject)
    {
        $_url=$this->url.'/contactapi/contactApi!sendVerifyContactEmailNotice.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*用户邮箱 接收验证邮件code*/
    public function verifyContactEmailByAuthCode( $_authCode)
    {
        $_url=$this->url.'/contactapi/contactApi!verifyContactEmailByAuthCode.action';
        $_params =json_encode(array("authCode"=>$_authCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*用户登录名（邮箱） 发送验证邮件*/
    public function sendVerifyECommLoginEmailNotice( $_requestObject)
    {
        $_url=$this->url.'/contactapi/contactApi!sendVerifyECommLoginEmailNotice.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*用户登录名（邮箱） 接收验证邮件code*/
    public function verifyECommLoginEmailByAuthCode( $_authCode)
    {
        $_url=$this->url.'/contactapi/contactApi!verifyECommLoginEmailByAuthCode.action';
        $_params =json_encode(array("authCode"=>$_authCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /**
     * 获取用户信息
     * 调用JAVA服务端文件中的 验证用户名唯一性 checkLoginName方法
     */
    public function checkLoginNameApi( $_requestObject , $_loginName )
    {
        $_url=$this->url.'/contactapi/contactApi!checkLoginName.action';
        $_params =json_encode(array('requestObject'=> $_requestObject,'loginName'=> $_loginName));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /**
     * 公司信息部分
     * 公司信息查看、编辑模块方法
     * 调用JAVA服务端文件中的
     */
    /**
     * 公司部分 验证公司名是否唯一
     * 调用JAVA服务端文件中的 获取用户列表 listContact方法
     */
    public function checkAccountNameApi( $_requestObject , $_accountName )
    {
        $_url=$this->url.'/accountapi/accountApi!checkAccountName.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"accountName"=> $_accountName));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    public function getAccountApi( $_requestObject)
    {
        $_url=$this->url.'/accountapi/accountApi!getAccountView.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    public function editAccountApi( $_requestObject , $_account)
    {
        $_url=$this->url.'/accountapi/accountApi!editAccount.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'account'=>$_account));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
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
        $_url=$this->url.'/accountapi/accountApi!listBankAccount.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'queryParams'=> $_queryParams,'querySorts'=> $_querySorts,'keyword'=> $_keyword,'skip'=> $_skip,'limit'=> $_limit));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    public function getBankAccountApi( $_requestObject , $_bankAcctID )
    {
        $_url=$this->url.'/accountapi/accountApi!getBankAccountView.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'bankAcctID'=> $_bankAcctID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    public function setDefaultBankAccountApi( $_requestObject , $_bankAcctID )
    {
        $_url=$this->url.'/accountapi/accountApi!setDefaultBankAccount.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'bankAcctID'=> $_bankAcctID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    public function addBankAccountApi( $_requestObject , $_bankAccount )
    {
        $_url=$this->url.'/accountapi/accountApi!addBankAccount.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'bankAccount'=> $_bankAccount));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    public function editBankAccountApi( $_requestObject , $_bankAccount )
    {
        $_url=$this->url.'/accountapi/accountApi!editBankAccount.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'bankAccount'=> $_bankAccount));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    public function delBankAccountApi( $_requestObject , $_bankAcctID )
    {
        $_url=$this->url.'/accountapi/accountApi!delBankAccount.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'bankAcctID'=> $_bankAcctID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    //启用银行账号
    public function validBankAccountApi( $_requestObject , $_bankAcctID )
    {
        $_url=$this->url.'/accountapi/accountApi!validBankAccount.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'bankAcctID'=> $_bankAcctID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    //禁用银行账号
    public function invalidBankAccountApi( $_requestObject , $_bankAcctID )
    {
        $_url=$this->url.'/accountapi/accountApi!invalidBankAccount.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'bankAcctID'=> $_bankAcctID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
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
        $_url=$this->url.'/partnerapi/partnerApi!listPartnerPublicContact.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"accountID"=>$_accountID,"queryParams"=> $_queryParams,"querySorts"=> $_querySorts,"keyword"=> $_keyword,"skip"=> $_skip,"limit"=> $_limit));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /**买家合作伙伴**/
    public function listBuyerPartnerApi( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        $_url=$this->url.'/partnerapi/partnerApi!listBuyerPartner.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"queryParams"=> $_queryParams,"querySorts"=> $_querySorts,"keyword"=> $_keyword,"skip"=> $_skip,"limit"=> $_limit));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**卖家合作伙伴**/
    public function listVendorPartnerApi( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        $_url=$this->url.'/partnerapi/partnerApi!listVendorPartner.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"queryParams"=> $_queryParams,"querySorts"=> $_querySorts,"keyword"=> $_keyword,"skip"=> $_skip,"limit"=> $_limit));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**模糊查询已存在合作伙伴**/
    public function listExistAccountApi( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        $_url=$this->url.'/partnerapi/partnerApi!listExistAccount.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"queryParams"=> $_queryParams,"querySorts"=> $_querySorts,"keyword"=> $_keyword,"skip"=> $_skip,"limit"=> $_limit));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**新增全新合作伙伴**/
    public function addPartnerApi( $_requestObject, $_account)
    {
        $_url=$this->url.'/partnerapi/partnerApi!addPartner.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"account"=> $_account));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**新增已有合作伙伴**/
    public function addPartnerByAccountIDApi($_requestObject,$_accountID,$_roleCode)
    {
        $_url=$this->url.'/partnerapi/partnerApi!addPartnerByAccount.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"accountID"=> $_accountID,"roleCode"=>$_roleCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**接受申请**/
    public function editPartnerAcceptApi( $_requestObject, $_accountID,$_roleCode)
    {
        $_url=$this->url.'/partnerapi/partnerApi!acceptInvitation.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"accountID"=> $_accountID,"roleCode"=>$_roleCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**拒绝申请**/
    public function editPartnerRejectApi( $_requestObject, $_accountID,$_roleCode)
    {
        $_url=$this->url.'/partnerapi/partnerApi!rejectInvitation.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"accountID"=> $_accountID,"roleCode"=>$_roleCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**查看合作伙伴**/
    public function getPartnerApi( $_requestObject, $_accountID)
    {
        $_url=$this->url.'/partnerapi/partnerApi!getPartnerView.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"accountID"=> $_accountID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**编辑合作伙伴**/
    public function editPartnerApi( $_requestObject, $_account)
    {
        $_url=$this->url.'/partnerapi/partnerApi!editPartner.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"account"=> $_account));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**删除合作伙伴**/
    public function delPartnerApi( $_requestObject, $_accountID,$_roleCode)
    {
        $_url=$this->url.'/partnerapi/partnerApi!delPartner.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"accountID"=> $_accountID,"roleCode"=> $_roleCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
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
        $_url=$this->url.'/partnerapi/partnerApi!countBuyerPartner.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**合作伙伴统计vendor**/
    public function countVendorPartnerApi( $_requestObject)
    {
        $_url=$this->url.'/partnerapi/partnerApi!countVendorPartner.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**合作伙伴结束**/

    /**
     * 商品模块接口
     * 商品列表（自有、采购）、添加、查看、提交审核、数量统计
     * 调用JAVA服务端文件中的
     */
    /**自有商品列表**/

    public function listSaleProductApi( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        $_url=$this->url.'/productapi/productApi!listSaleProduct.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"queryParams"=> $_queryParams,"querySorts"=> $_querySorts,"keyword"=> $_keyword,"skip"=> $_skip,"limit"=> $_limit));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**新增自有商品**/
    public function addSaleProductApi( $_requestObject, $_product)
    {
        $_url=$this->url.'/productapi/productApi!addSaleProduct.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"product"=> $_product));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**编辑自有商品**/
    public function editSaleProductApi( $_requestObject, $_product)
    {
        $_url=$this->url.'/productapi/productApi!editSaleProduct.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"product"=> $_product));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**采购商品列表**/
    public function listPurProductApi( $_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        $_url=$this->url.'/productapi/productApi!listPurProduct.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"queryParams"=> $_queryParams,"querySorts"=> $_querySorts,"keyword"=> $_keyword,"skip"=> $_skip,"limit"=> $_limit));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**新增采购商品**/
    public function addPurProductApi( $_requestObject, $_product)
    {
        $_url=$this->url.'/productapi/productApi!addPurProduct.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"product"=> $_product));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**编辑采购商品**/
    public function editPurProductApi( $_requestObject, $_product)
    {
        $_url=$this->url.'/productapi/productApi!editPurProduct.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"product"=> $_product));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**商品信息**/
    public function getProductApi( $_requestObject, $_productID)
    {
        $_url=$this->url.'/productapi/productApi!getProductView.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"productID"=>$_productID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**提交商品审核**/
    public function forReviewProductApi( $_requestObject, $_productID)
    {
        $_url=$this->url.'/productapi/productApi!forReviewProduct.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"productID"=> $_productID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**确认商品商品**/
    public function confrimProductApi( $_requestObject, $_productID)
    {
        $_url=$this->url.'/productapi/productApi!confirmProduct.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"productID"=> $_productID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**禁用商品**/
    public function invalidProductApi( $_requestObject, $_productID)
    {
        $_url=$this->url.'/productapi/productApi!invalidProduct.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"productID"=> $_productID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**启用商品**/
    public function validProductApi( $_requestObject, $_productID)
    {
        $_url=$this->url.'/productapi/productApi!validProduct.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"productID"=> $_productID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**删除商品**/
    public function delProductApi( $_requestObject, $_productID)
    {
        $_url=$this->url.'/productapi/productApi!delProduct.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"productID"=> $_productID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**自有商品数量统计**/
    public function countSaleProductApi( $_requestObject)
    {
        $_url=$this->url.'/productapi/productApi!countSaleProduct.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**采购商品数量统计**/
    public function countPurProductApi( $_requestObject)
    {
        $_url=$this->url.'/productapi/productApi!countPurProduct.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /****HSCODE列表******/
    public function listHSCodeApi($_requestObject, $_queryParams, $_querySorts , $_keyword, $_skip, $_limit)
    {
        $_url=$this->url.'/productapi/productApi!listHSCode.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"queryParams"=> $_queryParams,"querySorts"=> $_querySorts,"keyword"=> $_keyword,"skip"=> $_skip,"limit"=> $_limit));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /****HSCODE详细信息******/
    public function getHSCodeApi($_requestObject, $_hscode)
    {
        $_url=$this->url.'/productapi/productApi!getHSCode.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'hscode'=> $_hscode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
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
    public function doCancelOrderApi( $_requestObject, $_orderID/*, $_operationReason*/)
    {
        $_url=$this->url.'/orderapi/orderApi!doCancelOrder.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID/*,"operationReason"=>$_operationReason*/));
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
    public function doAgreeContractApi( $_requestObject, $_contractID, $_attachList)
    {
        $_url=$this->url.'/contractapi/contractApi!doAgreeContract.action';
        $_params =json_encode(array("requestObject" => $_requestObject, "contractID" => $_contractID, "attachList" => $_attachList));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    
    /** 发货单列表接口 **/
    public function listDelivery($_requestObject, $_orderID) {
        $_url=$this->url.'/orderapi/orderApi!listDelivery.action';
        $_params =json_encode(array("requestObject" => $_requestObject, "orderID" => $_orderID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    
    /** 发货单详情接口 **/
    public function getDeliveryView($_requestObject, $deliveryID) {
        $_url=$this->url.'/orderapi/orderApi!getDeliveryView.action';
        $_params =json_encode(array("requestObject" => $_requestObject, "deliveryID" => $deliveryID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    
    /** 订单商品列表接口 **/
    public function listOrderItem($_requestObject, $orderID) {
        $_url=$this->url.'/orderapi/orderApi!listOrderItem.action';
        $_params =json_encode(array("requestObject" => $_requestObject, "orderID" => $orderID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    
    /** 备货接口 **/
    public function doPrepareGoodsApi($_requestObject, $delivery, $deliveryItemList, $_attachList) {
        $_url = $this->url . '/orderapi/orderApi!doPrepareGoods.action';
        $_params = json_encode(array("requestObject"  => $_requestObject, "delivery" => $delivery, "deliveryItemList" => $deliveryItemList, "attachList" => $_attachList));
        $resultObject = $this->vpost($_url, $_params); //输出目标地址源码
        return $resultObject;
    }
    
    /** 编辑备货单 **/
    public function editDeliveryApi($_requestObject, $deliveryID, $deliveryItemList, $_attachList) {
        $_url = $this->url . '/orderapi/orderApi!editDelivery.action';
        $_params = json_encode(array("requestObject" => $_requestObject, "deliveryID" => $deliveryID, "deliveryItemList" => $deliveryItemList, "attachList" => $_attachList));
        $resultObject = $this->vpost($_url, $_params); //输出目标地址源码
        return $resultObject;
    }
    
    /** 删除备货单 **/
    public function delDeliveryApi($_requestObject, $deliveryID) {
        $_url = $this->url . '/orderapi/orderApi!delDelivery.action';
        $_params = json_encode(array("requestObject" => $_requestObject, "deliveryID" => $deliveryID));
        $resultObject = $this->vpost($_url, $_params); //输出目标地址源码
        return $resultObject;
    }
    
    /**验货接口**/
    public function doExamineGoodsApi($_requestObject, $deliveryID,$_attachList) {
        $_url=$this->url.'/orderapi/orderApi!doExamineGoods.action';
        $_params =json_encode(array("requestObject" => $_requestObject, "deliveryID" => $deliveryID, "attachList" => $_attachList));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**发货接口**/
    public function doDeliverGoodsApi($_requestObject, $deliveryID, $_attachList) {
        $_url=$this->url.'/orderapi/orderApi!doDeliverGoods.action';
        $_params = json_encode(array("requestObject" => $_requestObject, "deliveryID" => $deliveryID, "attachList" => $_attachList));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**收货接口**/
    public function doReceiptGoodsApi($_requestObject, $deliveryID, $_attachList) {
        $_url=$this->url.'/orderapi/orderApi!doReceiptGoods.action';
        $_params =json_encode(array("requestObject" => $_requestObject, "deliveryID" => $deliveryID, "attachList" => $_attachList));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    
    /** 生成发票资料接口 **/
    public function genBillingInfoApi($requestObject, $delivery) {
        $_url = $this->url . '/orderapi/orderApi!genBillingInfo.action';
        $_params = json_encode(array("requestObject" => $requestObject, "delivery" => $delivery));
        $resultObject = $this->vpost($_url, $_params); //输出目标地址源码
        return $resultObject;
    }
    
    /** 获取发票资料接口 **/
    public function getBillingInfoApi($requestObject, $deliveryID) {
        $_url = $this->url . '/orderapi/orderApi!getBillingInfo.action';
        $_params = json_encode(array("requestObject" => $requestObject, "deliveryID" => $deliveryID));
        $resultObject = $this->vpost($_url, $_params); //输出目标地址源码
        return $resultObject;
    }
    
    /** 编辑快递单号接口 **/
    public function editExpressNoApi($requestObject, $deliverySupplier) {
        $_url = $this->url . '/orderapi/orderApi!editExpressNo.action';
        $_params = json_encode(array("requestObject" => $requestObject, "deliverySupplier" => $deliverySupplier));
        $resultObject = $this->vpost($_url, $_params); //输出目标地址源码
        return $resultObject;
    }
    
    /**销售订单列表统计接口**/
    public function countSaleOrderStatusApi($_requestObject) {
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


    /**1.1结算模块列表查询  支付账户详情**/
    public function paymentViewApi($_requestObject)
    {
        $_url=$this->url.'/paymentapi/paymentApi!getAccountView.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**1.2结算模块 交易记录列表**/

    public function listpaymentTradApi( $_requestObject, $_queryParams, $_querySorts,$_keyword, $_skip, $_limit,$_startDate,$_endDate,$_lowerAmount,$_upperAmount,$_tradingStatus,$_tradingType,$_transNo)
    {
        // 如果调用java平台的hessian服务 需要指定你传递参数的类型,特别是整形和字符串.
        $_url=$this->url.'/paymentapi/paymentApi!listPaymentTrading.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"queryParams"=> $_queryParams,"querySorts"=> $_querySorts,"keyword"=> $_keyword,"skip"=> $_skip,"limit"=> $_limit,"startDate"=> $_startDate,"endDate"=> $_endDate,"lowerAmount"=> $_lowerAmount,"upperAmount"=> $_upperAmount,"tradingStatus"=> $_tradingStatus,"tradingType"=> $_tradingType,"transNo"=> $_transNo));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**1.3结算模块列表查询  交易记录详情列表**/
    public function getpaymentTradApi($_requestObject,$_tradingID)
    {
        $_url=$this->url.'/paymentapi/paymentApi!getPaymentTradingView.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"tradingID"=> $_tradingID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**1.4结算模块 交易流水列表**/
    public function listPaymentFlowApi( $_requestObject, $_queryParams, $_querySorts,$_keyword, $_skip, $_limit,$_startDate,$_endDate,$_lowerAmount,$_upperAmount,$_tradingStatus,$_tradingType,$_transNo)
    {
        // 如果调用java平台的hessian服务 需要指定你传递参数的类型,特别是整形和字符串.
        $_url=$this->url.'/paymentapi/paymentApi!listPaymentFlow.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"queryParams"=> $_queryParams,"querySorts"=> $_querySorts,"keyword"=> $_keyword,"skip"=> $_skip,"limit"=> $_limit,"startDate"=> $_startDate,"endDate"=> $_endDate,"lowerAmount"=> $_lowerAmount,"upperAmount"=> $_upperAmount,"tradingStatus"=> $_tradingStatus,"tradingType"=> $_tradingType,"transNo"=> $_transNo));
        $resultObject= $this->vpost($_url,$_params);
    //    $resultObject=$this->JsonToencode($_params);
//        $data=preg_replace('/[^,^\"^\'^{^}]*[,]*"[^,^\"^\'^{^}]*"[^,^\"^\'^{^}]*:[^,^\"^\'^{^}]*null/','',$_params);
//        $resultObject= $this->vpost($_url,$data); //输出目标地址源码
        return $resultObject;
    }
//    public function JsonToencode($data){
//        $_url=$this->url.'/paymentapi/paymentApi!listPaymentFlow.action';
//        $data=preg_replace('/[^,^\"^\'^{^}]*[,]*"[^,^\"^\'^{^}]*"[^,^\"^\'^{^}]*:[^,^\"^\'^{^}]*null/','',$data);
//        $resultObject= $this->vpost($_url,$data); //输出目标地址源码
//        return $resultObject;
//    }

    /**1.5结算列表 订单交易记录列表**/
    public function Trading4OrderApi($_requestObject, $_orderID)
    {
        $_url=$this->url.'/paymentapi/paymentApi!listPaymentTrading4Order.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderID"=> $_orderID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**1.6结算列表 支付初始化**/
    public function Request4PaymentApi($_requestObject, $_tradingID)
    {
        $_url=$this->url.'/paymentapi/paymentApi!assembleRequest4Payment.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"tradingID"=> $_tradingID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**1.6.2结算列表 提交支付**/
    public function addPaymentApi($_requestObject, $_paymentRequest)
    {
        $_url=$this->url.'/paymentapi/paymentApi!addPayment.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"paymentRequest"=> $_paymentRequest));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /**1.6.3.1结算列表 转账初始化**/
    public function Request4TransferApi($_requestObject, $_tradingID)
    {
        $_url=$this->url.'/paymentapi/paymentApi!assembleRequest4Transfer.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"tradingID"=> $_tradingID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**1.6.3.2结算列表 提现信息**/
    public function drawPaymentApi($_requestObject, $_paymentRequest)
    {
        $_url=$this->url.'/paymentapi/paymentApi!addWithdraw.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"paymentRequest"=> $_paymentRequest));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**1.6.4结算列表 转账信息**/
    public function transferPaymentApi($_requestObject, $_paymentRequest)
    {
        $_url=$this->url.'/paymentapi/paymentApi!addTransfer.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"paymentRequest"=> $_paymentRequest));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*2.4结算 充值接口*/
    public  function paymentaddRecharge($_requestObject,$_paymentRequest)
    {
        $_url=$this->url.'/paymentapi/paymentApi!addRecharge.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"paymentRequest"=>$_paymentRequest));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**1.6.7结算列表 结汇信息**/
    public function exchangePaymentApi($_requestObject, $_paymentRequest)
    {
        $_url=$this->url.'/paymentapi/paymentApi!addExchange.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"paymentRequest"=> $_paymentRequest));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**1.7结算列表 初始化支付密码**/
    public function paymentinitPasswordApi($_requestObject, $_password)
    {
        $_url=$this->url.'/paymentapi/paymentApi!initPassword.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"password"=> $_password));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /**1.8结算列表 修改支付密码**/
    public function paymentchangePasswordApi($_requestObject,$_oldPwd,$_password)
    {
        $_url=$this->url.'/paymentapi/paymentApi!changePassword.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"loginPwd"=>$_oldPwd,"password"=> $_password));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /**1.9结算列表 忘记支付密码**/
    public function paymentforgotPasswordApi($_requestObject)
    {
        $_url=$this->url.'/paymentapi/paymentApi!forgotPassword.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /**2.0结算列表 根据authcode修改支付密码**/
    public function paymentchangePasswordByAuthCodeApi($_requestObject, $_authCode,$_password)
    {
        $_url=$this->url.'/paymentapi/paymentApi!changePasswordByAuthCode.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"authCode"=>$_authCode,"password"=> $_password));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /**2.1结算列表 取当前登录用户的支付用户信息**/
    public function paymentgetAccountUserApi($_requestObject)
    {
        $_url=$this->url.'/paymentapi/paymentApi!getAccountUser.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /*2.2结算 获取账户余额接口*/
    public  function paymentgetAccountBal($_requestObject)
    {
        $_url=$this->url.'/paymentapi/paymentApi!getAccountBal.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /*2.3结算 取汇款银行信息接口*/
    public function paymentgetRechargeBank($_requestObject) {
        $_url = $this->url . '/paymentapi/paymentApi!getRechargeBank.action';
        $_params = json_encode(array("requestObject" => $_requestObject));
        $resultObject = $this->vpost($_url, $_params); //输出目标地址源码
        return $resultObject;
    }

    /*2.2结算 流水账汇入 汇出金额统计*/
    public function getPaymentFlowTotal( $_requestObject, $_queryParams, $_querySorts,$_keyword, $_skip, $_limit,$_startDate,$_endDate,$_lowerAmount,$_upperAmount,$_tradingStatus,$_tradingType,$_transNo)
    {
        // 如果调用java平台的hessian服务 需要指定你传递参数的类型,特别是整形和字符串.
        $_url=$this->url.'/paymentapi/paymentApi!getPaymentFlowTotal.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"queryParams"=> $_queryParams,"querySorts"=> $_querySorts,"keyword"=> $_keyword,"skip"=> $_skip,"limit"=> $_limit,"startDate"=> $_startDate,"endDate"=> $_endDate,"lowerAmount"=> $_lowerAmount,"upperAmount"=> $_upperAmount,"tradingStatus"=> $_tradingStatus,"tradingType"=> $_tradingType,"transNo"=> $_transNo));
        $resultObject= $this->vpost($_url,$_params);
        //    $resultObject=$this->JsonToencode($_params);
//        $data=preg_replace('/[^,^\"^\'^{^}]*[,]*"[^,^\"^\'^{^}]*"[^,^\"^\'^{^}]*:[^,^\"^\'^{^}]*null/','',$_params);
//        $resultObject= $this->vpost($_url,$data); //输出目标地址源码
        return $resultObject;
    }

    /*3.1金融模块接口 还款总额(按天分组 时间轴列表)  OLD 老版本*/
    public function  listRepaymentPlan($_requestObject){
        $_url=$this->url.'/loanapi/loanApi!listRepaymentPlan.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*3.2金融模块接口 时间轴 时间筛选详情 OLD 老版本*/
    public function   listLoanByRepaymentDate($_requestObject,$_repaymentDate){
        $_url=$this->url.'/loanapi/loanApi!listLoanByRepaymentDate.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"repaymentDate"=>$_repaymentDate));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*3.6金融模块接口 金融项目列表接口 OLD 老版本*/
    public function   listLoanCredit($_requestObject,$_queryParams,$_querySorts,$_keyword,$_skip,$_limit){
        $_url=$this->url.'/loanapi/loanApi!listLoanCredit.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"orderStatus"=> $_queryParams,"querySorts"=> $_querySorts,"keyword"=> $_keyword,"skip"=> $_skip,"limit"=> $_limit));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /*3.11金融模块接口 还款总额(按天分组 时间轴列表) */
    public function  listRepaymentPlanGroupByDay($_requestObject,$_crnCode){
        $_url=$this->url.'/factoringapi/factoringApi!listRepaymentPlanGroupByDay.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"crnCode"=>$_crnCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*3.12金融模块接口 还款总额(按天分组 时间轴列表) */
    public function  listRepaymentPlanByDate($_requestObject,$_repaymentDate,$_crnCode){
        $_url=$this->url.'/factoringapi/factoringApi!listRepaymentPlanByDate.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"repaymentDate"=>$_repaymentDate,"crnCode"=>$_crnCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }


    /*3.3金融模块接口 获取本月还款额/总应还款额 */
    public function   getRepaymentTotalAmount($_requestObject){
        $_url=$this->url.'/factoringapi/factoringApi!getRepaymentTotalAmount.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*3.3.1金融模块接口 金融项目列表 */
    /*参数说明
    requestObject	RequestObject	接口请求约定对象，必填
    queryParams	Map<String, String>	查询条件
    querySorts	LinkedHashMap<String, String>	排序条件
    keyword	String	模糊查询关键词
    skip	int	跳过记录数
    limit	int	每页记录数
    factoringStatus	String	状态01：待审核、11：待放款、12：待还款、04：不通过、05：完成
    waitConfirmed	boolean	待确权
    waitPayServiceCharge	boolean	待付服务费
    factoringNo	String	项目编号
    orderNo	String	订单编号
    crnCode	String	货币
    startDate	String	开始时间
    endDate	String	结束时间
    lowerAmount	String	金额范围
    upperAmount	String	金额范围
    creditor	boolean	债权方
    debtor	boolean	债务方*/
    public function   listFactoring($_requestObject,
                                    $_queryParams=null,
                                    $_querySorts=null,
                                    $_keyword=null,$_skip,$_limit,$_factoringStatus,$_waitConfirmed=null,
                                    $_waitPayServiceCharge=null,$_factoringNo,$_orderNo,$_crnCode,
                                    $_startDate=null,
                                    $_endDate=null,
                                    $_lowerAmount=null,
                                    $_upperAmount=null,$_creditor=null,$_debtor=null){
        $_url=$this->url.'/factoringapi/factoringApi!listFactoring.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,
                                     "queryParams"=>$_queryParams,
                                     "querySorts"=>$_querySorts,
                                     "keyword"=>$_keyword,
                                     "skip"=>$_skip,
                                     "limit"=>$_limit,
                                     "factoringStatus"=>$_factoringStatus,
                                     "waitConfirmed"=>$_waitConfirmed,
                                     "waitPayServiceCharge"=>$_waitPayServiceCharge,
                                     "factoringNo"=>$_factoringNo,
                                     "orderNo"=>$_orderNo,
                                     "crnCode"=>$_crnCode,
                                     "startDate"=>$_startDate,
                                     "endDate"=>$_endDate,
                                     "lowerAmount"=>$_lowerAmount,
                                     "upperAmount"=>$_upperAmount,
                                     "creditor"=>$_creditor,
                                     "debtor"=>$_debtor));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*3.4金融模块接口 金融项目详情 */
    public function   getFactoringView($_requestObject,$_factoringID){
        $_url=$this->url.'/factoringapi/factoringApi!getFactoringView.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"factoringID"=>$_factoringID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }


    /*3.5金融模块接口  确权*/
    public function   doConfrimLoan($_requestObject,$_factoringID,$_attachList){
        $_url=$this->url.'/factoringapi/factoringApi!doConfrimFactoring.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"factoringID"=>$_factoringID,"attachList"=>$_attachList));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*3.6金融模块接口  还款页详情*/
    public function   assembleRequest($_requestObject,$_loanID){
        $_url=$this->url.'/factoringapi/factoringApi!assembleRequest.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"loanID"=>$_loanID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*3.6.1金融模块接口   支付费用/利息*/
    public function   assembleRequest4Fee($_requestObject,$_loanID){
        $_url=$this->url.'/factoringapi/factoringApi!assembleRequest4Fee.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"loanID"=>$_loanID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*3.7金融模块接口   支付 */
    public function   doPayment($_requestObject,$_recordID,$_paymentRequest){
        $_url=$this->url.'/factoringapi/factoringApi!doPayment.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"recordID"=>$_recordID,"paymentRequest"=>$_paymentRequest));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*3.8金融模块接口   渠道列表 */
    public function   listFinancingChannel($_requestObject,
                                           $_queryParams,
                                           $_querySorts,
                                           $_keyword,
                                           $_skip,
                                           $_limit,
                                           $_loanNo,
                                           $_crnCode,
                                           $_startDate,
                                           $_endDate,
                                           $_lowerAmount,
                                           $_upperAmount

    ){
        $_url=$this->url.'/factoringapi/factoringApi!listFinancingChannel.action';
        $_params =json_encode(array( "requestObject"=>$_requestObject,
                                     "queryParams"=>$_queryParams,
                                     "querySorts"=>$_querySorts,
                                     "keyword"=>$_keyword,
                                     "skip"=>$_skip,
                                     "limit"=>$_limit,
                                     "loanNo"=>$_loanNo,
                                     "crnCode"=>$_crnCode,
                                     "startDate"=>$_startDate,
                                     "endDate"=>$_endDate,
                                     "lowerAmount"=>$_lowerAmount,
                                     "upperAmount"=>$_upperAmount,)
                            );
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*3.8.1金融模块接口  折现图  收益统计(按月分组) */
    public function   countGains($_requestObject,$_year,$_crnCode){
        $_url=$this->url.'/factoringapi/factoringApi!countGains.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"year"=>$_year,"crnCode"=>$_crnCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }

    /*3.8.2金融模块接口   统计累计收益/累计投资金额/平均收益率 */
    public function   countAccumulative($_requestObject,$crnCode){
        $_url=$this->url.'/factoringapi/factoringApi!countAccumulative.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,'crnCode'=>$crnCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*3.8.2金融模块接口   渠道详情 */
    public function   getFinancingChannelView($_requestObject,$_channelID){
        $_url=$this->url.'/factoringapi/factoringApi!getFinancingChannelView.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"channelID"=>$_channelID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*4.1 企业实名认证 申请*/
    public function   doEntRealNameAuth($_requestObject,$_account){
        $_url=$this->url.'/accountapi/accountApi!doEntRealNameAuth.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"account"=>$_account));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /* 企业实名认证 银行帐户信息 */
    public function doEntRealNameToPay($_requestObject, $acctName, $auth_acctNo, $bankName, $proviceName, $cityName, $subbranchName){
        $_url=$this->url.'/accountapi/accountApi!doEntRealNameToPay.action';
        $_params =json_encode(array("requestObject"=>$_requestObject, "acctName" => $acctName, "acctNo" => $auth_acctNo, "bankName" => $bankName, "proviceName" => $proviceName, "cityName" => $cityName, "subbranchName" => $subbranchName));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    
    /*4.12 企业重新实名认证 申请*/
    public function   doEntRealNameReAuth($_requestObject,$_account){
        $_url=$this->url.'/accountapi/accountApi!doEntRealNameReAuth.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"account"=>$_account));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*4.2 企业实名认证 确认*/
    public function   doEntRealNameVerify($_requestObject,$_verifyCode){
        $_url=$this->url.'/accountapi/accountApi!doEntRealNameVerify.action';
        $_params =json_encode(array("requestObject" => $_requestObject, "verifyAmount" => $_verifyCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*4.3 个人实名认证 确认*/
    public function   doPersRealNameAuth($_requestObject){
        $_url=$this->url.'/contactapi/contactApi!doPersRealNameAuth.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*5.1 业务合同列表*/
    public function listBizContract($_requestObject,$bizType,$bizID){

        $_url=$this->url.'/contractapi/contractApi!listBizContract.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"bizType"=>$bizType,"bizID"=>$bizID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*5.2 网签 初始化网签页面*/
    public function initContractSignView($_requestObject,$contractID,$returnUrl){

        $_url=$this->url.'/contractapi/contractApi!initContractSignView.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"contractID"=>$contractID,"returnUrl"=>$returnUrl));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /* 网签 获取验证手机号 */
    public function getSignMobile($_requestObject) {
        $_url=$this->url.'/contractapi/contractApi!getSignMobile.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /* 网签 获取短信验证码 */
    public function sendSignAuthCode($_requestObject) {
        $_url=$this->url.'/contractapi/contractApi!sendSignAuthCode.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /* 网签 签署 */
    public function doSignPDF($_requestObject, $contractID, $authCode) {
        $_url=$this->url.'/contractapi/contractApi!doSignPDF.action';
        $_params =json_encode(array("requestObject"=>$_requestObject, "contractID" => $contractID, "authCode" => $authCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    
    /* 网签 个人 获取验证手机号 */
    public function getPersonSignMobileApi($_requestObject) {
        $_url=$this->url.'/contractapi/contractApi!getPersSignMobile.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /* 网签 个人 获取短信验证码 */
    public function sendPersonSignAuthCodeApi($_requestObject) {
        $_url=$this->url.'/contractapi/contractApi!sendPersSignAuthCode.action';
        $_params =json_encode(array("requestObject"=>$_requestObject));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /* 网签 个人 签署 */
    public function doPersonSignPDFApi($_requestObject, $contractID, $authCode) {
        $_url=$this->url.'/contractapi/contractApi!doPersSignPDF.action';
        $_params =json_encode(array("requestObject"=>$_requestObject, "contractID" => $contractID, "authCode" => $authCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    
    /*5.3 合同PDF文件下载URL*/
    public function getPdfUrl($_requestObject,$contractID){

        $_url=$this->url.'/contractapi/contractApi!getPdfUrl.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"contractID"=>$contractID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    /*5.4 合同图片文件下载URL*/
    public function getImgUrl($_requestObject,$contractID){
        $_url=$this->url.'/contractapi/contractApi!getImgUrl.action';
        $_params =json_encode(array("requestObject"=>$_requestObject,"contractID"=>$contractID));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
    
    /* 数据字典模糊查找URL*/
    public function dataDictFuzzyQuery($dataDictCode, $keyword, $valuePCode) {
        $_url=$this->url.'/commonapi/commonApi!dataDictFuzzyQuery.action';
        $_params =json_encode(array("dataDictCode"=>$dataDictCode, "keyword"=>$keyword, "valuePCode"=>$valuePCode));
        $resultObject= $this->vpost($_url,$_params); //输出目标地址源码
        return $resultObject;
    }
}
?>