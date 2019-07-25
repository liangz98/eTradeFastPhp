<?php
/**
 * Created by Administrator.
 * Date: 2019/7/1 12:19
 */

class MpController extends Kyapi_Controller_Action {

    // test
    // private $appID = "wx907021acb9aae1ec";
    // private $appSecret = "b9a6f6f5f2a0bb3c33db2e76e76c8aed";
    // prd
    private $appID = "wx3de3c39694609a78";
    private $appSecret = "ee96922bdd2a588d2b52249b8ca89be7";

    function indexAction() {
        $this->view->headimgurl = $_SESSION['rev_session']['headimgurl'];

        // 个人信息页
        $content = $this->view->render(SEED_WWW_TPL . "/mp/index.phtml");
        echo $content;
        exit;
    }

    // 获取用户的openid (基本授权)
    function getBaseInfoAction() {
        //1.获取到code
        $redirect_uri = urlencode("https://dev.etradefast.com/user/mp/get-user-open-id");
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->appID . "&redirect_uri=" . $redirect_uri . "&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
        header('location:' . $url);//引导关注者打开页面
    }

    function getUserOpenIdAction() {
        $code = $_GET['code'];
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appID . "&secret=" . $this->appSecret . "&code=" . $code . "&grant_type=authorization_code";

        //3.拉取用的openid
        $res = $this->http_curl($url, 'get');
        $this->view->openid = $res['openid'];

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/mp/index.phtml");
            echo $content;
            exit;
        }
    }

    public function http_curl($url, $type = 'get', $res = 'json', $arr = '') {
        /*
        $url 请求的url
        $type 请求类型
        $res 返回数据类型
        $arr post请求参数
        ctrl+f键查找 extension=php_curl.dll 将前面的分号；去掉，
         */
        // 1. 初始化
        $ch = curl_init();

        // 2. 设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);//CURLOPT_URL 指定请求的URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//CURLOPT_RETURNTRANSFER 设置为1表示稍后执行的curl_exec函数的返回是URL的返回字符串，而不是把返回字符串定向到标准输出并返回TRUE
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//FALSE 禁止 cURL 验证对等证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if ($type == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);//TRUE 时会发送 POST 请求
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);//全部数据使用HTTP协议中的 "POST" 操作来发送
        }
        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        // 4. 释放curl句柄
        curl_close($ch);
        if ($res == 'json') {
            return json_decode($output, true);//对 JSON 格式的字符串进行解码(数组)
        } else {
            return json_decode($output, true);//对 JSON 格式的字符串进行解码(数组)
        }
    }

    //（详细授权）
    function getUserDetailAction() {
        //1.获取到code
        $redirect_uri = urlencode("https://dev.etradefast.com/user/mp/get-user-info");
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->appID . "&redirect_uri=" . $redirect_uri . "&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";

        header('location:' . $url);
    }

    function getUserInfoAction() {
        // 2.获取到网页授权的access_token
        $code = $_GET['code'];
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appID . "&secret=" . $this->appSecret . "&code=" . $code . "&grant_type=authorization_code";

        $res = $this->http_curl($url, 'get');
        $access_token = $res['access_token'];
        $openid = $res['openid'];

        // 3.拉取用户的详细信息
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
        $wechatRes = $this->http_curl($url);
        // 缓存用户详细信息
        $_SESSION['rev_session'] = array();
        $_SESSION['rev_session']['wechatRes'] = $wechatRes;

        // Login
        $requestObject = $this->_requestObject;
        $resultObject = $this->json->loginByOpenid($requestObject, $openid);
        $msg["status"] = json_decode($resultObject)->status;
        if ($msg["status"] == '1') {
            $contactResult = $this->objectToArray(json_decode($resultObject));
            $resultData = $contactResult['result'];
            $contact = json_decode($resultObject)->result;
            // 登录成功, 判断是否资金方
            if ($contact->account->accountType == 'CO07') {
                $wechatOpenid = $wechatRes['openid'];
                $weChatNickname = $wechatRes['nickname'];
                $wechatSex = $wechatRes['sex'];
                $wechatProvince = $wechatRes['province'];
                $wechatCity = $wechatRes['city'];
                $wechatCountry = $wechatRes['country'];
                $wechatHeadimgurl = $wechatRes['headimgurl'];
                $wechatUnionid = $wechatRes['unionid'];
                // 将Json对象转成String,且不为引号添加转义斜杠"\"
                $this->json->syncUserWechatInfo($requestObject, $contact->contactID, $wechatOpenid, $wechatUnionid, json_encode($wechatRes, JSON_UNESCAPED_SLASHES));

                // 更新缓存
                $this->refreshUserSessionAndRedis($resultData);
                $_SESSION['rev_session']['wechatOpenid'] = $wechatOpenid;
                $_SESSION['rev_session']['weChatNickname'] = $weChatNickname;
                $_SESSION['rev_session']['wechatSex'] = $wechatSex;
                $_SESSION['rev_session']['wechatProvince'] = $wechatProvince;
                $_SESSION['rev_session']['wechatCity'] = $wechatCity;
                $_SESSION['rev_session']['wechatCountry'] = $wechatCountry;
                $_SESSION['rev_session']['wechatHeadimgurl'] = $wechatHeadimgurl;
                $_SESSION['rev_session']['wechatUnionid'] = $wechatUnionid;

                $this->redirect("/mp/index");
            }
        } else {
            // 登录页
            $content = $this->view->render(SEED_WWW_TPL . "/mp/login.phtml");
            echo $content;
            exit;
        }
    }

    function loginAction() {
        // 请求服务端入参
        $requestObject = $this->_requestObject;
        $loginName = $this->_request->getParam('loginName');
        $loginName = trim($loginName);
        $password = $this->_request->getParam('ecommpasswsd');
        $password = trim($password);
        $authCode = $this->_request->getParam('authCode');
        $authCode = trim($authCode);

        // Login
        $resultObject = $this->json->loginApi($requestObject, $loginName, $password, $authCode);
        $contact = json_decode($resultObject)->result;

        // 判断返回结果
        if (json_decode($resultObject)->status == '1') {
            // 登录成功, 判断是否资金方
            if ($contact->account->accountType == 'CO07') {
                $contactResult = $this->objectToArray(json_decode($resultObject));
                $resultData = $contactResult['result'];

                // 将用户 openID 写入表
                $wechatRes = $_SESSION['rev_session']['wechatRes'];
                $wechatOpenid = $wechatRes['openid'];
                $weChatNickname = $wechatRes['nickname'];
                $wechatSex = $wechatRes['sex'];
                $wechatProvince = $wechatRes['province'];
                $wechatCity = $wechatRes['city'];
                $wechatCountry = $wechatRes['country'];
                $wechatHeadimgurl = $wechatRes['headimgurl'];
                $wechatUnionid = $wechatRes['unionid'];

                // 将Json对象转成String,且不为引号添加转义斜杠"\"
                $this->json->syncUserWechatInfo($requestObject, $contact->contactID, $wechatOpenid, $wechatUnionid, json_encode($wechatRes, JSON_UNESCAPED_SLASHES));

                // 更新缓存
                $this->refreshUserSessionAndRedis($resultData);
                $_SESSION['rev_session']['wechatOpenid'] = $wechatOpenid;
                $_SESSION['rev_session']['weChatNickname'] = $weChatNickname;
                $_SESSION['rev_session']['wechatSex'] = $wechatSex;
                $_SESSION['rev_session']['wechatProvince'] = $wechatProvince;
                $_SESSION['rev_session']['wechatCity'] = $wechatCity;
                $_SESSION['rev_session']['wechatCountry'] = $wechatCountry;
                $_SESSION['rev_session']['wechatHeadimgurl'] = $wechatHeadimgurl;
                $_SESSION['rev_session']['wechatUnionid'] = $wechatUnionid;

                $this->redirect("/mp/index");
            } else {
                $this->view->resultMsg = "仅允许资金方用户登录";
                // 登录页
                $content = $this->view->render(SEED_WWW_TPL . "/mp/login.phtml");
                echo $content;
                exit;
            }
        } else {
            $this->view->resultMsg = json_decode($resultObject)->error;
            // 登录页
            $content = $this->view->render(SEED_WWW_TPL . "/mp/login.phtml");
            echo $content;
            exit;
        }
    }

    /**
     * 可投列表
     */
    function getLoanListAction() {

        $content = $this->view->render(SEED_WWW_TPL . "/mp/loanList.phtml");
        echo $content;
        exit;
    }

    /**
     * 可投列表
     */
    function getLoanViewAction() {

        $content = $this->view->render(SEED_WWW_TPL . "/mp/loanView.phtml");
        echo $content;
        exit;
    }

    /**
     * 投标
     */
    function addLoanAction() {

        $content = $this->view->render(SEED_WWW_TPL . "/mp/loanAdd.phtml");
        echo $content;
        exit;
    }
}
