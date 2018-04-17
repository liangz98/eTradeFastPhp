<?php

class LogController extends Seed_Controller_Action4Admin {
    function preDispatch() {
    }
    
    function indexAction() {
        $logM = new Seed_Model_Log('system');
        
        $conditions = array();
        //查询条件
        if (trim($this->_request->getParam('user_name')) != '')
            $conditions['user_name'] = trim($this->_request->getParam('user_name'));
        if (trim($this->_request->getParam('user_id')) != '')
            $conditions['user_name'] = trim($this->_request->getParam('user_id'));
        
        $perpage = 15;
        $page = intval($this->_request->getParam('page'));
        $total = $logM->fetchRowsCount($conditions);
        $pageObj = new Seed_Page($this->_request, $total, $perpage);
        $this->view->page = $pageObj->getPageArray();
        if ($page > $this->view->page['totalpage'])
            $page = $this->view->page['totalpage'];
        if ($page < 1)
            $page = 1;
        $logs = $logM->fetchRows(array(
            ($page - 1) * $perpage,
            $perpage
        ), $conditions, "log_id DESC");
        $this->view->logs = $logs;
        $this->view->conditions = $conditions;
    
    
        /* 读取本地日志文件 */
    
        // $filePath = $_SERVER["DOCUMENT_ROOT"] . "/checkdata/app/files/nlp.txt";
        // $filePath = "/storage/html/eTradeFastWebhooksTest/eTradeFastPhp/public/ky/mod2018-04-17-16.log.txt";
        $filePath = "/storage/html/eTradeFastWebhooksTest/eTradeFastPhp/log/mod2018-04-17-16.log.txt";
        $file = fopen($filePath, "r");
    
        $directory = "/storage/html/eTradeFastWebhooksTest/eTradeFastPhp/log/";
        $fileList = scandir($directory, 1);
    
        // $str = file_get_contents('/ky/mod2018-04-17-16.log.txt');//将整个文件内容读入到一个字符串中
        // $str_encoding = mb_convert_encoding($file, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');//转换字符集（编码）
        // $arr = explode("\r\n", $str_encoding);//转换成数组
    
        //去除值中的空格
        // foreach ($arr as &$row) {
        //     $row = trim($row);
        // }
        //
        // unset($row);
        //得到后的数组
        // var_dump($arr);
    
    
        $fileContent = '';
        //Output a line of the file until the end is reached
        //feof() check if file read end EOF
        while(!feof($file))
        {
            //fgets() Read row by row
            $fileContent .= fgets($file). "<br />";
        }
        fclose($file);
        
        
        
        $this->view->firstLog = $file;
        $this->view->fileList = $fileList;
        $this->view->testStr = "test STR";
        $this->view->fileContent = $fileContent;
    }
    
    
    function viewAction() {
        $log_id = $this->_request->getParam('log_id');
        if ($log_id < 1)
            throw new Exception('参数错误');
        $logM = new Seed_Model_Log('system');
        $log = $logM->fetchRow(array('log_id' => $log_id));
        if ($log['log_id'] < 1)
            throw new Exception('没有找到相关数据');
        $log['log_data'] = unserialize($log['log_data']);
        $this->view->log = $log;
    }
    
    function testdataAction() {
        // echo "in";exit;
        $msg = '{"code":0,"msg":"","count":1000,"data":[{"id":10000,"username":"user-0","sex":"女","city":"城市-0","sign":"签名-0","experience":255,"logins":24,"wealth":82830700,"classify":"作家","score":57},{"id":10001,"username":"user-1","sex":"男","city":"城市-1","sign":"签名-1","experience":884,"logins":58,"wealth":64928690,"classify":"词人","score":27},{"id":10002,"username":"user-2","sex":"女","city":"城市-2","sign":"签名-2","experience":650,"logins":77,"wealth":6298078,"classify":"酱油","score":31}]}';
        echo $msg;
        exit;
    }
}