<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/14
 * Time: 14:57
 */
class Kyapi_Model_Hdb {
    /**
     * hession请求资源
     *
     * @public
     */
    public $url;
    public $option;

    /**
     * requestObject请求资源
     *
     * @public
     */
    public $lang;
    public $userID;
    public $sessionID;
    public $client;
    public $timeZone;
    public $extData;
    public $User_Agent;
    /**
     * account请求资源
     *
     * @public
     */
    public $accountName;	// 公司名称
    public $regdAddress;	// 注册地址
    public $name;			// 联系人名称
    public $phone;			// 电话
    public $mobilePhone;	// 手机
    public $ecommloginname;
    public $ecommpasswsd;

    public $crnCode;		// 货币类型
    public $accountType;	// 公司类型

    public $accountID;

    public $proxy;

    public function accountRH($url)
    {
        $options = new HessianOptions();
        $options->typeMap['requestObject'] = 'com.jtec.etrade.api.dto.RequestObject';
        $options->typeMap['contact'] = 'com.jtec.jump.contact.dto.Contact';
        $options->typeMap['queryParams'] = 'java.util.Map';
        $options->typeMap['account'] = 'com.jtec.jump.account.dto.Account';
        $options->typeMap['accountPk'] = 'com.jtec.jump.account.dto.AccountPk';
        $proxy = new HessianClient($url, $this->options);
        return $proxy;
    }

}