<?php
/**
 * REST Controller default actions
 *
 */
abstract class REST_Controller extends Zend_Controller_Action
{
    protected $seedConfig = array();   //模块配置

    protected $filterList = array();   //过滤器列表

    protected $modelList  = array();   //数据模型列表

    protected $filterAry  = array();   //过滤器配置

    /**
     * 初始化
     *
     */
    public function init()
	{
        # 检查是否已定义CURRENT_MODULE_NAME
		if( !defined('CURRENT_MODULE_NAME')) {
            return $this->getRequest()->dispatchError(REST_Message::UNKNOWED_MOUDLE_NAME);
        }

        # 组装模块名
		$mod_name = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;

        # URL根路径
		$this->seedBaseUrl = $this->getRequest()->getBaseUrl();

		# 模块配置
        $fileM = new Seed_Model_Cache2File();
		$setting = $fileM->get($mod_name."_setting");

        # 短信配置
        if ( !defined('MOBILE_SEND_ECODE') && isset($setting['mobile_send_ecode'])) {
            define('MOBILE_SEND_ECODE', $setting['mobile_send_ecode']);
        }
        if ( !defined('MOBILE_SEND_USERNAME') && isset($setting['mobile_send_username'])) {
            define('MOBILE_SEND_USERNAME', $setting['mobile_send_username']);
        }
        if ( !defined('MOBILE_SEND_PASSWORD') && isset($setting['mobile_send_password'])) {
            define('MOBILE_SEND_PASSWORD', $setting['mobile_send_password']);
        }

        # 系统配置
        $this->seedConfig = $setting;

        # 初始化view对象
        $this->initView();

        # 加载过滤器配置
        $filterPath = SEED_CONF_ROOT . '/filter.ini';
        if (file_exists($filterPath)) {
            $configM = new Zend_Config_Ini(SEED_CONF_ROOT . '/filter.ini');
            $this->filterAry = $configM->toArray();
        }

        # 检查应用账号参数
        $appKey = $this->param('app_key', 'alnum');
        $appToken = $this->param('app_token', 'alnum');
        if (empty($appKey) || empty($appToken)){
            return $this->getRequest()->dispatchError(REST_Message::INVALID_USER);
        }

        # 查询应用账号是否存在
        $appUserM = new Seed_Model_AppUser('system');
        $appUser = $appUserM->fetchRow(array('app_key' => $appKey, 'app_token' => $appToken));
        if (empty($appUser)) {
            return $this->getRequest()->dispatchError(REST_Message::INVALID_USER);
        }

        # 白名单过滤
        $clientIP = $this->getClientIP();
        $appIp = $appUser['app_ip'];
        if ( !empty($appIp) && !in_array($clientIP, explode(',', $appIp))) {
            return $this->getRequest()->dispatchError(REST_Message::INVALID_USER);
        }

        # 应用账号
        $this->appUser = $appUser;
	}

    /**
     * 抛出异常
     * @param string|array $param 消息编码
     */
    protected function throwException($param, $debugParam = '')
    {
        # 异常消息编码
        $code = is_array($param) ? $param[0] : $param;

        # 处理异常消息
        if (is_array($param)) {
            $param[0] = REST_Message::getMessage($param[0]);//获取异常消息
            $message = call_user_func_array('sprintf', $param);//格式化异常消息
        }
        elseif (is_numeric($param)) {
            $message = REST_Message::getMessage($param);
        }
        else {
            $message = $param;
        }

        # 处理调试消息
        if (is_array($debugParam)) {
            $debugParam[0] = REST_Message::getMessage($debugParam[0]);//获取调试消息
            $debugMessage = call_user_func_array('sprintf', $debugParam);//格式化调试消息
        }
        elseif (is_numeric($debugParam)) {
            $debugMessage = REST_Message::getMessage($debugParam);
        }
        else {
            $debugMessage = $debugParam;
        }

        # 抛出异常消息
        throw new REST_Exception($message, $code, $debugMessage);
    }

    /**
     * 过滤变量
     * @param mixed $var    需要过滤的变量
     * @param string $type  过滤器类型
     * @return mixed        过滤后的变量
     */
    protected function filter($var, $type)
    {
        # 检查过滤器类型是否允许，不支持过滤数组
        if (is_array($var) || !isset($this->filterAry[$type])) {
            return $var;
        }

        # 检查过滤器是否已存在
        if (isset($this->filterList[$type])) {
            return $this->filterList[$type]->filter($var);
        }

        # 新建过滤器
        $filter = new Zend_Filter();
        foreach ($this->filterAry[$type] as $filterName) {
            if (class_exists($filterName)) {//检查类是否存在
                $filter->addFilter(new $filterName);
            }
        }

        # 存入过滤器列表
        $this->filterList[$type] = $filter;

        # 返回过滤后的变量
        return $filter->filter($var);
    }

