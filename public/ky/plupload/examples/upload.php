<?php
$typeArr = array("jpg", "png", "gif");//允许上传文件格式
$path = "uploads/";//上传路径

if (isset($_POST)) {
	$name = $_FILES['file']['name'];
	$size = $_FILES['file']['size'];
	$name_tmp = $_FILES['file']['tmp_name'];
	if (empty($name)) {
		echo json_encode(array("error"=>"您还未选择图片"));
		exit;
	}
	$type = strtolower(substr(strrchr($name, '.'), 1)); //获取文件类型

	if (!in_array($type, $typeArr)) {
		echo json_encode(array("error"=>"清上传jpg,png或gif类型的图片！"));
		exit;
	}
	if ($size > (500 * 1024)) {
		echo json_encode(array("error"=>"图片大小已超过500KB！"));
		exit;
	}

	$pic_name = time() . rand(10000, 99999) . "." . $type;//图片名称
	$pic_url = $path . $pic_name;//上传后图片路径+名称

	//远程服务器上传图片
	$post_data = array(
		"sid"=>"e4lu31aq2450q80in0vmcob2p4",
		"name"=>$image_name,
		"file"=>"@".$name_tmp

	);
	print_r($post_data);
	
	$sid=$post_data['sid'];
	$url  = "https://123.207.120.251:8099/etrade/doc/upload.action";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url );
	curl_setopt($ch, CURLOPT_POST, 1 );
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_error($ch);  //查看报错信息
	$res=json_decode(curl_exec($ch),true);
	$nid=$res['nid'];
	$urld= "https://123.207.120.251:8099/etrade/doc/upload.action";
	curl_close($ch);
	echo '123<br>';
	print_r($res);
	echo "<br>";
	echo $res['nid'];
	echo "<br>";
	echo '999<br>';
	exit;
//返回结果
	if ($res['responseCode']=='success') {
		echo '<img src="'.$urld.'?sid='.$sid.'&nid='.$nid.'">';
	}else{
		echo $res['reponse_text'].'上传出错了！';
	}
//	if (move_uploaded_file($name_tmp, $pic_url)) { //临时文件转移到目标文件夹
//		echo json_encode(array("error"=>"0","pic"=>$pic_url,"name"=>$pic_name));
//		echo '<img src="'.$pic_url.'">';
//	} else {
//		echo json_encode(array("error"=>"上传有误，清检查服务器配置！"));
//	}
}