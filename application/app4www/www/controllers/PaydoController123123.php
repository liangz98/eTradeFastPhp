<?php

class PaydoController extends Shop_Controller_Action
{
    function alipaymobAction()
    {
        $order_sn = $this->_request->getParam("out_trade_no");
        $order_token = $this->_request->getParam("order_token");

        $orderM = new Shop_Model_Order('shop');
        $userM = new Seed_Model_User('system');
        $order = $orderM->fetchRow(array('order_sn' => $order_sn, 'order_token' => $order_token));
        if ($order['order_id'] < 1) {
            Mobile_Browser::redirect("数据出错，请联系客服！", $this->view->seed_Setting['vuser_url_path']);
            exit;
        }
        $user = $userM->fetchRow(array('user_id' => $order['user_id']));
        $jump_url = $this->view->seed_Setting['vuser_url_path'];

        $order_id = $order['order_id'];

        if (isset($order['shop_id']) && $order['shop_id'] > 0) {
            $shopM = new Shop_Model_Shop('shop');
            $shop = $shopM->fetchRow(array('shop_id' => $order['shop_id']));
            if ($shop) {
                $jump_url = 'http://' . $shop['mobile_host_name'] . '/vuser';
            }
        }

        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            Mobile_Browser::redirect('支付完成！', $jump_url . "/order/info?order_id=" . $order['order_id']);
            exit;
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED || $order['order_status'] == Shop_Model_Order::OS_INVALID || $order['payment_status'] == Shop_Model_Order::PS_REFUND){
            Mobile_Browser::redirect('订单无效或已退款！', $jump_url . "/order");
            exit;
        }

        //写入结算
        $this->setBalance($order);
        //提醒
        $this->sendNotice($order);
        //日志
        $this->setLog($order, 'alipaymob');
        //业绩统计
        $this->yeji($order);
        //更改TOKEN
        $orderM->updateRow(array('order_token' => md5(rand(1000, 9999))), array('order_id' => $order_id));

        //增销量
        if (isset($this->view->seed_Setting['sold_add']) && $this->view->seed_Setting['sold_add'] == '2' ){
            Shop_Biz_Goods::soldAdd($order_id);
        }
        //减库存
        if (isset($this->view->seed_Setting['stock_reduce']) && $this->view->seed_Setting['stock_reduce'] == '2' ){
            Shop_Biz_Goods::stockReduce($order_id);
         }



        Mobile_Browser::redirect('支付完成！', $jump_url . "/order/info?order_id=" . $order['order_id']);
        exit;
    }

    function alipaymobnotifyAction()
    {
        $order_sn = $this->_request->getParam("out_trade_no");
        $total_fee = $this->_request->getParam("total_fee");
        $order_token = $this->_request->getParam("order_token");
        $refund_status = $this->_request->getParam('refund_status');
        $gmt_refund = $this->_request->getParam('gmt_refund');
        $order_amount = $total_fee;

        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn' => $order_sn, 'order_token' => $order_token));
        if ($order['order_id'] < 1) {
            exit('0');
        }
        if ($order_amount != strval($order['order_amount'])) {
            exit('0');
        }
        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            exit('1');
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED || $order['order_status'] == Shop_Model_Order::OS_INVALID || $order['payment_status'] == Shop_Model_Order::PS_REFUND){
            exit('0');
        }

        $order_id = $order['order_id'];

        /*#begin
        //抢购 减商品库存  by jacent 2015.03.24
        $orderGoodsM = new Shop_Model_OrderGoods('shop');
        $order_goodses = $orderGoodsM->fetchRows(null, array('order_id' => $order_id));
        foreach ($order_goodses as $order_goods) {
            if ($order_goods['order_goods_type'] == '1') {
                $goodsM = new Shop_Model_Goods('shop');
                $goods = $goodsM->fetchRow(array('goods_id' => $order_goods['goods_id']));
                if ($goods['coupon_type'] == 5) {
                    $goodsStockM = new Shop_Model_GoodsStock('shop');
                    $goodsStockM->updateRow(array('stock_value' => new Zend_Db_Expr("stock_value-{$order_goods['goods_number']}")), array('stock_id' => $order_goods['stock_id']));
                    $goodsM->updateRow(array('sold' => new Zend_Db_Expr("sold+{$order_goods['goods_number']}")), array('goods_id' => $order_goods['goods_id']));
                }
            }
        }
        #end*/

        if ($refund_status == "") {
        	//写入结算
        	$this->setBalance($order);
        	//提醒
        	$this->sendNotice($order);
        	//日志
        	$this->setLog($order, 'alipaymob');
        	//更改TOKEN
        	$orderM->updateRow(array('order_token' => md5(rand(1000, 9999))), array('order_id' => $order_id));

        	//增销量
        	if (isset($this->view->seed_Setting['sold_add']) && $this->view->seed_Setting['sold_add'] == '2' ){
        		Shop_Biz_Goods::soldAdd($order_id);
        	}
        	//减库存
        	if (isset($this->view->seed_Setting['stock_reduce']) && $this->view->seed_Setting['stock_reduce'] == '2' ){
        		Shop_Biz_Goods::stockReduce($order_id);
        	}

        }
        exit('1');
    }

    function weipaynotifyAction()
    {
        $order_sn = $this->_request->getParam("out_trade_no");
        $total_fee = $this->_request->getParam("total_fee");
        $order_token = $this->_request->getParam("order_token");
        $order_amount = $total_fee;

        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn' => $order_sn, 'order_token' => $order_token));
        if ($order['order_id'] < 1) {
            exit('0');
        }
        if ($order_amount != strval($order['order_amount'] * 100)) {
            exit('0');
        }
        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            exit('1');
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED || $order['order_status'] == Shop_Model_Order::OS_INVALID || $order['payment_status'] == Shop_Model_Order::PS_REFUND){
            exit('0');
        }

        $order_id = $order['order_id'];

        #begin
        //抢购 减商品库存  by jacent 2015.03.24
