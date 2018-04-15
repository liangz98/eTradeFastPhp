<?php
class Seed_Cache2Js {
	/**
	 * cache4region
	 *
	 * @param array|struct $data
	 * @return boolean
	 */
	public function cache4region($data){
		$file=SEED_WWW_ROOT."/js/seed.region.js";
		if($fp = @fopen($file,"w")){
	    	flock($fp,LOCK_EX);
	   		fwrite($fp,$data);
	    	flock($fp,LOCK_UN);
			fclose($fp);
			@chmod($file,0666);
			return true;
	    }else{
	    	exit("Can't open the file: ".$file);
	    }
	}
	
	
	/**
	 * cache4category
	 *
	 * @param array|struct $data
	 * @return boolean
	 */
	public function cache4category($data){
		$file=SEED_WWW_ROOT."/js/seed.category.js";
		if($fp = @fopen($file,"w")){
	    	flock($fp,LOCK_EX);
	   		fwrite($fp,$data);
	    	flock($fp,LOCK_UN);
			fclose($fp);
			@chmod($file,0666);
			return true;
	    }else{
	    	exit("Can't open the file: ".$file);
	    }
	}
}
?>