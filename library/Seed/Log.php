<?php
class Seed_Log
{
    /**
     * 记录日志
     * @param unknown_type $log_content 日志内容
     * @param unknown_type $log_file SEED_TEMP_ROOT+文件名
     */
    public static function record($log_content,$log_file)
    {
		$filename = SEED_TEMP_ROOT.'/'.$log_file;
		$content = "[". date('Y-m-d H:i:s') ."]".$log_content;
		if(file_exists($filename)){
			$last_content = file_get_contents($filename);
			$content = $last_content."\r\n".$content ;
		}
		file_put_contents($filename, $content);
    }
}