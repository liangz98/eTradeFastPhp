<?php
/**
 * Created by Administrator.
 * Date: 2019/7/1 12:19
 */

class MpController extends Kyapi_Controller_Action {

    private $appID = "wx907021acb9aae1ec";
    private $appSecret = "b9a6f6f5f2a0bb3c33db2e76e76c8aed";

    function indexAction() {

    }

    // 获取用户的openid (基本授权)
    function getBaseInfoAction() {
        //1.获取到code
        $redirect_uri = urlencode("https://www.etradefast.com/user/mp/get-user-open-id");
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->appID . "&redirect_uri=" . $redirect_uri . "&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
        header('location:' . $url);//引导关注者打开页面
    }

    function getUserOpenIdAction() {
        $code = $_GET['code'];
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appID . "&secret=" . $this->appSecret . "&code=" . $code . "&grant_type=authorization_code";

        //3.拉取用的openid
        $res = $this->http_curl($url, 'get');
        var_dump($res);

        // echo "";

        $this->view->openid = $res['openid'];
        //time();  1,2,3
        //用户访问次数统计和限制

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
        $redirect_uri = urlencode("https://www.etradefast.com/User/mp/get-user-info");
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->appID . "&redirect_uri=" . $redirect_uri . "&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";


        header('location:' . $url);
    }

    function getUserInfoAction() {
        //2.获取到网页授权的access_token
        $code = $_GET['code'];
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appID . "&secret=" . $this->appSecret . "&code=" . $code . "&grant_type=authorization_code";

        $res = $this->http_curl($url, 'get');
        $access_token = $res['access_token'];
        $openid = $res['openid'];

        //3.拉取用户的详细信息
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
        $res = $this->http_curl($url);
        var_dump($res);
    }
}
