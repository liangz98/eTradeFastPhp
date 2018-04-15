<?php
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 0);

/**定义常量*/
//当前系统模块名称
define('CURRENT_MODULE_NAME','www');
//当前系统模块类型:admin为后台,front为前台
define('CURRENT_MODULE_TYPE','front');
//网站域名
define('WWW','http://'.$_SERVER['SERVER_NAME'].'/');

//网站根目录
define('SEED_WWW_ROOT',dirname(__FILE__));
//库根目录
define('SEED_LIB_ROOT',realpath('../library'));
//日志根目录
define('SEED_LOG_ROOT',realpath('../log'));
//应用根目录
define('SEED_APP_ROOT',realpath('../application/app4www/www'));
//配置根目录
define('SEED_CONF_ROOT',realpath('../config'));
//缓存根目录
define('SEED_CACHE_ROOT',realpath('../cache'));
//临时根目录
define('SEED_TEMP_ROOT',realpath('../temp'));
//图片目录
define('SEED_IMAGE_ROOT',realpath('./upload_files/images'));
////授权目录
define('SEED_LICENSE_ROOT',realpath('../license'));
if(file_exists(SEED_LICENSE_ROOT."/auth.php")){
     require_once SEED_LICENSE_ROOT."/auth.php";
}

//默认控制器
define('SEED_CONTROLLER_DIRECTORY',realpath('../application/app4www/www/controllers'));
//timezone
date_default_timezone_set('Asia/Chongqing');

set_include_path('.' . PATH_SEPARATOR . SEED_LIB_ROOT
    . PATH_SEPARATOR . get_include_path());

$includepath = get_include_path();
require_once "../library/Zend/Loader/Autoloader.php";
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);

$registry = Zend_Registry::getInstance();

/** session */
Zend_Session::start();
$session = new Zend_Session_Namespace('Center_Gzseed_Session');
$registry->set('session', $session);

/** Setting up the front controller */
$frontController = Zend_Controller_Front::getInstance();
$frontController->throwExceptions(false);
$frontController->setControllerDirectory(SEED_CONTROLLER_DIRECTORY);

/** run! */
$frontController->dispatch();
