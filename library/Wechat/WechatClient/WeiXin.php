<?php
class Wechat_WechatClient_WeiXin
{
	private $token; // 公共平台申请时填写的token
	private $account;
	private $password;

	// 每次登录后将cookie, webToken缓存起来, 调用其它api时直接使用
	// 注: webToken与token不一样, webToken是指每次登录后动态生成的token, 供难证用户是否登录而用
	private $cookiePath; // 保存cookie的文件路径
	private $webTokenPath; // 保存webToken的文路径
        private $voicePath;//语音路径
        private $videoPath;//视频路径
        private $avatarPath;//头像路径
        

        // 缓存的值
	private $webToken; // 登录后每个链接后都要加token
	private $cookie;

	private $lea;

       public function Wechat_WechatClient_WeiXin($config){
		if(!$config) {
			exit("error");
		}
		// 配置初始化
		$this->account = $config['account'];
		$this->password = $config['password'];
		$this->cookiePath = $config['cookiePath'];
		$this->webTokenPath = $config['webTokenPath'];
                $this->voicePath = isset($config['voicePath']) ? $config['voicePath'] : '';
                $this->videoPath = isset($config['videoPath']) ? $config['videoPath'] : '';
                $this->avatarPath = isset($config['avatarPath']) ? $config['avatarPath']: '';
		$this->lea = new Wechat_WechatClient_LeaWeiXinClient();
		// 读取cookie, webToken
		$this->getCookieAndWebToken();
        }
//	// 构造函数
//	public function __construct($config) {
//                $this->Wechat_WechatClient_WeiXin($config);
//	}

	// 登录, 并获取cookie, webToken
	public function login() {
		$url = "https://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN";
		$post["username"] = $this->account;
		$post["pwd"] = md5($this->password);
		$post["f"] = "json";
		$re = $this->lea->submit($url, $post);
		// 保存cookie
		$this->cookie = $re['cookie'];
		@file_put_contents($this->cookiePath, $this->cookie);
		// 得到token
		return $this->getWebToken($re['body']);
	}

	/**
	 * 登录后从结果中解析出webToken
	 * @param  [String] $logonRet
	 * @return [Boolen]
	 */
	private function getWebToken($logonRet) {
		$logonRet = json_decode($logonRet, true);
		$msg = $logonRet["ErrMsg"]; // /cgi-bin/indexpage?t=wxm-index&lang=zh_CN&token=1455899896
		$msgArr = explode("&token=", $msg);
		if(count($msgArr) != 2) {
			return false;
		} else {
			$this->webToken = $msgArr[1];
			@file_put_contents($this->webTokenPath, $this->webToken);
			return true;
		}
	}

	/**
	 * 从缓存中得到cookie和webToken
	 * @return [type]
	 */
	public function getCookieAndWebToken() {
		$this->cookie = @file_get_contents($this->cookiePath);
		$this->webToken = @file_get_contents($this->webTokenPath);

		// 如果有缓存信息, 则验证下有没有过时, 此时只需要访问一个api即可判断
		if($this->cookie && $this->webToken) {
			$url = "https://mp.weixin.qq.com/cgi-bin/getcontactinfo?t=ajax-getcontactinfo&lang=zh_CN&token={$this->webToken}&fakeid=";
			$re = $this->lea->submit($url, array(), $this->cookie);
			$result = json_decode($re['body'], 1);

			if(!is_array($result) || !isset($result['base_resp']['err_msg']) || !($result['base_resp']['err_msg'] == 'invalid bizpay url')) {
				return $this->login();
			} else {
				return true;
			}
		} else {
			return $this->login();
		}
	}

        public function getQrcode($dest){
            if(empty($dest))return '';
            $res = $this->getWXdata(2);
            if(isset($res['uin'])){
                $url = "https://mp.weixin.qq.com/misc/getqrcode?fakeid={$res['uin']}&token={$this->webToken}&style=1&action=download";
                $re = $this->lea->get($url, $this->cookie);
		if($re['body']){
                    $len = file_put_contents($dest,$re['body']);
                    $avtar_img = imagecreatefromjpeg($dest);
                    return $dest;
                }
            }
            return '';
        }
        
        
	// 其它API, 发送, 获取用户信息

