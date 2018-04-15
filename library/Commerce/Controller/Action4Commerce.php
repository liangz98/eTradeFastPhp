<?php
class Commerce_Controller_Action4Commerce extends Zend_Controller_Action
{
    protected $_setting;

	public function init()
	{
		if(file_exists(SEED_LICENSE_ROOT."/init.php")){
			require(SEED_LICENSE_ROOT."/init.php");
		}else{
			exit("License File Not Found!");
		}

		if(!defined('CURRENT_MODULE_NAME') || !defined('CURRENT_MODULE_TYPE'))throw new Exception("CURRENT_MODULE_NAME or CURRENT_MODULE_TYPE not defined");
		$this->initView();
		$this->view->seed_BaseUrl = $this->_request->getBaseUrl();

        $fileM = new Seed_Model_Cache2File();
        $mod_name = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;
		$controller_name = $this->_request->getControllerName();
		$action_name = $this->_request->getActionName();
		$current = $mod_name.".".$controller_name.".".$action_name;

        //获取系统设置
		if(defined('SEED_HOST_NAME')){
			$seed_host_name = str_replace(".","_",SEED_HOST_NAME);
			$cachefile = $mod_name."_".strtolower($seed_host_name)."_setting";
		}else{
			$cachefile = $mod_name."_setting";
		}

		$setting = $fileM->get($cachefile);
		if(isset($setting['mobile_send_ecode']) && isset($setting['mobile_send_username']) && isset($setting['mobile_send_password'])){
			if(!defined('MOBILE_SEND_ECODE'))define('MOBILE_SEND_ECODE',$setting['mobile_send_ecode']);
			if(!defined('MOBILE_SEND_USERNAME'))define('MOBILE_SEND_USERNAME',$setting['mobile_send_username']);
			if(!defined('MOBILE_SEND_PASSWORD'))define('MOBILE_SEND_PASSWORD',$setting['mobile_send_password']);
		}
		if(!isset($setting['website_domain']))$setting['website_domain']=$_SERVER['HTTP_HOST'];
		if(!isset($setting['upload_app_server']))$setting['upload_app_server']="http://".$_SERVER['HTTP_HOST']."/uploadapi";
		if(!isset($setting['upload_view_server']))$setting['upload_view_server']="http://".$_SERVER['HTTP_HOST']."/upload_files";
		if(!isset($setting['upload_return_server']))$setting['upload_return_server']="";
		if(!isset($setting['www_app_server']))$setting['www_app_server']="http://".$_SERVER['HTTP_HOST'];
		if(!isset($setting['v_app_server']))$setting['v_app_server']="http://".$_SERVER['HTTP_HOST'];
		if(!isset($setting['union_app_server']))$setting['union_app_server']="http://".$_SERVER['HTTP_HOST'];
		if(!isset($setting['wechat_app_server']))$setting['wechat_app_server']="http://".$_SERVER['HTTP_HOST'];
		if(!isset($setting['vuser_app_server']))$setting['vuser_app_server']="http://".$_SERVER['HTTP_HOST']."/vuser";
		if(!isset($setting['vmall_app_server']))$setting['vmall_app_server']="http://".$_SERVER['HTTP_HOST']."/vmall";
		if(!isset($setting['vhome_app_server']))$setting['vhome_app_server']="http://".$_SERVER['HTTP_HOST']."/vhome";
		if(!isset($setting['vservice_app_server']))$setting['vservice_app_server']="http://".$_SERVER['HTTP_HOST']."/vservice";
		if(!isset($setting['vmarketing_app_server']))$setting['vmarketing_app_server']="http://".$_SERVER['HTTP_HOST']."/vmarketing";
		if(!isset($setting['vplug_app_server']))$setting['vplug_app_server']="http://".$_SERVER['HTTP_HOST']."/vplug";
		if(!isset($setting['mobunion_app_server']))$setting['mobunion_app_server']="http://".$_SERVER['HTTP_HOST']."/mobunion";
		if(!isset($setting['agent_app_server']))$setting['agent_app_server']="http://".$_SERVER['HTTP_HOST']."/agent";
		if(!isset($setting['static_app_server']))$setting['static_app_server']="http://".$_SERVER['HTTP_HOST']."/static";
		$this->view->seed_Setting=$setting;

		$token = $this->_request->getParam('token');
		$fromapi=false;
		if(empty($token)){
			$token = Seed_Auth::getInstance()->getIdentity();
		}else{
			$fromapi=true;
		}
		$this->view->seed_Token = $token;
		$userM = new Seed_Model_User('system');
		if(!empty($token)){
			$my = Seed_Token::decode($token);
			if(!is_array($my)){
				if ($this->_request->isPost())
					exit('关键数据损坏！');
				else
					throw new Exception('关键数据损坏！');
				exit;
			}
			$user = $userM->fetchRow(array('user_id'=>$my['user_id'],'token'=>$my['token'],'is_admin'=>'1','is_actived'=>'1'));
			if($user['user_id']>0){
				$userRoleM = new Seed_Model_UserRole('system');
				$user['roles']=$userRoleM->fetchRows(null,array('user_id'=>$user['user_id']));
                $shop_role = empty($setting['shop_role']) ? array() : explode(',', $setting['shop_role']);
                foreach($user['roles'] as $role) {
                    if(in_array($role['role_name'], $shop_role)) {
                        $user['role_name'] = $role['role_name'];
                        break;
                    }
                }
//                if (empty($user['role_name'])) {
//                    $updateData = array();
//                    $updateData['token']='';
//                    $userM->updateRow($updateData,array('user_id'=>$my['user_id']));
//                    Seed_Auth::getInstance()->clearIdentity();
//                    Seed_Browser::redirect('此账号不是商家管理账号', $this->view->seed_BaseUrl.'/admin/login', 1000,"top");
//                }
			}
			$user['token']=$token;
			$this->view->seed_User = $user;
		}

		$exceptiveArr=array();
		$exceptiveArr[]=CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE.".admin.login";
		$exceptiveArr[]=CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE.".vcode.index";

		if ((!isset($this->view->seed_User['user_id']) || $this->view->seed_User['user_id']<1 || $this->view->seed_User['is_admin']!='1') && !in_array($current,$exceptiveArr)) {
            if(isset($_SERVER['REQUEST_URI']) && rtrim($_SERVER['REQUEST_URI'],'/') == '/b'){
                $this->_redirect('/admin/login');
                exit;
            }
            Seed_Browser::redirect('还没有登录？',"/b/admin/login",3000,'top');
            exit;
    	}

		//获取访问控制列表ACL
		if(defined('SEED_HOST_NAME')){
			$seed_host_name = str_replace(".","_",SEED_HOST_NAME);
			$aclfile = $mod_name."_".strtolower($seed_host_name)."_acls";
		}else{
			$aclfile = $mod_name."_acls";
		}

		$acls = $fileM->get($aclfile);
		$this->view->seed_Acls = $acls;

		if(is_array($acls['resources']) && in_array($current,$acls['resources'])){
			$deny=true;
			$roles =$this->view->seed_User['roles'];
			foreach ($roles as $role){
				if($role['mod_name']=="center_admin"){
					if(isset($acls['acls'][$role['role_name']]) && is_array($acls['acls'][$role['role_name']]) && in_array($current,$acls['acls'][$role['role_name']]))$deny=false;
				}
			}
			if($deny){
				if ($this->_request->isPost())
					exit('权限不够，访问受限！');
				else
					throw new Exception('权限不够，访问受限！');
				exit;
			}
		}

		if(isset($this->view->seed_User['user_id']) && $this->view->seed_User['user_id']>0){
			$ruleM = new Seed_Model_Rule('system');
			$check = $ruleM->fetchRow(array('mod_name'=>$mod_name,'res_name'=>$controller_name,'priv_name'=>$action_name,'is_log'=>'1'));
			if($check['rule_id']>0){
				$resourceM = new Seed_Model_Resource('system');
				$c_resource = $resourceM->fetchRow(array('mod_name'=>$mod_name,'res_name'=>$controller_name));

				$logM = new Seed_Model_Log('system');
				$logData = array();
				$logData['user_id']=$this->view->seed_User['user_id'];
				$logData['user_name']=$this->view->seed_User['user_name'];
				$logData['log_time']=time();
				$logData['log_url']=$current;
				$logData['log_desc']="[".$c_resource['res_desc']."]{".$check['priv_desc']."}";
				$logData['log_ip']=Seed_Browser::get_client_ip();
				$logData['log_data']=serialize(array('get'=>$this->_request->getParams(),'post'=>$_POST));
				$logM->insertRow($logData);
			}
		}

        if( !empty($this->view->seed_User['user_id'])) {$this->menuList();}
	}

