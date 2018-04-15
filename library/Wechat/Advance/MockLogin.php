<?php
/**
 * mock login
 */
class Wechat_Advance_MockLogin{
    public static $_WeiXinC = null;

    function  __construct() {
        
    }

    public static function login(){
         if(self::$_WeiXinC)return self::$_WeiXinC;
         $w_controller = Wechat_Base_Controller::getInstance();
         $config = array();
         $config['account'] = $w_controller->_wechat['account'];
         $config['password'] = base64_decode($w_controller->_wechat['password']);
         if(empty($config['account']) || empty($config['password'])){
             return '';
         }
         
         if(defined('SEED_HOST_NAME')){
			$seed_host_name = str_replace(".","_",SEED_HOST_NAME)."_";
		}else{ 
			$seed_host_name = "";
		}
		
         $config['cookiePath'] = rtrim(SEED_CACHE_ROOT,'/').'/'.$seed_host_name.'cookie_'.$w_controller->_wechat['id'];
         $config['webTokenPath'] = rtrim(SEED_CACHE_ROOT,'/').'/'.$seed_host_name.'webToken_'.$w_controller->_wechat['id'];
         
         $filePath = SEED_WWW_ROOT.DIRECTORY_SEPARATOR.'upload_files'.DIRECTORY_SEPARATOR.'files';
         if(!is_dir($filePath)){
            @mkdir($filePath,0777);
         }
         if(!is_dir($filePath.DIRECTORY_SEPARATOR.'wechat_voice')){
            @mkdir($filePath.DIRECTORY_SEPARATOR.'wechat_voice', 0777);
         }
         if(!is_dir($filePath.DIRECTORY_SEPARATOR.'wechat_video')){
            @mkdir($filePath.DIRECTORY_SEPARATOR.'wechat_video', 0777);
         }
         $imagePath = SEED_WWW_ROOT.DIRECTORY_SEPARATOR.'upload_files'.DIRECTORY_SEPARATOR.'images';
         if(!is_dir($imagePath)){
             @mkdir($imagePath,0777);
         }
         if(!is_dir($imagePath.DIRECTORY_SEPARATOR.'wechat_avatar')){
             @mkdir($imagePath.DIRECTORY_SEPARATOR.'wechat_avatar',0777);
         }
         
         $config['voicePath'] = $filePath.DIRECTORY_SEPARATOR.'wechat_voice';
         $config['videoPath'] = $filePath.DIRECTORY_SEPARATOR.'wechat_video';
         $config['avatarPath'] = $imagePath.DIRECTORY_SEPARATOR.'wechat_avatar';
         
         self::$_WeiXinC = new Wechat_WechatClient_WeiXin($config);
         return self::$_WeiXinC;
    }
    
    public static function loginByWcid($wc_id){
        $userWechatM = new Wechat_Model_Wechat('wechat');
        $userWechat = $userWechatM->fetchRow(array('is_del'=>'0','id'=>$wc_id));
        if($userWechat){
             $config = array();
             $config['account'] = $userWechat['account'];
             $config['password'] = base64_decode($userWechat['password']);
             if(empty($config['account']) || empty($config['password'])){
                 return '';
             }
             
	         if(defined('SEED_HOST_NAME')){
				$seed_host_name = str_replace(".","_",SEED_HOST_NAME)."_";
			}else{ 
				$seed_host_name = "";
			}
		
             $config['cookiePath'] = rtrim(SEED_CACHE_ROOT,'/').'/'.$seed_host_name.'cookie_'.$userWechat['id'];
             $config['webTokenPath'] = rtrim(SEED_CACHE_ROOT,'/').'/'.$seed_host_name.'webToken_'.$userWechat['id'];

             $filePath = SEED_WWW_ROOT.DIRECTORY_SEPARATOR.'upload_files'.DIRECTORY_SEPARATOR.'files';
             if(!is_dir($filePath)){
                @mkdir($filePath,0777);
             }
             if(!is_dir($filePath.DIRECTORY_SEPARATOR.'wechat_voice')){
                @mkdir($filePath.DIRECTORY_SEPARATOR.'wechat_voice', 0777);
             }
             if(!is_dir($filePath.DIRECTORY_SEPARATOR.'wechat_video')){
                @mkdir($filePath.DIRECTORY_SEPARATOR.'wechat_video', 0777);
             }
             
            $imagePath = SEED_WWW_ROOT.DIRECTORY_SEPARATOR.'upload_files'.DIRECTORY_SEPARATOR.'images';
            if(!is_dir($imagePath)){
                @mkdir($imagePath,0777);
            }
            if(!is_dir($imagePath.DIRECTORY_SEPARATOR.'wechat_avatar')){
                @mkdir($imagePath.DIRECTORY_SEPARATOR.'wechat_avatar',0777);
            }
             
             $config['voicePath'] = $filePath.DIRECTORY_SEPARATOR.'wechat_voice';
             $config['videoPath'] = $filePath.DIRECTORY_SEPARATOR.'wechat_video';
             $config['avatarPath'] = $imagePath.DIRECTORY_SEPARATOR.'wechat_avatar';
             
             $WeiXinC = new Wechat_WechatClient_WeiXin($config);
             return $WeiXinC;
        }
        return '';
    }
    
    
}