<?php
return array(
//系统配置
    'hello_world'=>'你好世界',
    'document'=>'Document',
    'get_lg'=>'en_US',    //语言编码
    'language'=>'English',//语言
    'chinese'=>'Chinese',//语言

    // 错误代码
    '111111' => '提交失败，数据不完整或缺少必填项！',

    //订单顶部进度说明
//1.买家确认订单（同卖家确认订单）
//2.买家签订合同（同卖家确认订单）
//3.卖家备货
//4.买家验货
//5.卖家发货
//6.买家收货
//    'going000'=>'此订单状态暂未生成',
//    'going001'=>'Please confirm the order information is correct, click the confirmation button, and our staff will continue to follow up order follow-up service for you',
//    'going002'=>'Please send the service order and download contract template, and stamp it separately to the platform, so that we can activate the order as soon as possible',
//    'going003'=>'If you have finished the stock, you can click the finish button to complete the stock operation',
//    'going004'=>'The order has entered the inspection stage. Click Finish button to complete the stock operation',
//    'going005'=>'The order has entered the delivery stage. If you have shipped, please click the finish button',
//    'going006'=>'The order has entered the receiving phase. If you have received the goods, please click the receive button',
//    'going007'=>'Order settlement',

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
    'nav_login' => 'Sign in',
    'nav_Mr' => 'Mr.',
    'nav_Miss' => 'Mrs.',
    'nav_user' => 'User Center',
    'register' => 'Register',
    'nav_index' => 'Index',
    'nav_baseservice' => 'Basic Services',
    'nav_finservice' => 'Financial Services',
    'nav_channel' => 'Financial Channels',
    'nav_help' => 'Help',
    'nav_about' => 'About Us',
    'nav_exit' => 'Sign out',
    //补充部分
    'mustTT' => 'Required',
    'madeTT' =>'Op.',
    'saleING'=>'Sales',
    'NoRegister'=>'Unregistered',

//用户角色
    'CompUser'=>'Average user',
    'CompAdmin'=>'Comp. Administrator',
    'SOAdmin'=>'Booking Administrator',
    'TOAdmin'=>'Trucking Administrator',
    'DeclarationAdmin'=>'Customs Declaration Administrator',
    'CompPublicInfoAdmin'=>'Public Info. Administrator',
    'CompSettleAdmin'=>'Settlement Administrator',
    'CompOrderAdmin'=>'Order Administrator',
    'CompClerk'=>'Merchandiser',
    'CompProductAdmin'=>'Commodity Administrator',
    'CompPartnerAdmin'=>'Partnership Administrator',


//常用状态
    'last' => 'Back',
    'next' => 'Next',
    'saveDRAFT' => 'Save Draft',
    'yes' => 'Yes',
    'no' => 'No',
    'ruler' => 'Ruler',
    'entries' => 'PCS',
    'disable' => 'Disable',
    'normal' => 'Normal',
    'check' => 'Verify',
    'confirm' => 'Confirm',
    'return' => 'Return',
    'returnReg' => 'Register Now',
    'add' => 'Add',
    'cancel' => 'Cancel',
    'copy' => 'Copy',
    'accept'=>'Accept',
    'reject'=>'Reject',
    'edit' => 'Edit',
    'save' => 'Save',
    'submit'=>'Submit',
    'submitCHECK' => 'Submitted for Review',
    'print' => 'Print',
    'view' => 'View ',
    'list' => 'List',
    'info' => 'Info',
    'de_null' => 'Can not be blank',
    'de_format'=>'Incorrect format',
    'de_nozore'=>'Cannot be 0, Please re-enter',
    'de_anything'=>'Please fill in one item',
    'mat_psd'=>'Password must be a min.8 alphanumeric characters',
    'is_psd'=>'Passwords do not match',
    'de_use'=>'Sorry, user name already taken',
    'en_use'=>'Congratulations, user name available',
    'xieyi'=>'Cooperation Agreement',
    'agree'=>'Approve',
    'close'=>'Close',



    //注册页表单注释文字
    'is_name'=>"Please fill out your real name",
    'is_emlog'=>"admin@etradfast.com",
    'is_passwd'=>"Please provide 8-30 upper and lowercase letters",
    'is_rpasswd'=>"Please re-enter password",
    'is_phone'=>"Please enter a valid phone number( or mobile)",
    'is_tel'=>"Please enter a valid landline",
    'is_comname'=>"Please enter the full legal name of the company",
    'is_regadress'=>"Company legal address",
    'ps_email'=>"admin@eTrade.com",
    'service_contract'=>"《Etradfast Trading Platform Services User Agreement》",
    'check_service_contract'=>"请阅读并同意相关服务条款",
    //登录/注册/表单/合作伙伴/我的卖家/买家/通讯录
    'alreadyRegistered' => '已经注册，现在就',
    'companyRegister' => '公司注册',
    'personRegister' => '个人注册',
    'username' => 'User Name',
    'password' => 'Password',
    'elogname'=>'User Name',
    'login_s'=>'LOGIN',
    'login'=>'LOGIN In',
    'forgetPSD' => 'Forgot password?',
    'longPSD' => 'Old Password',
    'userREG' => 'Register',
    'userRegister' => 'Register',
    'userTT'=>'User Profile',
    'confirmPSD' => 'Confirm Password',
    'newPSD' => 'New Password',
    'loginPSD' => 'Password',
    'companyFU' => 'Full Company Name',
    'regd_company_name' => '营业执照公司名称',
    'regaddress'=>'Company Address',
    'companyEM'=>'Company Email',
    'address' => 'Address',
    'contacts' => 'Contacts',
    'contactsID' => 'Contacts ID',
    'callMD' => 'Contact Method',
    'baseINF' => 'Staff Info.',
    'basicInfo' => 'Basic Info.',
    'viewInfo' => 'View Info.',
    'name' => 'Name',
    'myphone'=>'Personal Phone',
    'title' => 'Title',
    'sex'=>'Sex',
    'female'=>'Female',
    'male'=>'Male',
    'birthdate'=>'Birth date',
    'job' => 'Title',
    'myjob'=>'Position',
    'HPname'=>'Assistant\'s Name',
    'HPphone'=>'Assistant\'s Phone',
    'orderContact'=>'Contact',
    'is_orderContact'=>'Order Contact',
    'set_default_bank'=>'Set as default account',
    'no_default_bank'=>'No set up',
    'Df_Contact'=>'Default Contact',
    'division' => 'Department',
    'userP' => 'User Status',
    'userPT'=>'Staff Status',
    'userROLE' => 'Roles',
    'contactINF' => 'Contact Info.',
    'email' => 'Email',
    'fax' => 'Fax',
    'maillADD' => 'Postal Address',
    'country' => 'Country',
    'Region' => 'Region',
    'province' => 'Province/State',
    'city' => 'City',
    'street' => 'Street',
    'postcode' => 'Postcode',
    'telphone' => 'Landline',
    'call' => 'Contact No.',
    'mobile' => 'Mobile',
    'mobile_phone' => 'Mobile phone',
    'phone' => 'Phone',
    'company_phone' => 'Company Phone',
    'company_fax' => 'Company Fax',
    'company_website' => 'Website',
    'company_email' => 'Company Email',
    'company_business_license' => 'Business License',
    'INPkw_ac'=>'Enter keywords such as name, phone, email',
    'INPkw_bk'=>'Enter keywords such as account name, Bank name, account number',
    'INPkw_par_buyer'=>'Enter keywords such as buyer',
    'INPkw_par_vendor'=>'Enter keywords such as vendor',
    'INPfd_par'=>'Enter keywords such as eTradeFast codes',
    'INPkw_goodes'=>'Enter keywords such as name, HS Code, model or brand',
    'INPkw_order_xs'=>'Enter keywords such as oder number, buyer',
    'INPkw_order_cg'=>'Enter keywords such as oder number, vendor',

    'CHK_account'=>'All Status',

//友情链接
    'about_ef' => 'About Us',
    'marketC' => 'Marketing Center',
    'contactUS' => 'Contact Us',
    'contactSV' => 'Contact Us',
    'joinUS' => 'Join Us',
    'siteMAP' => 'Site Map',

//底部链接
    'findJOB' => 'Jobs',
    'about' => 'About',
    'privacy' => 'Privacy',
    'lawITEM' => 'Legal Notices',

//用户中心左侧导航
    'order' => 'Order',
    'orderIN' => 'Order Center',
    'orderREQ' => 'Tax Rebates Application',
    'orderSALLE' => 'Sales Order',
    'orderBUY' => 'Purchase Order',

    'partners' => 'Partners',
    'partners_buyer' => 'Buyers',
    'partners_vendor' => 'Vendors',

    'cooperationPTE' => 'Cooperation Partners',
    'cooperationC' => 'Buyers',
    'cooperationB' => 'Vendors',
    'cooperationS' => 'Service Providers',
    'cooperationCALL' => 'Address book',

    'sendBuyer' => 'Invite Buyers',
    'sendVendor' => 'Invite Vendors',

    'goods' => 'Products',
    'goods_4_index' => 'Products',
    'goodsME' => 'Our Products',
    'goodsINF'=>'Product Info.',
    'goodsBUY' => 'Product Procurement',


    'contract' => 'Contract',
    'contractSALL' => 'Sales Contract',
    'contractBUY' => 'Purchase Contract',


    'settle' => 'Trade Settlement',
    'overview' => 'Overview',
    'tradeLIST' => 'Trading Record',


//个人信息右侧title
    'myINF'=>'Personal Profile',
    'myPHOTO'=>'Profile Photo',
    'editPWD'=>'Change Password',
    'myNO'=>'Account Security',
    'WebINF'=>'Account Info.',

    'basicINF' => 'Info.',
    'company_info' => 'Company Info.',
    'personNO' => 'Staff Info.',
    'bankNO' => 'Bank Account',
    'file' => 'Files',
    'files' => 'File',


//附件部分
    'filename' => 'File Name',
    'finding' => 'Search Target',
    'find'=>'Search',
    'noData'=>'No Results',

//个人信息公司模块
    'companyEWM'=>'Click to view the company code',
    'companyMNG' => 'Company Management',
    'comNAME' => 'Name',
    'enNAME' => 'English Name',
    'enAdress' => 'English Adress',
    'company_legal_form' => 'Legal Forms',
    'legalForm' => 'Legal Forms',
    'crnCode' => 'Currency',
    'langCode' => 'Language',
    'timezone'=>'Timezone',
    'industry' => 'Industry',
    'ONcountry'=>'Country Region',
    'ONlimit'=>'Market Region',
    'partner_regdCountryCode' => '所在国家',
    'all' => 'All',
    'company_incorporation_date' => 'Est. Date',
    'type' => 'Type',
    'type1' => 'Type1, Buyers',
    'type2' => 'Type2, Partners',
    'type3' => 'Type3, Vendors',
    'website' => 'Website',
    'ICBNO' => 'IC Registration Number',
    'ECC' => 'Enterprise Code',
    'businessLicenseNo' => 'Tax ID Number',
    'TICNO' => 'Taxpayer Name',
    'company_profile' => 'Company profile',
    'UPMBLS' => 'Please scan and upload your business license',
    'UPICBNO' => 'Please scan and upload your tax registration certificate',
    'UPCOMFJ' => 'Please scan and upload your production licenses and trademarks',

    'GOODSLS' => 'Product photos',
    'UPGOODSLS' => 'Please scan and upload product packaging and materials',
    'UPBank' => 'Relevant banking information.',
//个人信息人员与账号模块（有些字段已经存在 下面模块不再进行说明）
    'state' => 'Status',
    'verify'=>'Verify',
    'enable' => 'Enable',
//产品&订单中心状态
    'valid' => 'Valid',
    'checkP' => 'Audit',
    'checkIN' => 'Audit',
    'checkNO' => 'Refuse',
    'checkR' => 'Pending',
    'nopass' => 'Rejected',
    'history'=>'History',
    'draft' => 'Draft',
    'void' => 'Void',

//产品页面
    'productNAME' => 'Product Name',
    'productENNAME' => 'English Name',
    'brand' => 'Brand Name',
    'model' => 'Model',
    'saleprice' => 'Unit Price',
    'saletotal' => 'Total Sale Price',
    'purprice' => 'Unit Cost',
    'purtotal' => 'Total PUR. Cost',
    'uiprice' => 'Invoiced Unit Price',
    'uitotal' => 'Invoice Total',
    'unitB' => 'Trading Unit',
    'unitF' => 'Legal Units',
    'HSCODE' => 'HSCODE',
    'uprice' => 'Unit Price',
    'number' => 'Quantity',
    //订单商品列表补充
    'orderprice' => 'Unit Price',
    'ordertotal' => 'Order Total',
    'quantity' => 'PKGS.',

    'RFRT' => 'Refund Rate',
    'RFADD' => 'Value Added Tax',
    'SBYSU' => 'Declared Units',
    'productSize'=>'Size Specifications',
    'functionUsage'=>'Functional Use',
    'productMaterial'=>'Materials',
    'supplierName'=>'Supplier Name',
    'packingMD' => 'Packaging Dimensions',
    'packageTYPE' => 'Packaging',
    'packINF' => 'Packing Info.',
    'netWET' => 'Net Weight',
    'grossWET' => 'Gross Weight',
    'isSNN'=>'Required Inspection',
    'productionMode'=>'Production Method',
    'image' => 'Image',
    'RLCT' => 'Supervision',
//新增商品页面
    'orderGOODS' => 'Order Goods',
    'imgGOODS' => 'Product Picture',






//银行账户模块
    'accountNO' => 'Account Number',
    'accountTP' => 'Account Type',
    'accountFA' => 'Foreign Exchange Account',
    'accountBA' => 'Basic Account',
    'accountNAME' => 'Account Name',
    'acctNO' => 'Account Number',
    'accountBK' => 'Bank',
    'accountBKname' => 'Name of Bank',
    'accountDF' => 'Default Account',
    'accountADR' => 'Bank Address',
    'accountSCD' => 'SWIFT CODE',
    'accountBZ' => 'Notes',

    //订单状态标题
    'orderSTATUS' => 'Order Status',
    'orderADD' => 'New Order',
    'partnerADM' => 'Partner Management',
    'partner' => 'Partners',
    'partnerINF' => 'Partner Info.',
    'userINF' => 'Personal Info Center',
    //最近订单进度
    'orderStatus' => 'Order Status',
    'orderStatus01' => 'Last Update',
    'confirming' => 'Confirm Order',
    'confirmed' => 'Confirmed',
    'signing'   => 'Sign',
    'sign' => 'Sign Contract',
    'signTitle' => '签署合同',
    'signSuccess' => '签署成功！',
    'signFail' => '签署失败，请重新签署！',
    'signAuthType' => '验证方式',
    'signSendAuthCode' => '发送验证码',
    'signAuthCodeSending' => '验证码已发送...',
    'signInfo' => '签署合同前需验证身份',
    'signInputAuthCode' => '请输入短信验证码',
    'noGOODS' => 'Not Started',
    'reGOODS' => 'Receipt',
    'deGOODS' => 'Delivery',
    'rdGOODS' => 'Stocking',
    'delivery' => '物流情况',
    'ckGOODS' => 'Inspection',
    'calculate' => 'Waiting Settlement',
    'calculated' => 'Settled',
    'finish' => 'Finish',
    'finished' => 'Finished',
    'waiting'=>'Waiting',
    'orderPS02'=>'Order was not generated',
    'orderPS01'=>'Order has not started',
    'deliveryView' => '收发货详情',
    'deliveryDel' => '删除本次发货单',
    'deliveryAdd' => '添加发货记录',
    'deliveryItemListNull' => '暂无收发货记录',
    'deliveryItemQuantity' => '商品数量',
    'deliveryBillInfo'      => '开票资料',
    'deliveryBillTitle'     => '查看开票资料前，请先签署供货合同',
    'deliveryBillTips'      => '开票资料及注意事项',
    'deliveryBillTipsBank'  => '* 签署前请选择供应商收款银行帐户',
    'deliveryBillTipsCheck' => '* 新增的银行帐户需等待后台审核，审核时间一般为1个工作日。审核通过后，再次点击开票资料才可选择',
    'signingNow'            => '现在签署',
    'bankAccount'           => '银行帐户',
    'deliveryBillTips_A'      => '若有两个或以上供应商，请点击选项框查看各自开票资料，各供应商须按以下资料开具真实的增值税发票',
    'deliveryBillTips_B'      => '请将开具后的增值税发票原件邮寄至以下地址',
    'deliveryBillTips_C'      => '地址：深圳市罗湖区嘉宾路2034号深化商业大厦1005室',
    'deliveryBillTips_D'      => '收件人：付先生',
    'deliveryBillTips_E'      => '联系电话：0755-83687432',
    'deliveryBillTips_F'      => '客服人员收到增值税发票原件并检验后，会尽快处理后续工作',
    'expressNo'             => '快递单号',



    //订单信息预览
    'orderVINF' => 'Order Preview',
    'orderID' => 'Order ID',
    'orderNo' => 'Order No.',
    'orderItem' => '商品',
    'orderTotal' => '金额',
    'shopPrities' => 'Currency',
    'Prities' => 'Currency',
    'orderPrice' => 'Amount',
    'UPdate' => 'Update Time',
    'date' => 'Date',

    'contartNo' => 'Contract No.',
    'proxyNo' => 'Power of Attorney',
    'orderATCH' => 'Order Attachments',
    'delegation' => 'Sealed Instructions',
    'contract_tmp' => 'Sealed Contract Template',
    'contract_seal' => 'Sealed Contract',
    'quality_tmp' => 'Quality Assurance Template',
    'quality_seal' => 'Quality Assurance',
    'receipt_confirmation_templ' => '收货确认函模板',
    'receipt_confirmation_formal' => '收货确认函正本',
    'valuationNo' => 'Quotation',
    'income' => 'Income',
    'expend' => 'Expenditures',
    'profit' => 'Profits',
    'rfrtNo' => 'Tax Refund Number',
    'progressING' => 'Progress',
    'stateING' => 'Status',
    'operatING' => 'Op.',
    //我有异议
    'objection' => 'Disputes»',


    'rated' => 'Evaluation',
    'download' => 'Download',
    'upload' => 'Upload',
    'contractUP' => 'Sealed Contract Upload',
    'contractUPInfo' => '下载合同并将盖章合同上传。',
    'delegationUP' => 'Sealed Instructions Upload',
    'stockUP' => 'Stock Attachment Upload',
    'examineUP' => 'Inspection Upload',
    'qualityUP' => 'Quality Assurance Upload',
    'deliverUP' => 'Shipping Related Upload',
    'receivingUP' => 'Receiving Related Upload',
    'stockVIEW' => 'Stock Attachment',
    'receivingVIEW' => 'Receiving Related Upload',
    'deliverVIEW' => 'Shipping Related Attachments',
    'examineVIEW' => 'Inspection Attachments',

    'tipQualityNoNull' => '请上传质量保证函正本!',

    'timeCLASS' => 'Sort by time',
    'eventCLASS' => 'Sort by Classification',
    'selectROLE' => 'Select a Task',


//新增订单模块
    //流程start
    'orderINF01' => 'Basic Info.',
    'orderINF02' => 'Add Item',
    'orderINF03' => 'Customs&Logistics Info.',
    'orderINF04' => 'Completed',
    //流程end
    'unorder' => 'Cancelled Orders',

    'infoED' => 'Edit Information',
    'buyers' => 'Buyers',
    'buyerATTN' => 'Buyer Contacts',
    'seller' => 'Vendor',
    'sellerATTN' => 'Vendor Contacts',
    'remitMD' => 'Payment Terms',
    'exportMD' => 'Export Terms',
    'thisATTN' => 'Order Contacts',
    'payDD' => 'Payment Period',
    'payCNY' => 'Currency',
    'payNeed' => 'Requirements',
    'payINF' => 'Order-Related Data Upload',
    'hetongUP' => 'Contract Upload',
    'zhiliangUP' => 'Quality Assurance Upload',
    'zhiliangDD' => 'Download Information',
    'hetongDD' => 'Attachment Download',
    'tupUP' => 'Picture Upload',
    'payJJ' => 'Pricing Mode',
    'payPrice' => 'Price Terms',
    'payCasing' => 'Packing Mode',
    'payGoods' => 'Commodity',
    'shopADD' => 'Click on Select Merchandise',
    'shopNet' => 'T.N.W',
    'shopGross' => 'T.G.W',
    'shopTotal' => 'Total',

    //城市
    'citypost1' => 'Departure city',
    'citypost2' => 'Unloading City',
    'citypost3' => 'Delivery City',
    //港口
    'shippost1' => 'P.O.L',
    'shippost2' => 'P.O.D',
    'shippost3' => 'P.A',


    'customsINF' => 'Customs Info.',
    'moveMD' => 'Shipping Mode',
    'POCRC' => 'Customs Port',
    'portSHIP' => 'P.O.L',
    'portDSCG' => 'P.O.D',
    'portDLVY' => 'P.A',
    'mabyDATE' => 'Estimated Delivery Date',

    'isFCL' => 'Type of Loading',
    'infoFCL' => 'Loading Quantity',

    'logisticSS' => 'Logistic Services',
    'logisticND' => 'Logistic REQS.',
    'transportation' => 'Logistics',

    'clearance' => 'Customs Declaration',
    'selectCHB' => 'Designated Broker',
    'nameCHB' => 'Customs Broker Information',
    'needCHB' => 'Declaration REQS.',
    'codeCHB' => 'Declaration Code',
    'whoCHB' => 'Name of Customs Broker',
    'logistics' => 'Customs Clearance Logistics',
    'customs' => 'Customs Declaration Form',
    'booking' => 'Booking Form',
    'packing' => 'Packing List',
    'carsbook' => 'Send a Truck',
    'carsCOM' => 'Freight Company',
    'carsNo' => 'Truck ID Number',
    'shippingNo' => 'Container Number',
    'carsPrice' => 'Trucking Cost',
    'creatdate' => 'Hauling Date',
    'DERIL_log' => 'Tracking Log',
    'manCHB' => 'Customer',
    'companyCHB' => 'Company',
    'registerCHB' => 'Application',
    'adressCHB' => 'Reporting Site',
    'cityCHB' => 'Shipment Port/City',
    'shipping' => 'Booking form',
    'companySPP' => 'Transport Company',
    'poxySPP' => 'Freight Agency',
    /*  运输方式-已有*/
    'saveSPP' => 'Delivery Mode',
    'isbatchSPP' => 'Batch',

    //订单金融服务
    'finance' => 'Finance',
    'nameSRV' => 'Financial Services',
    'typeSRV' => 'Type of Service',
    'typeDATE' => 'Service period',
    'isPOA' => 'If You Need an Advance',
    'needOTH' => 'Financial Service REQS.',

    'payISSET' => 'Import or Export',
    'siteEX' => 'Agent',
    'sellersEX' => 'Self-support',
    'upADD' => 'Additional Attachments',
    'upINFO' => 'Please download the relevant contract documents, read carefully, after confirming, sign, print and return.',



    'goodsPS01' => '*After changing these parameters, goods need to be re-inspected',
    'goodsPS02' => '*After changing these parameters, goods need to be re-inspected',
    'goodsPS03' => '*After changing these parameters, goods need to be re-inspected',
    'goodsPS04' => '*After changing these parameters, goods need to be re-inspected',
    'goodsPS05' => '*After changing these parameters, goods need to be re-inspected',
    'goodsPS06' => '*After changing these parameters, goods need to be re-inspected',
    'infoGD' => 'Product Info.',
    'priceTT' => 'Price Terms',
    'priceMD' => 'Trade Currency',
    'ctsAMT' => 'Customs Value',
    'packMAX' => 'Maximum Number of Packages',

    'piece' => 'Pieces',
    'box' => 'Boxes',
    'total' => 'Total',
    'parities' => 'Exchange Rates',
    'fee' => 'Service Charge',
    'dataSN' => 'Inspection Data',
    'sourceCN' => 'Domestic Suppliers',




//订单退税服务
    'serviceDBK' => 'Tax Refund Services',
    'ispayDBK' => 'If you need to pay out the refund',
    'needDBK' => 'Refund REQS.',


//个人/订单中心
    'historyNO' => 'Total Number of Transactions',
    'performanceB' => 'Performance Balance',
    'audit' => 'Audit',
    'confirmDD' => 'Waiting Confirmation',
    'running' => 'Running',
	'finished' => 'Finished',
    'recentLIST' => 'Recent List',
    'tradeGOODS' => 'Trade Goods',
    'recoveryLIST' => 'Payments Received',

    'AYSRPO' => 'Account Analysis Report',
    'cashing' => 'Receipts and Payments',
    'evaluating' => 'Evaluating',
    'orderNO' => 'Order No.',
    'currSTATE' => 'Current Status',
    'myperson' => 'My Contacts',
    'thperson' => 'OPP. contacts',
    'remarks' => 'Remarks',

    'clientNAME' => 'Client Name',
    'clientDATE' => 'Creation Date',
    'clientEAA' => 'Approved',
    'alreadySIGN' => 'Signed',
    'refuseSIGN' => 'Rejected',
    'findORDER' => 'Order Search',
    'viewORDER' => 'View Order',
    'analysisREP' => 'Analysis Report',
    'orderINF' => 'Order Info',
    'track' => 'Tracking',
    'information' => 'Info.',
    'proxyVIEW' => 'Proxy View',

    'billMD' => 'Invoicing Terms',
    'currency' => 'Currency',
    'deliveryMD' => 'Delivery Mode',

    'plasticPK' => 'Plastic Packaging',
    'carton' => 'Carton',
    'bulk' => 'Bulk',
    'referenceNOTE' => 'Reference Notes',

    'declareMD' => 'Customs Declaration',
    'packedMD' => 'Packing Method',
    'destination' => 'Destination',
    'valueADD' => 'Value Added Services',

    'informationDD' => 'Show Detailed Information',
    'confirmORDER' => 'Confirm Order',



    'replace' => 'Replace',
    'invoice' => 'Invoice',
    'delete' => 'Delete',

    'company' => 'Company',
    'invitation' => 'Invite',


	//后台返回状态说明
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

   'tip_active_00'=>'确认发送邀请!',
   'tip_active_01'=>'验证出错请检查邮箱是否存在!',
   'tip_active_02'=>'已发送邮件请前往邮箱验证!',
   'tip_active_03'=>'已成功发送邀请链接至该邮箱!',
   'tip_active_04'=>'验证码超时，请重新验证!',
   'tip_active_05'=>'验证成功',

    'tip_eComm_login_name'    => '用户名/电子邮箱',
   'tip_login_name'=>'请输入登录用户名',
   'tip_code'=>'请输入验证码',
    'tip_remember'=>'记住账号',
   'tip_find_pwd'=>'确 定',
   'tip_login_back'=>'返回登录',
   'tip_pwd_ts'=>'*请输入8-14位数字/英文，不能有特殊字符',
   'tip_login_please'=>'请先登录系统!',
    'tip_eloginname_no'=>'用户名不存在或不正确，请重新输入!',
   'tip_email_active'=>'请到邮箱激活!',
   'tip_email_error'=>'验证错误，请5分钟后再试!',
   'tip_eamil_re'=>'激活成功，进行重置!',
   'tip_email_try'=>'邮件激活码有误，请重新输入!',
   'tip_email_check'=>'激活码不正确!',
   'tip_auth_no'=>'对不起，没有权限访问该页面!',
   'tip_auth_check'=>'验证失败!',
   'tip_find_no'=>'没有找到相关信息！',
   'tip_bank_no'=>'银行附件不能为空！',

   'tip_register_ready'=>'该用户已注册！',
   'tip_register_sucess'=>'注册成功',
    'tip_register_fail'=>'注册失败',

   'tip_reset_sucess'=>'重置成功',
   'tip_reset_fail'=>'重置失败',
    'tip_reset_pwd'=>'重置密码',

   'tip_login_sucess'=>'登录成功',
   'tip_login_fail'=>'登录失败',
   'tip_login_two'=>'已登录,请勿重复登录',
   'tip_login_out'=>'成功退出',

   'tip_add_sucess'=>'添加成功',
   'tip_add_fail'=>'添加失败',

    'tip_payment_sucess'=>'支付成功',
    'tip_payment_fail'=>'支付失败',
    'tip_payment_pwd'=>'未设置支付密码',

    'tip_edit_sucess'=>'编辑成功',
    'tip_edit_fail'=>'编辑失败',

    'tip_copy_sucess'=>'复制成功',
    'tip_copy_fail'=>'复制失败',

    'tip_del_sucess'=>'删除成功',
    'tip_del_fail'=>'删除失败',

    'tip_valid_sucess'=>'启用成功',
    'tip_valid_fail'=>'启用失败',

    'tip_invalid_sucess'=>'禁用成功',
    'tip_invalid_fail'=>'禁用失败',

    'tip_accept_sucess'=>'已成功接受邀请',
    'tip_accept_fail'=>'接受邀请失败',
    'tip_reject_sucess'=>'已拒绝邀请',
    'tip_reject_fail'=>'拒绝邀请失败',

    'tip_confrim_sucess'=>'成功确认',
    'tip_confrim_fail'=>'确认失败',

    'tip_forReview_sucess'=>'提交成功',
    'tip_forReview_fail'=>'提交失败',

    'tip_cancel_sucess'=>'成功取消',
    'tip_cancel_fail'=>'取消失败 ',

    'tip_default_sucess'=>'设为默认成功',
    'tip_default_fail'=>'设为默认失败 ',

    'tip_request_sucess'=>'邀请成功',
    'tip_request_fail'=>'邀请失败',


    'tip_recharge_sucess'=>'充值成功',
    'tip_recharge_fail'=>'充值失败',

    'tip_draw_sucess'=>'提现成功',
    'tip_draw_fail'=>'提现失败',

    'tip_transfer_sucess'=>'转账成功',
    'tip_transfer_fail'=>'转账失败',

    'tip_exchange_sucess'=>'结汇成功',
    'tip_exchange_fail'=>'结汇失败',


//结算
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
    'Saltter_ETIFO'=>'快移金融',
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
    'Saltter_add_BankAmount'=>'添加银行帐号',
    'Saltter_BankAmount'=>'银行支付',
    'Saltter_eAmount'=>'余额支付',
    'Saltter_total'=>'支付合计',




    'gains'     =>'累计收益',
    'invested'=>'累计投资金额',
    'arr'=>'实际平均收益率',
    'diffTime'=>'支付期限',
    'accountsReceivable'=>'支付期限',
    'expiryDate'=>'还款日期',






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

    //
    'certificateType'=>'certificateType',
    'certificateNo'=>'certificateNo',
    'ID_card'=>'身份证',

    'companySignh3'=>'企业实名认证',
    'companySignh31'=>'系统正在审核......',
    'companySignh32'=>'电子签署功能授权完成',
    'companySignp01'=>'*以下资料适用于企业在快移平台上进行实名认证，请认真填写并核对资料后再提交',
    'companySignp02'=>'*系统会在次日往您的企业银行基本户自动转入随机金额人民币，请填写转账信息备注里的验证码',
    'companySignp025'=>'*系统会在次日往您的企业银行基本户自动转入随机金额人民币，请耐心等候',
    'companySignp03'=>'恭喜你，资料通过审核，电子签署功能可以使用了',
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