    public function menuList()
	{
		$myshop_role = "";
        $shop_role = empty($this->view->seed_Setting['shop_role']) ? array() : explode(',', $this->view->seed_Setting['shop_role']);
        foreach($this->view->seed_User['roles'] as $role) {
            if(in_array($role['role_name'], $shop_role)) {
                if(!empty($myshop_role))
                        $myshop_role.=" OR role_name='".$role['role_name']."'";
                else
                        $myshop_role.=" role_name='".$role['role_name']."'";
            }
        }
        if(empty($myshop_role))return;

        $menu_id = intval($this->_request->getParam('menu_id'));
        $sub_id  = intval($this->_request->getParam('sub_id'));
        $menu_name = '';
        $sub_name = '';
        $indexBaseUrl = '';
        $role_name = $this->view->seed_User['role_name'];

        if( !$menu_id) {
            $menu_id = empty($_COOKIE['menu_id']) ? 0 : intval(Seed_Cookie::getCookie('menu_id'));
        } else {
            Seed_Cookie::setCookie('menu_id', $menu_id);
        }
        if( !$sub_id) {
            $sub_id = empty($_COOKIE['sub_id']) ? 0 : intval(Seed_Cookie::getCookie('sub_id'));
        } else {
            Seed_Cookie::setCookie('sub_id', $sub_id);
        }

        $controller_name = $this->_request->getControllerName();
		$action_name = $this->_request->getActionName();

        if ($controller_name == 'index' && $action_name == 'index') {
            $menu_id = 0;
            $sub_id = 0;
            Seed_Cookie::setCookie('menu_id', $menu_id);
            Seed_Cookie::setCookie('sub_id', $sub_id);
        }

		$seconds = $submenus = array();
        $thirdIds = $thirdNames = array();

		$menuM = new Seed_Model_Menu('system');
		$menu = $menuM->fetchMenu(array('parent'=>0, $myshop_role => null));
		if(!empty($menu)){
            // 查询二级菜单
			$seconds = $menuM->fetchMenuList(null,array('parent'=>$menu["menu_id"], $myshop_role => null), array('order_by ASC', 't1.menu_id ASC'));

            if(!empty($seconds)){
                foreach($seconds as $k => $second){
                    if($k == 0 && !$menu_id) {
                        $menu_id = $second['menu_id'];
                        Seed_Cookie::setCookie('menu_id', $menu_id);
                    }
                    if($second['menu_id'] == $menu_id) {$menu_name = $second['menu_name'];}
                    // 有第三级分类的才算二级菜单
                    $thirds=$menuM->fetchMenuList(null,array('parent'=>$second["menu_id"], $myshop_role => null), array('order_by ASC', 't1.menu_id ASC'));
                    if(!empty($thirds)){
                        foreach($thirds as $tkey => $third){
                            if($second['menu_id'] == $menu_id) {
                                $submenus[]=$third;
                                if( !$sub_id) {
                                    $sub_id = $third['menu_id'];
                                    Seed_Cookie::setCookie('sub_id', $sub_id);
                                    $indexBaseUrl = $third["link_url"].(strpos($third["link_url"], '?')>0?'&':'?')."menu_id={$menu_id}&sub_id={$sub_id}";
                                }
                                $thirdIds[] = $third['menu_id'];
                                $thirdNames[] = $third['menu_name'];
                            }
                            if($third['menu_id'] == $sub_id) {$sub_name = $third['menu_name'];}
                        }
                        if($second['menu_id'] == $menu_id && !in_array($sub_id, $thirdIds)) {
                            $sub_id = empty($thirdIds[0]) ? 0 : $thirdIds[0];
                            $sub_name = empty($thirdNames[0]) ? 0 : $thirdNames[0];
                            Seed_Cookie::setCookie('sub_id', $sub_id);
                        }
                    }
                }
            }
		}

		$this->view->headmenus = $seconds;
		$this->view->submenus = $submenus;
        $this->view->menu_id = $menu_id;
        $this->view->sub_id = $sub_id;
        $this->view->menu_name = $menu_name;
        $this->view->sub_name = $sub_name;
        $this->view->indexBaseUrl = $indexBaseUrl;
	}

