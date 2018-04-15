<?php
/**
 * Seed_Model_TransactionDb
 *
 * @author Biaoest (biaoest@gmail.com)
 * @link http://www.gzseed.com
 * @version 1.0.1
 * @copyright guangzhou seed studio
 *
 **/
class Seed_Model_TransactionDb{
    
    /**
     * 数据库资源
     *
     * @var resource
     */
	public $_db;

    /**
     * 构造函数
     *
     * 自动更加标签引入相应的数据库，并注册，如果同一进程需要引用相同的数据库则无需二次连接
     *
     * @param	string	$label	标签
     * @param	string	$config_file	配置文件名
     *
     */
    public function __construct($label='system', $config_file=null)
    {
        //首先引入事务版的授权文件，事务版的不存在则引入传统版的授权文件，by brave，2015-06-02 14:55:30
        if(file_exists(SEED_LICENSE_ROOT."/check_tran.php")) {
            require(SEED_LICENSE_ROOT . "/check_tran.php");
        }elseif(file_exists(SEED_LICENSE_ROOT."/check.php")){
            require(SEED_LICENSE_ROOT."/check.php");
        }else{
            exit("License File Not Found!");
        }
    }

    /**
     * 开启事务
     *
     */
    public function beginTransaction()
    {
        $this->_db->beginTransaction();
    }

    /**
     * 开启事务
     *
     */
    public function commit() {
        $this->_db->commit();
    }

    /**
     * 回滚事务
     *
     */
    public function rollBack() {
        $this->_db->rollBack();
    }
}