//                $orderGoodsM = new Shop_Model_OrderGoods('shop');
//                $order_goodses = $orderGoodsM->fetchRows(null, array('order_id' => $order_id));
//                foreach ($order_goodses as $order_goods) {
//                    if($order_goods['order_goods_type']=='1'){
//                        $goodsM = new Shop_Model_Goods('shop');
//                        $goods = $goodsM->fetchRow(array('goods_id'=>$order_goods['goods_id']));
//                        if($goods['coupon_type']==5){
//                            $goodsStockM = new Shop_Model_GoodsStock('shop');
//                            $goodsStockM->updateRow(array('stock_value'=>new Zend_Db_Expr("stock_value-{$order_goods['goods_number']}")),array('stock_id' => $order_goods['stock_id']));
//                            $goodsM->updateRow(array('sold'=>new Zend_Db_Expr("sold+{$order_goods['goods_number']}")),array('goods_id' => $order_goods['goods_id']));
//                        }
//                    }
//                }
        #end
        //写入结算
        $this->setBalance($order);
        //提醒
        $this->sendNotice($order);
        //日志
        $this->setLog($order, 'weipay');
        //更改TOKEN
        $orderM->updateRow(array('order_token' => md5(rand(1000, 9999))), array('order_id' => $order_id));

        //增销量
        if (isset($this->view->seed_Setting['sold_add']) && $this->view->seed_Setting['sold_add'] == '2' ){
            Shop_Biz_Goods::soldAdd($order_id);
        }
        //减库存
        if (isset($this->view->seed_Setting['stock_reduce']) && $this->view->seed_Setting['stock_reduce'] == '2' ){
            Shop_Biz_Goods::stockReduce($order_id);
        }
        exit('1');
    }

    //红包/优惠券/积分/抵扣时，当金额大于等于购物金额的时，则修改订单状态为已支付
    //此方法作为接口被其他控制器调用，用网关的形式调用
    //@author brave @date 2015-02-15
    function accountAction()
    {
        $order_sn = $this->_request->getParam("out_trade_no");
        $total_fee = $this->_request->getParam("total_fee");
        $order_token = $this->_request->getParam("order_token");
        $paytype = $this->_request->getParam("paytype");
        $order_amount = $total_fee;

        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn' => $order_sn, 'order_token' => $order_token));
        if ($order['order_id'] < 1) {
            exit('0');
        }
        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            exit('1');
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED || $order['order_status'] == Shop_Model_Order::OS_INVALID || $order['payment_status'] == Shop_Model_Order::PS_REFUND){
            exit('0');
        }

        $order_id = $order['order_id'];
        //写入结算
        $this->setBalance($order);
        //日志
        $this->setLog($order, $paytype);

        //增销量
        if (isset($this->view->seed_Setting['sold_add']) && $this->view->seed_Setting['sold_add'] == '2' ){
            Shop_Biz_Goods::soldAdd($order_id);
         }
        //减库存
        if (isset($this->view->seed_Setting['stock_reduce']) && $this->view->seed_Setting['stock_reduce'] == '2' ){
            Shop_Biz_Goods::stockReduce($order_id);
         }
        exit('1');
    }


    /**
     * 翼支付同步返回操作
     */
    function bestpayAction()
    {
        $order_sn = $this->_request->getParam("out_trade_no");
        $order_token = $this->_request->getParam("order_token");

        $orderM = new Shop_Model_Order('shop');
        $userM = new Seed_Model_User('system');
        $order = $orderM->fetchRow(array('order_sn' => $order_sn, 'order_token' => $order_token));
        if ($order['order_id'] < 1) {
            Mobile_Browser::redirect("数据出错，请联系客服！", $this->view->seed_Setting['vuser_url_path']);
            exit;
        }
        $user = $userM->fetchRow(array('user_id' => $order['user_id']));
        $jump_url = $this->view->seed_Setting['vuser_url_path'];

        if (isset($order['shop_id']) && $order['shop_id'] > 0) {
            $shopM = new Shop_Model_Shop('shop');
            $shop = $shopM->fetchRow(array('shop_id' => $order['shop_id']));
            if ($shop) {
                $jump_url = 'http://' . $shop['mobile_host_name'] . '/vuser';
            }
        }

        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            Mobile_Browser::redirect('支付完成！', $jump_url . "/order/info?order_id=" . $order['order_id']);
            exit;
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED || $order['order_status'] == Shop_Model_Order::OS_INVALID || $order['payment_status'] == Shop_Model_Order::PS_REFUND){
            Mobile_Browser::redirect('订单无效或已退款！', $jump_url . "/order");
            exit;
        }

        $order_id = $order['order_id'];

        //写入结算
        $this->setBalance($order);
        //提醒
        $this->sendNotice($order);
        //日志
        $this->setLog($order, 'bestpay');
        //业绩统计
        $this->yeji($order);
        //更改TOKEN
        $orderM->updateRow(array('order_token' => md5(rand(1000, 9999))), array('order_id' => $order_id));

        //增销量
        if (isset($this->view->seed_Setting['sold_add']) && $this->view->seed_Setting['sold_add'] == '2' ){
            Shop_Biz_Goods::soldAdd($order_id);
        }
        //减库存
        if (isset($this->view->seed_Setting['stock_reduce']) && $this->view->seed_Setting['stock_reduce'] == '2' ){
            Shop_Biz_Goods::stockReduce($order_id);
        }

        Mobile_Browser::redirect('支付完成！', $jump_url . "/order/info?order_id=" . $order['order_id']);
        exit;
    }

    /**
     * 翼支付通知返回操作
     */
    function bestpaynotifyAction()
    {
        $order_sn = $this->_request->getParam("out_trade_no");
        $total_fee = $this->_request->getParam("total_fee");
        $order_token = $this->_request->getParam("order_token");
        $refund_status = $this->_request->getParam('refund_status');
        $gmt_refund = $this->_request->getParam('gmt_refund');
        $order_amount = $total_fee;

        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn' => $order_sn, 'order_token' => $order_token));
        if ($order['order_id'] < 1) {
            exit('0');
        }
        if ($order_amount != strval($order['order_amount'])) {
            exit('0');
        }
        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            exit('1');
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED || $order['order_status'] == Shop_Model_Order::OS_INVALID || $order['payment_status'] == Shop_Model_Order::PS_REFUND){
            exit('0');
        }

        $order_id = $order['order_id'];
        if ($refund_status == "") {
            //写入结算
            $this->setBalance($order);
            //提醒
            $this->sendNotice($order);
            //日志
            $this->setLog($order, 'bestpay');
            //更改TOKEN
            $orderM->updateRow(array('order_token' => md5(rand(1000, 9999))), array('order_id' => $order_id));

            //增销量
            if (isset($this->view->seed_Setting['sold_add']) && $this->view->seed_Setting['sold_add'] == '2' ){
               Shop_Biz_Goods::soldAdd($order_id);
            }
            //减库存
            if (isset($this->view->seed_Setting['stock_reduce']) && $this->view->seed_Setting['stock_reduce'] == '2' ){
                Shop_Biz_Goods::stockReduce($order_id);
             }
        }
        exit('1');
    }

    /**
     * 快钱支付同步返回操作
     */
    function bill99Action()
    {
        $f1 = new Zend_Filter();
        $f1->addFilter(new Zend_Filter_StripTags())
                ->addFilter(new Zend_Filter_StripNewlines());
        $f2 = new Zend_Filter_Int();

        $order_sn = $f1->filter($this->_request->getParam("out_trade_no"));
        $order_token = $f1->filter($this->_request->getParam("order_token"));
        $payment_sn = $f1->filter($this->_request->getParam("payment_sn"));
        $total_fee = $f2->filter($this->_request->getParam("total_fee"));
        $jump_url = $this->view->seed_Setting['user_url_path'];

        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn'=>$order_sn,'order_token'=>$order_token));

        # 检查来源
        if ( !empty($order['app_key'])) {
            $appUserM = new Seed_Model_AppUser('system');
            $appUser = $appUserM->fetchRow(array('app_key' => $order['app_key']));
        }

        # 基础URL
        $baseUrl = empty($appUser['app_base_url']) ? $jump_url . '/order' : $appUser['app_base_url'];

        # 检查订单
        if (empty($order) || $total_fee != intval($order['order_amount'] * 100)) {
            Shop_Browser::redirect("数据出错，请联系客服！", $baseUrl);
            exit;
        }
        $order_amount = $order['order_amount'];

        # 跳转URL
        if (empty($appUser['app_return_url'])) {
            $redirectUrl = "{$jump_url}/order/info?order_id={$order['order_id']}";
        }
        else {
            $redirectUrl = $appUser['app_return_url'] . "?order_sn={$order['order_sn']}";
        }

        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            Shop_Browser::redirect('支付完成！', $redirectUrl);
            exit;
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED ||
                $order['order_status'] == Shop_Model_Order::OS_INVALID ||
                $order['payment_status'] == Shop_Model_Order::PS_REFUND) {
            Shop_Browser::redirect('订单无效或已退款！', $baseUrl);
            exit;
        }

        $updateData = array();
        $updateData['order_cs_status']=Shop_Model_Order::CS_WAIT_SHIP;
        $updateData['payment_status']=Shop_Model_Order::PS_PAYED;
        $updateData['order_status']=Shop_Model_Order::OS_CONFIRMED;
        $updateData['payment_time']=time();
        $updateData['payment_real_name'] = "bill99";
        $updateData['payment_real_desc'] = "快钱";
        $updateData['payment_real_amount'] = $order_amount;
        $updateData['payment_note'] = "快钱在线支付：".$order_amount;
        $updateData['payment_fee'] = $order['payment_fee'];
        $updateData['payment_name'] = "bill99";
        $updateData['payment_desc'] = '快钱';
        $updateData['payment_online'] = 1;
        $updateData['payment_sn'] = $payment_sn;
        $updateData['order_token'] = md5(time().rand(1000, 9999));
        $upId = $orderM->updateRow($updateData,array('order_id'=>$order['order_id']), true);

        if($upId !== false && $upId > 0) {
            # 提醒
            $this->notice($order);
            # 添加日志
            $actionM = new Shop_Model_OrderAction('shop');
            $insertData = array();
            $insertData['order_id'] = $order['order_id'];
            $insertData['action_user_id'] = '0';
            $insertData['action_user_name'] = '系统';
            $insertData['payment_status'] = Shop_Model_Order::PS_PAYED;
            $insertData['order_status'] = Shop_Model_Order::OS_CONFIRMED;
            $insertData['shipping_status'] = '0';
            $insertData['action_note'] = "快钱在线支付：".$order_amount;
            $insertData['log_time'] = time();
            $insertData['action_name'] = '支付';
            $actionM->insertRow($insertData);
            Shop_Browser::redirect('支付完成！', $redirectUrl);
        }
        else {
            Shop_Browser::redirect("数据出错，请联系客服！", $baseUrl);
        }
        exit;
    }

    /**
     * 快钱支付异步返回操作
     */
    function bill99notifyAction()
    {
        $f1 = new Zend_Filter();
        $f1->addFilter(new Zend_Filter_StripTags())
                ->addFilter(new Zend_Filter_StripNewlines());
        $f2 = new Zend_Filter_Int();

        $order_sn = $f1->filter($this->_request->getParam("out_trade_no"));
        $order_token = $f1->filter($this->_request->getParam("order_token"));
        $payment_sn = $f1->filter($this->_request->getParam("payment_sn"));
        $total_fee = $f2->filter($this->_request->getParam("total_fee"));
        $jump_url = $this->view->seed_Setting['user_url_path'];

        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn'=>$order_sn,'order_token'=>$order_token));
        if (empty($order) || $total_fee != intval($order['order_amount'] * 100)) {
            exit("<result>0</result>");
        }
        $order_amount = $order['order_amount'];

        # 检查来源
        if ( !empty($order['app_key'])) {
            $appUserM = new Seed_Model_AppUser('system');
            $appUser = $appUserM->fetchRow(array('app_key' => $order['app_key']));
        }

         # 跳转URL
        if (empty($appUser['app_return_url'])) {
            $redirectUrl = "{$jump_url}/order/info?order_id={$order['order_id']}";
        }
        else {
            $redirectUrl = $appUser['app_return_url'] . "?order_sn={$order['order_sn']}";
        }

        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            exit("<result>1</result><redirecturl>{$redirectUrl}</redirecturl>");
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED ||
                $order['order_status'] == Shop_Model_Order::OS_INVALID ||
                $order['payment_status'] == Shop_Model_Order::PS_REFUND) {
            exit("<result>0</result>");
        }

        $updateData = array();
        $updateData['order_cs_status']=Shop_Model_Order::CS_WAIT_SHIP;
        $updateData['payment_status']=Shop_Model_Order::PS_PAYED;
        $updateData['order_status']=Shop_Model_Order::OS_CONFIRMED;
        $updateData['payment_time']=time();
        $updateData['payment_real_name'] = "bill99";
        $updateData['payment_real_desc'] = "快钱";
        $updateData['payment_real_amount'] = $order_amount;
        $updateData['payment_note'] = "快钱在线支付：".$order_amount;
        $updateData['payment_fee'] = $order['payment_fee'];
        $updateData['payment_name'] = "bill99";
        $updateData['payment_desc'] = '快钱';
        $updateData['payment_online'] = 1;
        $updateData['payment_sn'] = $payment_sn;
        $updateData['order_token'] = md5(time().rand(1000, 9999));
        $upId = $orderM->updateRow($updateData,array('order_id'=>$order['order_id']), true);

        if($upId !== false && $upId > 0) {
            # 提醒
            $this->notice($order);
            # 添加日志
            $actionM = new Shop_Model_OrderAction('shop');
            $insertData = array();
            $insertData['order_id'] = $order['order_id'];
            $insertData['action_user_id'] = '0';
            $insertData['action_user_name'] = '系统';
            $insertData['payment_status'] = Shop_Model_Order::PS_PAYED;
            $insertData['order_status'] = Shop_Model_Order::OS_CONFIRMED;
            $insertData['shipping_status'] = '0';
            $insertData['action_note'] = "快钱在线支付：".$order_amount;
            $insertData['log_time'] = time();
            $insertData['action_name'] = '支付';
            $actionM->insertRow($insertData);
            # 访问异步支付页面
            if ( !empty($appUser['app_notify_url'])) {
                $appNotifyUrl = $appUser['app_notify_url'] . "?order_sn={$order['order_sn']}";
                Seed_Browser::view_page($appNotifyUrl);
            }
            exit("<result>1</result><redirecturl>{$redirectUrl}</redirecturl>");
        }
        else {
            exit("<result>0</result>");
        }
        exit;
    }

    /**
     * 通联支付同步返回操作
     */
    function allinpayAction()
    {
        $f1 = new Zend_Filter();
        $f1->addFilter(new Zend_Filter_StripTags())
                ->addFilter(new Zend_Filter_StripNewlines());
        $f2 = new Zend_Filter_Int();

        $order_sn = $f1->filter($this->_request->getParam("out_trade_no"));
        $order_token = $f1->filter($this->_request->getParam("order_token"));
        $payment_sn = $f1->filter($this->_request->getParam("payment_sn"));
        $total_fee = $f2->filter($this->_request->getParam("total_fee"));
        $jump_url = $this->view->seed_Setting['user_url_path'];
        $payDatetime = $f1->filter($this->_request->getParam("payDatetime"));

        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn'=>$order_sn,'order_token'=>$order_token));

        # 检查来源
        if ( !empty($order['app_key'])) {
            $appUserM = new Seed_Model_AppUser('system');
            $appUser = $appUserM->fetchRow(array('app_key' => $order['app_key']));
        }

        # 基础URL
        $baseUrl = empty($appUser['app_base_url']) ? $jump_url . '/order' : $appUser['app_base_url'];

        # 检查订单
        if (empty($order) || $total_fee != intval($order['order_amount'] * 100)) {
            Shop_Browser::redirect("数据出错，请联系客服！", $baseUrl);
            exit;
        }
        $order_amount = $order['order_amount'];

         # 跳转URL
        if (empty($appUser['app_return_url'])) {
            $redirectUrl = "{$jump_url}/order/info?order_id={$order['order_id']}";
        }
        else {
            $redirectUrl = $appUser['app_return_url'] . "?order_sn={$order['order_sn']}";
        }

        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            Shop_Browser::redirect('支付完成！', $redirectUrl);
            exit;
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED ||
                $order['order_status'] == Shop_Model_Order::OS_INVALID ||
                $order['payment_status'] == Shop_Model_Order::PS_REFUND) {
            Shop_Browser::redirect('订单无效或已退款！', $baseUrl);
            exit;
        }

        $updateData = array();
        $updateData['order_cs_status']=Shop_Model_Order::CS_WAIT_SHIP;
        $updateData['payment_status']=Shop_Model_Order::PS_PAYED;
        $updateData['order_status']=Shop_Model_Order::OS_CONFIRMED;
        $updateData['payment_time']=strtotime($payDatetime);
        $updateData['payment_real_name'] = "allinpay";
        $updateData['payment_real_desc'] = "通联在线支付";
        $updateData['payment_real_amount'] = $order_amount;
        $updateData['payment_note'] = "通联在线支付：".$order_amount;
        $updateData['payment_fee'] = $order['payment_fee'];
        $updateData['payment_name'] = 'allinpay';
        $updateData['payment_desc'] = '通联在线支付';
        $updateData['payment_online'] = 1;
        $updateData['payment_sn'] = $payment_sn;
        $updateData['order_token'] = md5(time().rand(1000, 9999));
        $upId = $orderM->updateRow($updateData,array('order_id'=>$order['order_id']), true);

        if($upId !== false && $upId > 0) {
            # 报关
            $this->allinpay_baoguan($order_sn);
            # 提醒
            $this->notice($order);
            # 添加日志
            $actionM = new Shop_Model_OrderAction('shop');
            $insertData = array();
            $insertData['order_id'] = $order['order_id'];
            $insertData['action_user_id'] = '0';
            $insertData['action_user_name'] = '系统';
            $insertData['payment_status'] = Shop_Model_Order::PS_PAYED;
            $insertData['order_status'] = Shop_Model_Order::OS_CONFIRMED;
            $insertData['shipping_status'] = '0';
            $insertData['action_note'] = "通联在线支付：".$order_amount;
            $insertData['log_time'] = $payDatetime;
            $insertData['action_name'] = '支付';
            $actionM->insertRow($insertData);
            Shop_Browser::redirect('支付完成！', $redirectUrl);
        }
        else {
            Shop_Browser::redirect("数据出错，请联系客服！", $baseUrl);
        }
        exit;
    }

    /**
     * 通联支付异步返回操作
     */
    function allinpaynotifyAction()
    {
        $f1 = new Zend_Filter();
        $f1->addFilter(new Zend_Filter_StripTags())
                ->addFilter(new Zend_Filter_StripNewlines());
        $f2 = new Zend_Filter_Int();

        $order_sn = $f1->filter($this->_request->getParam("out_trade_no"));
        $order_token = $f1->filter($this->_request->getParam("order_token"));
        $payment_sn = $f1->filter($this->_request->getParam("payment_sn"));
        $total_fee = $f2->filter($this->_request->getParam("total_fee"));
        $order_token = $f1->filter($this->_request->getParam("order_token"));
        $payDatetime = $f1->filter($this->_request->getParam("payDatetime"));
        
        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn'=>$order_sn,'order_token'=>$order_token));
        if (empty($order) || $total_fee != intval($order['order_amount'] * 100)) {
            exit("0");
        }
        $order_amount = $order['order_amount'];

        # 检查来源
        if ( !empty($order['app_key'])) {
            $appUserM = new Seed_Model_AppUser('system');
            $appNotifyUrl = $appUserM->fetchOne(array('app_key' => $order['app_key']), 'app_notify_url');
        }

        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            exit("1");
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED ||
                $order['order_status'] == Shop_Model_Order::OS_INVALID ||
                $order['payment_status'] == Shop_Model_Order::PS_REFUND) {
            exit("0");
        }

        $updateData = array();
        $updateData['order_cs_status']=Shop_Model_Order::CS_WAIT_SHIP;
        $updateData['payment_status']=Shop_Model_Order::PS_PAYED;
        $updateData['order_status']=Shop_Model_Order::OS_CONFIRMED;
        $updateData['payment_time']=strtotime($payDatetime);
        $updateData['payment_real_name'] = "allinpay";
        $updateData['payment_real_desc'] = "通联在线支付";
        $updateData['payment_real_amount'] = $order_amount;
        $updateData['payment_note'] = "通联在线支付：".$order_amount;
        $updateData['payment_fee'] = $order['payment_fee'];
        $updateData['payment_name'] = 'allinpay';
        $updateData['payment_desc'] = '通联在线支付';
        $updateData['payment_online'] = 1;
        $updateData['payment_sn'] = $payment_sn;
        $updateData['order_token'] = md5(time().rand(1000, 9999));
        $upId = $orderM->updateRow($updateData,array('order_id'=>$order['order_id']), true);

        if($upId !== false && $upId > 0) {
            # 报关
            $this->allinpay_baoguan($order_sn);
            # 提醒
            $this->notice($order);
            # 添加日志
            $actionM = new Shop_Model_OrderAction('shop');
            $insertData = array();
            $insertData['order_id'] = $order['order_id'];
            $insertData['action_user_id'] = '0';
            $insertData['action_user_name'] = '系统';
            $insertData['payment_status'] = Shop_Model_Order::PS_PAYED;
            $insertData['order_status'] = Shop_Model_Order::OS_CONFIRMED;
            $insertData['shipping_status'] = '0';
            $insertData['action_note'] = "通联在线支付：".$order_amount;
            $insertData['log_time'] = time();
            $insertData['action_name'] = '支付';
            $actionM->insertRow($insertData);
            # 访问异步支付页面
            if ( !empty($appNotifyUrl)) {
                Seed_Browser::view_page($appNotifyUrl . "?order_sn={$order['order_sn']}");
            }
            exit("1");
        }
        exit("0");
    }

    /**
     * 通联移动支付同步返回操作
     */
    function allinpaymobAction()
    {
        $f1 = new Zend_Filter();
        $f1->addFilter(new Zend_Filter_StripTags())
                ->addFilter(new Zend_Filter_StripNewlines());
        $f2 = new Zend_Filter_Int();

        $order_sn = $f1->filter($this->_request->getParam("out_trade_no"));
        $order_token = $f1->filter($this->_request->getParam("order_token"));
        $payment_sn = $f1->filter($this->_request->getParam("payment_sn"));
        $total_fee = $f2->filter($this->_request->getParam("total_fee"));
        $jump_url = $this->view->seed_Setting['vuser_url_path'];
        $payDatetime = $f1->filter($this->_request->getParam("payDatetime"));
        
        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn'=>$order_sn,'order_token'=>$order_token));

        # 检查来源
        if ( !empty($order['app_key'])) {
            $appUserM = new Seed_Model_AppUser('system');
            $appUser = $appUserM->fetchRow(array('app_key' => $order['app_key']));
        }

        # 基础URL
        $baseUrl = empty($appUser['app_base_url']) ? $jump_url . '/order' : $appUser['app_base_url'];

        # 检查订单
        if (empty($order) || $total_fee != intval($order['order_amount'] * 100)) {
            Mobile_Browser::redirect("数据出错，请联系客服！", $baseUrl);
            exit;
        }
        $order_amount = $order['order_amount'];

         # 跳转URL
        if (empty($appUser['app_return_url'])) {
            $redirectUrl = "{$jump_url}/order/info?order_id={$order['order_id']}";
        }
        else {
            $redirectUrl = $appUser['app_return_url'] . "?order_sn={$order['order_sn']}";
        }

        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            Mobile_Browser::redirect('支付完成！', $redirectUrl);
            exit;
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED ||
                $order['order_status'] == Shop_Model_Order::OS_INVALID ||
                $order['payment_status'] == Shop_Model_Order::PS_REFUND) {
            Mobile_Browser::redirect('订单无效或已退款！', $baseUrl);
            exit;
        }

        $updateData = array();
        $updateData['order_cs_status']=Shop_Model_Order::CS_WAIT_SHIP;
        $updateData['payment_status']=Shop_Model_Order::PS_PAYED;
        $updateData['order_status']=Shop_Model_Order::OS_CONFIRMED;
        $updateData['payment_time']=strtotime($payDatetime);
        $updateData['payment_real_name'] = "allinpaymob";
        $updateData['payment_real_desc'] = "通联移动支付";
        $updateData['payment_real_amount'] = $order_amount;
        $updateData['payment_note'] = "通联移动支付：".$order_amount;
        $updateData['payment_fee'] = $order['payment_fee'];
        $updateData['payment_name'] = 'allinpaymob';
        $updateData['payment_desc'] = '通联移动支付';
        $updateData['payment_online'] = 1;
        $updateData['payment_sn'] = $payment_sn;
        $updateData['order_token'] = md5(time().rand(1000, 9999));
        $upId = $orderM->updateRow($updateData,array('order_id'=>$order['order_id']), true);

        if($upId !== false && $upId > 0) {
            # 报关
            $this->allinpay_baoguan($order_sn, true);
            # 提醒
            $this->notice($order);
            # 添加日志
            $actionM = new Shop_Model_OrderAction('shop');
            $insertData = array();
            $insertData['order_id'] = $order['order_id'];
            $insertData['action_user_id'] = '0';
            $insertData['action_user_name'] = '系统';
            $insertData['payment_status'] = Shop_Model_Order::PS_PAYED;
            $insertData['order_status'] = Shop_Model_Order::OS_CONFIRMED;
            $insertData['shipping_status'] = '0';
            $insertData['action_note'] = "通联移动支付：".$order_amount;
            $insertData['log_time'] = time();
            $insertData['action_name'] = '支付';
            $actionM->insertRow($insertData);
            Mobile_Browser::redirect('支付完成！', $redirectUrl);
        }
        else {
            Mobile_Browser::redirect("数据出错，请联系客服！", $baseUrl);
        }
        exit;
    }

    /**
     * 通联支付异步返回操作
     */
    function allinpaymobnotifyAction()
    {
        $f1 = new Zend_Filter();
        $f1->addFilter(new Zend_Filter_StripTags())
                ->addFilter(new Zend_Filter_StripNewlines());
        $f2 = new Zend_Filter_Int();

        $order_sn = $f1->filter($this->_request->getParam("out_trade_no"));
        $order_token = $f1->filter($this->_request->getParam("order_token"));
        $payment_sn = $f1->filter($this->_request->getParam("payment_sn"));
        $total_fee = $f2->filter($this->_request->getParam("total_fee"));
        $payDatetime = $f1->filter($this->_request->getParam("payDatetime"));
        
        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn'=>$order_sn,'order_token'=>$order_token));
        if (empty($order) || $total_fee != intval($order['order_amount'] * 100)) {
            exit("0");
        }
        $order_amount = $order['order_amount'];

        # 检查来源
        if ( !empty($order['app_key'])) {
            $appUserM = new Seed_Model_AppUser('system');
            $appNotifyUrl = $appUserM->fetchOne(array('app_key' => $order['app_key']), 'app_notify_url');
        }

        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            exit("1");
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED ||
                $order['order_status'] == Shop_Model_Order::OS_INVALID ||
                $order['payment_status'] == Shop_Model_Order::PS_REFUND) {
            exit("0");
        }

        $updateData = array();
        $updateData['order_cs_status']=Shop_Model_Order::CS_WAIT_SHIP;
        $updateData['payment_status']=Shop_Model_Order::PS_PAYED;
        $updateData['order_status']=Shop_Model_Order::OS_CONFIRMED;
        $updateData['payment_time']=strtotime($payDatetime);
        $updateData['payment_real_name'] = "allinpaymob";
        $updateData['payment_real_desc'] = "通联移动支付";
        $updateData['payment_real_amount'] = $order_amount;
        $updateData['payment_note'] = "通联移动支付：".$order_amount;
        $updateData['payment_fee'] = $order['payment_fee'];
        $updateData['payment_name'] = 'allinpaymob';
        $updateData['payment_desc'] = '通联移动支付';
        $updateData['payment_online'] = 1;
        $updateData['payment_sn'] = $payment_sn;
        $updateData['order_token'] = md5(time().rand(1000, 9999));
        $upId = $orderM->updateRow($updateData,array('order_id'=>$order['order_id']), true);

        if($upId !== false && $upId > 0) {
            # 报关
            $this->allinpay_baoguan($order_sn, true);
            # 提醒
            $this->notice($order);
            # 添加日志
            $actionM = new Shop_Model_OrderAction('shop');
            $insertData = array();
            $insertData['order_id'] = $order['order_id'];
            $insertData['action_user_id'] = '0';
            $insertData['action_user_name'] = '系统';
            $insertData['payment_status'] = Shop_Model_Order::PS_PAYED;
            $insertData['order_status'] = Shop_Model_Order::OS_CONFIRMED;
            $insertData['shipping_status'] = '0';
            $insertData['action_note'] = "通联移动支付：".$order_amount;
            $insertData['log_time'] = time();
            $insertData['action_name'] = '支付';
            $actionM->insertRow($insertData);
            # 访问异步支付页面
            if ( !empty($appNotifyUrl)) {
                Seed_Browser::view_page($appNotifyUrl . "?order_sn={$order['order_sn']}");
            }
            exit("1");
        }
        exit("0");
    }

    /**
     * 支付宝国际支付同步返回操作
     */
    function alipayforexAction()
    {
        $f1 = new Zend_Filter();
        $f1->addFilter(new Zend_Filter_StripTags())
           ->addFilter(new Zend_Filter_StripNewlines());

        $order_sn = $f1->filter($this->_request->getParam("out_trade_no"));
        $order_token = $f1->filter($this->_request->getParam("order_token"));
        $payment_sn = $f1->filter($this->_request->getParam("payment_sn"));
        $jump_url = $this->view->seed_Setting['user_url_path'];

        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn'=>$order_sn,'order_token'=>$order_token));

        # 检查来源
        if ( !empty($order['app_key'])) {
            $appUserM = new Seed_Model_AppUser('system');
            $appUser = $appUserM->fetchRow(array('app_key' => $order['app_key']));
        }

        # 基础URL
        $baseUrl = empty($appUser['app_base_url']) ? $jump_url . '/order' : $appUser['app_base_url'];

        # 检查订单
        if (empty($order)) {
            Shop_Browser::redirect("数据出错，请联系客服！", $baseUrl);
            exit;
        }
        $order_amount = $order['order_amount'];

         # 跳转URL
        if (empty($appUser['app_return_url'])) {
            $redirectUrl = "{$jump_url}/order/info?order_id={$order['order_id']}";
        }
        else {
            $redirectUrl = $appUser['app_return_url'] . "?order_sn={$order['order_sn']}";
        }

        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            Shop_Browser::redirect('支付完成！', $redirectUrl);
            exit;
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED ||
                $order['order_status'] == Shop_Model_Order::OS_INVALID ||
                $order['payment_status'] == Shop_Model_Order::PS_REFUND) {
            Shop_Browser::redirect('订单无效或已退款！', $baseUrl);
            exit;
        }

        $updateData = array();
        $updateData['order_cs_status']=Shop_Model_Order::CS_WAIT_SHIP;
        $updateData['payment_status']=Shop_Model_Order::PS_PAYED;
        $updateData['order_status']=Shop_Model_Order::OS_CONFIRMED;
        $updateData['payment_time']=time();
        $updateData['payment_real_name'] = "alipayforex";
        $updateData['payment_real_desc'] = "支付宝国际";
        $updateData['payment_real_amount'] = $order_amount;
        $updateData['payment_note'] = "支付宝国际支付：".$order_amount;
        $updateData['payment_fee'] = $order['payment_fee'];
        $updateData['payment_name'] = 'alipayforex';
        $updateData['payment_desc'] = '支付宝国际';
        $updateData['payment_online'] = 1;
        $updateData['payment_sn'] = $payment_sn;
        $updateData['order_token'] = md5(time().rand(1000, 9999));
        $upId = $orderM->updateRow($updateData,array('order_id'=>$order['order_id']), true);

        if($upId !== false && $upId > 0) {
            # 报关
            $this->alipayforex_baoguan($order_sn);
            # 提醒
            $this->notice($order);
            # 添加日志
            $actionM = new Shop_Model_OrderAction('shop');
            $insertData = array();
            $insertData['order_id'] = $order['order_id'];
            $insertData['action_user_id'] = '0';
            $insertData['action_user_name'] = '系统';
            $insertData['payment_status'] = Shop_Model_Order::PS_PAYED;
            $insertData['order_status'] = Shop_Model_Order::OS_CONFIRMED;
            $insertData['shipping_status'] = '0';
            $insertData['action_note'] = "支付宝国际在线支付：".$order_amount;
            $insertData['log_time'] = time();
            $insertData['action_name'] = '支付';
            $actionM->insertRow($insertData);
            Shop_Browser::redirect('支付完成！', $redirectUrl);
        }
        else {
            Shop_Browser::redirect("数据出错，请联系客服！", $baseUrl);
        }
        exit;
    }

    /**
     * 支付宝国际支付异步返回操作
     */
    function alipayforexnotifyAction()
    {
        $f1 = new Zend_Filter();
        $f1->addFilter(new Zend_Filter_StripTags())
           ->addFilter(new Zend_Filter_StripNewlines());

        $order_sn = $f1->filter($this->_request->getParam("out_trade_no"));
        $order_token = $f1->filter($this->_request->getParam("order_token"));
        $payment_sn = $f1->filter($this->_request->getParam("payment_sn"));

        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn'=>$order_sn,'order_token'=>$order_token));
        if (empty($order)) {
            exit("0");
        }
        $order_amount = $order['order_amount'];

        # 检查来源
        if ( !empty($order['app_key'])) {
            $appUserM = new Seed_Model_AppUser('system');
            $appNotifyUrl = $appUserM->fetchOne(array('app_key' => $order['app_key']), 'app_notify_url');
        }

        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            exit("1");
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED ||
                $order['order_status'] == Shop_Model_Order::OS_INVALID ||
                $order['payment_status'] == Shop_Model_Order::PS_REFUND) {
            exit("0");
        }

        $updateData = array();
        $updateData['order_cs_status']=Shop_Model_Order::CS_WAIT_SHIP;
        $updateData['payment_status']=Shop_Model_Order::PS_PAYED;
        $updateData['order_status']=Shop_Model_Order::OS_CONFIRMED;
        $updateData['payment_time']=time();
        $updateData['payment_real_name'] = "alipayforex";
        $updateData['payment_real_desc'] = "支付宝国际";
        $updateData['payment_real_amount'] = $order_amount;
        $updateData['payment_note'] = "支付宝国际在线支付：".$order_amount;
        $updateData['payment_fee'] = $order['payment_fee'];
        $updateData['payment_name'] = 'alipayforex';
        $updateData['payment_desc'] = '支付宝国际';
        $updateData['payment_online'] = 1;
        $updateData['payment_sn'] = $payment_sn;
        $updateData['order_token'] = md5(time().rand(1000, 9999));
        $upId = $orderM->updateRow($updateData,array('order_id'=>$order['order_id']), true);

        if($upId !== false && $upId > 0) {
            # 报关
            $this->alipayforex_baoguan($order_sn);
            # 提醒
            $this->notice($order);
            # 添加日志
            $actionM = new Shop_Model_OrderAction('shop');
            $insertData = array();
            $insertData['order_id'] = $order['order_id'];
            $insertData['action_user_id'] = '0';
            $insertData['action_user_name'] = '系统';
            $insertData['payment_status'] = Shop_Model_Order::PS_PAYED;
            $insertData['order_status'] = Shop_Model_Order::OS_CONFIRMED;
            $insertData['shipping_status'] = '0';
            $insertData['action_note'] = "支付宝国际在线支付：".$order_amount;
            $insertData['log_time'] = time();
            $insertData['action_name'] = '支付';
            $actionM->insertRow($insertData);
            # 访问异步支付页面
            if ( !empty($appNotifyUrl)) {
                Seed_Browser::view_page($appNotifyUrl . "?order_sn={$order['order_sn']}");
            }
            exit("1");
        }
        exit("0");
    }

    /**
     * 支付宝国际移动支付同步返回操作
     */
    function alipayforexmobAction()
    {
        $f1 = new Zend_Filter();
        $f1->addFilter(new Zend_Filter_StripTags())
           ->addFilter(new Zend_Filter_StripNewlines());

        $order_sn = $f1->filter($this->_request->getParam("out_trade_no"));
        $order_token = $f1->filter($this->_request->getParam("order_token"));
        $payment_sn = $f1->filter($this->_request->getParam("payment_sn"));
        $jump_url = $this->view->seed_Setting['vuser_url_path'];

        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn'=>$order_sn,'order_token'=>$order_token));

        # 检查来源
        if ( !empty($order['app_key'])) {
            $appUserM = new Seed_Model_AppUser('system');
            $appUser = $appUserM->fetchRow(array('app_key' => $order['app_key']));
        }

        # 基础URL
        $baseUrl = empty($appUser['app_base_url']) ? $jump_url . '/order' : $appUser['app_base_url'];

        # 检查订单
        if (empty($order)) {
            Mobile_Browser::redirect("数据出错，请联系客服！", $baseUrl);
            exit;
        }
        $order_amount = $order['order_amount'];

         # 跳转URL
        if (empty($appUser['app_return_url'])) {
            $redirectUrl = "{$jump_url}/order/info?order_id={$order['order_id']}";
        }
        else {
            $redirectUrl = $appUser['app_return_url'] . "?order_sn={$order['order_sn']}";
        }

        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            Mobile_Browser::redirect('支付完成！', $redirectUrl);
            exit;
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED ||
                $order['order_status'] == Shop_Model_Order::OS_INVALID ||
                $order['payment_status'] == Shop_Model_Order::PS_REFUND) {
            Mobile_Browser::redirect('订单无效或已退款！', $baseUrl);
            exit;
        }

        $updateData = array();
        $updateData['order_cs_status']=Shop_Model_Order::CS_WAIT_SHIP;
        $updateData['payment_status']=Shop_Model_Order::PS_PAYED;
        $updateData['order_status']=Shop_Model_Order::OS_CONFIRMED;
        $updateData['payment_time']=time();
        $updateData['payment_real_name'] = "alipayforexmob";
        $updateData['payment_real_desc'] = "支付宝国际移动支付";
        $updateData['payment_real_amount'] = $order_amount;
        $updateData['payment_note'] = "支付宝国际移动支付：".$order_amount;
        $updateData['payment_fee'] = $order['payment_fee'];
        $updateData['payment_name'] = 'alipayforexmob';
        $updateData['payment_desc'] = '支付宝国际移动支付';
        $updateData['payment_online'] = 1;
        $updateData['payment_sn'] = $payment_sn;
        $updateData['order_token'] = md5(time().rand(1000, 9999));
        $upId = $orderM->updateRow($updateData,array('order_id'=>$order['order_id']), true);

        if($upId !== false && $upId > 0) {
            # 报关
            $this->alipayforex_baoguan($order_sn, true);
            # 提醒
            $this->notice($order);
            # 添加日志
            $actionM = new Shop_Model_OrderAction('shop');
            $insertData = array();
            $insertData['order_id'] = $order['order_id'];
            $insertData['action_user_id'] = '0';
            $insertData['action_user_name'] = '系统';
            $insertData['payment_status'] = Shop_Model_Order::PS_PAYED;
            $insertData['order_status'] = Shop_Model_Order::OS_CONFIRMED;
            $insertData['shipping_status'] = '0';
            $insertData['action_note'] = "支付宝国际移动支付：".$order_amount;
            $insertData['log_time'] = time();
            $insertData['action_name'] = '支付';
            $actionM->insertRow($insertData);
            Mobile_Browser::redirect('支付完成！', $redirectUrl);
        }
        else {
            Mobile_Browser::redirect("数据出错，请联系客服！", $baseUrl);
        }
        exit;
    }

    /**
     * 支付宝国际移动支付异步返回操作
     */
    function alipayforexmobnotifyAction()
    {
        $f1 = new Zend_Filter();
        $f1->addFilter(new Zend_Filter_StripTags())
           ->addFilter(new Zend_Filter_StripNewlines());

        $order_sn = $f1->filter($this->_request->getParam("out_trade_no"));
        $order_token = $f1->filter($this->_request->getParam("order_token"));
        $payment_sn = $f1->filter($this->_request->getParam("payment_sn"));

        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn'=>$order_sn,'order_token'=>$order_token));
        if (empty($order)) {
            exit("0");
        }
        $order_amount = $order['order_amount'];

        # 检查来源
        if ( !empty($order['app_key'])) {
            $appUserM = new Seed_Model_AppUser('system');
            $appNotifyUrl = $appUserM->fetchOne(array('app_key' => $order['app_key']), 'app_notify_url');
        }

        if ($order['payment_status'] == Shop_Model_Order::PS_PAYED) {
            exit("1");
        }elseif ($order['order_status'] == Shop_Model_Order::OS_CANCELED ||
                $order['order_status'] == Shop_Model_Order::OS_INVALID ||
                $order['payment_status'] == Shop_Model_Order::PS_REFUND) {
            exit("0");
        }

        $updateData = array();
        $updateData['order_cs_status']=Shop_Model_Order::CS_WAIT_SHIP;
        $updateData['payment_status']=Shop_Model_Order::PS_PAYED;
        $updateData['order_status']=Shop_Model_Order::OS_CONFIRMED;
        $updateData['payment_time']=time();
        $updateData['payment_real_name'] = "alipayforexmob";
        $updateData['payment_real_desc'] = "支付宝国际移动支付";
        $updateData['payment_real_amount'] = $order_amount;
        $updateData['payment_note'] = "支付宝国际移动支付：".$order_amount;
        $updateData['payment_fee'] = $order['payment_fee'];
        $updateData['payment_name'] = 'alipayforexmob';
        $updateData['payment_desc'] = '支付宝国际移动支付';
        $updateData['payment_online'] = 1;
        $updateData['payment_sn'] = $payment_sn;
        $updateData['order_token'] = md5(time().rand(1000, 9999));
        $upId = $orderM->updateRow($updateData,array('order_id'=>$order['order_id']), true);

        if($upId !== false && $upId > 0) {
            # 报关
            $this->alipayforex_baoguan($order_sn, true);
            # 提醒
            $this->notice($order);
            # 添加日志
            $actionM = new Shop_Model_OrderAction('shop');
            $insertData = array();
            $insertData['order_id'] = $order['order_id'];
            $insertData['action_user_id'] = '0';
            $insertData['action_user_name'] = '系统';
            $insertData['payment_status'] = Shop_Model_Order::PS_PAYED;
            $insertData['order_status'] = Shop_Model_Order::OS_CONFIRMED;
            $insertData['shipping_status'] = '0';
            $insertData['action_note'] = "支付宝国际移动支付：".$order_amount;
            $insertData['log_time'] = time();
            $insertData['action_name'] = '支付';
            $actionM->insertRow($insertData);
            # 访问异步支付页面
            if ( !empty($appNotifyUrl)) {
                Seed_Browser::view_page($appNotifyUrl . "?order_sn={$order['order_sn']}");
            }
            exit("1");
        }
        exit("0");
    }

    /**
     * 通联支付报关操作
     */
    private function allinpay_baoguan($order_sn, $is_mob = false)
    {
        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn'=>$order_sn));
        if (empty($order) || $order['return_code'] != 'EE') { return; }

        $paymentM = new Shop_Model_Payment('shop');
        $paymentParamM = new Shop_Model_PaymentParam('shop');
        if ($is_mob === true) {
            $condition = array(
                'payment_name' => 'allinpaymob',
                'is_mob' => '1'
            );
            $logName = 'allinpaymob';
        }
        else {
            $condition = array(
                'payment_name' => 'allinpay',
                'is_actived' => '1'
            );
            $logName = 'allinpay';
        }
        $payment = $paymentM->fetchRow($condition);
        if (empty($payment)) { return; }

        $paymentParams = $paymentParamM->fetchRows(null, array('payment_name' => $payment['payment_name']));
        if (!is_array($paymentParams) || count($paymentParams) < 1) { return; }
        $paramList = array();
        foreach ($paymentParams as $param) {
            $paramList[$param['setting_variable']] = $param['setting_content'];
        }

        $content = array(
            'MessageHead' => array(
                'MessageCode' => 'VNB3PARTY_PAYVOUCHER',
                'MessageID' => date('Ymdhis').'0001',
                'SenderID' => $paramList['SenderID'],
                'SendTime' => date('Y-m-d H:i:s'),
                'Sign' => ''
            ),
            'MessageBodyList' => array(
                'MessageBody' => array(
                    'customICP' => trim($paramList['customICP']),
                    'orderNo' => $order['order_sn'],
                    'ciqType'=>'01',
                    'cbepComCode'=>'1500000132',
                    'payTransactionNo' => $order['payment_sn'],
                    'payChnlID' => trim($paramList['payChnlID']),
                    'payTime' => date("Y-m-d H:i:s", $order['payment_time']),
                    'payGoodsAmount' => $order['goods_amount'] - $order['discount_amount'],
                    'payTaxAmount' => $order['tax_amount'],
                    'freight' => $order['shipping_fee'],
                    'payCurrency' => '142',
                    'payerName' => trim($order['order_realname']),
                    'payerDocumentType' => '01',
                    'payerDocumentNumber' => strtoupper(trim($order['order_idcard']))
                )
            )
        );
        $header = '<?xml version="1.0" encoding="utf-8"?>';
        $xml = new SimpleXMLElement($header . '<VnbMessage/>');
        $this->array_to_xml($content, $xml);
        $xmlRequest = $xml->asXML();
        $this->log("[BaoguanRequest]\n".$xmlRequest, $logName);
        $VnbMessage = trim(str_replace(array("\r", "\n", $header), '', $xmlRequest));
        $key = $paramList['baoguanKey'];
        $sign = strtoupper(md5($VnbMessage . $key));
        $result = current($xml->xpath('/VnbMessage/MessageHead'));
        $result->Sign = $sign;
        $stream_options = array(
            'http' => array(
               'method'  => 'POST',
               'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
               'content' => trim(str_replace(array("\r", "\n"), '', $xml->asXML())),
            ),
        );
        $context  = stream_context_create($stream_options);
        $response = file_get_contents($paramList['baoguanUrl'], null, $context);

        # 记录日志
        $this->log("[BaoguanResponse]\n".$response, $logName);

        # 检查响应内容
        if (empty($response)) { return; }

        # 解析XML
        $input = Zend_Json::decode(Zend_Json::fromXml($response));
        if (is_array($input) && !empty($input['VnbMessage'])) {
            $commCode  = @$input['VnbMessage']['MessageHead']['CommCode'];
            $bizStatus = @$input['VnbMessage']['MessageHead']['BizStatus'];
            $upData = array(
                'return_code' => 'EE',
                'return_info' => $response
            );
            if ($commCode == '000000' && $bizStatus == 'BZ0001') {
                $upData['return_code'] = 'SS';
            }
            $orderM->updateRow($upData, array('order_sn'=>$order['order_sn']));
        }
        return;
    }

    private function alipayforex_baoguan($order_sn, $is_mob = false)
    {
        $orderM = new Shop_Model_Order('shop');
        $order = $orderM->fetchRow(array('order_sn'=>$order_sn));
        if (empty($order) || $order['return_code'] != 'EE') { return; }

        $paymentM = new Shop_Model_Payment('shop');
        $paymentParamM = new Shop_Model_PaymentParam('shop');
        if ($is_mob === true) {
            $paymentName = 'alipayforexmob';
            $paymentCond = array('payment_name' => 'alipayforexmob', 'is_mob' => '1');
        }
        else {
            $paymentName = 'alipayforex';
            $paymentCond = array('payment_name' => 'alipayforex', 'is_actived' => '1');
        }
        $payment = $paymentM->fetchRow($paymentCond);
        if (empty($payment)) { return; }

        $paymentParams = $paymentParamM->fetchRows(null, array('payment_name' => $payment['payment_name']));
        if (!is_array($paymentParams) || count($paymentParams) < 1) { return; }
        $paramList = array();
        foreach ($paymentParams as $param) {
            $paramList[$param['setting_variable']] = $param['setting_content'];
        }

        # 支付宝配置
        $alipay_config = array(
            'partner' => $paramList['partnerid'],//合作身份者id
            'key' => $paramList['security_code'],//安全检验码
            'sign_type' => strtoupper('MD5'),//签名方式
            'input_charset' => strtolower('utf-8'),//字符编码格式
            'cacert' => SEED_WWW_ROOT . '/api/alipayforex/cacert.pem',//ca证书路径地址
            'transport' => 'http'//访问模式
        );

        # 构造要请求的参数数组，无需改动
        $parameter = array(
            'service' => 'alipay.acquire.customs',
            'partner' => trim($alipay_config['partner']),
            'out_request_no' => date('Ymdhis').'0001',
            'trade_no' => $order['payment_sn'],
            'merchant_customs_code' => trim($paramList['customICP']),
            'merchant_customs_name' => trim($paramList['customICPName']),
            'amount' => $order['goods_amount'] - $order['discount_amount'],
            'customs_place' => 'GUANGZHOU',
            '_input_charset' => trim(strtolower($alipay_config['input_charset']))
        );
           # 构造要请求的参数数组，无需改动
        $parameter2 = array(
            'service' => 'alipay.acquire.customs',
            'partner' => trim($alipay_config['partner']),
            'out_request_no' => date('Ymdhis').'0001',
            'trade_no' => $order['payment_sn'],
            'merchant_customs_code' => trim($paramList['customICP']),
            'merchant_customs_name' => trim($paramList['customICPName']),
            'amount' => $order['goods_amount'] - $order['discount_amount'],
            'customs_place' => 'NANSHAGJ',
            '_input_charset' => trim(strtolower($alipay_config['input_charset']))
        );

        # 记录日志
        $this->log("[BaoguanRequest]\n".Zend_Json::encode($parameter), $paymentName);

        # 建立请求
        $payAlipay = new Pay_Alipay($alipay_config);
        $response = $payAlipay->buildRequestHttp($parameter);
        $response = $payAlipay->buildRequestHttp($parameter2);

        # 记录日志
        $this->log("[BaoguanResponse]\n".$response, $paymentName);

        # 检查响应内容
        if (empty($response)) { return; }

        # 解析XML
        $result = Zend_Json::decode(Zend_Json::fromXml($response));
        if (is_array($result) && !empty($result['alipay'])) {
            $upData = array(
                'return_code' => 'EE',
                'return_info' => $response
            );
            $alipay = $result['alipay'];
            if ($alipay['is_success'] == 'T') {
                $param = $alipay['response']['alipay'];
                $isSign = $payAlipay->getSignVeryfy($param, $alipay['sign']);
                if ($isSign && $param['result_code'] == 'SUCCESS') {
                    $upData['return_code'] = 'SS';
                }
            }
            $orderM->updateRow($upData, array('order_sn'=>$order['order_sn']));
        }
        return;
    }

    /**
     * 日志记录
     */
    private function log($content, $pay_name = 'unkonwn')
    {
        $file=SEED_TEMP_ROOT."/{$pay_name}-".date('Ymd').".txt";
        if($fp = @fopen($file,"a")){
            flock($fp,LOCK_EX);
            fwrite($fp,'['.date('Y-m-d H:i:s')."]\n".$content."\n");
            flock($fp,LOCK_UN);
            fclose($fp);
        }
        return;
    }

    /**
     * 支付通知
     */
    private function notice($order)
    {
        //短信提醒(管理员)
        $mobileTempM = new Seed_Model_MobileTemplate('system');
        $condition = array('temp_name' => 'payed_notice', 'is_actived' => '1');
        $mobileTemp = $mobileTempM->fetchRow($condition);
        $mobileOutboxM = new Seed_Model_MobileOutbox('system');
        if ( !empty($mobileTemp) && strlen($mobileTemp['send_to']) == 11) {
            $content = $mobileTemp['temp_content'];
            $patterns = array();
            $replacements = array();
            $patterns[] = '/{user_name}/';
            $patterns[] = '/{order_sn}/';
            $patterns[] = '/{order_amount}/';
            $patterns[] = '/{time}/';
            $replacements[] = $order['order_realname'];
            $replacements[] = $order['order_sn'];
            $replacements[] = $order['order_amount'];
            $replacements[] = date('Y-m-d H:i:s');
            $content = preg_replace($patterns, $replacements, $content);
            $mobileOutboxM->mobileSend($mobileTemp['send_to'],$content,time());
        }

        //短信提醒(消费者)
        $mobileTempM = new Seed_Model_MobileTemplate('system');
        $condition = array('temp_name' => 'order_payed_notice', 'is_actived' => '1');
        $mobileTemp = $mobileTempM->fetchRow($condition);
        $mobileOutboxM = new Seed_Model_MobileOutbox('system');
        if ( !empty($mobileTemp) && strlen($order['mobile']) == 11) {
            $content = $mobileTemp['temp_content'];
            $patterns = array();
            $replacements = array();
            $patterns[] = '/{user_name}/';
            $patterns[] = '/{order_sn}/';
            $patterns[] = '/{order_amount}/';
            $patterns[] = '/{time}/';
            $replacements[] = $order['order_realname'];
            $replacements[] = $order['order_sn'];
            $replacements[] = $order['order_amount'];
            $replacements[] = date('Y-m-d H:i:s');
            $content = preg_replace($patterns, $replacements, $content);
            $mobileOutboxM->mobileSend($order['mobile'], $content, time());
        }
    }

    /**
     * Array to Xml
     * @param array  $ary 数组
     * @param object $xml xml对象
     */
    private function array_to_xml($ary, &$xml) {
        foreach($ary as $key => $value) {
            if(is_array($value)) {
                $child = is_numeric($key) ? "item{$key}" : $key;
                $subnode = $xml->addChild($child);
                $this->array_to_xml($value, $subnode);
            }
            else {
                $xml->addChild($key,htmlspecialchars($value));
            }
        }
    }

    /* 写入结算 */
    private function setBalance($order)
    {
        //创业者
        if ($order['shop_id'] > 0) {
            $shopM = new Shop_Model_Shop('shop');
            $userM = new Seed_Model_User('system');
            $accountM = new Shop_Model_FinanceAccount('shop');

            $shop = $shopM->fetchRow(array('shop_id' => $order['shop_id']));
            $user_id = $shop['user_id'];
            $is_balance = $shop['is_balance'];
            $is_agent = $shop['is_agent'];
            $user_name = $userM->fetchOne(array('user_id' => $user_id), 'user_name');
            $shop_account = $order['shop_account'] - $order['discount_amount'];
            $union_amount = $order['goods_union_amount'];

            if ($user_id > 0 && $shop_account > 0) {
                $accountData = array();
                $accountData['refer_id'] = $order['order_id'];
                $accountData['refer_from'] = 'order_id';
                $accountData['user_id'] = $user_id;
                $accountData['user_name'] = $user_name;
                $accountData['add_time'] = time();
                $accountData['account_value'] = sprintf('%.2f', $shop_account);
                $accountData['account_desc'] = "创业者佣金，订单号：" . $order['order_sn'];
                $accountData['admin_user_id'] = 0;
                $accountData['admin_user_name'] = '系统';
                $accountData['user_type'] = 4;
                $accountData['account_type'] = 1;
                $accountData['account_status'] = 0;

                $check = $accountM->fetchRow(array('refer_id' => $accountData['refer_id'], 'refer_from' => $accountData['refer_from'], 'user_id' => $accountData['user_id'], 'user_type' => $accountData['user_type']));
                if ($check['account_id'] < 1) {
                    $inId = $accountM->insertRow($accountData);
                    if ($inId > 0) {
                        $dataSet = array(
                            'user_amount' => new Zend_Db_Expr("user_amount+{$accountData['account_value']}"),
                            'user_freeze' => new Zend_Db_Expr("user_freeze+{$accountData['account_value']}")
                        );
                        $userM->updateRow($dataSet, array('user_id' => $accountData['user_id']));
                    }
                }
            }

            if ($user_id > 0 && $union_amount > 0 && $is_agent == '1' && $is_balance == '1') {
                $accountData = array();
                $accountData['refer_id'] = $order['order_id'];
                $accountData['refer_from'] = 'order_id';
                $accountData['user_id'] = $user_id;
                $accountData['user_name'] = $user_name;
                $accountData['add_time'] = time();
                $accountData['account_value'] = $union_amount;  //当前计算为结算货款
                $accountData['account_desc'] = "商家分销代收款，订单号：" . $order['order_sn'];
                $accountData['admin_user_id'] = 0;
                $accountData['admin_user_name'] = '系统';
                $accountData['user_type'] = 2;
                $accountData['account_type'] = 1;
                $accountData['account_status'] = 0;

                $check = $accountM->fetchRow(array('refer_id' => $accountData['refer_id'], 'refer_from' => $accountData['refer_from'], 'user_id' => $accountData['user_id'], 'user_type' => $accountData['user_type']));
                if ($check['account_id'] < 1) {
                    $inId = $accountM->insertRow($accountData);
                    if ($inId > 0) {
                        $dataSet = array(
                            'user_amount' => new Zend_Db_Expr("user_amount+{$accountData['account_value']}"),
                            'user_freeze' => new Zend_Db_Expr("user_freeze+{$accountData['account_value']}")
                        );
                        $userM->updateRow($dataSet, array('user_id' => $accountData['user_id']));
                    }
                }
            }


            /* 推荐者，修改多级分销，多级分销关联到的文件有/config/param.php
             * 2015-04-10之前部署的项目均没多级分销，过要多级分销则要重新部署，或者是增加param文件
             * 修改时间：2015-04-10
             * 修改人：brave
             */
            $user_recommender = $userM->fetchOne(array('user_id' => $user_id), 'user_recommender');
            $param_file = SEED_CONF_ROOT . "/param.php";
            //识别是否是多级结算
            $is_multi_account = 0;
            $recommender_users = array();
            if (file_exists($param_file)) {
                require $param_file;
                if (isset($SHOP_LEVEL_PERCENT)) {
                    //判断有多少个级别
                    $level=0;
                    foreach($SHOP_LEVEL_PERCENT as $kk=>$vv){
                        if($vv>0)$level++;
                    }
                    $userM = new Seed_Model_User('system');
                    $userM->_parent_nav = array();
                    $userM->_level=$level;
                    $recommender_users = $userM->getRecommender(intval($user_recommender));
                    if(is_array($recommender_users)&&count($recommender_users)>0){
                        //多级结算
                        $is_multi_account = 1;
                    }
                }
            }

            $recommender_user_ids=array();
            foreach($recommender_users as $u){
                $recommender_user_ids[]=$u['user_id'];
            }
            #Seed_Log::record('$recommender_user_ids:'.http_build_query($recommender_user_ids),'paydo.txt');
            #Seed_Log::record('$is_multi_account:'.$is_multi_account,'paydo.txt');
            if ($is_multi_account > 0) {
                foreach ($recommender_users as $k => $user) {
                    //若不存在该级别，则直接跳出
                    if (!isset($SHOP_LEVEL_PERCENT[$k])) break;

                    $recommender = $user;
                    $recommend_amount = $order['recommend_amount'] * $SHOP_LEVEL_PERCENT[$k];

                    if ($recommender['user_id'] > 0 && $recommend_amount > 0) {
                        $accountData = array();
                        $accountData['refer_id'] = $order['order_id'];
                        $accountData['refer_from'] = 'order_id';
                        $accountData['user_id'] = $recommender['user_id'];
                        $accountData['user_name'] = $recommender['user_name'];
                        $accountData['add_time'] = time();
                        $accountData['account_value'] = sprintf('%.2f', $recommend_amount);
                        $accountData['account_desc'] = "推荐者佣金，订单号：" . $order['order_sn'];
                        $accountData['admin_user_id'] = 0;
                        $accountData['admin_user_name'] = '系统';
                        $accountData['user_type'] = 2;
                        $accountData['account_type'] = 1;
                        $accountData['account_status'] = 0;
                        $check = $accountM->fetchRow(array('refer_id' => $accountData['refer_id'], 'refer_from' => $accountData['refer_from'], 'user_id' => $accountData['user_id']));
                        if ($check['account_id'] < 1) {
                            $inId = $accountM->insertRow($accountData);
                            if ($inId > 0) {
                                $dataSet = array(
                                    'user_amount' => new Zend_Db_Expr("user_amount+{$accountData['account_value']}"),
                                    'user_freeze' => new Zend_Db_Expr("user_freeze+{$accountData['account_value']}")
                                );
                                $userM->updateRow($dataSet, array('user_id' => $accountData['user_id']));
                            }
                        }
                    }
                }
            } else {
                if ($user_recommender > 0) {
                    $recommender = $userM->fetchRow(array('user_id' => $user_recommender));
                    $recommend_amount = $order['recommend_amount'];

                    if ($recommender['user_id'] > 0 && $recommend_amount > 0) {
                        $accountData = array();
                        $accountData['refer_id'] = $order['order_id'];
                        $accountData['refer_from'] = 'order_id';
                        $accountData['user_id'] = $recommender['user_id'];
                        $accountData['user_name'] = $recommender['user_name'];
                        $accountData['add_time'] = time();
                        $accountData['account_value'] = sprintf('%.2f', $recommend_amount);
                        $accountData['account_desc'] = "推荐者佣金，订单号：" . $order['order_sn'];
                        $accountData['admin_user_id'] = 0;
                        $accountData['admin_user_name'] = '系统';
                        $accountData['user_type'] = 2;
                        $accountData['account_type'] = 1;
                        $accountData['account_status'] = 0;
                        $check = $accountM->fetchRow(array('refer_id' => $accountData['refer_id'], 'refer_from' => $accountData['refer_from'], 'user_id' => $accountData['user_id']));
                        if ($check['account_id'] < 1) {
                            $inId = $accountM->insertRow($accountData);
                            if ($inId > 0) {
                                $dataSet = array(
                                    'user_amount' => new Zend_Db_Expr("user_amount+{$accountData['account_value']}"),
                                    'user_freeze' => new Zend_Db_Expr("user_freeze+{$accountData['account_value']}")
                                );
                                $userM->updateRow($dataSet, array('user_id' => $accountData['user_id']));
                            }
                        }
                    }
                }
            }
        }
    }

    private function sendNotice($order)
    {
        $order_id = $order['order_id'];

        $userM = new Seed_Model_User('system');
        $shopM = new Shop_Model_Shop('shop');

        //短信提醒(管理员)
        $mobileTempM = new Seed_Model_MobileTemplate('system');
        $mobileTemps = $mobileTempM->fetchRows(null, array('temp_name' => 'payed_notice', 'is_actived' => '1'));
        $mobileOutboxM = new Seed_Model_MobileOutbox('system');
        foreach ($mobileTemps as $mobileTemp) {
            if ($mobileTemp['temp_id'] > 0 && strlen($mobileTemp['send_to']) == 11) {
                $content = $mobileTemp['temp_content'];
                $patterns = array();
                $replacements = array();
                $patterns[] = '/{user_name}/';
                $patterns[] = '/{order_sn}/';
                $patterns[] = '/{order_amount}/';
                $patterns[] = '/{time}/';
                $replacements[] = $order['user_name'];
                $replacements[] = $order['order_sn'];
                $replacements[] = $order['order_amount'];
                $replacements[] = date('Y-m-d H:i:s');
                $content = preg_replace($patterns, $replacements, $content);
                $mobileOutboxM->mobileSend($mobileTemp['send_to'],$content,time());
            }
        }

        if (isset($order['shop_id']) && $order['shop_id'] > 0) {
            $shop = $shopM->fetchRow(array('shop_id' => $order['shop_id']));
            if ($shop['shop_id'] > 0) {
                $shop_user = $userM->fetchRow(array('user_id' => $shop['user_id']));
                if ($shop_user['user_id'] > 0 && $shop_user['user_recommender'] > 0) {
                    $recommend_user = $userM->fetchRow(array('user_id' => $shop_user['user_recommender']));
                }
            }
        }

        $f = new Seed_Filter_Mobile();
        //短信提醒(创业者)
        if ($order['shop_id'] > 0 && /*$order['shop_account'] > 0 &&*/ isset($shop_user) && strlen($f->filter($shop_user['user_mobile'])) == 11) {
            $mobileTemp = $mobileTempM->fetchRow(array('temp_name' => 'payed_shop_notice', 'is_actived' => '1'));
            if ($mobileTemp['temp_id'] > 0) {
                $content = $mobileTemp['temp_content'];
                $patterns = array();
                $replacements = array();
                $patterns[] = '/{user_name}/';
                $patterns[] = '/{order_sn}/';
                $patterns[] = '/{order_amount}/';
                $patterns[] = '/{shop_account}/';
                $patterns[] = '/{time}/';
                $replacements[] = $order['user_name'];
                $replacements[] = $order['order_sn'];
                $replacements[] = $order['order_amount'];
                $replacements[] = $order['shop_account'] - $order['discount_amount'];
                $replacements[] = date('Y-m-d H:i:s');
                $content = preg_replace($patterns, $replacements, $content);

                $mobileOutboxM->mobileSend($shop_user['user_mobile'], $content, time());
            }
        }
        //推荐者的短信通知，加入了多级分销
        $user_recommender=0;
        if(isset($recommend_user))$user_recommender = $recommend_user['user_id'];
        $param_file = SEED_CONF_ROOT . "/param.php";
        //识别是否是多级结算
        $is_multi_account = 0;
        $recommender_users = array();
        if (file_exists($param_file)) {
            require $param_file;
            if (isset($SHOP_LEVEL_PERCENT)) {
                //判断有多少个级别
                $level=0;
                foreach($SHOP_LEVEL_PERCENT as $kk=>$vv){
                    if($vv>0)$level++;
                }
                $userM = new Seed_Model_User('system');
                $userM->_parent_nav = array();
                $userM->_level=$level;
                $recommender_users = $userM->getRecommender(intval($user_recommender));
                if(is_array($recommender_users)&&count($recommender_users)>0){
                    //多级结算
                    $is_multi_account = 1;
                }
            }
        }
        $recommender_user_ids=array();
        foreach($recommender_users as $k=>$recommender_user){
            $recommender_user_ids[]=$recommender_user['user_id'];
        }
        if ($is_multi_account > 0) {
            foreach ($recommender_users as $k => $user) {
                //若不存在该级别，则直接跳出
                if (!isset($SHOP_LEVEL_PERCENT[$k])) break;
                $recommend_user = $userM->fetchRow(array('user_id' => $user['user_id']));
                //短信提醒(推荐者)
                if ($order['shop_id'] > 0 && $order['recommend_amount'] > 0 && isset($recommend_user) && strlen($f->filter($recommend_user['user_mobile'])) == 11) {
                    $mobileTemp = $mobileTempM->fetchRow(array('temp_name' => 'payed_recommender_notice', 'is_actived' => '1'));
                    if ($mobileTemp['temp_id'] > 0) {
                        $content = $mobileTemp['temp_content'];
                        $patterns = array();
                        $replacements = array();
                        $patterns[] = '/{user_name}/';
                        $patterns[] = '/{shop_name}/';
                        $patterns[] = '/{order_sn}/';
                        $patterns[] = '/{order_amount}/';
                        $patterns[] = '/{recommend_amount}/';
                        $patterns[] = '/{time}/';
                        $replacements[] = $order['user_name'];
                        $replacements[] = $shop_user['user_name'];
                        $replacements[] = $order['order_sn'];
                        $replacements[] = $order['order_amount'];
                        //按照固定的比例算佣金
                        $replacements[] = $order['recommend_amount'] * $SHOP_LEVEL_PERCENT[$k];
                        $replacements[] = date('Y-m-d H:i:s');
                        $content = preg_replace($patterns, $replacements, $content);

                        $mobileOutboxM->mobileSend($recommend_user['user_mobile'], $content, time());
                    }
                }
            }
        } else {
            //短信提醒(推荐者)
            if ($order['shop_id'] > 0 && $order['recommend_amount'] > 0 && isset($recommend_user) && strlen($f->filter($recommend_user['user_mobile'])) == 11) {
                $mobileTemp = $mobileTempM->fetchRow(array('temp_name' => 'payed_recommender_notice', 'is_actived' => '1'));
                if ($mobileTemp['temp_id'] > 0) {
                    $content = $mobileTemp['temp_content'];
                    $patterns = array();
                    $replacements = array();
                    $patterns[] = '/{user_name}/';
                    $patterns[] = '/{shop_name}/';
                    $patterns[] = '/{order_sn}/';
                    $patterns[] = '/{order_amount}/';
                    $patterns[] = '/{recommend_amount}/';
                    $patterns[] = '/{time}/';
                    $replacements[] = $order['user_name'];
                    $replacements[] = $shop_user['user_name'];
                    $replacements[] = $order['order_sn'];
                    $replacements[] = $order['order_amount'];
                    $replacements[] = $order['recommend_amount'];
                    $replacements[] = date('Y-m-d H:i:s');
                    $content = preg_replace($patterns, $replacements, $content);

                    $mobileOutboxM->mobileSend($recommend_user['user_mobile'], $content, time());
                }
            }
        }

        //短信提醒(消费者)
        if ($order['order_amount'] > 0 && strlen($f->filter($order['telephone'])) == 11) {
            $mobileTemp = $mobileTempM->fetchRow(array('temp_name' => 'order_payed_notice', 'is_actived' => '1'));
            if ($mobileTemp['temp_id'] > 0) {
                $content = $mobileTemp['temp_content'];
                $patterns = array();
                $replacements = array();
                $patterns[] = '/{user_name}/';
                $patterns[] = '/{order_sn}/';
                $patterns[] = '/{order_amount}/';
                $patterns[] = '/{time}/';
                $replacements[] = $order['user_name'];
                $replacements[] = $order['order_sn'];
                $replacements[] = $order['order_amount'];
                $replacements[] = date('Y-m-d H:i:s');
                $content = preg_replace($patterns, $replacements, $content);

                $mobileOutboxM->mobileSend($order['telephone'], $content, time());
            }
        }
        $wechatUserM = new Wechat_Model_User('wechat');
        $wc_user = $wechatUserM->fetchRow(array('user_id' => $order['user_id']));

        $bid = 0;
        $wechatM = new Wechat_Model_Wechat('wechat');
        $wechat = $wechatM->fetchRow(array('is_del' => '0'));
        if ($wechat['id'] > 0) {
            $bid = $wechat['id'];
            $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($bid);
        }

        $orderGoodsM = new Shop_Model_OrderGoods('shop');
        $goodses = $orderGoodsM->fetchRows(null, array('order_id' => $order_id));
        $goods_list = "";
        foreach ($goodses as $v) {
            $goods_list .= "[" . $v['goods_number'] . "] × " . $v['goods_name'] . "";
            if (!empty($v['goods_attr'])) $goods_list .= " " . $v['goods_attr'];
            $goods_list .= "";
        }

        //微信模版通知
        //微信通知(创业者)
        $noteTplM = new Wechat_Model_Notetpl('wechat');
        $tpl = $noteTplM->fetchRow(array('nt_name' => 'pay_success', 'is_actived' => '1'));
        if (/*$order['shop_account'] > 0 && */$tpl && $bid > 0 && isset($shop_user) && !empty($shop_user['wc_username'])) {
            preg_match_all("/\{\{([\w]+)\.DATA\}\}/is", $tpl['nt_data'], $matches, PREG_PATTERN_ORDER);
            if (isset($matches[1]) && count($matches[1]) == 4) {
                $replacements = array();
                $replacements[] = '尊敬的用户，我们很高兴的通知您，您的微店有新订单支付';
                $replacements[] = $order['order_amount'];
                $replacements[] = $goods_list;
                $replacements[] = '客户确认订单后，您将获得' . ($order['shop_account'] - $order['discount_amount']) . '元佣金！';
                $Template = new Wechat_AdvanceAPI_Template($bid);
                foreach ($matches[1] as $key => $value) {//处理数据组合
                    $Template->combineData($value, $replacements[$key]);
                }
                $touser = $tpl['send_to'] ? $tpl['send_to'] : $shop_user['wc_username'];
                $template_id = $tpl['tpl_id'];
                $url = $this->view->seed_Setting['mobunion_app_server'] . "/order/";
                $res = $Template->send($touser, $template_id, $url);
            }
        }

        //推荐者的微信官方模版通知，加入了多级分销
        if ($is_multi_account > 0) {
            //微信通知(推荐者)
            $noteTplM = new Wechat_Model_Notetpl('wechat');
            $tpl = $noteTplM->fetchRow(array('nt_name' => 'pay_success', 'is_actived' => '1'));
            foreach ($recommender_users as $k => $user) {
                //若不存在该级别，则直接跳出
                if (!isset($SHOP_LEVEL_PERCENT[$k])) break;
                $recommend_user = $userM->fetchRow(array('user_id' => $user['user_id']));
                if ($order['recommend_amount'] > 0 && $tpl && $bid > 0 && isset($recommend_user) && !empty($recommend_user['wc_username'])) {
                    preg_match_all("/\{\{([\w]+)\.DATA\}\}/is", $tpl['nt_data'], $matches, PREG_PATTERN_ORDER);
                    if (isset($matches[1]) && count($matches[1]) == 4) {
                        $replacements = array();
                        $replacements[] = '恭喜！你推荐的微店主' . $shop_user['user_name'] . '，有人在那里下单支付了';
                        $replacements[] = $order['order_amount'] * $SHOP_LEVEL_PERCENT[$k];
                        $replacements[] = $goods_list;
                        $replacements[] = '推荐店主越多，订单越多，推荐佣金越多！';
                        $Template = new Wechat_AdvanceAPI_Template($bid);
                        foreach ($matches[1] as $key => $value) {//处理数据组合
                            $Template->combineData($value, $replacements[$key]);
                        }
                        $touser = $tpl['send_to'] ? $tpl['send_to'] : $recommend_user['wc_username'];
                        $template_id = $tpl['tpl_id'];
                        $url = '';
                        $res = $Template->send($touser, $template_id, $url);
                    }
                }
            }
        } else {
            //微信通知(推荐者)
            $noteTplM = new Wechat_Model_Notetpl('wechat');
            $tpl = $noteTplM->fetchRow(array('nt_name' => 'pay_success', 'is_actived' => '1'));
            if ($order['recommend_amount'] > 0 && $tpl && $bid > 0 && isset($recommend_user) && !empty($recommend_user['wc_username'])) {
                preg_match_all("/\{\{([\w]+)\.DATA\}\}/is", $tpl['nt_data'], $matches, PREG_PATTERN_ORDER);
                if (isset($matches[1]) && count($matches[1]) == 4) {
                    $replacements = array();
                    $replacements[] = '恭喜！你推荐的微店主' . $shop_user['user_name'] . '，有人在那里下单支付了';
                    $replacements[] = $order['order_amount'];
                    $replacements[] = $goods_list;
                    $replacements[] = '推荐店主越多，订单越多，推荐佣金越多！';
                    $Template = new Wechat_AdvanceAPI_Template($bid);
                    foreach ($matches[1] as $key => $value) {//处理数据组合
                        $Template->combineData($value, $replacements[$key]);
                    }
                    $touser = $tpl['send_to'] ? $tpl['send_to'] : $recommend_user['wc_username'];
                    $template_id = $tpl['tpl_id'];
                    $url = '';
                    $res = $Template->send($touser, $template_id, $url);
                }
            }
        }


        //微信通知(消费者)
        $noteTplM = new Wechat_Model_Notetpl('wechat');
        $tpl = $noteTplM->fetchRow(array('nt_name' => 'pay_success', 'is_actived' => '1'));
        if ($order['order_amount'] > 0 && $tpl && $bid > 0 && isset($user) && !empty($user['wc_username'])) {
            preg_match_all("/\{\{([\w]+)\.DATA\}\}/is", $tpl['nt_data'], $matches, PREG_PATTERN_ORDER);
            if (isset($matches[1]) && count($matches[1]) == 4) {
                $replacements = array();
                $replacements[] = '支付成功通知';
                $replacements[] = $order['order_amount'];
                $replacements[] = $goods_list;
                $replacements[] = '我们将尽快给您发货';
                $Template = new Wechat_AdvanceAPI_Template($bid);
                foreach ($matches[1] as $key => $value) {//处理数据组合
                    $Template->combineData($value, $replacements[$key]);
                }
                $touser = $tpl['send_to'] ? $tpl['send_to'] : $user['wc_username'];
                $template_id = $tpl['tpl_id'];
                $url = $this->view->seed_Setting['vuser_url_path'] . "/order/info?order_id=" . $order['order_id'];
                $res = $Template->send($touser, $template_id, $url);
            }
        }

        //微信通知(创业者)
        $wechatTempM = new Seed_Model_WechatTemplate('system');
        $wechatTemp = $wechatTempM->fetchRow(array('temp_name' => 'payed_shop_notice', 'is_actived' => '1'));
        if (/*$order['shop_account'] > 0 &&*/ $wechatTemp['temp_id'] > 0 && $bid > 0 && isset($shop_user) && !empty($shop_user['wc_username'])) {
            $patterns = array();
            $replacements = array();
            $patterns[] = '/{nick_name}/';
            $patterns[] = '/{order_sn}/';
            $patterns[] = '/{goods_amount}/';
            $patterns[] = '/{shipping_fee}/';
            $patterns[] = '/{payment_fee}/';
            $patterns[] = '/{discount_amount}/';
            $patterns[] = '/{integral_fee}/';
            $patterns[] = '/{offer_fee}/';
            $patterns[] = '/{order_amount}/';
            $patterns[] = '/{shop_account}/';
            $patterns[] = '/{goods_list}/';
            $patterns[] = '/{time}/';
            $replacements[] = $wc_user['nick_name'];
            $replacements[] = "<a href='" . $this->view->seed_Setting['vuser_url_path'] . "/order/info?order_id=" . $order['order_id'] . "'>" . $order['order_sn'] . "</a>";
            $replacements[] = $order['goods_amount'];
            $replacements[] = $order['shipping_fee'];
            $replacements[] = $order['payment_fee'];
            $replacements[] = $order['discount_amount'];
            $replacements[] = $order['integral_fee'];
            $replacements[] = $order['offer_fee'];
            $replacements[] = $order['order_amount'];
            $replacements[] = $order['shop_account'] - $order['discount_amount'];
            $replacements[] = $goods_list;
            $replacements[] = date('Y-m-d H:i:s');

            $content = $wechatTemp['temp_content'];
            $content = preg_replace($patterns, $replacements, $content);
            $content = $content . " <a href='" . $this->view->seed_Setting['mobunion_app_server'] . "/order/'> 点击查看订单详情 >></a>";

            $re = $kefuAPI->send_notice($shop_user['wc_username'], $content);
        }

        //推荐者微信模版通知（非官方模版）加入多级分销了
        if ($is_multi_account > 0) {
            //微信通知(推荐者)
            $wechatTemp = $wechatTempM->fetchRow(array('temp_name' => 'payed_recommender_notice', 'is_actived' => '1'));
            foreach ($recommender_users as $k => $user) {
                //若不存在该级别，则直接跳出
                if (!isset($SHOP_LEVEL_PERCENT[$k])) break;
                $recommend_user = $userM->fetchRow(array('user_id' => $user['user_id']));
                if ($order['recommend_amount'] > 0 && $wechatTemp['temp_id'] > 0 && $bid > 0 && isset($recommend_user) && !empty($recommend_user['wc_username'])) {
                    $patterns = array();
                    $replacements = array();
                    $patterns[] = '/{nick_name}/';
                    $patterns[] = '/{shop_name}/';
                    $patterns[] = '/{order_sn}/';
                    $patterns[] = '/{goods_amount}/';
                    $patterns[] = '/{shipping_fee}/';
                    $patterns[] = '/{payment_fee}/';
                    $patterns[] = '/{discount_amount}/';
                    $patterns[] = '/{integral_fee}/';
                    $patterns[] = '/{offer_fee}/';
                    $patterns[] = '/{order_amount}/';
                    $patterns[] = '/{recommend_amount}/';
                    $patterns[] = '/{goods_list}/';
                    $patterns[] = '/{time}/';
                    $replacements[] = $wc_user['nick_name'];
                    $replacements[] = $shop_user['user_name'];
                    $replacements[] = $order['order_sn'];
                    $replacements[] = $order['goods_amount'];
                    $replacements[] = $order['shipping_fee'];
                    $replacements[] = $order['payment_fee'];
                    $replacements[] = $order['discount_amount'];
                    $replacements[] = $order['integral_fee'];
                    $replacements[] = $order['offer_fee'];
                    $replacements[] = $order['order_amount'];
                    $replacements[] = $order['recommend_amount'] * $SHOP_LEVEL_PERCENT[$k];
                    $replacements[] = $goods_list;
                    $replacements[] = date('Y-m-d H:i:s');

                    $content = $wechatTemp['temp_content'];
                    $content = preg_replace($patterns, $replacements, $content);

                    $re = $kefuAPI->send_notice($recommend_user['wc_username'], $content);
                }
            }
        } else {
            //微信通知(推荐者)
            $wechatTemp = $wechatTempM->fetchRow(array('temp_name' => 'payed_recommender_notice', 'is_actived' => '1'));
            if ($order['recommend_amount'] > 0 && $wechatTemp['temp_id'] > 0 && $bid > 0 && isset($recommend_user) && !empty($recommend_user['wc_username'])) {
                $patterns = array();
                $replacements = array();
                $patterns[] = '/{nick_name}/';
                $patterns[] = '/{shop_name}/';
                $patterns[] = '/{order_sn}/';
                $patterns[] = '/{goods_amount}/';
                $patterns[] = '/{shipping_fee}/';
                $patterns[] = '/{payment_fee}/';
                $patterns[] = '/{discount_amount}/';
                $patterns[] = '/{integral_fee}/';
                $patterns[] = '/{offer_fee}/';
                $patterns[] = '/{order_amount}/';
                $patterns[] = '/{recommend_amount}/';
                $patterns[] = '/{goods_list}/';
                $patterns[] = '/{time}/';
                $replacements[] = $wc_user['nick_name'];
                $replacements[] = $shop_user['user_name'];
                $replacements[] = $order['order_sn'];
                $replacements[] = $order['goods_amount'];
                $replacements[] = $order['shipping_fee'];
                $replacements[] = $order['payment_fee'];
                $replacements[] = $order['discount_amount'];
                $replacements[] = $order['integral_fee'];
                $replacements[] = $order['offer_fee'];
                $replacements[] = $order['order_amount'];
                $replacements[] = $order['recommend_amount'];
                $replacements[] = $goods_list;
                $replacements[] = date('Y-m-d H:i:s');

                $content = $wechatTemp['temp_content'];
                $content = preg_replace($patterns, $replacements, $content);

                $re = $kefuAPI->send_notice($recommend_user['wc_username'], $content);
            }
        }


        //微信通知(消费者)
        $wechatTemp = $wechatTempM->fetchRow(array('temp_name' => 'order_pay', 'is_actived' => '1'));
        $user = $userM->fetchRow(array('user_id' => $order['user_id']));
        if ($order['order_amount'] > 0 && $wechatTemp['temp_id'] > 0 && $bid > 0 && isset($user) && !empty($user['wc_username'])) {
            $patterns = array();
            $replacements = array();
            $patterns[] = '/{nick_name}/';
            $patterns[] = '/{order_sn}/';
            $patterns[] = '/{goods_amount}/';
            $patterns[] = '/{shipping_fee}/';
            $patterns[] = '/{payment_fee}/';
            $patterns[] = '/{discount_amount}/';
            $patterns[] = '/{integral_fee}/';
            $patterns[] = '/{offer_fee}/';
            $patterns[] = '/{order_amount}/';
            $patterns[] = '/{goods_list}/';
            $patterns[] = '/{time}/';
            $replacements[] = $wc_user['nick_name'];
            $replacements[] = "<a href='" . $this->view->seed_Setting['vuser_url_path'] . "/order/info?order_id=" . $order['order_id'] . "'>" . $order['order_sn'] . "</a>";
            $replacements[] = $order['goods_amount'];
            $replacements[] = $order['shipping_fee'];
            $replacements[] = $order['payment_fee'];
            $replacements[] = $order['discount_amount'];
            $replacements[] = $order['integral_fee'];
            $replacements[] = $order['offer_fee'];
            $replacements[] = $order['order_amount'];
            $replacements[] = $goods_list;
            $replacements[] = date('Y-m-d H:i:s');

            $content = $wechatTemp['temp_content'];
            $content = preg_replace($patterns, $replacements, $content);
            $content = $content . " <a href='" . $this->view->seed_Setting['vuser_url_path'] . "/order/info?order_id=" . $order['order_id'] . "'> 点击查看订单详情 >></a>";

            $re = $kefuAPI->send_notice($user['wc_username'], $content);
        }
    }

    private function setLog($order, $paytype)
    {
        $action_note = "";
        if ($paytype == 'alipaymob') $action_note = "支付宝手机";
        if ($paytype == 'weipay') $action_note = "微支付";

        $updateData = array();
        $updateData['update_last_userid'] = 0;
        $updateData['update_time'] = time();
        $updateData['payment_status'] = Shop_Model_Order::PS_PAYED;
        $updateData['order_status'] = Shop_Model_Order::OS_CONFIRMED;
        $updateData['order_cs_status'] = Shop_Model_Order::CS_WAIT_SHIP;
        $updateData['payment_time'] = time();
        $updateData['payment_real_name'] = $paytype;
        $updateData['payment_real_desc'] = $action_note;
        $updateData['payment_real_amount'] = $order['order_amount'];
        $updateData['payment_note'] = $action_note;
        $orderM = new Shop_Model_Order('shop');
        $orderM->updateRow($updateData, array('order_id' => $order['order_id']));

        //添加日志
        $actionM = new Shop_Model_OrderAction('shop');
        $insertData = array();
        $insertData['order_id'] = $order['order_id'];
        $insertData['action_user_id'] = '0';
        $insertData['action_user_name'] = '系统';
        $insertData['payment_status'] = Shop_Model_Order::PS_PAYED;
        $insertData['order_status'] = Shop_Model_Order::OS_CONFIRMED;
        $insertData['shipping_status'] = '0';
        $insertData['action_note'] = $action_note;
        $insertData['log_time'] = time();
        $insertData['action_name'] = '支付';
        $actionM->insertRow($insertData);
    }

    private function yeji($order)
    {
        $order_id = $order['order_id'];
        $kf_id = $order['order_kefu_id'];
        if ($kf_id > 0 && isset($this->view->seed_Setting['yeji_app_server']) && isset($this->view->seed_Setting['yeji_app_host']) && !empty($this->view->seed_Setting['yeji_app_server']) && !empty($this->view->seed_Setting['yeji_app_host'])) {
            $goods_list = "";
            $orderGoodsM = new Shop_Model_OrderGoods('shop');
            $goodses = $orderGoodsM->fetchRows(null, array('order_id' => $order_id));
            $goods_name_list = array();
            foreach ($goodses as $v) {
                $goods_name_list[] = $v['goods_name'];
            }
            $userM = new Seed_Model_User('system');
            $user_info = $userM->fetchRow(array('user_id' => $order['user_id']));

            $kf_info = array(
                'kf_id' => $kf_id,
                'order_sn' => $order['order_sn'],
                'goods_name' => implode('，', $goods_name_list),
                'money' => $order['order_amount'],
                'order_time' => $order['add_time'],
                'user_id' => $user_info['user_id'],
                'qq' => $user_info['user_qq'],
                'telephone' => !empty($order['mobile']) ? $order['mobile'] : $order['telephone'],
                'user_real_name' => isset($order['consignee']) ? $order['consignee'] : $user_name,
            );
            $kf_data = base64_encode(Zend_Json::encode($kf_info));
            $key = md5($kf_data . $kf_info['order_sn'] . $kf_info['kf_id'] . $this->view->seed_Setting['yeji_app_host']);
            $rs = $this->curlpost($this->view->seed_Setting['yeji_app_server'], array(
                'key' => md5($kf_data . $kf_info['order_sn'] . $kf_info['kf_id'] . $this->view->seed_Setting['yeji_app_host']),
                'data' => $kf_data,
            ));
        }
    }

    private function curlpost($url, array $post = array(), array $options = array())
    {
        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36',
            CURLOPT_POSTFIELDS => $post
        );
        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($result === false) {
            return false;
        } else {
            return $result;
        }
    }
}