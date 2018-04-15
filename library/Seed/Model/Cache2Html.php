<?php
/**
 * 文件类
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_Cache2Html {
    
    /**
     * 文件后缀
     *
     * @var string
     */
	protected $_postfix;
    
    /**
     * 构造函数
     *
     */
	function __construct()
	{
		if(!defined('SEED_WWW_ROOT'))throw new Exception("SEED_WWW_ROOT not defined");
	}
	
	/**
	 * 保存缓存文件
	 *
	 * @param	string	$file	文件路径
	 * @param	array|struct	$data	要保存的数据
	 * @return	boolean
	 */
	public function save($file, $data){
		$file=SEED_WWW_ROOT."/".$file;
		$this->createFolder($file);
		if($fp = @fopen($file,"w")){
	    	flock($fp,LOCK_EX);
	   		fwrite($fp,$data);
	    	flock($fp,LOCK_UN);
			fclose($fp);
			@chmod($file,0666);
			return true;
	    }else{
	    	throw new Exception("Can't open the file: ".$file);
	    }
	}
	
    /**
	 * 创建文件夹
	 *
	 * @param	string	$path	文件路径
	 */
	function createFolder($path){
		$path=str_replace("\\","/",$path);
		$pathA=explode("/",$path);
		$currentFold=$pathA[0];
		$pNum=count($pathA);
		for($i=1;$i<($pNum-1);$i++){
			$currentFold.="/".$pathA[$i];
			if(!is_dir($currentFold) && $pathA[$i]){
				if(!@mkdir($currentFold)){
					throw new Exception("Can't create folder: ".$currentFold);
				}
				if(!@chmod($currentFold,0777)){
					throw new Exception("Can't chmod folder: ".$currentFold);
				}
			}
		}
	}
	
    /**
	 * 删除文件夹
	 *
	 * @param	string	$path	文件路径
	 */
	function removeFolder($path){
		$dir=SEED_WWW_ROOT."/".$path;
		if(!is_dir($dir)){
			return true;
		}
		
		if (@rmdir($dir)==false && is_dir($dir)) {
			if ($dp = @opendir($dir)) {
				while (($file=@readdir($dp)) != false) {
					if (is_dir($dir."/".$file) && $file!='.' && $file!='..') {
						$this->removeFolder($path."/".$file);
					} else {
						@unlink($dir."/".$file);
					}
				}
				@closedir($dp);
				@rmdir($dir);
			} else {
				throw new Exception("Not permission");
			}
		}
	}
	
    /**
	 * 删除文件
	 *
	 * @param	string	$file	文件路径
	 */
	function remove($file)
	{
		$file=SEED_WWW_ROOT."/".$file.$this->_postfix;
		if(file_exists($file)){
			if(!@unlink($file)){
				throw new Exception("Can't remove file: ".$file);
			}
		}
	}
	
    /**
	 * 获取缓存
	 *
	 * @param	string	$file	文件路径
	 * @return	mixed
	 */
	function get($file)
	{
		$file=SEED_WWW_ROOT."/".$file.$this->_postfix;
		if(file_exists($file)){
			require($file);
			return unserialize($seed_cache_data);
		}else{
			return null;
		}
	}
}