	/**
	 * 主动发消息
	 * @param  string $id      用户的fakeid
	 * @param  string $content 发送的内容
	 * @return [type]          [description]
	 */
	public function send($id, $content)
	{
		$post = array();
		$post['type'] = 1;
		$post['content'] = $content;
		$post['error'] = false;
		$post['tofakeid'] = $id;
		$post['token'] = $this->webToken;
		$post['ajax'] = 1;
		$url = "https://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response&lang=zh_CN";
		$re = $this->lea->submit($url, $post, $this->cookie);
		return json_decode($re['body'],true);
	}

	/**
	 * 批量发送
	 * @param  [array] $ids     用户的fakeid集合
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	public function batSend($ids, $content)
	{
		$result = array();
		foreach($ids as $id) {
			$result[$id] = $this->send($id, $content);
		}
		return $result;
	}	

	/**
	 * 发送图片
	 * @param  int $fakeId [description]
	 * @param  int $fileId 图片ID
	 * @return [type]         [description]
	 */
	public function sendImage($fakeId, $fileId) {
		$post = array();
		$post['tofakeid'] = $fakeId;
		$post['type'] = 2;
		$post['fid'] = $post['fileId'] = $fileId; // 图片ID
		$post['error'] = false;
		$post['ajax'] = 1;
		$post['token'] = $this->webToken;

		$url = "https://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response&lang=zh_CN";
		$re = $this->lea->submit($url, $post, $this->cookie);

		return json_decode($re['body'],true);
	}

        /*
         * 发送语音消息
         */
        public function sendVoice($fakeId, $fileId){
		$post = array();
		$post['tofakeid'] = $fakeId;
		$post['type'] = 3;
		$post['fid'] = $post['fileId'] = $fileId; // 语音ID
		$post['error'] = false;
                $post['imgcode'] = '';
		$post['ajax'] = 1;
		$post['token'] = $this->webToken;
		$url = "https://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response&lang=zh_CN";
		$re = $this->lea->submit($url, $post, $this->cookie);

		return json_decode($re['body'],true);
        }

        /*
         * 发送视频
         */
        public function sendVideo($fakeId, $fileId){
		$post = array();
                $post['ajax'] = 1;
                $post['appmsgid'] = $post['app_id'] = $fileId; // 视频ID
                $post['f'] = "json";
                $post['imgcode'] = '';
                $post['lang'] = 'zh_CN';
                $post['random'] = time();
                $post['t'] = "ajax-response";
                $post['tofakeid'] = $fakeId;
                $post['token'] = $this->webToken;
                $post['type'] = 15;
		$url = "https://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response&lang=zh_CN";
		$re = $this->lea->submit($url, $post, $this->cookie);
		return json_decode($re['body'],1);
        }



        /*
         * 发送图文
         */
        public function sendNews($fakeId, $fileId){
		$post = array();
		$post['tofakeid'] = $fakeId;
		$post['type'] = 10;
		$post['AppMsgId'] = $fileId; // 图文ID
		$post['error'] = true;
                $post['imgcode'] = '';
		$post['ajax'] = 1;
		$post['token'] = $this->webToken;
		$url = "https://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response&lang=zh_CN";
		$re = $this->lea->submit($url, $post, $this->cookie);

		return json_decode($re['body'],true);
        }


	/**
	 * 获取用户的信息
	 * @param  string $fakeId 用户的fakeId
	 * @return [type]     [description]
	 */
	public function getUserInfo($fakeId)
	{
		$url = "https://mp.weixin.qq.com/cgi-bin/getcontactinfo?t=ajax-getcontactinfo&lang=zh_CN&token={$this->webToken}&fakeid=$fakeId";
		$re = $this->lea->submit($url, array(), $this->cookie);
		$result = json_decode($re['body'], 1);
//		if(!$result) {
//			$this->login();
//		}
		return $result;
	}

	/*
	 得到最近发来的信息
    [0] => Array
        (
            [id] => 189
            [type] => 1
            [fileId] => 0
            [hasReply] => 0
            [fakeId] => 1477341521
            [nickName] => lealife
            [remarkName] => 
            [dateTime] => 1374253963
        )
        [ok]
	 */
	public function getLatestMsgs($page = 0,$frommsgid = 100000000) {
                $url = "https://mp.weixin.qq.com/cgi-bin/message?t=message/list&count=20&day=7&token={$this->webToken}&lang=zh_CN";
                $re = $this->lea->get($url, $this->cookie);
		$preg = '/"msg_item":(.*)}\).msg_item/iUs';
		preg_match_all($preg, $re['body'], $arr);
		return json_decode(isset($arr[1][0])?$arr[1][0]:'', 1);
	}