    /**
     * 获取商品的JSON数据，以供jQuery插件Select2使用
     */
    public function goodselectAction()
	{
		$output = array('rst'=>array(),'total'=>0);
        $f1 = new Zend_Filter();
        $f1->addFilter(new Zend_Filter_StripTags())
           ->addFilter(new Zend_Filter_StripNewlines());
        $goods_name = $f1->filter($this->_request->getParam('q'));
        $page = intval($this->_request->getParam('page'));
        $perpage=20;

        $goodsM = new Shop_Model_Goods('shop');
        $stockM = new Shop_Model_GoodsStock('shop');
        $stockGroupM = new Shop_Model_GoodsStockGroup('shop');

		//条件处理
		$condition = array();
		$condition['is_group']='0';
		$condition['is_gift']='0';
		$condition['is_actived'] = '1';
		$condition['is_auth'] = '1';

		if (!empty($goods_name)) {
            $condition["goods_name LIKE '%".$goods_name."%'"] = null;
        }

    	$total = $goodsM->fetchRowsCount($condition);
    	$pageObj = new Seed_Page($this->_request,$total,$perpage);
    	$pageOpt = $pageObj->getPageArray();
        if($page>$pageOpt['totalpage']) {$page=$pageOpt['totalpage'];}
        if($page<1) {$page=1;}
        $orderBy = array('order_by ASC','goods_id DESC');
        $field = array('goods_id', 'goods_name', 'goods_m_list_image AS img_url');
		$goodsList = $goodsM->fetchRows(array(($page-1)*$perpage,$perpage), $condition, $orderBy, $field);
        if( empty($goodsList) ) { $goodsList = array(); }
        foreach( $goodsList as $k => $v ) {
            $imgUrl = $this->view->seed_Setting['upload_view_server'].$v['img_url'];
            $goodsList[$k]['text'] = '<table><tr><td><img src="'.$imgUrl.'" style="width:45px;height:45px;"/></td><td style="padding-left:10px;">'.
                    $v['goods_name'].'</td></tr></table>';
            $goodsList[$k]['stock_id'] = 0;
            $goodsList[$k]['stock_name'] = '';
            $goodsList[$k]['img_url'] = $imgUrl;
            $condGroup = array('goods_id' => $v['goods_id']);
            $group = $stockGroupM->fetchRow($condGroup, array('group_id', 'group_name'));
            $group_id = empty($group['group_id']) ? 0 : $group['group_id'];
            $condStock = array_merge($condGroup, array('group_id' => $group_id));
            $stockList = $stockM->fetchRows(NULL, $condStock, NULL, array('stock_id AS id', 'stock_name AS text', 'stock_shop_price AS price'));
            if( empty($stockList) ) {
                unset($goodsList[$k]);
                continue;
            }
            foreach( $stockList as $i => $s ) {
                if( !empty($group) && $group['group_name'] != '默认' ) {
                    $stockList[$i]['text'] = $group['group_name'] . ' - ' . $s['text'];
                }
                $stockList[$i]['stock_id'] = $s['id'];
                $stockList[$i]['stock_name'] = $stockList[$i]['text'];
                $stockList[$i]['shop_price'] = $stockList[$i]['price'];
                $stockList[$i]['goods_id'] = $v['goods_id'];
                $stockList[$i]['goods_name'] = $v['goods_name'];
                $stockList[$i]['img_url'] = $imgUrl;
            }
            if (empty($group)) {
                $goodsList[$k]['id'] = $v['goods_id'];
                $goodsList[$k]['stock_id'] = $stockList[0]['id'];
                $goodsList[$k]['shop_price'] = $stockList[0]['price'];
                continue;
            }
            $goodsList[$k]['children'] = $stockList;
        }

        if($goodsList) {
            sort($goodsList);//对索引重新排序，以免json转换的时候出错。
            $output['rst'] = $goodsList;
        }
        if($total) {$output['total'] = $total;}
        exit(Zend_Json::encode($output));
	}
	   
