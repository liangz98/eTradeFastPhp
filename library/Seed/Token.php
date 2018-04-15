<?php
/**
 * Seed_Model_Token
 * 
 * @author Biaoest (biaoest@gmail.com)
 * @link http://www.gzseed.com
 * @version 1.0.1
 * @copyright guangzhou seed studio
 * 
 **/
class Seed_Token
{	
	public static function encode($user_id,$token,$key='gzseed'){
		$txt = $user_id.":".$token;
		srand((double)microtime() * 1000000);		
		$encrypt_key = md5(rand(0, 32000));		
		$ctr = 0;		
		$tmp = '';
		for($i = 0; $i < strlen($txt); $i++) {
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;	
			$tmp .= $encrypt_key[$ctr].($txt[$i] ^ $encrypt_key[$ctr++]);		
		}
		$data = base64_encode(self::passport_key($tmp, $key));
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
	}
	
	public static function decode($txt,$key='gzseed'){
		$txt = str_replace(array('-','_'),array('+','/'),$txt);
		$mod4 = strlen($txt) % 4;
		if ($mod4) {
		    $txt .= substr('====', $mod4);
		}
		$txt = self::passport_key(base64_decode($txt), $key);
		$tmp = '';
		for ($i = 0; $i < strlen($txt); $i++) {
			$tmp .= $txt[$i] ^ $txt[++$i];		
		}
		$my=explode(":",$tmp);
		if(!is_array($my) || count($my)!=2)return false;
		return array('user_id'=>$my[0],'token'=>$my[1]);
	}
	
	public static function passport_key($txt, $encrypt_key) {
		$encrypt_key = md5($encrypt_key);	
		$ctr = 0;
		$tmp = '';	
		for($i = 0; $i < strlen($txt); $i++) {	
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;	
			$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];	
		}
		return $tmp;	
	}
}