	// 解析用户信息
	// 当有用户发送信息后, 如何得到用户的fakeId?
	// 1. 从web上得到最近发送的信息
	// 2. 将用户发送的信息与web上发送的信息进行对比, 如果内容和时间都正确, 那肯定是该用户
	// 		实践发现, 时间可能会不对, 相隔1-2s或10多秒也有可能, 此时如果内容相同就断定是该用户
	// 		如果用户在时间相隔很短的情况况下输入同样的内容很可能会出错, 此时可以这样解决: 提示用户输入一些随机数.
	
	/**
	 * 通过时间 和 内容 双重判断用户
	 * @param  [type] $createTime
	 * @param  [type] $content
	 * @return [type]
	 */
	public function getLatestMsgByCreateTimeAndContent($createTime, $content) {
		$lMsgs = $this->getLatestMsgs(0);
		// 最先的数据在前面
		$contentMatchedMsg = array();
		foreach($lMsgs as $msg) {
			// 仅仅时间符合
			if(isset($msg['date_time']) && $msg['date_time'] == $createTime) {
				// 内容+时间都符合
				if(isset($msg['content']) && $msg['content'] == $content) {
					return $msg;
				}

			// 仅仅是内容符合
			} else if(isset($msg['content']) && $msg['content'] == $content) {
				$contentMatchedMsg[] = $msg;
			}
		}
		// 最后, 没有匹配到的数据, 内容符合, 而时间不符
		// 返回最新的一条
		if($contentMatchedMsg) {
			return $contentMatchedMsg[0];
		}
		return false;
	}

       /**
	 * 获取分租
	 * @param  string $fakeId 用户的fakeId
	 * @return [type]     [description]
	 */
	public function GetUserGroup(){
		$url = "https://mp.weixin.qq.com/cgi-bin/contactmanage?t=user/index&pagesize=1&pageidx=0&type=0&groupid=0&token={$this->webToken}&lang=zh_CN";
		$re = $this->lea->get($url, $this->cookie);
		$preg = '/{"groups":(.*)}\).groups/iUs';
		preg_match_all($preg, $re['body'], $arr);
		return  isset($arr[1][0])?@json_decode($arr[1][0], true):'';
	}
        
        /**
         *获取头像
         * @param <string> $fakeId 用户的fakeId
         * @return <type>
         */
        public function GetAvatar($fakeId){
		$url = "https://mp.weixin.qq.com/cgi-bin/getheadimg?fakeid={$fakeId}&token={$this->webToken}&lang=zh_CN";
		$re = $this->lea->get($url, $this->cookie);
                $seed_host_name = '';
                if(defined('SEED_HOST_NAME')){
                    $seed_host_name = str_replace(".","_",SEED_HOST_NAME)."_";
                }
                $imgM = new Seed_Image();
                $ext = $imgM->getExtByHeader($re['header']);
		$new_avatar_path =  $this->avatarPath.'/'.$seed_host_name.date('YmdHis').$fakeId.'.'.$ext;
		$len = file_put_contents($new_avatar_path,$re['body']);
		$avtar_img = imagecreatefromjpeg($new_avatar_path);
		//imagejpeg($avtar_img,'test/'.$new_avatar_path);
		return $new_avatar_path;
	}

        /**
         *获取与某用户的对话内容
         * @param <string> $fakeId 用户的fakeId
         * @return <type>
         */
        public function GetConversation($fakeId,$count = 20){
            $url = "https://mp.weixin.qq.com/cgi-bin/singlesendpage?tofakeid={$fakeId}&t=message/send&action=index&token={$this->webToken}&lang=zh_CN";
            $re =  $this->lea->getSource($url,$this->cookie);
            $preg = '/"msg_item":(.*)}};/isU';
            preg_match_all($preg, $re, $arr);
            $re = @json_decode(isset($arr[1][0])?$arr[1][0]:'', true);
            return $re;
        }
        
         /**
         *下载语音消息
         * @param <type> $msgid 消息id
         * @return <type>
         */
        public function GetVoice($msgid){
            $url = "https://mp.weixin.qq.com/cgi-bin/getvoicedata?token={$this->webToken}&msgid={$msgid}&fileid=0";
            $re =  $this->lea->get($url,$this->cookie);//获取未经处理的源数据
            $seed_host_name = '';
            if(defined('SEED_HOST_NAME')){
                $seed_host_name = str_replace(".","_",SEED_HOST_NAME)."_";
            }
            $filename = $this->voicePath.'/'.$seed_host_name.$msgid.'.mp3';
            if(isset($re['body']) && $re['body']){
                file_put_contents($filename, $re['body']);
                return $filename;
            }else{
                return '';
            }
        }

