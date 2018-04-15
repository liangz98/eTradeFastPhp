<?php
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 0);

/**定义常量*/
//当前系统模块名称
define('CURRENT_MODULE_NAME','mobunion');
//当前系统模块类型:admin为后台,front为前台
define('CURRENT_MODULE_TYPE','front');
//网站根目录
define('SEED_WWW_ROOT',realpath(dirname(__FILE__).'/../'));
//库根目录
define('SEED_LIB_ROOT',realpath('../../library'));
//图片目录，用于下载微信用户头像信息
define('SEED_IMG_ROOT',realpath('../../public'));
//图片目录，用于下载微信用户头像信息
define('SEED_IMAGE_ROOT',realpath('../../public/upload_files/images'));
//应用根目录
define('SEED_APP_ROOT',realpath('../../application/app4www/mobunion'));
//配置根目录
define('SEED_CONF_ROOT',realpath('../../config'));
//缓存根目录
define('SEED_CACHE_ROOT',realpath('../../cache'));
//临时根目录
define('SEED_TEMP_ROOT',realpath('../../temp'));
//默认控制器
define('SEED_CONTROLLER_DIRECTORY',realpath('../../application/app4www/mobunion/controllers'));
//授权目录
define('SEED_LICENSE_ROOT',realpath('../../license'));
if(file_exists(SEED_LICENSE_ROOT."/auth.php")){
require_once SEED_LICENSE_ROOT."/auth.php";
}
//timezone
date_default_timezone_set('Asia/Chongqing');

set_include_path('.' . PATH_SEPARATOR . SEED_LIB_ROOT
    . PATH_SEPARATOR . get_include_path());
    
$includepath = get_include_path();

require_once "Zend/Loader/Autoloader.php";
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