    /**
     * 处理信天邮项目商品库存没有group_id
     * 获取商品的JSON数据，以供jQuery插件Select2使用
     */
    public function goodselect2Action()
	{  
		$output = array('rst'=>array(),'total'=>0);
        $f1 = new Zend_Filter();
        $f1->addFilter(new Zend_Filter_StripTags())
           ->addFilter(new Zend_Filter_StripNewlines());
        $goods_name = $f1->filter($this->_request->getParam('q'));
        $page = intval($this->_request->getParam('page'));
        $perpage=20;

        $goodsM = new Shop_Model_Goods('shop');
        $stockM = new Shop_Model_GoodsStock('shop');
        $stockGroupM = new Shop_Model_GoodsStockGroup('shop');
 
		//条件处理
		$condition = array();
		$condition['is_group']='0';
		$condition['is_gift']='0';
		$condition['is_actived'] = '1';
		$condition['is_auth'] = '1';
  
		if (!empty($goods_name)) {
            $condition["goods_name LIKE '%".$goods_name."%'"] = null;
        }
 
    	$total = $goodsM->fetchRowsCount($condition);
    	$pageObj = new Seed_Page($this->_request,$total,$perpage);
    	$pageOpt = $pageObj->getPageArray();
        if($page>$pageOpt['totalpage']) {$page=$pageOpt['totalpage'];}
        if($page<1) {$page=1;}
        $orderBy = array('order_by ASC','goods_id DESC');
        $field = array('goods_id', 'goods_name', 'goods_m_list_image AS img_url','goods_channel','goods_warehouse');
		$goodsList = $goodsM->fetchRows(array(($page-1)*$perpage,$perpage), $condition, $orderBy, $field);
        if( empty($goodsList) ) { $goodsList = array(); }
        foreach( $goodsList as $k => $v ) {
            $imgUrl = $this->view->seed_Setting['upload_view_server'].$v['img_url'];
            $goodsList[$k]['text'] = '<table><tr><td><img src="'.$imgUrl.'" style="width:45px;height:45px;"/></td><td style="padding-left:10px;">'.
                    $v['goods_name'].'----'.$v['goods_channel'].'---'.$v['goods_warehouse'].'</td></tr></table>';
            $goodsList[$k]['stock_id'] = 0;
            $goodsList[$k]['stock_name'] = '';
            $goodsList[$k]['img_url'] = $imgUrl;
            //$condGroup = array('goods_id' => $v['goods_id']);
            //$group = $stockGroupM->fetchRow($condGroup, array('group_id', 'group_name'));
           // $group_id = empty($group['group_id']) ? 0 : $group['group_id'];
           // $condStock = array_merge($condGroup, array('group_id' => $group_id));
            $stockList = $stockM->fetchRows(NULL, array('goods_id'=>$v['goods_id']), NULL, array('stock_id AS id', 'stock_name AS text', 'stock_market_price AS price', 'stock_barcode', 'stock_sn', 'tax_rate'));
            if( empty($stockList) ) {
                unset($goodsList[$k]);
                continue;
            }
            foreach( $stockList as $i => $s ) {
                if( !empty($group) && $group['group_name'] != '默认' ) {
                    $stockList[$i]['text'] = $group['group_name'] . ' - ' . $s['text'];
                }
                $stockList[$i]['stock_id'] = $s['id'];
                $stockList[$i]['stock_name'] = $stockList[$i]['text'];
                $stockList[$i]['shop_price'] = $stockList[$i]['price'];
                $stockList[$i]['goods_id'] = $v['goods_id'];
                $stockList[$i]['goods_name'] = $v['goods_name'];
                $stockList[$i]['stock_barcode'] = $stockList[$i]['stock_barcode'];//库存条形码
                $stockList[$i]['stock_sn'] = $stockList[$i]['stock_sn'];//库存SN
                $stockList[$i]['tax_rate'] = $stockList[$i]['tax_rate'];//税率
                $stockList[$i]['img_url'] = $imgUrl;
            }
            /*if (empty($group)) {
                $goodsList[$k]['id'] = $v['goods_id'];
                $goodsList[$k]['stock_id'] = $stockList[0]['id'];
                $goodsList[$k]['shop_price'] = $stockList[0]['price'];
                $goodsList[$k]['stock_barcode'] = $stockList[0]['stock_barcode'];//库存条形码
                $goodsList[$k]['stock_sn'] = $stockList[0]['stock_sn'];//库存SN
                $goodsList[$k]['tax_rate'] = $stockList[0]['tax_rate'];//税率
                continue;
            }*/
            $goodsList[$k]['children'] = $stockList;
        }

        if($goodsList) {
            sort($goodsList);//对索引重新排序，以免json转换的时候出错。
            $output['rst'] = $goodsList;
        }
        if($total) {$output['total'] = $total;}
        exit(Zend_Json::encode($output));
	}

