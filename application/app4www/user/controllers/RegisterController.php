<?php

class RegisterController extends Kyapi_Controller_Action
{
    public function preDispatch()
    {
        $this->view->cur_pos = 'login';
        if(!empty($this->view->userID)){
            //已登录，不要重复登录
            Mobile_Browser::redirect($this->view->translate('tip_login_two'),$this->view->seed_Setting['user_app_server']."/index");
        }
    }
    function indexAction(){
        if ($this->_request->isPost()) {
            try {
                $_requestOb=$this->_requestObject;

                /*
                注册会员模块
                 */
                $_contact = new Kyapi_Model_contact();
                $_contact->accountName = $this->_request->getParam('accountName');
                $_contact->regdAddress = $this->_request->getParam('regdAddress');
                $_contact->name = $this->_request->getParam('name');
                $_contact->mobilePhone = $this->_request->getParam('mobilePhone');
                $_contact->ecommloginname = $this->_request->getParam('ecommloginname');
                $_contact->ecommpasswsd = $this->_request->getParam('ecommpasswsd');
                $_contact->regdCountryCode = $this->_request->getParam('regdCountryCode');
                $_contact->accountEmail = $this->_request->getParam('email');
                $_contact->isPersonAccount = $this->_request->getParam('isPersonAccount');
                $_contact->ecommrole = "CompUser,CompAdmin";

                if ($_contact->regdCountryCode == "CN") {
                    $_contact->crnCode = 'CNY';
                } else {
                    $_contact->crnCode = 'USD';
                }

                // 设置accountID, 为同一间公司注册用户
                $resultObject = $this->json->registerApi($_requestOb, $_contact);
                $userKY = $this->objectToArray(json_decode($resultObject));

                if ($userKY['status'] != 1) {
                    //增加失败
                    Shop_Browser::redirect($this->view->translate('tip_add_fail') . $userKY['error'], $this->view->seed_Setting['user_app_server'] . "/register");
                } else {
                    //销毁绑定sessionID的key

                    $resultObject = $this->json->loginApi($_requestOb, $_contact->ecommloginname, $_contact->ecommpasswsd);
                    $userLOGIN = $this->objectToArray(json_decode($resultObject));
                    if ($userLOGIN['status'] != 1) {
                        //自动登陆失败
                        Shop_Browser::redirect($this->view->translate('tip_login_fail') . $userLOGIN['error'], $this->view->seed_Setting['user_app_server'] . "/login");
                    } else {

                        $existData = $userLOGIN['result'];
                        $userDetail = array();
                        $userDetail['user_id'] = $existData['contactID'];
                        $userDetail['accountID'] = $existData['account']['accountID'];//公司ID
                        $userDetail['accountName'] = $existData['account']['accountName'];//公司名称
                        $userDetail['accountStatus'] = $existData['account']['accountStatus'];//公司状态
                        $userDetail['crnCode'] = $existData['account']['crnCode'];  //公司默认货币
                        $userDetail['user_name'] = $existData['name'];
                        $userDetail['ecommloginname'] = $existData['ecommloginname'];//登陆名
                        $userDetail['isPersonAccount'] = $existData['isPersonAccount']; // 是否个人用户
                        $userDetail['ecommrole'] = $existData['ecommrole'];//用户权限
                        $contactPreference = $existData['contactPreference'];//个性化
                        $userDetail['contactPreference']['contactID'] = $contactPreference['contactID'];
                        $userDetail['contactPreference']['timeZone'] = $contactPreference['timeZone'];
                        //   $userDetail['contactPreference']['themeCode']=$contactPreference['themeCode'];
                        $userDetail['contactPreference']['langCode'] = $contactPreference['langCode'];
                        $userDetail['contactPreference']['firstLoginTime'] = $contactPreference['firstLoginTime'];
                        $userDetail['contactPreference']['lastLoginTime'] = $contactPreference['lastLoginTime'];
                        $userDetail['contactPreference']['lastLoginIP'] = $contactPreference['lastLoginIP'];
                        $userDetail['contactPreference']['regdCountryCode'] = $existData['account']['regdCountryCode'];
                        $userDetail['contactPreference']['regdAddress'] = $existData['account']['regdAddress'];
                        if (empty($userDetail['contactPreference'])) {
                            $userDetail['contactPreference']['contactID'] = $existData['contactID'];
                            $userDetail['contactPreference']['timeZone'] = "";
                            $userDetail['contactPreference']['themeCode'] = "";
                            $userDetail['contactPreference']['langCode'] = "zh_CN";
                            $userDetail['contactPreference']['firstLoginTime'] = "";
                            $userDetail['contactPreference']['lastLoginTime'] = date('Y-m-d', time());
                            $userDetail['contactPreference']['lastLoginIP'] = $_SERVER['REMOTE_ADDR'];
                            $userDetail['contactPreference']['regdCountryCode'] = 'CN';
                            $userDetail['contactPreference']['regdAddress'] = '';
                        }

                        /****将权限菜单更新入redis缓存 start****/
                        $menuM = new Seed_Model_Menu('system');
                        $rows = array();
                        $roleArray = explode(",", $userDetail['ecommrole']);
                        foreach ($roleArray as $k => $v) {
                            //#code 根据角色查询菜单 排序
                            $rows[$k] = $menuM->fetchMenuList(null, array('role_name' => $v), array('order_by ASC', 't1.menu_id ASC'));
                        }
                        // $rowsAtt = Seed_Common::arrayUnique($rows,'menu_id');

                        $rowsArr = array();
                        foreach ($rows as $k1 => $v1) {
                            foreach ($v1 as $k2 => $v2) {
                                if ($v2['menu_id'] != "737") {
                                    $rowsArr[$v2['menu_id']] = $v2;
                                }
                            }
                        }

                        $menus = array();
                        $nav = array();
                        foreach ($rowsArr as $k3 => $v3) {

                            if ($v3['parent'] == '737') {
                                $menus[$v3['menu_id']]['menu_id'] = $v3['menu_id'];
                                $menus[$v3['menu_id']]['menu_lang'] = $v3['menu_lang'];
                                $menus[$v3['menu_id']]['menu_name'] = $v3['menu_name'];
                                $menus[$v3['menu_id']]['link_url'] = $v3['link_url'];
                                $menus[$v3['menu_id']]['parent'] = $v3['parent'];
                                $menus[$v3['menu_id']]['order_by'] = $v3['order_by'];
                            } else {
                                $nav[$v3['parent']][$v3['order_by']] = $v3;
                            }
                        }
                        foreach ($menus as $k4 => $v4) {
                            if (empty($v4['menu_id'])) {
                                unset($menus[$k4]);
                            } else {
                                $menus[$v4['order_by']] = $v4;
                                if (is_array($nav[$v4['menu_id']])) {
                                    $menus[$v4['order_by']]['nav'] = $nav[$v4['menu_id']];
                                }
                                unset($menus[$k4]);
                            }
                        }
                        $_Menus = Kyapi_Common::multi_array_sort($menus, 'order_by');
                        /****将权限菜单更新入redis缓存 END******/

                        //将session写入redis
                        $_SESSION['rev_session'] = array(
                            'userID'            => $userDetail['user_id'],
                            'accountID'         => $userDetail['accountID'],
                            'accountName'       => $userDetail['accountName'],
                            'accountStatus'     => $userDetail['accountStatus'],
                            'crnCode'           => $userDetail['crnCode'],
                            'userName'          => $userDetail['user_name'],
                            'userLoginName'     => $userDetail['ecommloginname'],
                            'isPersonAccount'   => $userDetail['isPersonAccount'],
                            'contactPreference' => $userDetail['contactPreference'],
                            'menus'             => $_Menus,
                            'role_nav'          => $rowsArr,
                            'ecommrole'         => $userDetail['ecommrole']
                        );

                        //  redis写入对应键值对
                        $config = array();
                        $config['server'] = $this->view->seed_Setting['KyUrlRedis'];
                        $config['port'] = $this->view->seed_Setting['KyUrlRedisPort'];
                        $redis = new Kyapi_Model_redisInit();
                        $redis->connect($config);
                        $redis->set('PHPREDIS_ACTIVE_USERS:' . $userDetail['user_id'], 'PHPREDIS_SESSION:' . session_id(), 86400);
                        $redis->set('PHPREDIS_ACTIVE_SESSION:' . session_id(), $userDetail['user_id'], 86400);

                        Shop_Browser::redirect($this->view->translate('tip_register_sucess'), $this->view->seed_Setting['user_app_server'] . "/index");
                    }
                }
            } catch (HttpError $ex) {
                echo $ex->getMessage();
                Shop_Browser::redirect($ex->getMessage());
            }
        }
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/register/index.phtml");
            echo $content;
            exit;
        }
    }

    function checkAction() {

        $_requestOb = $this->_requestObject;
        $_userLoginName = $this->_request->getPost('ecommloginname');
        $resultObject = $this->json->checkLoginNameApi($_requestOb, $_userLoginName);
        echo $resultObject;
        exit;
    }

    public function servicehtmlAction() {
        $content = $this->view->render(SEED_WWW_TPL . "/register/service_.phtml");
        echo $content;
        exit;
    }
}