        /**
         * 下载视频
         */
        public function GetVideo($msgid){
            $url = "https://mp.weixin.qq.com/cgi-bin/getvideodata?msgid={$msgid}&fileid=&token={$this->webToken}";
            $re =  $this->lea->get($url,$this->cookie);//获取未经处理的源数据
            $seed_host_name = '';
            if(defined('SEED_HOST_NAME')){
                $seed_host_name = str_replace(".","_",SEED_HOST_NAME)."_";
            }
            $filename = $this->videoPath.'/'.$seed_host_name.$msgid.'.mp4';
            if(isset($re['body']) && $re['body']){
                file_put_contents($filename, $re['body']);
                return $filename;
            }else{
                return '';
            }
        }

    /**
     * 获取分组朋友列表
     * 0未分组,1黑名单,2星标组
     */
   public function getFriendslist($pagesize = 10,$pageidx = 0,$groupid = 0){
        $url = "https://mp.weixin.qq.com/cgi-bin/contactmanage?t=user/index&pagesize={$pagesize}&pageidx={$pageidx}&type=0&groupid={$groupid}&token={$this->webToken}&lang=zh_CN";
        $re = $this->lea->get($url, $this->cookie);
        $preg = '/"contacts"\s*:\s*(.*)}\)\.contacts/iUs';
        preg_match_all($preg, $re['body'], $arr);
        $re = @json_decode($arr[1][0], true);
        return  $re;
   }

  /*
 *获取刚关注用户的信息
 */
   public function getLastuserinfoByFriendslist(){
        $re = $this->getFriendslist(1);
        if(isset($re[0]['id'])){
            return $re[0];
        }
        return '';
   }

/**
 *获取上传密钥ticket等参数
 */
  public function getWXdata($type = 2){
       $url = "https://mp.weixin.qq.com/cgi-bin/filepage?type={$type}&begin=0&count=10&t=media/list&token={$this->webToken}&lang=zh_CN";
       $re = $this->lea->getSource($url, $this->cookie);
       $preg = '/data:\{.*ticket:"(\w+)".*uin:"(\d+)".*user\_name:"(.*)".*\},/iUs';
       preg_match_all($preg, $re, $arr,PREG_SET_ORDER);
       $res = array();
       if(isset($arr[0][1]) && isset($arr[0][2]) && isset($arr[0][3])){
           $res['ticket'] = $arr[0][1];
           $res['uin'] = $arr[0][2];
           $res['user_name'] = $arr[0][3];
       }
       return $res;
   }

    /**
     *获取上传视频密钥ticket等参数
     */
     public function getVideoWXdata(){
       $url = "https://mp.weixin.qq.com/cgi-bin/appmsg?t=media/videomsg_edit&action=video_edit&type=15&isNew=1&lang=zh_CN&token={$this->webToken}";
       $re = $this->lea->getSource($url, $this->cookie);
       $preg = '/data:\{.*ticket:"(\w+)".*uin:"(\d+)".*user\_name:"(.*)".*\},/iUs';
       preg_match_all($preg, $re, $arr,PREG_SET_ORDER);
       $res = array();
       if(isset($arr[0][1]) && isset($arr[0][2]) && isset($arr[0][3])){
           $res['ticket'] = $arr[0][1];
           $res['uin'] = $arr[0][2];
           $res['user_name'] = $arr[0][3];
       }
       return $res;
   }

    public function uploadVideo($file_name) {
       $wx_data = $this->getVideoWXdata();
       if(empty($wx_data)){
           return false;
       }
       $send_snoopy = new Wechat_WechatClient_Snoopy();
       $send_snoopy->set_SourceData(true);
       $send_snoopy->agent = $_SERVER['HTTP_USER_AGENT'];
       $send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/appmsg?t=media/videomsg_edit&action=video_edit&type=15&isNew=1&lang=zh_CN&token=1284613687";
       $post = array();
       $postfile = array('file'=>$file_name);
       $send_snoopy->rawheaders['Cookie'] = $this->cookie;
       $send_snoopy->set_submit_multipart();
       $submit = "https://mp.weixin.qq.com/cgi-bin/filetransfer?action=upload_video_cdn&f=json&ticket_id={$wx_data['user_name']}&ticket={$wx_data['ticket']}&token={$this->webToken}&lang=zh_CN&folder=uploads&t={$this->webToken}";
       $send_snoopy->submit($submit,$post,$postfile);
       $tmp = $send_snoopy->results;
       preg_match("/\"content\":\"(.*)\"/",$tmp,$matches);
       if (isset($matches[1])) {
          $content_arr = explode(' ', $matches[1]);
          if(isset($content_arr[1])){
              return $matches[1];
          }
        }
        return false;
  }

