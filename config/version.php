<?php
/**
 * 版本发布：Brave
 * 系统版本：1.2.0.4_r0709120515
 * 发布时间：2015-07-09 12:05:38
 * 更新概述：
 * 
1、销售清单导出加一列会员编号字段；
2、订单流程中店主短信提示问题；
3、订单下单退款的短信提醒问题；
4、“销售清单”和“微店酬金对账表”中需要增加“是否店主下单”和商品编码与商品名称分开；
5、“分店主店铺访问量统计报表”需导出一列各店主的销售金额；
6、取数据没有唯一值来对照；
7、底部菜单加上我最后访问店铺链接，推荐我店铺链接；
8、修改物流公司跟订单号；
9、产品库调价后页面不刷新；
10、云产品库加关键字搜索；
11、当上架商品库存为0时，能否加个 “已售罄”的水印 或者“无货”之类；
12、抢购页面，增加商品数量的时候，就算不能增加也要提示一下；
13、修改商品分类的模型时，需要影响到已添加的产品的模型id
14、非实物发货不显示快递方式,配送费用!
15、商品限制店主购买：商品编辑、增加时有个是否限制店主下单的按钮
16、积分兑换详细页增加一个原价购买按钮：实现原价购买商品
17、增加普通用户的手机号码绑定功能
18、修改物流,发货增加业务单号
19、商品首页的排序
20、修正支付超时取消订单的问题（不包括已取消的，已取消的需要另行处理）
21、修改取消商品角色没有下架所有经销商的所有这个角色下的商品
22、BUG修复：修复店铺下注册新账号不会跳转的问题
23、BUG修复：去掉平台和店铺查看更多商品时的默认售罄样式
24、修改个人店铺设置了有的商品区域价格时 显示column 'stock_price' cannot be null
25、修改经销商取消订单库存销量回滚
26、mobunion添加抢购商品订单关闭回滚
 */
define('SEED_VERSION','1.2.0.4_r0709120515');