<?php
class Seed_File{
	public $_postfix=array();
	
	public function setPostfix($postfix){
		$this->_postfix=explode(",",$postfix);
	}
	
	public function upload($file,$path,$keep_orgname=0,$folder=""){
		$upload_file=$file['tmp_name'];
		if($keep_orgname==1){
			$name=strtolower($file['name']);
			$ext=$this->getExt($name);
			$dest=$path;
			if(!empty($folder)){
				$dest.="/".$folder;
				if(!is_dir($dest)){
					mkdir($dest);
					chmod($dest,0777);
				}
			}
			$dest.="/".$name;
			if(!in_array($ext,$this->_postfix)){
				throw new Exception("format not support!");
			}else if(move_uploaded_file($upload_file,$dest)){
				chmod($dest,0666);
				if(!empty($folder))
					return $folder."/".$name;
				else 
					return $name;
			}
		}else{
			$name=$file['name'];
			$path=str_replace("\\","/",$path);
			$folder=substr($path,strrpos($path,"/")+1);
			$ext=$this->getExt($name);
			$name="org_".Seed_Common::genRandomString(12);
			$name.=".".$ext;
			$ndate=date("Ymd");
			$dest=$path.'/'.$ndate;
			if(!is_dir($dest)){
				mkdir($dest);
				chmod($dest,0777);
			}
			$dest.="/".$name;
			if(!in_array($ext,$this->_postfix)){
				throw new Exception("format not support!");
			}else if(move_uploaded_file($upload_file,$dest)){
				chmod($dest,0666);
				return $ndate."/".$name;
			}
		}
	}
	
	public function download($url,$path){
		$name=strtolower(substr($url,strrpos($url,"/")+1));
		$path=str_replace("\\","/",$path);
		$folder=substr($path,strrpos($path,"/")+1);
		$ext=$this->getExt($name);
		$name="org_".Seed_Common::genRandomString(12);
		$name.=".".$ext;
		$ndate=date("Ymd");
		$dest=$path.'/'.$ndate;
		if(!is_dir($dest)){
			mkdir($dest);
			chmod($dest,0777);
		}
		$dest.="/".$name;
		
		if(!in_array($ext,$this->_postfix)){
			throw new Exception("format not support!");
		}else if($this->mydownload($url,$dest)){
			return $ndate."/".$name;
		}
	}
	
	public function getExt($fileName){
		$extStr=explode('.',$fileName);
		$count=count($extStr)-1;
		return strtolower($extStr[$count]);
	}
	
	public function getFilename($fileName){
		$extStr=explode('/',$fileName);
		$count=count($extStr)-1;
		return strtolower($extStr[$count]);
	}
	
