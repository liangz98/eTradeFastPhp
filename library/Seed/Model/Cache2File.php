<?php
/**
 * 文件缓存类
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c)
 */
class Seed_Model_Cache2File {
    
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
		if(!defined('SEED_CACHE_ROOT'))throw new Exception("SEED_CACHE_ROOT not defined");
		$this->_postfix=".cache.php";
	}
	
//	/**
//	 * 保存缓存文件
//	 *
//	 * @param	string	$file	文件路径
//	 * @param	array|struct	$data	要保存的数据
//	 * @return	boolean
//	 */
	public function save($file, $data){
		$file=SEED_CACHE_ROOT."/".$file.$this->_postfix;
		$this->createFolder($file);
		$data="<?php\r\n\$seed_cache_data = <<<EOD
".serialize($data)."
EOD;
?>";
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
	
//    /**
//	 * 创建文件夹
//	 *
//	 * @param	string	$path	文件路径
//	 */
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
	
//    /**
//	 * 删除文件夹
//	 *
//	 * @param	string	$path	文件路径
//	 */
	function removeFolder($path){
		$dir=SEED_CACHE_ROOT."/".$path;
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
	
//    /**
//	 * 删除文件
//	 *
//	 * @param	string	$file	文件路径
//	 */
	function remove($file)
	{
		$file=SEED_CACHE_ROOT."/".$file.$this->_postfix;
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
		$file=SEED_CACHE_ROOT."/".$file.$this->_postfix;
		if(file_exists($file)){
			require($file);
			return unserialize($seed_cache_data);
		}else{
			return null;
		}
	}
	function find($file,$type,$key)
	{
		$file=SEED_CACHE_ROOT."/".$file.$this->_postfix;
		if(file_exists($file)){
			require($file);
			$dataDD=unserialize($seed_cache_data);
			return $dataDD[$type][$key];
		}else{
			return null;
		}
	}
}