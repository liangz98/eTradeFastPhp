<?php

// function vpost($url,$data)
//{
//    // 模拟提交数据函数
//    $curl = curl_init(); // 启动一个CURL会话
//    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
//    /*  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
//      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在   */
//    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
//    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
//    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
//    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);    //获取的信息以文件流的形式返回
//    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
//    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
//    //curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_file); // 读取上面所储存的Cookie信息
//    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
//    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
//    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
//    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
//    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
//            'Content-Type: application/json; charset=utf-8',
//            'User_Agent: Etrade_PhpAgent',
//            'Authorization: MbMv1uuJWsufm5cA2GEU',
//            'Content-Length: ' . strlen($data)
//        )
//    );
//    $cacheM = new Seed_Model_Cache2Log();
//    $tmpInfo = curl_exec($curl); // 执行操作
//
//    curl_close($curl); // 关键CURL会话
//    if (curl_errno($curl)) {
//        echo 'Errno'.curl_error($curl);
//        $errorData=date('Y-m-d  H:i:s').'Errno'.curl_error($curl)."\r\n";
//        $cacheM->save("error_curl",$errorData);
//    }
//    if(json_decode($tmpInfo)->status!=1){
//        $logData='【'.date('Y-m-d  H:i:s').'】'."\r\n".$url."\r\n".$data."\r\n".$tmpInfo."\r\n"."\r\n";
//        $cacheM->save("error_st",$logData);
//    }
//    $logData='【'.date('Y-m-d  H:i:s').'】'."\r\n".$url."\r\n".$data."\r\n".$tmpInfo."\r\n"."\r\n";
//    $cacheM->save("mod",$logData);
//    return $tmpInfo; // 返回数据
//}


$typeArr = array("jpg", "png", "gif","jpeg"); //允许上传文件格式
$path = "uploads/"; //上传路径

if (isset($_POST)) {
    $name = $_FILES['file']['name'];
    $size = $_FILES['file']['size'];
    $name_tmp = $_FILES['file']['tmp_name'];
    $urlNew = $_REQUEST['urlDD'];
    $bizID = $_REQUEST['bizID'];
    $bizType = $_REQUEST['bizType'];
    $attachType = 'CRSE';
    $typeDD = $_REQUEST['typeDD'];

    if (empty($name)) {
        echo json_encode(array("error" => "您还未选择图片"));
        exit;
    }
    $type = strtolower(substr(strrchr($name, '.'), 1)); //获取文件类型

//    if (!in_array($type, $typeArr)) {
//        echo json_encode(array("error" => "清上传jpg,png或gif类型的图片！"));
//        exit;
//    }
    if ($size > (50000 * 1024)) { //上传大小
        echo json_encode(array("error" => "图片大小已超过50000KB！"));
        exit;
    }

    //远程服务器上传图片
    session_start();
    $sid=session_id();
    if ($typeDD=="1") {
        $post_data = array(
            "sid"=>$sid,
            "name"=>$name,
            "file"=>"@".$name_tmp,
            "bizID"=>$bizID,
            "bizType"=>$bizType,
            "type"=>$attachType
        );
        $url  =$urlNew."/doc/uploadAttach.action";
    }else{
        $post_data = array(
            "sid"=>$sid,
            "name"=>$name,
            "file"=>"@".$name_tmp
        );
        $url  =$urlNew."/doc/upload.action";
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
    $res=json_decode(curl_exec($ch),true);
//    echo json_encode(array("res"=>$res,"post"=>$post_data));
//    exit;
    if ($typeDD=="1") {
        $nid=$res['attachment']['attachID'];
        $vid=$res['attachment']['verifyID'];
        $urlRQ =$urlNew."/doc/download.action";
        $nd='&nid='.$nid;
        $vd='&vid='.$vid;
    }else{
        $nid=$res['nid'];
        $urlRQ= $urlNew."/doc/temporary.action";
        $nd='&nid='.$nid;
        $vd="";
    }


    $pic_url=$urlRQ.'?sid='.$sid.'&size=MIDDLE&'.$nd.$vd;
    $doc_url=$urlRQ.'?sid='.$sid.'&'.$nd.$vd;
    $pic_name = time() . rand(10000, 99999) . "." . $type; //图片名称
//返回结果
    if ($res['responseCode']=='success') {
    echo json_encode(array("res"=>$res,"error" => "0","type"=>$type,  "pic" => $pic_url, "doc" => $doc_url, "name" => $pic_name,"nid"=>$nid,"size"=>$size,"attachTT"=>$attachType));
    }else{
        echo json_encode(array("res"=>$res,"data"=>$post_data,"url"=>$url));
    }
 exit();
}
