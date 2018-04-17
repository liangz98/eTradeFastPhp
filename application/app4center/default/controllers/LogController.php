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
    
    function logfilelistAction() {
        $page = $this->_request->getParam('page');
        $limit = $this->_request->getParam('limit');
        $result = array();
        $files = array();
    
        $result['code'] = 0;
        $result['msg'] = "";
        $result['count'] = 0;
    
        $limit = $limit < 10 ? 10 : $limit;
        
        $start = $page * $limit - $limit;
        $end = $page * $limit - 1;
    
        $directory = "/storage/html/eTradeFastWebhooksTest/eTradeFastPhp/log/";
        $fileList = scandir($directory, 1);
        foreach ($fileList as $index => $value) {
            if (is_file($directory.$value)) {
                if ($index >= $start && $index <= $end) {
                    $files[$index]['id'] = $index + 1;
                    $files[$index]['name'] = $value;
                    $files[$index]['fileTime'] = date('Y-m-d H:i:s', filectime($directory . $value));
                    $files[$index]['page'] = $page;
                    $files[$index]['limit'] = $limit;
                }
                $result['count']++;
            }
        }
        $result['data'] = $files;
        echo json_encode($result);
        exit;
    }
    
    function loginfoAction() {
        $fileName = $this->_request->getParam('fileName');
        $directory = "/storage/html/eTradeFastWebhooksTest/eTradeFastPhp/log/";
    
        $file = fopen($directory.$fileName, "r");
        $fileContent = '';
        //Output a line of the file until the end is reached
        //feof() check if file read end EOF
        while (!feof($file)) {
            //fgets() Read row by row
            $fileContent .= fgets($file) . "<br />";
        }
        fclose($file);
    
        echo $fileContent;
        exit;
    }
}