     /*
    * 保存视频
    */
   public function saveVideo($title0,$digest0,$fileid0,$content0){
       $url = "https://mp.weixin.qq.com/cgi-bin/operate_appmsg";
       $post = array();
       $post['AppMsgId'] = '';
       $post['ajax'] = '1';
       $post['content0'] = $content0;
       $post['count'] = '1';
       $post['digest0'] = $digest0;
       $post['f'] = 'json';
       $post['fileid0'] = $fileid0;
       $post['lang'] = "zh_CN";
       $post['random'] = time();
       $post['sub'] = "create";
       $post['t'] = "ajax-response";
       $post['title0'] = $title0;
       $post['token'] = $this->webToken;
       $post['type'] = '15';
       $re = $this->lea->submit($url, $post,$this->cookie);
       return json_decode($re['body'],1);
   }

 /*
  * 获取视频素材
  */
 public function getVideoMaterial(){
      $url = "https://mp.weixin.qq.com/cgi-bin/appmsg?begin=0&count=10&t=media/appmsg_list&type=15&action=list&token={$this->webToken}&lang=zh_CN";
       $re = $this->lea->get($url, $this->cookie);
       $preg = '/"item":(.*),"file_cnt/iUs';
       preg_match_all($preg, $re['body'], $arr);
       $res = @json_decode(isset($arr[1][0])?$arr[1][0]:'', 1);
       return  $res;
  }

   /**
 *保存素材
 */
   public function saveMaterial($msgid,$filename = ''){
       if(empty($filename)){
          $filename = 'wx_'.date('YmdHis').'_'.rand(100, 999);
       }
       $url = "https://mp.weixin.qq.com/cgi-bin/savemsgtofile";
       $post = array();
       $post['filename'] = $filename;
       $post['lang'] = 'zh_CN';
       $post['msgid'] = $msgid;
       $post['random'] = time();
       $post['t'] = 'ajax-response';
       $post['token'] = $this->webToken;
       $re = $this->lea->submit($url, $post,$this->cookie);
       return json_decode($re['body'],1);
   }

    /**
    * 获取素材
    * type 2:图片，3：语音，4：视频
    */
   public function getMaterial($type = 2,$begin = 0,$count = 10){
       $url = "https://mp.weixin.qq.com/cgi-bin/filepage?type={$type}&begin={$begin}&count={$count}&t=media/list&token={$this->webToken}&lang=zh_CN";
       $re = $this->lea->get($url, $this->cookie);
       $preg = '/"file_item":(.*)};/iUs';
       preg_match_all($preg, $re['body'], $arr);
       $res = @json_decode(isset($arr[1][0])?$arr[1][0]:'', 1);
       return  $res;
   }
   
   
      /**
    * 删除素材，不能删除图文消息
    */
public function delMaterial($fileid){
       $url = "https://mp.weixin.qq.com/cgi-bin/modifyfile";
       $post = array();
       $post['fileid'] = $fileid;
       $post['f'] = "json";
       $post['ajax'] = "1";
       $post['lang'] = 'zh_CN';
       $post['oper'] = 'del';
       $post['random'] = time();
       $post['t'] = 'ajax-response';
       $post['token'] = $this->webToken;
       $re = $this->lea->submit($url, $post,$this->cookie);
       return json_decode($re['body'],true);
   }

/**
 *上传素材
 * @param <type> $file_name //图片路径
 * @param <type> $type 类型，图片默认是2
 * @return <type>
 */
  public function uploadMaterial($file_name,$type = 2) {

       $wx_data = $this->getWXdata($type);
       if(empty($wx_data))return false;
      
       $send_snoopy = new Wechat_WechatClient_Snoopy();
       $send_snoopy->set_SourceData(true);
       $send_snoopy->agent = $_SERVER['HTTP_USER_AGENT'];
       $send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/filepage?type={$type}&begin=0&count=10&t=media/list&token={$this->webToken}&lang=zh_CN";
//       $post = array('formId'=>'');
       $post = array();
       $postfile = array('file'=>$file_name);
       $send_snoopy->rawheaders['Cookie'] = $this->cookie;
       $send_snoopy->set_submit_multipart();
       $submit = "https://mp.weixin.qq.com/cgi-bin/filetransfer?action=upload_material&f=json&ticket_id={$wx_data['user_name']}&ticket={$wx_data['ticket']}&token={$this->webToken}&lang=zh_CN&folder=uploads&t={$this->webToken}";
       $send_snoopy->submit($submit,$post,$postfile);
       $tmp = $send_snoopy->results;
       preg_match("/\"content\":\"(\d+)\"/",$tmp,$matches);
       if (isset($matches[1])) {
          $photoid = $matches[1];
          return $photoid;
       }
        return false;
  }


/**
 *上传单张图片
 */
public function uploadPic($file_name,$type = 2){
    return $this->uploadMaterial($file_name, $type);
}


