<?php

$typeArr = array("jpg", "png", "gif","jpeg"); //允许上传文件格式
$path = "uploads/"; //上传路径

if (isset($_POST)) {
    $name = $_FILES['file']['name'];
    $size = $_FILES['file']['size'];
    $name_tmp = $_FILES['file']['tmp_name'];
    $urlNew = $_REQUEST['urlDD'];
    $bizID = $_REQUEST['bizID'];
    $bizType = $_REQUEST['bizType'];
    $attachType = $_REQUEST['attachType'];
    $typeDD = $_REQUEST['typeDD'];

    if (empty($name)) {
        echo json_encode(array("error" => "您还未选择文件", "size" => $size, "files" => $_FILES));
        exit;
    }
    $type = strtolower(substr(strrchr($name, '.'), 1)); //获取文件类型

    // 此处不再做文件大小验证 - 20190725
    // if ($size > (16000 * 1024)) { //上传大小
    //     echo json_encode(array("error" => "图片大小已超过16MB！"));
    //     exit;
    // }

    // 远程服务器上传图片
    session_start();
    $sid = session_id();
    
    if ($typeDD == "1") {
        $post_data = array(
            "sid"     => $sid,
            "name"    => $name,
            "file"    => curl_file_create($name_tmp),
            "bizID"   => $bizID,
            "bizType" => $bizType,
            "type"    => $attachType
        );
        $url = $urlNew . "/doc/uploadAttach.action";
    } else {
        $post_data = array(
            "sid"  => $sid,
            "name" => $name,
            "file" => curl_file_create($name_tmp)
        );
        $url = $urlNew . "/doc/upload.action";
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_POST, 1 );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
       //     'Content-Type: application/json; charset=utf-8',
//            'User_Agent: Etrade_PhpAgent',
//            'Authorization: MbMv1uuJWsufm5cA2GEU',
//            'Content-Length: ' . strlen($post_data)
        )
    );
    curl_error($ch);  //查看报错信息

    // 提交
    $tmpInfo = curl_exec($ch);

    $res = json_decode($tmpInfo, true);

    if ($typeDD == "1") {
        $nid = $res['attachment']['attachID'];
        $vid = $res['attachment']['verifyID'];
        $urlRQ = $urlNew . "/doc/download.action";
        $nd = '&nid=' . $nid;
        $vd = '&vid=' . $vid;
    } else {
        $nid = $res['nid'];
        $urlRQ = $urlNew . "/doc/temporary.action";
        $nd = '&nid=' . $nid;
        $vd = "";
    }


    $pic_url = $urlRQ . '?sid=' . $sid . '&size=MIDDLE&' . $nd . $vd;
    $fullPicUrl = $urlRQ . '?sid=' . $sid . $nd . $vd;
    $doc_url = $urlRQ . '?sid=' . $sid . '&' . $nd . $vd;

    $pic_name = $name; //图片名称

    $logData='【'.date('Y-m-d  H:i:s').'】'."\r\n".$url."\r\n".json_encode($post_data)."\r\n".$tmpInfo."\r\n"."\r\n";

    $file  = '../../../log/UP';//要写入文件的文件名（可以是任意文件名），如果文件不存在，将会创建一个
    
    
    if (save($file, $logData)) {// 这个函数支持版本(PHP 5)
        $wt = 'isok';
    } else {
        $wt = 'fail';
    }
    
    //返回结果
    if ($res['responseCode'] == 'success') {
        echo json_encode(array(
            "res"        => $res,
            "error"      => "0",
            "type"       => $type,
            "wt"         => $wt,
            "pic"        => $pic_url,
            "fullPic"    => $fullPicUrl,
            "doc"        => $doc_url,
            "name"       => $pic_name,
            "nid"        => $nid,
            "size"       => $size,
            "attachTT"   => $attachType,
            "bizTT"      => $bizType,
            "uploadType" => $typeDD,
            "bizID"      => $bizID
        ));
    } else {
        echo json_encode(array(
            "res"  => $res,
            "data" => $post_data,
            "url"  => $url
        ));
    }
    exit();
}

function save($file, $data){
    $file=$file.date('Y-m-d-H-i').'.log.txt';
    createFolder($file);

    if($fp = @fopen($file,"a")){
        flock($fp,LOCK_EX);
        fwrite($fp,$data);
        flock($fp,LOCK_UN);
        fclose($fp);
        @chmod($file,0666);
        return true;
    }else{
        return false;
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

