<?php
/**
 * 消息类
 * @author: rainame
 * @time: 2015-07-10
 *
 */

class REST_Message
{
    // System Error 100xx
    const SYSTEM_ERROR              = 10001;
    const SYSTEM_BUSY               = 10002;
    const UNKNOWED_MOUDLE_NAME      = 10003;
    const INVALID_USER              = 10004;
    const ILLEGAL_REQUEST           = 10005;
    const CLASS_NOT_EXIST           = 10006;
    const THIS_IS_TEST              = 10007;
    const INTERFACE_CLOSE           = 20008;

    // Service Error 200xx
    const USER_NOT_EXIST            = 20001;
    const GOODS_NOT_EXIST           = 20002;
    const GOODS_PARAM_INVALID       = 20003;
    const PROVINCE_NOT_EXIST        = 20004;
    const CITY_NOT_EXIST            = 20005;
    const DISTRICT_NOT_EXIST        = 20006;
    const TOWN_NOT_EXIST            = 20007;
    const PARAMETER_IS_MISS         = 20008;
    const NEED_PHONE_OR_MOBILE      = 20009;
    const NEED_INVOICE_TITLE        = 20010;
    const NEED_INVOICE_INFO         = 20011;
    const SHIIPPING_NOT_EXIST       = 20012;
    const PAYMENT_NOT_EXIST         = 20013;
    const SUPPLIER_NOT_EXIST        = 20014;
    const STOCK_NOT_ENOUGH          = 20015;
    const PREFERENCE_ONLY_ONE       = 20016;
    const ORDER_NOT_EXIST           = 20017;
    const ORDER_CANNOT_CANCEL       = 20018;
    const ORDER_GOODS_NOT_EXIST     = 20019;
    const MOBILE_IS_EXIST           = 20020;
    const SHOP_NAME_IS_EXIST        = 20021;
    const USER_NAME_IS_EXIST        = 20022;
    const ADDRESS_NOT_EXIST         = 20023;
    const NEED_USER_ADDRESS         = 20024;
    const CONSIGNEE_NOT_EXIST       = 20025;
    const ZIPCODE_NOT_EXIST         = 20026;
    const NEED_IDCARD_OR_PHONE      = 20027;
    const ORDER_STATUS_ERROR        = 20028;
    const GOODS_IS_EXIST            = 20029;
    const GOODS_NAME_IS_EXIST       = 20030;
    const CATE_NOT_EXIST            = 20031;
    const BRAND_NOT_EXIST           = 20032;
    const GOODS_MUL_ATTR_EMPTY      = 20033;
    const GOODS_SN_NOT_EXIST        = 20034;
    const ORDER_SN_NOT_EXIST        = 20035;
    const SOTCK_IS_EXIST            = 20036;
    const SOTCK_NAME_IS_EXIST       = 20037;
    const ORDER_NOT_PAY_EXIST       = 20038;
    const SOTCK_NOT_EXIST           = 20039;
    const STOCK_SN_IS_EXIST         = 20040;
    const STOCK_BARCODE_IS_EXIST    = 20041;
    const STOCK_FETCH_PARAM_EMPTY   = 20042;
    const VENDOR_CODE_IS_EXIST      = 20043;
    const GOODS_SN_IS_EXIST         = 20044;
    const GOODS_AMOUNT_LIMIT        = 20045;
    const GOODS_WAREHOUSE_MUST_ONE  = 20046;
    const TAX_RATE_GT_ONE           = 20047;
    const REIMBURSE_IS_EXIST         = 20048;
    const GOODS_STOCK_SN_DIFF       = 20049;
    const ORDER_NOT_PAY             = 20050;
    const ORDER_NOT_SHIP            = 20051;
    const ORDER_IS_PAY              = 20052;
    const ORDER_IS_SHIP             = 20053;
    const USER_REALNAME_NOT_EXIST   = 20054;
    const USER_IDCARDNO_NOT_EXIST   = 20055;
    const ORDER_PAYMENT_NOT_EXIST   = 20056;
    const BAOGUAN_APPLY_IS_SEND     = 20057;
    const REIMBURSE_NOT_EXIST       = 20058;
    const GOODS_NUM_MIN_QUANTITY    = 20059;
    const BAOGUAN_RESPONSE_IS_EMPTY = 20060;
    const BOTH_REALNAME_IDCARD      = 20061;
    const IDCARD_IS_EXIST           = 20062;   
    const BRAND_ID_IS_EXIST         = 20063;
    const BRAND_NAME_IS_EXIST       = 20064;

    // Message
    static protected $_message = array(
        // System Error 100xx
        10001 => "系统错误",
        10002 => "系统繁忙",
        10003 => "模块未定义",
        10004 => "非法用户",
        10005 => "非法请求",
        10006 => "类[%s]不存在",
        10007 => "这是一个测试",
        10008 => "接口关闭",
        // Service Error 200xx
        20001 => "用户不存在",
        20002 => "商品不存在",
        20003 => "商品参数非法",
        20004 => "省份不存在",
        20005 => "城市不存在",
        20006 => "地区不存在",
        20007 => "城镇不存在",
        20008 => "参数[%s]缺失或异常",
        20009 => "需要提供固定电话或手机号码",
        20010 => "需要提供发票抬头",
        20011 => "需要提供发票信息",
        20012 => "配送方式不存在",
        20013 => "支付方式不存在",
        20014 => "供应商不存在",
        20015 => "商品库存不足",
        20016 => "只能选择一种优惠方式",
        20017 => "订单不存在",
        20018 => "此订单不能取消",
        20019 => "订单没有任何商品可供发货",
        20020 => "此手机号已被注册",
        20021 => "此供应商名称已存在",
        20022 => "此用户名称已被注册",
        20023 => '收货地址不存在',
        20024 => '用户收货地址需完善',
        20025 => '收货人不存在',
        20026 => '邮政编码不存在',
        20027 => '需要提供身份证号或手机号码',
        20028 => "订单状态错误",
        20029 => "商品已存在",
        20030 => "商品名称已存在",
        20031 => "商品分类不存在",
        20032 => "商品品牌不存在",
        20033 => "商品多属性参数异常",
        20034 => "商品编码不存在",
        20035 => "订单编码不存在",
        20036 => "商品库存记录已存在",
        20037 => "库存规格名称已存在",
        20038 => "存在未付款订单",
        20039 => "商品库存记录不存在",
        20040 => "商品库存SKU码已存在",
        20041 => "商品库存条形码已存在",
        20042 => "商品库存查询参数异常",
        20043 => "供应商标识已经存在",
        20044 => "商品SN已存在",
        20045 => "多个商品的总额需在1000元以内",
        20046 => "同一订单不允许存在多个发货仓库的商品",
        20047 => "税率值大于1",
        20048 => "退款申请已提交",
        20049 => "商品SN与规格SN不匹配",
        20050 => "订单未支付",
        20051 => "订单未发货",
        20052 => "订单已支付",
        20053 => "订单已发货",
        20054 => "用户真实姓名不存在",
        20055 => "用户身份证号不存在",
        20056 => "订单支付方式不存在",
        20057 => "报关申请已发送",
        20058 => "退款申请不存在",
        20059 => "商品数量少于最低购买数量[%s]",
        20060 => "报关响应报文为空",
        20061 => "需同时提供报关的姓名及身份证号",
        20062 => "此身份证号码已被注册",
        20063 => "品牌ID冲突",
        20064 => "品牌名称已存在"
    );

    public static function getMessage($code)
    {
        return isset(self::$_message[$code]) ? self::$_message[$code] : '';
    }
}