  /**
   *删除单张图片
   * @param <type> $fileid //图片id
   * @return <type> 
   */
  public function delPic($fileid){
         return $this->delMaterial($fileid);
  }

  

  /*
   * 获取图文消息
   */
  public function GetNewsText($begin = 0,$count = 10){
        $url = "https://mp.weixin.qq.com/cgi-bin/appmsg?begin={$begin}&count={$count}&t=media/appmsg_list&type=10&action=list&token={$this->webToken}&lang=zh_CN";
        $re = $this->lea->get($url, $this->cookie);
        $preg = '/{"item":(.*),"file_cnt"/iUs';
        preg_match_all($preg, $re['body'], $arr);
        $res = @json_decode(isset($arr[1][0])?$arr[1][0]:'', 1);
        return  $res;
  }


/**
 *上传单图文消息
 * @param <type> $title
 * @param <type> $photoid
 * @param <type> $digest
 * @param <type> $content
 * @param <type> $author
 * @param <type> $srcurl
 * @return <type> 
 */
    public function uploadSingleNews($title,$photoid,$digest,$content,$author = '',$srcurl = ''){
      $url = "https://mp.weixin.qq.com/cgi-bin/operate_appmsg";
      $this->lea->get($url,$this->cookie);
      $post = array();
      $post['AppMsgId'] = '';
      $post['ajax'] = 1;
      $post['author0'] = $author;//作者
      $post['content0'] = $content;//正文
      $post['count'] = 1;
      $post['digest0'] = $digest;//摘要
      $post['f'] = 'json';
      $post['fileid0'] = $photoid;
      $post['lang'] = "zh_CN";
      $post['random'] = time();
      $post['sourceurl0'] = $srcurl;//原文链接
      $post['sub'] = "create";
      $post['t'] = "ajax-response";
      $post['title0'] = $title; //标题
      $post['token'] = $this->webToken;
      $post['type'] = "10";
      $re = $this->lea->submit($url, $post,$this->cookie);
      return json_decode($re['body'],true);
 }

 /**
  *删除单图文信息
  * @param <type> $fileid 信息的id
  * @return <type>
  */
   public function delSingleNews($fileid){
      $url = "https://mp.weixin.qq.com/cgi-bin/operate_appmsg?sub=del&t=ajax-response";
      $post = array();
      $post['ajax'] = 1;
      $post['AppMsgId'] = $fileid;
      $post['token'] = $this->webToken;
      $re = $this->lea->submit($url, $post,$this->cookie);
      return json_decode($re['body'],true);
  }


   public function uploadMulNews($data){
     if(empty($data) || !is_array($data))return false;
     $url = "https://mp.weixin.qq.com/cgi-bin/operate_appmsg";
     $this->lea->get($url,$this->cookie);

     $post = array();
     $post['AppMsgId'] = "";
     $post['ajax'] = "1";
     $post['count'] = count($data);
     $post['f'] = "json";
     $post['lang'] = "zh_CN";
     $post['random'] = time();
     $post['sub'] = "create";
     $post['t'] = "ajax-response";
     $post['token'] = $this->webToken;
     $post['type'] = "10";

     $i = 0;
     foreach ($data as $key => $value) {
         $post['title'.$i] = isset($value['title']) ? $value['title'] : '';
         $post['author'.$i] = isset($value['author']) ? $value['author'] : '';
         $post['content'.$i] = isset($value['content']) ? $value['content'] : '';
         $post['digest'.$i] = isset($value['digest']) ? $value['digest'] : '';
         $post['fileid'.$i] = isset($value['fileid']) ? $value['fileid'] : '';
         $post['sourceurl'.$i] = isset($value['sourceurl']) ? $value['sourceurl'] : '';
         ++$i;
     }
     $re = $this->lea->submit($url, $post,$this->cookie);
     return json_decode($re['body'],true);
 }




}