    /**
     * 删除记录
     */
    protected function deleteAction()
    {
        try{
            if (empty($this->_setting)) {
                $this->output(-1, '缺少相关的参数');
            }
            $f3 = new Zend_Filter_Int();
            $ids = $this->_request->getPost('ids');
            if(empty($ids)){
                $this->output(-1, '未接收到相关的参数');
            }
            $modelM = new $this->_setting['model']($this->_setting['mark']);
            $item_ids = explode(',', $ids);
            foreach ($item_ids as $item_id){
                $item_id = $f3->filter($item_id);
                if ( !empty($item_id)) {
                    $modelM->deleteRow(array($this->_setting['cond'] => $item_id));
                }
            }
            if (method_exists($this, 'deleteHandler')) {
                $this->deleteHandler($item_ids);
            }
            $this->output(1, '删除成功');
        } catch (Exception $e) {
            $this->output(-1, $e->getMessage());
        }
        $this->output(-1, '未接收到相关的参数');
    }

    /**
     * 切换开关
     *
     * 更改单条记录的单字段值
     * 其取值在{0, 1}之间
     * 应用于开关类功能
     */
    protected function toggleAction()
    {
        try{
            if (empty($this->_setting)) {
                $this->output(-1, '缺少相关的参数');
            }
            $f1 = new Zend_Filter();
            $f1->addFilter(new Zend_Filter_StripTags())
               ->addFilter(new Zend_Filter_StripNewlines());
            $f3 = new Zend_Filter_Int();
            $id = $f3->filter($this->_request->getPost('id'));
            $fkey = $f1->filter($this->_request->getPost('fkey'));
            if(empty($id) || empty($fkey) || empty($this->_setting['field'][$fkey])){
                $this->output(-1, '未接收到相关的参数');
            }
            $field = $this->_setting['field'][$fkey];
            $modelM = new $this->_setting['model']($this->_setting['mark']);
            $dataSet = array($field => new Zend_Db_Expr("1-$field"));
            $upId = $modelM->updateRow($dataSet, array($this->_setting['cond'] => $id), true);
            if ($upId !== false) {
                if ( !empty($this->_setting['func'][$fkey])
                        && method_exists($this, $this->_setting['func'][$fkey])) {
                    $func = $this->_setting['func'][$fkey];
                    $this->$func($id, 1-$field);
                }
                $this->output(1, '提交成功', array('fkey' => $fkey));
            } else {
                $this->output(-1, '提交失败');
            }
        } catch (Exception $e) {
            $this->output(-1, $e->getMessage());
        }
        $this->output(-1, '未接收到相关的参数');
    }

