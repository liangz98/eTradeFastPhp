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

        // 正式
        // $directory = "/storage/html/kuaiyi/log/";

        // 测试
        $directory = "/storage/html/eTradeFastWebhooksTest/eTradeFastPhp/log/";

        // 本地
        // $directory = "D:/kuaiyi/log/";

        $fileList = scandir($directory, 1);
        foreach ($fileList as $index => $value) {
            if (is_file($directory.$value)) {
                if ($index >= $start && $index <= $end) {
                    $files[$index]['id'] = $index + 1;
                    $files[$index]['name'] = $value;
                    $files[$index]['fileTime'] = date('Y-m-d H:i:s', filectime($directory . $value));
                    $files[$index]['fileSize'] = $this->size(filesize($directory . $value));
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

        // $directory = "/storage/html/kuaiyi/log/";
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

    function changeFileSize($size, $format) {
        $p = 0;
        if ($format == 'kb') {
            $p = 1;
        } elseif ($format == 'mb') {
            $p = 2;
        } elseif ($format == 'gb') {
            $p = 3;
        }
        $size /= pow(1024, $p);
        return number_format($size);
    }

    function size($byte) {
        if ($byte < 1024) {
            $unit = "B";
        } else if ($byte < 10240) {
            $byte = $this->round_dp($byte / 1024, 2);
            $unit = "KB";
        } else if ($byte < 102400) {
            $byte = $this->round_dp($byte / 1024, 2);
            $unit = "KB";
        } else if ($byte < 1048576) {
            $byte = $this->round_dp($byte / 1024, 2);
            $unit = "KB";
        } else if ($byte < 10485760) {
            $byte = $this->round_dp($byte / 1048576, 2);
            $unit = "MB";
        } else if ($byte < 104857600) {
            $byte = $this->round_dp($byte / 1048576, 2);
            $unit = "MB";
        } else if ($byte < 1073741824) {
            $byte = $this->round_dp($byte / 1048576, 2);
            $unit = "MB";
        } else {
            $byte = $this->round_dp($byte / 1073741824, 2);
            $unit = "GB";
        }

        $byte .= ' '.$unit;
        return $byte;
    }

    function round_dp($num, $dp) {
        $sh = pow(10, $dp);
        return (round($num * $sh) / $sh);
    }
}
