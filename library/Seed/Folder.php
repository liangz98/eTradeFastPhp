<?php
/**
 * 文件夹工具类
 */
class Seed_Folder
{
    	const READ_ALL = '0';
	const READ_FILE = '1';
	const READ_DIR = '2';
        
        /**
	 * 获取文件列表
	 * 
	 * @param string $dir
	 * @param boolean $mode 只读取文件列表,不包含文件夹
	 * @return array
	 */
	public static function read($dir, $mode = self::READ_ALL) {
		if (!$handle = @opendir($dir)) return array();
		$files = array();
		while (false !== ($file = @readdir($handle))) {
			if ('.' === $file || '..' === $file) continue;
			if ($mode == self::READ_DIR) {
				if (self::isDir($dir . '/' . $file)) $files[] = $file;
			} elseif ($mode == self::READ_FILE) {
				if (is_file($dir . '/' . $file)) $files[] = $file;
			} else
				$files[] = $file;
		}
		@closedir($handle);
		return $files;
	}
        
        
        /**
	 * 判断输入是否为目录
	 *
	 * @param string $dir
	 * @return boolean
	 */
	public static function isDir($dir) {
		return $dir ? is_dir($dir) : false;
	}
        
        /**
	 * 创建目录
	 *
	 * @param string $path 目录路径
	 * @param int $permissions 权限
	 * @return boolean
	 */
	public static function mk($path, $permissions = 0777) {
		return @mkdir($path, $permissions);
	}
        
        
        /**
	 * 取得目录信息
	 * 
	 * @param string $dir 目录路径
	 * @return array
	 */
	public static function getInfo($dir) {
		return self::isDir($dir) ? stat($dir) : array();
	}
        
	/**
	 * 递归的创建目录
	 *
	 * @param string $path 目录路径
	 * @param int $permissions 权限
	 * @return boolean
	 */
	public static function mkRecur($path, $permissions = 0777) {
		if (is_dir($path)) return true;
		$_path = dirname($path);
		if ($_path !== $path) self::mkRecur($_path, $permissions);
		return self::mk($path, $permissions);
	}
        
        
}