    /**
     * 更改某个字段的值
     *
     * 更改多条记录的单字段值
     * 其取值目前只能是整数值
     */
    protected function setvalAction()
    {
        try{
            if (empty($this->_setting)) {
                $this->output(-1, '缺少相关的参数');
            }
            $f1 = new Zend_Filter();
            $f1->addFilter(new Zend_Filter_StripTags())
               ->addFilter(new Zend_Filter_StripNewlines());
            $f3 = new Zend_Filter_Int();
            $ids = $this->_request->getPost('ids');
            $state = $f3->filter($this->_request->getPost('state'));
            $fkey = $f1->filter($this->_request->getPost('fkey'));
            if(empty($ids) || empty($fkey) || empty($this->_setting['field'][$fkey])){
                $this->output(-1, '未接收到相关的参数');
            }
            $field = $this->_setting['field'][$fkey];
            $modelM = new $this->_setting['model']($this->_setting['mark']);
            $item_ids = explode(',', $ids);
            if ( !empty($this->_setting['func'][$fkey])
                    && method_exists($this, $this->_setting['func'][$fkey])) {
                $func = $this->_setting['func'][$fkey];
                $result = $this->$func($ids, $state);
            }
            if (isset($result) && is_array($result) && $result['code'] == -1) {
                $this->output(-1, $result['msg']);
            }
            foreach ($item_ids as $item_id){
                $item_id = $f3->filter($item_id);
                if ( !empty($item_id)) {
                    $modelM->updateRow(array($field => $state), array($this->_setting['cond'] => $item_id));
                }
            }
            $this->output(1, '提交成功', array('fkey' => $fkey));
        } catch (Exception $e) {
            $this->output(-1, $e->getMessage());
        }
        $this->output(-1, '未接收到相关的参数');
    }

    /**
     * 输出JSON数据
     *
     * @param  $code  状态码
     * @param  $msg   消息
     * @param  $data  返回数据
     */
    protected function output($code = 0, $msg = '', $data = array())
    {
        exit(Zend_Json::encode(array('code' => $code, 'msg' => $msg, 'data' => $data)));
    }
}