    /**
     * 返回SEED系列的数据模型对象
     * @param string $name  数据模型名称
     * @return object       数据模型对象
     */
    protected function seedModel($name)
    {
        # 检查数据模型是否已存在
        if (isset($this->modelList["seed_$name"])) {
            return $this->modelList["seed_$name"];
        }

        # 组装类名
        $className = 'Seed_Model_' . $name;

        # 检查类是否存在
        if ( !class_exists($className)) {
            $this->throwException(REST_Message::SYSTEM_ERROR, array(REST_Message::CLASS_NOT_EXIST, $className));
        }

        # 新建数据模型
        $model = new $className('system');

        # 存入数据模型列表
        $this->modelList["seed_$name"] = $model;

        # 返回数据模型
        return $model;
    }

    /**
     * 返回SHOP系列的数据模型对象
     * @param string $name  数据模型名称
     * @return object       数据模型对象
     */
    protected function shopModel($name)
    {
        # 检查数据模型是否已存在
        if (isset($this->modelList["shop_$name"])) {
            return $this->modelList["shop_$name"];
        }

        # 组装类名
        $className = 'Shop_Model_' . $name;

        # 检查类是否存在
        if ( !class_exists($className)) {
            $this->throwException(REST_Message::SYSTEM_ERROR, array(REST_Message::CLASS_NOT_EXIST, $className));
        }

        # 新建数据模型
        $model = new $className('shop');

        # 存入数据模型列表
        $this->modelList["shop_$name"] = $model;

        # 返回数据模型
        return $model;
    }

    /**
     * 获取过滤后的参数
     * @param string $paramName    参数名称
     * @param string $filterName   过滤器索引
     * @param boolean $checkEmpty  是否需要非空检查
     * @return mixed               处理后的参数
     */
    protected function param($paramName, $filterName, $checkEmpty = false)
    {
        # 获取参数并过滤
        $value = $this->filter($this->getParam($paramName), $filterName);

        # 非空检查
        if ( !!$checkEmpty && empty($value)) {
            $this->throwException(array(REST_Message::PARAMETER_IS_MISS, $paramName));
        }

        # 返回处理后的参数
        return $value;
    }

    /**
     * 获取过滤后的 POST 参数
     * @param string  $paramName   参数名称
     * @param string  $filterName  过滤器索引
     * @param boolean $checkEmpty  是否需要非空检查
     * @return mixed               处理后的参数
     */
    protected function postParam($paramName, $filterName, $checkEmpty = false)
    {
        # 获取参数并过滤
        $value = $this->filter($this->getRequest()->getPost($paramName), $filterName);

        # 非空检查
        if ( !!$checkEmpty && empty($value)) {
            $this->throwException(array(REST_Message::PARAMETER_IS_MISS, $paramName));
        }

        # 返回处理后的参数
        return $value;
    }

    /**
     * 获取访问IP
     */
    protected function getClientIP()
    {
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
            $ip = getenv("HTTP_CLIENT_IP");
        }
		else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }
		else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
            $ip = getenv("REMOTE_ADDR");
        }
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
		else {
            $ip = "unknown";
        }
		return($ip);
	}

    /**
     * 返回输出
     */
    protected function ok($data = array())
    {
        $this->view->data = $data;
        $this->view->code = 1;
        $this->view->message = 'ok';
        $this->_response->ok();
    }

    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction()
    {
        $this->notAllowed();
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction()
    {
        $this->notAllowed();
    }

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction()
    {
        $this->notAllowed();
    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction()
    {
        $this->notAllowed();
    }

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction()
    {
        $this->notAllowed();
    }

    /**
     * The head action handles HEAD requests; it should respond with an
     * identical response to the one that would correspond to a GET request,
     * but without the response body.
     */
    public function headAction()
    {
        $this->_forward('get');
    }

    /**
     * The options action handles OPTIONS requests; it should respond with
     * the HTTP methods that the server supports for specified URL.
     */
    public function optionsAction()
    {
        $this->_response->setBody(null);
        $this->_response->setHeader('Allow', $this->_response->getHeaderValue('Access-Control-Allow-Methods'));
        $this->_response->ok();
    }

    protected function notAllowed()
    {
        $this->_response->setBody(null);
        $this->_response->notAllowed();
    }
}
