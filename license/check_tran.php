<?php
//decode by QQ:270656184 http://www.yunlu99.com/
if (!defined('GZSEED_AUTH_STEP2') || GZSEED_AUTH_STEP2 != '1') {
	$ch = curl_init();
	if (!$ch) exit;
	$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36';
	curl_setopt($ch, CURLOPT_URL, 'http://lic.gzseed123.cn/?from=sendtou&host=' . $_SERVER['HTTP_HOST'] . '&ip=' . $_SERVER['SERVER_ADDR']);
	curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_exec($ch);
	exit('Invalid License file!');
}
if (defined('SEED_HOST_NAME')) {
	$seed_host_name = str_replace('.', '_', SEED_HOST_NAME);
	$dbfile = strtolower($seed_host_name) . '.xml';
} else {
	$dbfile = 'mydbconfig.xml';
}
if ($config_file == null) $config_file = SEED_CONF_ROOT . '/' . $dbfile;
$registry = Zend_Registry::getInstance();
if ($registry->isRegistered('db4transaction')) {
	$this->_db = $registry->get('db4transaction');
}
if ($registry->isRegistered('db4' . $label)) {
	$this->_prefix = $registry->get('prefix4' . $label);
	$this->_config = $registry->get('config4' . $label);
	$this->_dbname = $registry->get('dbname4' . $label);
	return;
}
try {
	$this->_config = new Zend_Config_Xml($config_file, $label);
} catch (Exception $e) {
	throw $e;
}
try {
	if ($registry->isRegistered('db4transaction')) {
		$this->_db = $registry->get('db4transaction');
		$dbconfig = $this->_config->db->params->toArray();
		$this->_prefix = $dbconfig['prefix'];
		$this->_dbname = $dbconfig['dbname'];
		if (isset($dbconfig['charset'])) {
			$this->_db->query('SET NAMES ' . $dbconfig['charset']);
		}
		Zend_Db_Table::setDefaultAdapter($this->_db);
		$registry->set('prefix4' . $label, $this->_prefix);
		$registry->set('config4' . $label, $this->_config);
		$registry->set('dbname4' . $label, $this->_dbname);
	} else {
		$this->_db = Zend_Db::factory($this->_config->db);
		$dbconfig = $this->_config->db->params->toArray();
		$this->_prefix = $dbconfig['prefix'];
		$this->_dbname = $dbconfig['dbname'];
		if (isset($dbconfig['charset'])) {
			$this->_db->query('SET NAMES ' . $dbconfig['charset']);
		}
		Zend_Db_Table::setDefaultAdapter($this->_db);
		$registry->set('db4transaction', $this->_db);
		$registry->set('prefix4' . $label, $this->_prefix);
		$registry->set('config4' . $label, $this->_config);
		$registry->set('dbname4' . $label, $this->_dbname);
	}
} catch (Exception $e) {
	throw $e;
}