	public function mydownload($url,$dest){
		//temp file
		$tempSaveName=$dest.".tmp";
		//check folder
		$folder=substr($dest,0,strrpos($dest,"/"));
		if(!is_dir($folder)){
			throw new Exception("folder not exists!");
		}
		if(file_exists($dest)){
			throw new Exception("file exists!");
		}
		$rangeStart=0;
		if(file_exists($tempSaveName)){
			if($ofile=fopen($tempSaveName,"r")){
				$ofilesize=filesize($tempSaveName);
				if($ofilesize){
					$rangeStart=$ofilesize;
				}
			}
			else{
				throw new Exception("file cannot open!");
			}
		}
		
		$urlArr = null;
		$urlArr = @parse_url($url);
        if(is_array($urlArr))
        {
             //open host
            $host=$urlArr["host"];
            $port=(isset($urlArr["port"]) && $urlArr["port"])?$urlArr["port"]:"80";
            $query=(isset($urlArr["query"]) && $urlArr["query"])?"?".$urlArr["query"]:"";
            $path=$urlArr["path"].$query;
            $fsp = fsockopen($host, $port, $errno, $errstr,10);
            if($fsp){
            	//post header
            	fputs($fsp,"GET ".$path." HTTP/1.0\r\n");
            	
            	$headerArr=array();
            	if(!isset($headerArr["Accept"])) { $headerArr["Accept"] = "*/*"; }
                if(!isset($headerArr["User-Agent"])) { $headerArr["User-Agent"] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2)"; }
                if(!isset($headerArr["Refer"])) { $headerArr["Refer"] = "http://".$host; }
                if(!isset($headerArr["Host"])) { $headerArr["Host"] = $host; }
				if(!isset($headerArr["Range"]) && $rangeStart) { $headerArr["Range"] = "bytes=".$rangeStart."-"; }
				
                foreach($headerArr as $k=>$v){
                        $k = trim($k);
                        $v = trim($v);
                        if($k!=""&&$v!=""){
                                fputs($fsp,"$k: $v\r\n");
                        }
                }
                fputs($fsp,"Connection: Close\r\n\r\n");
                
                //httpstatus
                $httpstatus = explode(" ",fgets($fsp,256));
                $headerArr["http-edition"] = trim($httpstatus[0]);
                $headerArr["http-state"] = trim($httpstatus[1]);
                $headerArr["http-describe"] = "";
                for($i=2;$i<count($httpstatus);$i++){
                        $headerArr["http-describe"] .= " ".trim($httpstatus[$i]);
                }
                
                while(!feof($fsp)){
                    $line = trim(fgets($fsp,256));
                    if($line == "") break;
                    
                    $hkey = "";
                    $hvalue = "";
                    $v = 0;
                    for($i=0;$i<strlen($line);$i++){
                            if($v==1) $hvalue .= $line[$i];
                            if($line[$i]==":") $v = 1;
                            if($v==0) $hkey .= $line[$i];
                    }
                    $hkey = trim($hkey);
                    if($hkey!="") $headerArr[strtolower($hkey)] = trim($hvalue);
                }
                
                //file not found
                if($headerArr['http-state']==404){
                	throw new Exception("file not found!");
                }
                
                //save file
                if($fp = fopen($tempSaveName,"a")){
                	flock($fp,LOCK_EX);
                	while(!feof($fsp)){
                		fwrite($fp,fread($fsp,4096));
                	}
                	flock($fp,LOCK_UN);
					fclose($fp);
					rename($tempSaveName,$dest);
					chmod($dest,0666);
					return true;
                }
                fclose($fsp);
            }else{
            	throw new Exception("host cannot open!");
            }
        }
	}
	
	public function deldir($dir) {
		$dh=opendir($dir);
		while ($file=readdir($dh)) {
			if($file!="." && $file!="..") {
				$fullpath=$dir."/".$file;
				if(!is_dir($fullpath)) {
					unlink($fullpath);
				} else {
					deldir($fullpath);
				}
			}
		}
		
		closedir($dh);
		
		if(rmdir($dir)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function formatFileName($path,$orgFileName,$fileId,$oldStr="org_"){
		$orgFile=realpath($path."/".$orgFileName);
		if(file_exists($orgFile)){
			$newFileName=str_replace($oldStr,$fileId."_",$orgFileName);
			@copy($path."/".$orgFileName,$path."/".$newFileName);
			@unlink($path."/".$orgFileName);
			chmod($path."/".$newFileName,0666);
			return $newFileName;
		}
		return $orgFileName;
	}
	
	public $_listfile=array();
	public function listfile($dir) {
		$dh=opendir($dir);
		while ($file=readdir($dh)) {
			if($file!="." && $file!="..") {
				$fullpath=$dir."/".$file;
				if(!is_dir($fullpath)) {
					$this->_listfile[]=$fullpath;
				} else {
					$this->listfile($fullpath);
				}
			}
		}
		closedir($dh);
		return $this->_listfile;
	}
	
	public function readfile($filename){
		if(!is_readable($filename))throw new Exception("file cannot open!");
		$handle = fopen($filename,"r");
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		return $contents;
	}
	
	public function savefile($file, $data){
		$this->createFolder($file);
		if($fp = @fopen($file,"w")){
	    	flock($fp,LOCK_EX);
	   		fwrite($fp,$data);
	    	flock($fp,LOCK_UN);
			fclose($fp);
			chmod($file,0666);
			return true;
	    }else{
	    	throw new Exception("Can't open the file: ".$file);
	    }
	}
	
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
}