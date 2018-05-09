<?php
return array(
//系统配置
    'hello_world'=>'你好世界',
    'document'=>'文档',
    'get_lg'=>'en_US',    //语言编码
    'language'=>'English',//语言
    'englich'=>'English',//语言
    'chinese'=>'Chinese',//语言
    //订单顶部进度说明





    'going000'=>'此订单状态暂未生成',
    //1.买家确认订单（同卖家确认订单）
    'going001'=>'请确认订单信息无误后，点击确认按钮，我们的工作人员将继续为您跟进订单后续服务',

    //2.买家签订合同（同卖家确认订单） b->买家 v->卖家
    'going002_01b'=>'卖家确认订单中，请耐心等候',
    'going002_01v'=>'买家确认订单中，请耐心等候',
    'going002_02'=>'等待快移交易平台确认订单',
    'going002_03'=>'请服务委托书和下载合同范本，并加盖公章后分别上传至平台以便我们尽快激活该订单',

      //3.卖家备货
    'going003_01'=>'买家正在进行合同签订，请耐心等候',
    'going003_02'=>'快移交易平台正在处理双方合同，请耐心等候',
    'going003_03'=>'快移交易平台已激活订单，如您已备货完成，可点击完成按钮，完成备货操作',

     //4.买家验货
    'going004_01'=>'卖家正在进行合同签订，请耐心等候',
    'going004_02'=>'快移交易平台正在处理双方合同，请耐心等候',
    'going004_03'=>'快移交易平台已激活订单，订单已进入验货阶段。可点击完成按钮，完成备货操作',

      //5.卖家发货
    'going005_01'=>'买家还没确认验货，请耐心等候。',
    'going005_02'=>'订单已进入待发货阶段，请按实际情况点击完成按钮',

     //6.买家收货
    'going006_01'=>'卖家正在备货中，请耐心等候',
    'going006_02'=>'请耐心等候卖家发货',
    'going006_03'=>'卖家已经发货，订单已进入待收货阶段。如您已收到货，请点击收货按钮',
    //7.结算 b->买家 v->卖家
    'going007b'=>'订单已经进入结算状态，请到结算分页或结算中心处理交易付款。',
    'going007v'=>'订单已经进入结算状态，请到结算分页或结算中心等待买方付款。',
    //8.完成 b->买家 v->卖家
    'going008b'=>'完成。',
    'going008v'=>'订单已经进入结算状态，请到结算分页或结算中心等待买方付款。',


    //头部主导航
    'nav_login' => '登录',
    'nav_Mr' => '先生',
    'nav_Miss' => '小姐',
    'nav_user' => '用户中心',
    'register' => '注册',
    'nav_index' => '首页',
    'nav_baseservice' => '基础服务',
    'nav_finservice' => '金融服务',
    'nav_channel' => '金融项目',
    'nav_help' => '帮助中心',
    'nav_about' => '关于我们',
    'nav_exit' => '退出',
    //补充部分
    'mustTT' => '必填',
    'madeTT' =>'操作',
    'saleING'=>'销售',
    'NoRegister'=>'暂未注册',
    'NoData'=>'无',

    //用户角色
    'CompUser'=>'普通用户',
    'CompAdmin'=>'公司管理员',
    'SOAdmin'=>'订舱单管理员',
    'TOAdmin'=>'派车单管理员',
    'DeclarationAdmin'=>'报关单管理员',
    'CompPublicInfoAdmin'=>'公司公告信息管理员',
    'CompSettleAdmin'=>'结算中心管理员',
    'CompOrderAdmin'=>'订单管理员',
    'CompClerk'=>'跟单员',
    'CompProductAdmin'=>'商品管理员',
    'CompPartnerAdmin'=>'合作伙伴管理员',


    //常用状态
    'last' => '上一步',
    'next' => '下一步',
    'saveDRAFT' => '保存草稿',
    'yes' => '是',
    'no' => '否',
    'ruler' => '尺',
    'entries' => '个',
    'disable' => '禁用',
    'normal' => '正常',
    'check' => '支票',
    'confirm' => '确定',
    'clearing' => '清空',
    'return' => '返回',
    'returnReg' => '立即注册',
    'add' => '新增',
    'cancel' => '取消',
    'copy' => '复制',
    'accept'=>'接收邀请',
    'reject'=>'拒绝邀请',
    'edit' => '编辑',
    'save' => '保存',
    'submit'=>'提交',
    'submitCHECK' => '提交审核',
    'print' => '打印',
    'view' => '查看',
    'list' => '列表',
    'info' => '详细',
    'de_null' => '不能为空',
    'de_format'=>'格式不正确',
    'de_nozore'=>'不能为0，请重新输入',
    'de_anything'=>'请填写任意一项',
    'mat_psd'=>'密码最少8位，包含大小字母数字',
    'is_psd'=>'两次输入的密码不一致',
    'de_use'=>'真遗憾，用户名已经被使用',
    'en_use'=>'恭喜你，用户名可以使用',
    'xieyi'=>'合作协议',
    'agree'=>'同意',

    //注册页表单注释文字
    'is_name'=>"请填写真实姓名",
    'is_emlog'=>"zhangsan@etradFast.com",
    'is_passwd'=>"请设置8-30位，包含数字、大小写字母的密码",
    'is_rpasswd'=>"请再次输入密码",
    'is_phone'=>"请填写有效的移动电话号码或固话号码",
    'is_tel'=>"请填写有效的固定电话",
    'is_comname'=>"请填写营业执照的企业名称",
    'is_regadress'=>"请填写公司的联系地址",
    'ps_email'=>"admin@eTradeFast.com",
    'service_contract'=>"《快移交易云平台服务协议》",
    'check_service_contract'=>"请阅读并同意相关服务条款",
    //登录/注册/表单/合作伙伴/我的卖家/买家/通讯录
    'alreadyRegistered' => '已经注册，现在就',
    'companyRegister' => '企业注册',
    'personRegister' => '个人注册',
    'username' => '用户名',
    'password' => '密码',
    'elogname'=>'登录名',
    'login_s'=>'登录',
    'login'=>'登录 LOGIN',
    'forgetPSD' => '忘记密码',
    'longPSD' => '原始密码',
    'userREG' => '用户注册',
    'userRegister' => '免费注册',
    'userTT'=>'用户资料',
    'confirmPSD' => '确认密码',
    'newPSD' => '新 密 码',
    'loginPSD' => '登录密码',
    'companyFU' => '企业全称',
    'regd_company_name' => '企业名称',
    'regaddress'=>'企业地址',
    'companyEM'=>'企业邮箱',
    'address' => '地址',
    'contacts' => '联系人',
    'contactsID' => '联系人ID',
    'callMD' => '联系方式',
    'baseINF' => '员工资料',
    'basicInfo' => '基本资料',
    'viewInfo' => '查看信息',
    'name' => '姓名',
    'myphone'=>'个人电话',
    'title' => '称谓',
    'sex'=>'性别',
    'female'=>'女',
    'male'=>'男',
    'birthdate'=>'生日',
    'job' => '公司职务',
    'myjob'=>'职务',
    'HPname'=>'助理姓名',
    'HPphone'=>'助理电话',
    'orderContact'=>'订单联系人',
    'is_orderContact'=>'是否订单联系人',
    'set_default_bank'=>'设为默认账户',
    'no_default_bank'=>'禁止设置账户',
    'Df_Contact'=>'默认联系人',
    'division' => '部门',
    'userP' => '用户状态',
    'userPT'=>'员工状态',
    'userROLE' => '用户角色',
    'contactINF' => '通讯资料',
    'email' => '电子邮箱',
    'fax' => '传真',
    'maillADD' => '邮寄地址',
    'country' => '国家',
    'Region' => '地区',
    'province' => '省份',
    'city' => '城市',
    'street' => '街道',
    'postcode' => '邮政编码',
    'telphone' => '固定电话',
    'call' => '联系电话',
    'mobile' => '移动电话',
    'mobile_phone' => '手机',
    'phone' => '联系电话',
    'company_phone' => '公司电话',
    'company_fax' => '公司传真',
    'company_website' => '公司网址',
    'company_email' => '公司电子邮箱',
    'company_business_license' => '工商注册信息',
    'INPkw_ac'=>'可输入姓名、移动电话、电子邮箱等关键字',
    'INPkw_bk'=>'可输入账户名称、银行名称、帐号等关键字',
    'INPkw_par_buyer'=>'可输入买家名称',
    'INPkw_par_vendor'=>'可输入卖家名称',
    'INPfd_par'=>'可通过公司快移码、邓白氏搜索新合作伙伴',
    'INPkw_goodes'=>'可输入名称、HSCode、型号、品牌等关键字',
    'INPkw_order_xs'=>'可输入订单号、买家名称',
    'INPkw_order_cg'=>'可输入订单号、卖家名称',

    'CHK_account'=>'呈现全部状态',

    //友情链接
    'about_ef' => '关于快移',
    'marketC' => '营销中心',
    'contactUS' => '联系我们',
    'contactSV' => '联系客服',
    'joinUS' => '诚征英才',
    'siteMAP' => '网站地图',

    //底部链接
    'findJOB' => '查找职位',
    'about' => '关于',
    'privacy' => '隐私',
    'lawITEM' => '法律条款',

    //用户中心左侧导航
    'order' => '订单',
    'orderIN' => '订单中心',
    'orderREQ' => '退税申请',
    'orderSALLE' => '销售订单',
    'orderBUY' => '采购订单',

    'partners' => '合作伙伴',
    'partners_buyers' => '我的买家',
    'partners_vendors' => '我的卖家',

    'cooperationPTE' => '合作伙伴',
    'cooperationC' => '我的买家',
    'cooperationB' => '我的卖家',
    'cooperationS' => '服务提供商',
    'cooperationCALL' => '通讯录',

    'sendBuyer' => '邀请为买家',
    'sendVendor' => '邀请为卖家',

    'goods' => '商品管理',
    'goods_4_index' => '商品',
    'goodsME' => '我的商品',
    'goodsINF'=>'商品详情',
    'goodsBUY' => '采购的商品',


    'contract' => '合同',
    'contractSALL' => '销售合同',
    'contractBUY' => '采购合同',


    'settle' => '结算',
    'overview' => '总览',
    'tradeLIST' => '交易记录',


    // 个人信息右侧title
    'myINF'=>'个人信息',
    'myPHOTO'=>'头像上传',
    'editPWD'=>'修改密码',
    'myNO'=>'账户安全',
    'WebINF'=>'平台账号信息',

    'basicINF' => '基本信息',
    'company_info' => '公司信息',
    'personNO' => '员工信息',
    'bankNO' => '银行账号',
    'file' => '文档',



    // 附件部分
    'files' => '文件',
    'filename' => '文件名称',
    'finding' => '查找目标',
    'find'=>'查找',
    'Nodata'=>'暂无数据',

    // 个人信息公司模块
    'companyEWM'=>'查看公司快移码',
    'companyMNG' => '公司管理',
    'comNAME' => '公司名称',
    'enNAME' => '英文名称',
    'enAdress' => '英文地址',
    'company_legal_form' => '公司形式',
    'comlegals' => '形式',
    'defaultCCY' => '默认货币',
    'dLANG' => '默认语言',
    'LANGCODE'=>'语言',
    'timezone'=>'时区',
    'industry' => '行业',
    'ONcountry'=>'所在国家',
    'ONlimit'=>'合作额度',
    'partner_regdCountryCode' => '所在国家',
    'all' => '所有人',
    'company_incorporation_date' => '成立日期',
    'type' => '类型',
    'type1' => '01客户',
    'type2' => '02合作伙伴',
    'type3' => '03供应商',
    'website' => '网址',
    'ICBNO' => '工商注册号',
    'ECC' => '企业海关编码',
    'businessLicenseNo' => '统一社会信用代码',
    'TICNO' => '纳税人企业名称',
    'company_profile' => '公司资料档案',
    'UPMBLS' => '请扫描营业执照并上传',
    'UPICBNO' => '请扫描工商注册证并上传',
    'UPCOMFJ' => '可上传公司营业执照、生产许可证、商标等',

    'GOODSLS' => '商品相关图片资料',
    'UPGOODSLS' => '可上传商品外观图、包装图、生产许可证等',
    'UPBank' => '相关银行资料',
    // 个人信息人员与账号模块（有些字段已经存在 下面模块不再进行说明）
    'state' => '状态',
    'ACTcheck'=>'验证',
    'valids' => '启用',
    // 产品&订单中心状态
    'valid' => '有效',
    'checkP' => '审核中',
    'checkIN' => '审核中',
    'checkNO' => '审核未通过',
    'checkR' => '待确认',
    'nopass' => '不通过',
    'history'=>'历史',
    'draft' => '草稿',
    'void' => '无效',

    // 产品页面
    'productNAME' => '商品名称',
    'productENNAME' => '英文名称',
    'brand' => '品牌',
    'model' => '型号',
    'saleprice' => '销售价格',
    'saletotal' => '销售金额',
    'purprice' => '采购价格',
    'purtotal' => '采购金额',
    'uiprice' => '开票价格',
    'uitotal' => '开票金额',
    'unitB' => '交易单位',
    'unitF' => '法定单位',
    'HSCODE' => 'HSCODE',
    'uprice' => '单价',
    'number' => '数量',
    // 订单商品列表补充
    'orderprice' => '价格',
    'ordertotal' => '金额',
    'quantity' => '件数',

    'RFRT' => '退税率',
    'RFADD' => '增值税率',
    'SBYSU' => '申报要素',
    'productSize'=>'规格尺寸',
    'functionUsage'=>'功能用途',
    'productMaterial'=>'材质',
    'supplierName'=>'供应商',
    'packingMD' => '包装体积',
    'packageTYPE' => '包装类型',
    'packINF' => '包装说明',
    'netWET' => '净重',
    'grossWET' => '毛重',
    'isSNN'=>'是否商检',
    'productionMode'=>'生产方式',
    'image' => '图片',
    'RLCT' => '监管条件',
    // 新增商品页面
    'orderGOODS' => '订单商品',
    'imgGOODS' => '商品图片',






    // 银行账户模块
    'accountNO' => '银行账号',
    'accountTP' => '账号类型',
    'accountFA' => '外汇账户',
    'accountBA' => '基本账户',
    'accountNAME' => '账号名称',
    'acctNO' => '账号',
    'accountBK' => '所属银行',
    'accountBKname' => '银行名称',
    'accountDF' => '默认',
    'accountADR' => '开户行地址',
    'accountSCD' => 'SWIFT CODE',
    'accountBZ' => '备注',

    // 订单状态标题
    'orderSTATUS' => '订单状态',
    'orderADD' => '新增订单',
    'partnerADM' => '合作伙伴管理',
    'partner' => '合作伙伴',
    'partnerINF' => '合作伙伴信息',
    'userINF' => '个人中心',
    // 最近订单进度
    'orderStatus'           => '当前订单进度',
    'orderStatus01'         => '最近更新订单进度',
    'confirming'            => '确认订单',
    'confirmed'             => '确认',
    'signing'               => '签署',
    'sign'                  => '签订合同',
    'signTitle'             => '签署合同',
    'signSuccess'           => '签署成功！',
    'signFail'              => '签署失败，请重新签署！',
    'signAuthType'          => '验证方式',
    'signSendAuthCode'      => '发送验证码',
    'signAuthCodeSending'   => '验证码重发',
    'signInfo'              => '签署合同前需验证身份',
    'signInputAuthCode'     => '请输入短信验证码',
    'noGOODS'               => '未开始',
    'reGOODS'               => '收货',
    'deGOODS'               => '发货',
    'rdGOODS'               => '备货',
    'delivery'              => '物流情况',
    'ckGOODS'               => '验货',
    'calculate'             => '结算中',
    'calculated'            => '结算',
    'finish'                => '完成',
    'finished'              => '已完成',
    'waiting'               => '待处理',
    'orderPS02'             => '当前订单【未生成】',
    'orderPS01'             => '当前订单未开始',
    'deliveryView'          => '收发货详情',
    'deliveryDel'           => '删除本次发货单',
    'deliveryAdd'           => '添加发货记录',
    'deliveryItemListNull'  => '暂无收发货记录',
    'deliveryItemQuantity'  => '商品数量',
    'deliveryBillInfo'      => '开票资料',
    'deliveryBillTitle'     => '查看开票资料前，请先签署供货合同',
    'deliveryBillTips'      => '开票资料及注意事项',
    'deliveryBillTipsBank'  => '* 签署前请选择供应商收款银行帐户',
    'deliveryBillTipsCheck' => '* 新增的银行帐户需等待后台审核，审核时间一般为1个工作日。审核通过后，再次点击开票资料才可选择',
    'signingNow'            => '现在签署',
    'bankAccount'           => '银行帐户',
    'deliveryBillTips_A'    => '若有两个或以上供应商，请点击选项框查看各自开票资料，各供应商须按以下资料开具真实的增值税发票',
    'deliveryBillTips_B'    => '请将开具后的增值税发票原件邮寄至以下地址',
    'deliveryBillTips_C'    => '地址：深圳市罗湖区嘉宾路2034号深化商业大厦1005室',
    'deliveryBillTips_D'    => '收件人：付先生',
    'deliveryBillTips_E'    => '联系电话：0755-83687432',
    'deliveryBillTips_F'    => '客服人员收到增值税发票原件并检验后，会尽快处理后续工作',
    'expressNo'             => '快递单号',



    // 订单信息预览
    'orderVINF' => '订单信息预览',
    'orderID' => '订单ID',
    'orderNo' => '订单号',
    'orderItem' => '商品',
    'orderStatue' => [
        '01' => '有效',
        '02' => '无效'
    ],
    'orderTotal' => '金额',
    'shopPrities' => '订单汇率',
    'Prities' => '订单汇率',
    'orderPrice' => '订单金额',
    'UPdate' => '更新时间',
    'date' => '日期',

    'contartNo' => '合同号码',
    'proxyNo' => '委托书',
    'orderATCH' => '订单附件',
    'delegation' => '盖章委托书',
    'contract_tmp' => '合同范本',
    'contract_seal' => '盖章合同',
    'quality_tmp' => '质量保证函模板',
    'quality_seal' => '质量保证函正本',
    'receipt_confirmation_templ' => '收货确认函模板',
    'receipt_confirmation_formal' => '收货确认函正本',
    'valuationNo' => '计价单',
    'income' => '收入',
    'expend' => '支出',
    'profit' => '利润',
    'rfrtNo' => '退税额',
    'progressING' => '进度说明',
    'stateING' => '当前状态',
    'operatING' => '当前操作',
    // 我有异议
    'objection' => '我有异议»',


    'rated' => '评价',
    'download' => '下载',
    'upload' => '上传',
    'contractUP' => '盖章合同上传',
    'contractUPInfo' => '下载合同并将盖章合同上传。',
    'delegationUP' => '盖章委托书上传',
    'stockUP' => '备货相关附件上传',
    'examineUP' => '验货相关附件上传',
    'qualityUP' => '质量保证函上传',
    'deliverUP' => '发货相关附件上传',
    'receivingUP' => '收货相关附件上传',
    'stockVIEW' => '备货相关附件查看',
    'receivingVIEW' => '收货相关附件查看',
    'deliverVIEW' => '发货相关附件查看',
    'examineVIEW' => '验货相关附件查看',

    'tipQualityNoNull' => '请上传质量保证函正本!',

    'timeCLASS' => '按时间分类',
    'eventCLASS' => '按事件分类',
    'selectROLE' => '选择角色',


    //新增订单模块
    //流程start
    'orderINF01' => '填写订单基本资料',
    'orderINF02' => '添加商品至订单',
    'orderINF03' => '填写报关物流等信息',
    'orderINF04' => '完成下单',
    //流程end
    'unorder' => '取消下单',

    'infoED' => '填写信息',
    'buyers' => '买家',
    'buyerATTN' => '买家联系人',
    'seller' => '卖家',
    'sellerATTN' => '卖家联系人',
    'remitMD' => '结算方式',
    'exportMD' => '出口方式',
    'thisATTN' => '本单联系人',
    'payDD' => '支付期限',
    'payCNY' => '订单货币',
    'payNeed' => '订单要求',
    'payINF' => '订单相关资料上传',
    'hetongUP' => '合同上传',
    'zhiliangUP' => '质量保证函上传',
    'zhiliangDD' => '资料下载',
    'hetongDD' => '附件下载',
    'tupUP' => '图片上传',
    'payJJ' => '计价方式',
    'payPrice' => '价格条款',
    'payCasing' => '包装方式',
    'payGoods' => '商品项目',
    'shopADD' => '点击选择商品',
    'shopNet' => '总净重',
    'shopGross' => '总毛重',
    'shopTotal' => '总金额',

    //城市
    'citypost1' => '起运城市',
    'citypost2' => '卸货城市',
    'citypost3' => '交货城市',
    //港口
    'shippost1' => '起运港',
    'shippost2' => '卸货港',
    'shippost3' => '交货港',


    'customsINF' => '报关信息',
    'moveMD' => '运输方式',
    'POCRC' => '报关口岸',
    'portSHIP' => '起运港',
    'portDSCG' => '卸货港',
    'portDLVY' => '交货港',
    'mabyDATE' => '预计交货日期',

    'isFCL' => '装柜类型',
    'infoFCL' => '装柜数量',

    'logisticSS' => '物流服务',
    'logisticND' => '物流要求',
    'transportation' => '物流',

    'clearance' => '报关',
    'selectCHB' => '指定报关行',
    'nameCHB' => '报关行信息',
    'needCHB' => '报关要求',
    'codeCHB' => '报关行代码',
    'whoCHB' => '报关行名称',
    'logistics' => '报关物流',
    'customs' => '报关单',
    'booking' => '订舱单',
    'packing' => '装箱单',
    'carsbook' => '派车单',
    'carsCOM' => '拖车公司',
    'carsNo' => '车牌号',
    'shippingNo' => '货柜号',
    'carsPrice' => '货值',
    'creatdate' => '生成时间',
    'DERIL_log' => '跟踪日志',
    'manCHB' => '委托人',
    'companyCHB' => '经营单位',
    'registerCHB' => '申报单位',
    'adressCHB' => '申报现场',
    'cityCHB' => '指运港/城市',
    'shipping' => '订舱单',
    'companySPP' => '运输公司',
    'poxySPP' => '货运代理',
    /*  运输方式-已有*/
    'saveSPP' => '放货方式',
    'isbatchSPP' => '是否分批',

    //订单金融服务
    'finance' => '金融',
    'nameSRV' => '金融服务',
    'typeSRV' => '服务类型',
    'typeDATE' => '服务周期',
    'isPOA' => '是否需要垫付',
    'needOTH' => '金融服务要求',

    'payISSET' => '是否自营出口',
    'siteEX' => '平台代理出口',
    'sellersEX' => '自营出口',
    'upADD' => '添加附件',
    'upINFO' => '请下载相关合同文件，仔细阅读，确认无误后打印签约，并回传。',



    'goodsPS01' => '*修改此参数后，商品需重新审核',
    'goodsPS02' => '*修改此参数后，商品需重新审核',
    'goodsPS03' => '*修改此参数后，商品需重新审核',
    'goodsPS04' => '*修改此参数后，商品需重新审核',
    'goodsPS05' => '*修改此参数后，商品需重新审核',
    'goodsPS06' => '*修改此参数后，商品需重新审核',
    'infoGD' => '商品信息',
    'priceTT' => '价格条款',
    'priceMD' => '报价方式',
    'ctsAMT' => '报关金额',
    'packMAX' => '最大包装件数',

    'piece' => '件',
    'box' => '箱',
    'total' => '总',
    'parities' => '汇率',
    'fee' => '服务费',
    'dataSN' => '商检资料',
    'sourceCN' => '境内货源地',



    //订单退税服务
    'serviceDBK' => '退税服务',
    'ispayDBK' => '是否需要垫付退税款',
    'needDBK' => '退税要求',


    //个人/订单中心
    'historyNO' => '历史交易笔数',
    'performanceB' => '业绩余额',
    'audit' => '审核中',
    'confirmDD' => '待确认',
    'running' => '执行中',
    'recentLIST' => '近期清单',
    'tradeGOODS' => '交易商品',
    'recoveryLIST' => '应收付列表',

    'AYSRPO' => '账户分析报表',
    'cashing' => '收付中',
    'evaluating' => '评价中',
    'orderNO' => '订单号',
    'currSTATE' => '当前状态',
    'myperson' => '我方联系人',
    'thperson' => '对方联系人',
    'remarks' => '备注',

    'clientNAME' => '客户名',
    'clientDATE' => '创建时间',
    'clientEAA' => '审批中',
    'alreadySIGN' => '已签收',
    'refuseSIGN' => '被拒绝',
    'findORDER' => '订单查找',
    'viewORDER' => '查看订单',
    'analysisREP' => '分析报表',
    'orderINF' => '订单信息',
    'track' => '跟踪',
    'information' => '信息',
    'proxyVIEW' => '委托书浏览',

    'billMD' => '开票方式',
    'currency' => '货币',
    'deliveryMD' => '交货方式',

    'plasticPK' => '塑条封装',
    'carton' => '纸箱',
    'bulk' => '散装',
    'referenceNOTE' => '参考备注',

    'declareMD' => '报关方式',
    'packedMD' => '装箱方式',
    'destination' => '目的地',
    'valueADD' => '增值服务',

    'informationDD' => '显示详细信息',
    'confirmORDER' => '确认订单',



    'replace' => '回传',
    'invoice' => '发票',
    'delete' => '删除',

    'company' => '公司',
    'invitation' => '邀请',

    // 后台返回状态说明
    'is_delete'=>'您确认要删除吗?',
    'is_invalid'=>'您确认要禁用吗?',
    'is_valid'=>'您确认要启用吗?',
    'is_cancel'=>'您确认取消该订单吗?',
    'is_confirm'=>'是否确认订单?',
    'is_confirm2'=>'是否确认商品?',
    'is_forReview'=>'是否提交审核?',
    'is_email'=>'是否验证该邮箱?',
    'is_emails'=>'请填写有效电子邮箱',
    'is_submit'=>'确认提交该订单?',
    'is_default'=>'确认设为默认账户吗?',
    'is_accept'=>'是否接受合作伙伴邀请',
    'is_reject'=>'确认拒绝合作伙伴邀请',
    'in_delete'=>'正在删除中，请稍候...',
    'in_save'=>'正在保存中，请稍候...',
    'delete_y'=>'已成功删除！',
    'delete_n'=>'未成功删除！',
    'default_y'=>'成功设为默认联系人！',
    'default_n'=>'未成功设为默认联系人！',
    'codes'=>'验证码',

    'tip_active_00' => '确认发送邀请!',
    'tip_active_01' => '验证出错请检查邮箱是否存在!',
    'tip_active_02' => '已发送邮件请前往邮箱验证!',
    'tip_active_03' => '已成功发送邀请链接至该邮箱!',
    'tip_active_04' => '验证码超时，请重新验证!',
    'tip_active_05' => '验证成功',

    'tip_eComm_login_name'    => '用户名/电子邮箱',
    'tip_login_name'    => '请输入登录用户名',
    'tip_code'          => '请输入验证码',
    'tip_remember'       => '记住账号',
    'tip_find_pwd'      => '确 定',
    'tip_login_back'    => '返回登录',
    'tip_pwd_ts'        => '*请输入8-14位数字/英文，不能有特殊字符',
    'tip_login_please'  => '请先登录系统!',
    'tip_eloginname_no' => '用户名不存在或不正确，请重新输入!',
    'tip_email_active'  => '请到邮箱激活!',
    'tip_email_error'   => '验证错误，请5分钟后再试!',
    'tip_eamil_re'      => '激活成功，进行重置!',
    'tip_email_try'     => '邮件激活码有误，请重新输入!',
    'tip_email_check'   => '激活码不正确!',
    'tip_auth_no'       => '对不起，没有权限访问该页面!',
    'tip_auth_check'    => '验证失败!',
    'tip_find_no'       => '没有找到相关信息！',
    'tip_bank_no'       => '银行附件不能为空！',

    'tip_register_ready'  => '该用户已注册！',
    'tip_register_sucess' => '注册成功',
    'tip_register_fail'   => '注册失败',

    'tip_reset_sucess' => '重置成功',
    'tip_reset_fail'   => '重置失败',
    'tip_reset_pwd'    => '重置密码',

    'tip_login_sucess' => '登录成功',
    'tip_login_fail'   => '登录失败',
    'tip_login_two'    => '已登录,请勿重复登录',
    'tip_login_out'    => '成功退出',

    'tip_add_sucess' => '添加成功',
    'tip_add_fail'   => '添加失败',

    'tip_payment_sucess' => '支付成功',
    'tip_payment_fail'   => '支付失败',
    'tip_payment_pwd'    => '未设置支付密码',

    'tip_edit_sucess' => '编辑成功',
    'tip_edit_fail'   => '编辑失败',

    'tip_copy_sucess' => '复制成功',
    'tip_copy_fail'   => '复制失败',

    'tip_del_sucess' => '删除成功',
    'tip_del_fail'   => '删除失败',

    'tip_valid_sucess' => '启用成功',
    'tip_valid_fail'   => '启用失败',

    'tip_invalid_sucess' => '禁用成功',
    'tip_invalid_fail'   => '禁用失败',

    'tip_accept_sucess' => '已成功接受邀请',
    'tip_accept_fail'   => '接受邀请失败',
    'tip_reject_sucess' => '已拒绝邀请',
    'tip_reject_fail'   => '拒绝邀请失败',

    'tip_confrim_sucess' => '成功确认',
    'tip_confrim_fail'   => '确认失败',

    'tip_forReview_sucess' => '提交成功',
    'tip_forReview_fail'   => '提交失败',

    'tip_cancel_sucess' => '成功取消',
    'tip_cancel_fail'   => '取消失败 ',

    'tip_default_sucess' => '设为默认成功',
    'tip_default_fail'   => '设为默认失败 ',

    'tip_request_sucess' => '邀请成功',
    'tip_request_fail'   => '邀请失败',


    'tip_recharge_sucess' => '充值成功',
    'tip_recharge_fail'   => '充值失败',

    'tip_draw_sucess' => '提现成功',
    'tip_draw_fail'   => '提现失败',

    'tip_transfer_sucess' => '转账成功',
    'tip_transfer_fail'   => '转账失败',

    'tip_exchange_sucess' => '结汇成功',
    'tip_exchange_fail'   => '结汇失败',


    // 结算
    'Saltter_IN'=>'结算中心',
    'Saltter_Bank'=>'银行账户管理',
    'Saltter_Turn'=>'转账',
    'Saltter_Balance'=>'账户余额',
    'Saltter_Yuan'=>'元',
    'Saltter_setting'=>'支付设置',
    'Saltter_IFO'=>'金额详细',
    'Saltter_CIFO'=>'可支配余额',
    'Saltter_DIFO'=>'定向余额',
    'Saltter_FIFO'=>'冻结余额',
    'Saltter_FREE'=>'自由余额',
    'Saltter_ETIFO'=>'金融额度',
    'Saltter_VIFO'=>'查看金融详情',
    'Saltter_credit'=>'信用等级',
    'Saltter_Available'=>'可用额度',
    'Saltter_recive'=>'应收',
    'Saltter_pay'=>'应付',
    'Saltter_payment'=>'支付',
    'Saltter_transfer'=>'转账',
    'Saltter_in_amount'=>'转入金额',
    'Saltter_out_amount'=>'转出金额',
    'Saltter_able'=>'余额',
    'Saltter_Lastlist'=>'最近交易记录',
    'Saltter_wtlist'=>'记录明细',
    'Saltter_paylist'=>'支付申请',
    'Saltter_VALL'=>'查看所有',
    'Saltter_INF'=>'详情',
    'Saltter_List'=>'交易记录',
    'Saltter_Run'=>'流水账',
    'Saltter_Status'=>'状态',
    'Saltter_Forname'=>'对方名称',
    'Saltter_Name'=>'名称',
    'Saltter_For'=>'对方',
    'Saltter_OrderID'=>'订单号',
    'Saltter_RunID'=>'流水号',
    'Saltter_TradeID'=>'交易号',
    'Saltter_Class'=>'交易分类',
    'Saltter_amount'=>'金额',
    'Saltter_amount_rev'=>'收款余额',
    'Saltter_amount_bank'=>'收款银行账号',
    'Saltter_InitMount'=>'初始金额',
    'Saltter_TradeMount'=>'支付金额',
    'Saltter_Time'=>'时间',
    'Saltter_Made'=>'操作详情',
    'Saltter_Record'=>'记录详情',
    'Saltter_Sucess'=>'交易成功',
    'Saltter_DEC'=>'摘要',
    'Saltter_mark'=>'明细',
    'Saltter_Money'=>'款项',
    'Saltter_wtname'=>'记录名称',
    'Saltter_Unsettle'=>'未结算金额',
    'Saltter_contartNo'=>'合同号',
    'Saltter_FTO_R'=>'收方',
    'Saltter_FTO_P'=>'付方',
    'Saltter_Time_ING'=>'预计收付时间',
    'Saltter_Time_ING_R'=>'预计收齐时间',
    'Saltter_Time_ING_P'=>'预计付齐时间',
    'Saltter_Time_Now_R'=>'实际收齐时间',
    'Saltter_Time_Now_P'=>'实际付齐时间',
    'Saltter_FTO_Status_R'=>'收款状态',
    'Saltter_FTO_Status_P'=>'付款状态',
    'Saltter_GTime'=>'发生时间',
    'Saltter_DirectBalAmount'=>'定向余额支付',
    'Saltter_add_BankAmount'=>'添加银行支付',
    'Saltter_BankAmount'=>'银行支付',
    'Saltter_eAmount'=>'余额支付',
    'Saltter_total'=>'支付合计',

    'Saltter_pay_pwd'=>'支付密码',
    'Saltter_edit_pwd'=>'修改支付密码',
    'Saltter_set_pwd'=>'设置支付密码',
    'Saltter_init_pwd'=>'初始化支付密码',
    'Saltter_forget_pwd'=>'初始化支付密码',
    'Saltter_free'=>'可用剩余金额',
    'Saltter_CNY_free'=>'人民币账户自由余额',
    'Saltter_USD_free'=>'美元账户自由余额',
    'Saltter_tips00'=>'当使用银行支付时，必须上传转账水单，填写金额必须和水单金额一致',
    'Saltter_tips01'=>'该笔订单有定向资金，可用定向金额',
    'Saltter_tips02'=>'自由余额支付金额不允许大于可用的自由余额',
    'Saltter_tips03'=>'余额支付金额(含定向余额)不允许大于可申请余额的金额',
    'Saltter_tips05'=>'本次支付合计大于应付金额，超出部分自动转入自由余额',
    'Saltter_tips06'=>'首次使用支付功能需要设置支付密码',
    'Saltter_tips07'=>'是否成功设置支付密码',
    'Saltter_tips08'=>'连续输入5次错误密码，账户已冻结',
    'Saltter_tips09'=>'输入登录密码，前往找回密码',
    'Saltter_tips10'=>'好的,去设置',
    'Saltter_tips11'=>'暂时不设置离开该页面',
    'Saltter_tips12'=>'已完成，继续支付',
    'Saltter_tips13'=>'登录密码错误',
    'Saltter_tips14'=>'好的，稍后再试',
    'Saltter_tips15'=>'是否成功设置修改密码',
    'Saltter_tips16'=>'是的，继续支付',
    'Saltter_tips17'=>'登录密码错误',
    'Saltter_tips20'=>'请输入登录密码',
    'Saltter_tips23'=>'输入金额不可以大于当前余额',
    'Saltter_direction'=>'流向',
    'Saltter_all'=>'全部',
    'Saltter_into'=>'转入',
    'Saltter_out'=>'转出',
    'more'=>'更多',
    //充值
    'Saltter_recharge'=>'充值',
    'Saltter_currency'=>'货币',
    'Saltter_prompt'=>'温馨提示',
    'Saltter_prompt_r1'=>'1、目前仅支持线下汇款，到账时间一般为2-3个工作日（具体到账时间以银行的实际到账时间为准）。',
    'Saltter_prompt_r2'=>'2、线下汇款直接向快移平台的专属账户汇款，系统会将汇款直接匹配到您的快移账户。',
    'Saltter_prompt_r3'=>'请您通过网银转账，或者直接到银行柜台汇款，汇款账号如下：',
    'Saltter_bankactname'=>'开户名称',
    'Saltter_bankname'=>'开户银行',
    'Saltter_bankactno'=>'银行账号',
    'Saltter_bankaddress'=>'银行地址',
    //'Saltter_bankamount'=>'充值金额',
    'Saltter_bankact'=>'银行凭证',
    //提现
    'Saltter_draw'=>'提现',
    'Saltter_draw_name'=>'提现账户',
    'Saltter_draw_amount'=>'提取金额',
    'Saltter_surplus'=>'剩余',
    'Saltter_usable'=>'可用',
    'Saltter_bank_add'=>'添加账号',

    'Saltter_bill_info'=>'发票信息',
    'Saltter_bill_no'=>'发票号码',
    'Saltter_bill_amount'=>'发票金额',
    'Saltter_bill_date'=>'开票日期',
    'Saltter_tracking_is'=>'是否已邮寄',
    'Saltter_tracking_no'=>'物流单号',
    'Saltter_tracking_company'=>'物流公司',
    'Saltter_tracking_up'=>'发票影像',

    //结汇
    'Saltter_exg'=>'结汇',
    'Saltter_exg_act'=>'结汇账户',
    'Saltter_exg_amt'=>'结汇金额',
    'Saltter_huilv'=>'当前汇率',
    'Saltter_prompt_e1'=>'1、结汇功能仅支持兑换为公司默认货币。',
    'Saltter_prompt_e2'=>'2、结汇后金额系统会自动转入公司快移账户自由余额。',

    //金融
    'Finance_index'=>'金融中心',
    'Finance_info'=>'项目详情',
    'Finance_pamount'=>'批复金额',
    'Finance_reAmount'=>'距债务方还款日',
    'Finance_rePlan'=>'计划费/息期',
    'Finance_reDate'=>'还款日期',
    'Finance_Lamount'=>'贷款额',
    'Finance_mouth_amount'=>'本月应还金额',
    'Finance_repay_amount'=>'应付金额',
    'Finance_re_amount'=>'总应还款金额',
    'Finance_repay'=>'还款金额',
    'Finance_bamount'=>'本金',
    'Finance_itermNo'=>'项目号',
    'Finance_slcom'=>'受理公司',
    'Finance_order'=>'订单',
    'Finance_Unpaid'=>'未付',
    'Finance_paid'=>'已付',
    'Finance_cost'=>'费用',
    'Finance_colorred_tip01'=>'根据你所选的日期提前还款金额',
    'Finance_colorred_tip02'=>'（*计划只做参考，实际情况视订单进度而定)',
    'Finance_Interest'=>'利息',
    'Finance_Total'=>'合计',
    'Finance_project_plan'=>'项目计划',
    'Finance_project_number'=>'项目金额',
    'Finance_project_date'=>'起始日期',
    'Finance_project_amount'=>'本金',
    'Finance_project_Interest'=>'预计利息',
    'Finance_overdue _Interest'=>'逾期利息',
    'Finance_project_total'=>'本息合计',
    'Finance_creditor'=>'债权',
    'Finance_debt'=>'债务',
    'Finance_ING'=>'进行中',
    'Finance_authentic'=>'确权及费用',
    'Finance_authentic_submit'=>'提交确权资料',
    'Finance_tip01'=>'（*请点击下载下列资料，签署后在上传）',
    'Finance_confirm'=>'确认函',
    'Finance_Factoring'=>'保理合同',
    'Finance_policy'=>'保单',
    'Finance_upload'=>'编辑上传资料',
    'Finance_instructions'=>'费用及说明',
    'Finance_tip02'=>'1、实际费用会在确权资料审核后产生，请以支付页面为准。',
    'Finance_tip03'=>'2、费用支付情况会直接影响到本次金融项目的审核进度。',
    'Finance_service'=>'服务费',
    'Finance_premium'=>'保险费',
    'Finance_payment'=>'马上付款',
    'Finance_Have_paid'=>'已支付',
    'Finance_lending'=>'放款',
    'Finance_tip04'=>'以下放款计划会根据实际变动作出调整',
    'Finance_Number'=>'次数',
    'Finance_tamount'=>'本次金额',
    'Finance_amount'=>'本金',
    'Finance_accountsReceivable'=>'项目本金',
    'Finance_planService'=>'服务收费',
    'Finance_planInterest'=>'利息',
    'Finance_planRepayment'=>'还款情况',
    'Finance_Receivable_amount'=>'应付金额',
    'Finance_serviceCharge'=>'基础保理服务费',
    'Finance_guaranteeServiceFee'=>'担保保理服务费',
    'Finance_interest'=>'融资保理利息',
    'Finance_overdueInterest'=>'逾期利息',
    'Finance_repayment_ok'=>'待还本金',
    'Finance_repayment_no'=>'已还本金',
    //状态
    'Finance_rstatus'=>'还款中',
    'Finance_dstatus'=>'已还款',
    'Finance_cstatus'=>'待确认',

    'Finance_daijhuo'=>'待激活',
    'Finance_daifkuan'=>'待放款',
    'Finance_daihkuan'=>'待还款',
    'Finance_butongguo'=>'不通过',
    'Finance_finesh'=>'完成',
    //
    'certificateType'=>'证件类型',
    'certificateNo'=>'证件号码',
    'ID_card'=>'身份证',

    'companySignh3'=>'企业实名认证',
    'companySignh31'=>'系统正在审核......',
    'companySignh32'=>'电子签署功能授权完成',
    'companySignp01'=>'*以下资料适用于企业在快移平台上进行实名认证，请认真填写并核对资料后再提交',
    'companySignp02'=>'*系统会在次日往您的企业银行基本户自动转入随机金额人民币，请填写转账信息备注里的验证码',
    'companySignp025'=>'*系统会在次日往您的企业银行基本户自动转入随机金额人民币，请耐心等候',
    'companySignp03'=>'*恭喜你，资料通过审核，电子签署功能可以使用了',
    'et_auth_name'          => '企业名称',
    'et_auth_code'          => '统一社会信用代码',
    'et_auth_bankaccount'   => '企业银行基本账户',
    'et_auth_bankno'        => '基本户清算联行号',
    'et_auth_acctNo'        => '企业银行帐号',
    'et_auth_proviceName'   => '开户行所在地',
    'et_auth_cityName'      => '开户行所在地',
    'et_auth_bankName'      => '开户银行名称',
    'et_auth_subbranchName' => '开户行支行全称',
    'et_auth_mg_name'       => '企业法人名称',
    'et_auth_mg_id'         => '法人身份证号码',
    'et_auth_mg_mobile'     => '法人移动电话',
    'et_auth_verify'        => '请输入收款金额',
    'et_auth_amount'        => '系统将在2小时内往你您公司的基本户打一笔款项，请收到款项后再次点击实名图标进入本页面，输入收款金额完成操作',
    'et_select'             => '直接选择或搜索选择',
    'et_tips01'             => '验证码不能为空!',
    'et_tips02'             => '提示：所输入的验证码不正确，请确认无误后重新提交',
    'et_tips03'             => '提示：所填写的资料不能为空',
    'et_tips04'             => '提示：所填写的资料与真实不符，请确认无误后重新提交',
    'et_tips05'             => '提交成功,已进入审核',
    'userSignh3'=>'个人实名认证',
    'userSignh31'=>'个人实名认证完成',
    'userSignp01'=>'*仅支持由中华人民共和国居民身份证的用户进行认证，请准确填写并核对无误后，点击确认按钮提交认证',
    'userSignp02'=>'*恭喜您，个人实名认证已通过',
    'userTips01'=>'提示：所输入的资料与真实不符，请重新',
    'userTips02'=>'个人资料后再提交',
    'replay_sid'=>'重新授权',
    'shouqi'=>'收起',



    'gains'     =>'累计收益',
    'invested'=>'累计投资金额',
    'arr'=>'收益率',
    'diffTime'=>'支付期限',
    'accountsReceivable'=>'项目总额',
    'expiryDate'=>'还款日期',
    'factoringNo'=>'项目号',
    'loanAmount'=>'项目金额',
    'tradingStatus'=>'状态',
    'arrview'=>'收益率',
    'financingAmountview'=>'投入资金',
    //'loanAmount'=>'投资金额',
    'financingAmount'=>'投资金额',
    'period'=>'周期',
    'inputProportion'=>'投入比例',
    'companyName'=>'受理公司',
    'yq'=>'预期收益',
    'yqr'=>'预期收益日',
    'yjsyr'=>'预计收益日',
    'orderNon'=>'订单',
    'diffTimes'=>'距收益日',
    'yjsy'=>'预计收益',
    'yqsy'=>'逾期收益',
    'default'=>'默认',
    'Finance_operating'=>'当前在投资金',
    'Finance_available'=>'可用资金',
    'Finance_done'=>'已完成项目',






    /*企业认证第一步错误代码*/
    '-1'=>'服务器繁忙，请联系管理员',
    '100002'=>'缺少 persName 参数',
    '100004'=>'缺少 persMobilePhone 参数',
    '100029'=>'缺少 persIdentityNo 参数',
    '100043'=>'缺少 basicBankAcctNo 参数',
    '100044'=>'缺少 basicBankSettleNo 参数',
    '100045'=>'缺少 accountName 参数',
    '100046'=>'缺少 businessLicenseNo 参数',
    '110001'=>'persName格式不正确，支持汉字',
    '110003'=>'persMobilePhone 格式不正确，支持标准手机号格式',
    '110018'=>'persIdentityNo 格式不正确，支持身份证号码格式',
    '140002'=>'persName参数不能为空',
    '140004'=>'persMobilePhone参数不能为空',
    '140029'=>'persIdentityNo参数不能为空',
    '140043'=>'basicBankAcctNo 参数不能为空',
    '140044'=>'basicBankSettleNo 参数不能为空',
    '140045'=>'accountName 参数不能为空',
    '140046'=>'businessLicenseNo 参数不能为空',
    '150033'=>'basicBankAcctNo 参数过长，请控制在 50 个字符以内',
    '150034'=>'basicBankSettleNo 参数过长，请控制在 50 个字符以内',
    '150035'=>'accountName 参数过长，请控制在 50 个字符以内',
    '150036'=>'businessLicenseNo 参数过长，请控制在 50 个字符以内',
    '600002'=>'责任人实名认证信息不匹配',
    '600012'=>'企业实名认证信息不匹配',

    /*企业认证验证码*/
    '100047'=>'缺少 verify_code 参数',
    '140047'=>'verify_code 参数不能为空',
    '150037'=>'verify_code 参数过长，请控制在 6 个字符以内',
    '160007'=>'verify_code 不正确',
    '444444'=>' 实名认证每日只允许提交3次，请24小时后再尝试。',
    /*个人认证码*/
    'user100002'=>'缺少 name 参数',
    'user100004'=>'缺少 mobilePhone参数',
    'user100029'=>'缺少 identityNo参数',
    'user110001'=>'name 格式不正确，支持汉字',
    'user110003'=>'mobilePhone格式不正确，支持标准手机号格式',
    'user110018'=>'identityNo格式不正确，支持身份证号码格式',
    'user140002'=>'name 参数不能为空',
    'user140004'=>'mobilePhone参数不能为空',
    'user140029'=>'identityNo参数不能为空',
    'user600002'=>'实名认证信息不匹配',
    'user444444'=>'实名认证每日只允许提交3次，请24小时后再尝试。',
    /*金融补充*/
    'Finance_doc_title'=>'文档签署及放款',
    'Finance_doc_write'=>'文档签署',

    /* Attach Type */
    'PREPARE_GOODS'               => '备货相关附件',
    'EXAMINE_GOODS'               => '验货相关附件',
    'DELIVER_GOODS'               => '发货相关附件',
    'RECEIPT_GOODS'               => '收货相关附件',
    'QUALITY_AGREE_TEMPL'         => '质量保证函模板',
    'QUALITY_AGREE_FORMAL'        => '质量保证函正本',
    'RECEIPT_CONFIRMATION_TEMPL'  => '收货确认函模板',
    'RECEIPT_CONFIRMATION_FORMAL' => '收货确认函正本',

);
?>
