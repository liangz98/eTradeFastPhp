<?php
class AccountController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$userM = new Seed_Model_User('system');

		$conditions = array();
		$is_actived=trim($this->_request->getParam('is_actived'));
		$is_admin=trim($this->_request->getParam('is_admin'));
		$user_name=trim($this->_request->getParam('user_name'));
		$user_email=trim($this->_request->getParam('user_email'));
		$idcard_no=trim($this->_request->getParam('idcard_no'));
		
	    $st = strtotime($this->_request->getParam('begin_time'));
		$et = strtotime($this->_request->getParam('end_time'));
		if($st>0 && $et>=$st){
            //$et = $et + 86400;
			$conditions["register_time >='".$st."'"]=null;
			$conditions["register_time <='".$et."'"]=null;
			$this->view->begin_time = $st;
			$this->view->end_time = $et;
		}

		$this->view->is_actived = $is_actived;
		$this->view->is_admin = $is_admin;
		$this->view->user_name = $user_name;
		$this->view->user_email = $user_email;
		$this->view->idcard_no = $idcard_no;

		//查询条件
		if($is_actived!='-1' && $is_actived!='')
			$conditions['is_actived']=$is_actived;
		if($is_admin!='-1' && $is_admin!='')
			$conditions['is_admin']=$this->_request->getParam('is_admin');
		if($user_name!='')
			$conditions["user_name like '%".$user_name."%'"]=null;
		if($user_email!='')
			$conditions['user_email']=$user_email;
		if($user_email!='')
			$conditions['user_email']=$user_email;
		if($idcard_no!='')
			$conditions["idcard_no like '%".$idcard_no."%'"]=null;

		$perpage=15;
    	$page=intval($this->_request->getParam('page'));
    	$total = $userM->fetchRowsCount($conditions);
    	$pageObj = new Seed_Page($this->_request,$total,$perpage);
    	$this->view->page = $pageObj->getPageArray();
	   	if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
    	if($page<1)$page=1;
		$users = $userM->fetchRows(array(($page-1)*$perpage,$perpage),$conditions);
		$this->view->users = $users;
	}

	function addAction()
	{
		$userM = new Seed_Model_User('system');
                $profileM = new Seed_Model_Profile('system');
		if ($this->_request->isPost()) {
			try{
				$f1=new Seed_Filter_Email();
				$f2=new Zend_Filter_Int();
				$f3=new Seed_Filter_Mobile();
				$f4 = new Zend_Filter( );
				$f4->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f5=new Seed_Filter_IdCard();
				$f6=new Seed_Filter_Date();

				$insertData = array();
				$insertData['user_name']=$f4->filter($this->_request->getPost('user_name'));
				$passwd = $this->_request->getPost('user_password');
				$insertData['user_password']=$insertData['user_pay_password']=md5($insertData['user_name'].md5($passwd));

				if(empty($insertData['user_name'])){
					Seed_Browser::tip_show('用户名不能为空！');
				}elseif(empty($passwd)){
					Seed_Browser::tip_show('密码不能为空！');
				}else{
					$check = $userM->fetchRow(array('user_name'=>$insertData['user_name']));
					if($check['user_id']>0){
						Seed_Browser::tip_show('用户名已经存在！');
					}
				}


				$insertData['user_email']=$f1->filter($this->_request->getPost('user_email'));
				$insertData['email_valid']=intval($this->_request->getPost('email_valid'));
				$insertData['user_mobile']=$f3->filter($this->_request->getPost('user_mobile'));
				$insertData['mobile_valid']=intval($this->_request->getPost('mobile_valid'));
				$insertData['is_admin']=intval($this->_request->getPost('is_admin'));
				$insertData['is_actived']=intval($this->_request->getPost('is_actived'));
				$insertData['nick_name']=$f4->filter($this->_request->getPost('nick_name'));
				$insertData['real_name']=$f4->filter($this->_request->getPost('real_name'));
				$insertData['idcard_no']=$f5->filter($this->_request->getPost('idcard_no'));
				$insertData['idcard_valid']=intval($this->_request->getPost('idcard_valid'));
				$insertData['idcard_cover1']=$f4->filter($this->_request->getPost('idcard_cover1'));
				$insertData['idcard_cover2']=$f4->filter($this->_request->getPost('idcard_cover2'));
				$insertData['user_gender']=intval($this->_request->getPost('user_gender'));
				$insertData['user_birthday']=$f6->filter($this->_request->getPost('user_birthday'));
				if(empty($insertData['user_birthday']))$insertData['user_birthday']=date('Y-m-d');

				$insertData['province_id'] = intval($this->_request->getPost('province_id'));
				$insertData['city_id'] = intval($this->_request->getPost('city_id'));
				$insertData['district_id'] = intval($this->_request->getPost('district_id'));

				$regionM = new Seed_Model_Region('system');
				$regions = $regionM->fetchRowsByIdsPairs('reg_id',array($insertData['province_id'],$insertData['city_id'],$insertData['district_id']));
				if(isset($regions[$insertData['province_id']]))$insertData['province_name']=$regions[$insertData['province_id']];
				if(isset($regions[$insertData['city_id']]))$insertData['city_name']=$regions[$insertData['city_id']];
				if(isset($regions[$insertData['district_id']]))$insertData['district_name']=$regions[$insertData['district_id']];

				$insertData['user_address'] = $f4->filter($this->_request->getPost('user_address'));
				$insertData['zip_code'] = $f2->filter($this->_request->getPost('zip_code'));

				$user_id = $userM->insertRow($insertData);
				if($user_id>0){
                                    $profileData=array();
					foreach ($_POST as $k=>$v){
						if (preg_match("/^attr_([\w]+)$/i",$k,$matches)&&!empty($v)){
							if(is_array($v))
								$profileData[$matches[1]]=implode("_SEEDATTR_",$v);
							else
								$profileData[$matches[1]]=$v;
						}
					}
					if(count($profileData)>0){
						$check = $profileM->fetchRow(array('user_id'=>$user_id));
						if($check['user_id']>0){
							$profileM->updateRow($profileData,array('user_id'=>$user_id));
						}else{
							$profileData['user_id']=$user_id;
							$profileM->insertRow($profileData);
						}
					}
					Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl."/account/update?user_id=".$user_id);
	    		}else{
	    			Seed_Browser::tip_show('添加失败！');
	    		}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
		$userAttrM = new Seed_Model_UserAttr('system');
		$attrs = $userAttrM->fetchRows(null,array(),'order_by asc');
		$this->view->attrs = $attrs;
	}

	function updateAction()
	{
		$userM = new Seed_Model_User('system');
                $profileM = new Seed_Model_Profile('system');
		if ($this->_request->isPost()) {
			try{
				$f1=new Seed_Filter_Email();
				$f2=new Zend_Filter_Int();
				$f3=new Seed_Filter_Mobile();
				$f4 = new Zend_Filter( );
				$f4->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f5=new Seed_Filter_IdCard();
				$f6=new Seed_Filter_Date();
				$user_id=$f2->filter($this->_request->getPost('user_id'));
				$userDetail = $userM->fetchRow(array('user_id'=>$user_id));
				if($userDetail['user_id']<1){
					Seed_Browser::tip_show("关键数据错误！");
				}

				$updateData = array();
				$updateData['user_email']=$f1->filter($this->_request->getPost('user_email'));
				$updateData['email_valid']=intval($this->_request->getPost('email_valid'));
				$updateData['user_mobile']=$f3->filter($this->_request->getPost('user_mobile'));
				$updateData['mobile_valid']=intval($this->_request->getPost('mobile_valid'));
				$updateData['is_admin']=intval($this->_request->getPost('is_admin'));
				$updateData['is_actived']=intval($this->_request->getPost('is_actived'));
				$updateData['is_supplier']=intval($this->_request->getPost('is_supplier'));
				$updateData['nick_name']=$f4->filter($this->_request->getPost('nick_name'));
				$updateData['real_name']=$f4->filter($this->_request->getPost('real_name'));
				$updateData['idcard_no']=$f5->filter($this->_request->getPost('idcard_no'));
				$updateData['idcard_valid']=intval($this->_request->getPost('idcard_valid'));
				$updateData['idcard_cover1']=$f4->filter($this->_request->getPost('idcard_cover1'));
				$updateData['idcard_cover2']=$f4->filter($this->_request->getPost('idcard_cover2'));
				$updateData['user_gender']=intval($this->_request->getPost('user_gender'));
				$updateData['user_birthday']=$f6->filter($this->_request->getPost('user_birthday'));
				$updateData['store_id'] = $f2->filter($this->_request->getPost('store_id'));
				$updateData['is_open']=intval($this->_request->getPost('is_open'));
				
				if(empty($updateData['user_birthday']))$updateData['user_birthday']=date('Y-m-d');

				$updateData['province_id'] = intval($this->_request->getPost('province_id'));
				$updateData['city_id'] = intval($this->_request->getPost('city_id'));
				$updateData['district_id'] = intval($this->_request->getPost('district_id'));

				$regionM = new Seed_Model_Region('system');
				$regions = $regionM->fetchRowsByIdsPairs('reg_id',array($updateData['province_id'],$updateData['city_id'],$updateData['district_id']));
				if(isset($regions[$updateData['province_id']]))$updateData['province_name']=$regions[$updateData['province_id']];
				if(isset($regions[$updateData['city_id']]))$updateData['city_name']=$regions[$updateData['city_id']];
				if(isset($regions[$updateData['district_id']]))$updateData['district_name']=$regions[$updateData['district_id']];

				$updateData['user_address'] = $f4->filter($this->_request->getPost('user_address'));
				$updateData['zip_code'] = $f2->filter($this->_request->getPost('zip_code'));

				if($userM->updateRow($updateData,array('user_id'=>$user_id))){
                                    $profileData=array();
					foreach ($_POST as $k=>$v){
						if (preg_match("/^attr_([\w]+)$/i",$k,$matches)&&!empty($v)){
							if(is_array($v))
								$profileData[$matches[1]]=implode("_SEEDATTR_",$v);
							else
								$profileData[$matches[1]]=$v;
						}
					}
					if(count($profileData)>0){
						$check = $profileM->fetchRow(array('user_id'=>$user_id));
						if($check['user_id']>0){
							$profileM->updateRow($profileData,array('user_id'=>$user_id));
						}else{
							$profileData['user_id']=$user_id;
							$profileM->insertRow($profileData);
						}
					}
					Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/account/update?user_id=".$user_id);
	    		}else{
	    			Seed_Browser::tip_show('修改失败！');
	    		}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
		
		$storeM = new Shop_Model_Store('shop');
		$stores = $storeM->fetchRows(null,array(),'store_id asc');
        $this->view->stores = $stores;

		$user_id=intval($this->_request->getParam('user_id'));
		$this->view->user = $userM->fetchRow(array('user_id'=>$user_id));
		if($this->view->user['user_id']<1)Seed_Browser::error('没有找到相关数据！');
                $this->view->profile = $profileM->fetchRow(array('user_id'=>$user_id));
		$userAttrM = new Seed_Model_UserAttr('system');
		$attrs = $userAttrM->fetchRows(null,array(),'order_by asc');
		$this->view->attrs = $attrs;
	}

	function passwdAction()
	{
		$userM = new Seed_Model_User('system');
		if ($this->_request->isPost()) {
			try{
				$f1=new Zend_Filter_Int();
				$user_id=$f1->filter($this->_request->getPost('user_id'));
				$userDetail = $userM->fetchRow(array('user_id'=>$user_id));
				if($userDetail['user_id']<1){
					Seed_Browser::tip_show("关键数据错误！");
				}
				$user_password = $this->_request->getPost('user_password');
				if(empty($user_password)){
					Seed_Browser::tip_show('登录密码不能为空！');
				}

				$updateData = array();
				$updateData['user_password']=md5($userDetail['user_name'].md5($user_password));

				if($userM->updateRow($updateData,array('user_id'=>$user_id))){
					Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/account/index");
	    		}else{
	    			Seed_Browser::tip_show('修改失败！');
	    		}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}

		$user_id=intval($this->_request->getParam('user_id'));
		$this->view->user = $userM->fetchRow(array('user_id'=>$user_id));
		if($this->view->user['user_id']<1)Seed_Browser::error('没有找到相关数据！');
	}

	function paypasswdAction()
	{
		$userM = new Seed_Model_User('system');
		if ($this->_request->isPost()) {
			try{
				$f1=new Zend_Filter_Int();
				$user_id=$f1->filter($this->_request->getPost('user_id'));
				$userDetail = $userM->fetchRow(array('user_id'=>$user_id));
				if($userDetail['user_id']<1){
					Seed_Browser::tip_show("关键数据错误！");
				}
				$user_pay_password = $this->_request->getPost('user_pay_password');
				if(empty($user_password)){
					Seed_Browser::tip_show('支付密码不能为空！');
				}

				$updateData = array();
				$updateData['user_pay_password']=md5($userDetail['user_name'].md5($user_pay_password));

				if($userM->updateRow($updateData,array('user_id'=>$user_id))){
					Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/account/index");
	    		}else{
	    			Seed_Browser::tip_show('修改失败！');
	    		}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}

		$user_id=intval($this->_request->getParam('user_id'));
		$this->view->user = $userM->fetchRow(array('user_id'=>$user_id));
		if($this->view->user['user_id']<1)Seed_Browser::error('没有找到相关数据！');
	}

	function authAction()
	{
		if ($this->_request->isPost()) {
			try{
				$userRoleM = new Seed_Model_UserRole('system');
				$action = $this->_request->getPost('action');
				if($action=="add"){
					$insertData = array();
					$f1=new Zend_Filter_Int();
					$f2=new Seed_Filter_Alnum();
					$insertData['user_id']=$f1->filter($this->_request->getPost('user_id'));
					if($insertData['user_id']<1){
						Seed_Browser::tip_show("用户数据错误！");
					}
					$insertData['mod_name']=$f2->filter($this->_request->getPost('mod_name'));
					if(empty($insertData['mod_name'])){
						Seed_Browser::tip_show("模块不能为空！");
					}
					$insertData['role_name']=$f2->filter($this->_request->getPost('role_name'));
					if(empty($insertData['role_name']) || $insertData['role_name']=='-1'){
						Seed_Browser::tip_show("角色不能为空！");
					}

					$userRoleDetail = $userRoleM->fetchRow(array('user_id'=>$insertData['user_id'],'mod_name'=>$insertData['mod_name'],'role_name'=>$insertData['role_name']));
					if($userRoleDetail['user_id']<1){
						$userRoleM->insertRow($insertData);
					}

					Seed_Browser::tip_show("添加角色成功！",$this->view->seed_BaseUrl."/account/auth/user_id/".$insertData['user_id']);
				}elseif ($action=="delete"){
					$user_role = $this->_request->getPost('user_role');
					$user_id = $this->_request->getPost('user_id');
					if(count($user_role)==0){
						Seed_Browser::tip_show("选择为空！");
					}
					foreach ($user_role as $row){
						$tmp = explode("^",$row);
						$userRoleM->deleteRow(array('user_id'=>$tmp[0],'mod_name'=>$tmp[1],'role_name'=>$tmp[2]));
					}
					Seed_Browser::tip_show("删除角色成功！",$this->view->seed_BaseUrl."/account/auth/user_id/".$user_id);
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}

		$user_id=intval($this->_request->getParam('user_id'));
		$userM = new Seed_Model_User('system');
		$this->view->user = $userM->fetchRow(array('user_id'=>$user_id));
		if($this->view->user['user_id']<1)Seed_Browser::error('没有找到相关数据！');

		$moduleM = new Seed_Model_Module('system');
		$roleM = new Seed_Model_Role('system');
		$modules = $moduleM->fetchRows(null,array('mod_name'=>'center','mod_type'=>'admin'));
		$roles = $roleM->fetchRows();

		$userRoleM = new Seed_Model_UserRole('system');
		$userroles = $userRoleM->fetchRows(array(0,0),array('user_id'=>$user_id),array('mod_name ASC'));
		foreach ($userroles as $k=>$row){
			foreach ($modules as $v){
				if($v['mod_name']."_".$v['mod_type']==$row['mod_name'])$userroles[$k]['mod_desc']=$v['mod_desc'];
			}
			foreach ($roles as $v){
				if($v['role_name']==$row['role_name'])$userroles[$k]['role_desc']=$v['role_desc'];
			}
		}
		$this->view->roles = $userroles;
		$this->view->modules = $modules;
	}

	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Int();
				$user_ids = $this->_request->getPost('user_id');
				if(count($user_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				
				$userM=new Seed_Model_User('system');
				$userRoleM=new Seed_Model_UserRole('system');
				foreach ($user_ids as $user_id){
					$user_id = $f3->filter($user_id);
					if($user_id>0){
						if($userM->deleteRow(array('user_id'=>$user_id))){
							$userRoleM->deleteRow(array('user_id'=>$user_id));
						}
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/account/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}

		$user_ids = $this->_request->getParam('user_ids');
	    if(empty($user_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$user_ids = explode(',',$user_ids);
		$f3 = new Zend_Filter_Int();
		$myusers_arr=array();
		foreach ($user_ids as $user_id)
		{
			$user_id = $f3->filter($user_id);
			if($user_id>0)
					$myusers_arr[]=$user_id;
		}
		if(count($myusers_arr)>0){
			$userM = new Seed_Model_User('system');
			$users = $userM->fetchRowsByIds('user_id',$myusers_arr);
	   		$this->view->users = $users;
		}
	}

	function searchAction()
	{
		$keyword = trim($this->_request->getParam('keyword'));
		$userM = new Seed_Model_User('system');
		$conditions = array();

		$orconditions = array();
		$orconditions["user_name like '%".$keyword."%'"]=null;
		$orconditions["user_mobile like '%".$keyword."%'"]=null;


		$users = $userM->searchRows(null,$conditions,$orconditions,null,array('user_id','user_name','nick_name','real_name'));
		$myusers=array();
		foreach ($users as $user) {
			$myusers[]=array('user_id'=>$user['user_id'],'user_show'=>$user['real_name']." [ ".$user['user_name']." ]");
		}
		$json = Zend_Json::encode($myusers);
		echo $json;
		exit;
	}


	function excelAction(){
		try
		{
			if ($this->_request->isPost()) 
			{
				$myfileM = new Seed_File();
				$myfileM->setPostfix('xlsx');
				
				if(empty($_FILES['file']['name']))
				{
					Seed_Browser::error('上传文件不能为空！');
				}
				
				$file=strrchr($_FILES['file']['name'],'.');
				
				if($file!='.xlsx')
				{
					Seed_Browser::error('上传的excel文件后缀必须为xlsx！');
				}

				$rs = $myfileM->upload($_FILES['file'],SEED_TEMP_ROOT);//上传文件
				
				if($rs)
				{
					$filePath = SEED_TEMP_ROOT."/".$rs;
					set_include_path(SEED_LIB_ROOT.'/Plugin/'
			    	. PATH_SEPARATOR . get_include_path());
			    	/** PHPExcel */
					require_once SEED_LIB_ROOT.'/Plugin/PHPExcel.php';
					
					/** PHPExcel_IOFactory */
					require_once SEED_LIB_ROOT.'/Plugin/PHPExcel/IOFactory.php';
					
					// Create new PHPExcel object
					$PHPExcel = new PHPExcel();
					
					$PHPReader = new PHPExcel_Reader_Excel2007();                  
					if(!$PHPReader->canRead($filePath)){
					    $PHPReader = new PHPExcel_Reader_Excel5(); 
					    if(!$PHPReader->canRead($filePath)){
					        Seed_Browser::tip_show("ERROR:".$filePath);
					    }
					}
					$PHPExcel = $PHPReader->load($filePath);
					$currentSheet = $PHPExcel->getSheet(0);
					/**取得一共有多少列*/
					$allColumn = $currentSheet->getHighestColumn();
					/**取得一共有多少行*/
					$allRow = $currentSheet->getHighestRow();
					$myresult="";	

					$f1 = new Zend_Filter( );
					$f1->addFilter(new Zend_Filter_StripTags())
						->addFilter(new Seed_Filter_EscapeQuotes());
					$mobileOutboxM = new Seed_Model_MobileOutbox('system');
					$mycount=0;
					for($currentRow = 2;$currentRow<=$allRow;$currentRow++)
					{
						$myerror=0;						
						$userData = array();				
			            $userData['user_name']= $f1->filter(trim($currentSheet->getCell('A'.$currentRow)->getValue()));
			            $userData['real_name']= $f1->filter(trim($currentSheet->getCell('B'.$currentRow)->getValue()));
			            $userData['user_telephone']= $f1->filter(trim($currentSheet->getCell('C'.$currentRow)->getValue()));
			            $userData['idcard_no']= $f1->filter(trim($currentSheet->getCell('D'.$currentRow)->getValue()));
						$gender= $f1->filter(trim($currentSheet->getCell('E'.$currentRow)->getValue()));	
						$userData['user_birthday']= $f1->filter(trim($currentSheet->getCell('F'.$currentRow)->getValue()));
						$userData['province_name']= $f1->filter(trim($currentSheet->getCell('G'.$currentRow)->getValue()));	 
						$userData['city_name']= $f1->filter(trim($currentSheet->getCell('H'.$currentRow)->getValue()));
						$userData['district_name']= $f1->filter(trim($currentSheet->getCell('I'.$currentRow)->getValue()));
						$userData['town_name']= $f1->filter(trim($currentSheet->getCell('J'.$currentRow)->getValue()));
						$userData['user_address']= $f1->filter(trim($currentSheet->getCell('K'.$currentRow)->getValue()));
						$userData['zip_code']= $f1->filter(trim($currentSheet->getCell('L'.$currentRow)->getValue()));	
						$userData['user_email']= $f1->filter(trim($currentSheet->getCell('M'.$currentRow)->getValue()));		
						$userData['user_bank_account']= $f1->filter(trim($currentSheet->getCell('N'.$currentRow)->getValue()));			
						$userData['user_mobile']= $userData['user_name'];
						$userData['register_time']= time();
						$userData['vip_id']='1';
						$userData['service_site'] = $this->_request->getPost('service_site');
						if($gender='男'){
							$userData['user_gender']='1';
						}else{
							$userData['user_gender']='2';
						}
						
						if(empty($userData['service_site'])){
							Seed_Browser::error('请选择门店,谢谢！');
						}

						$userM = new Seed_Model_User('system');
						$check = $userM->fetchRow(array('user_name'=>$userData['user_name']));
						
						if($check['user_id']>0){
							$myerror=1;
							$myresult.="<p style=\"color:#ff0000;\">".$check['user_name']."手机号码已被注册,请修改其他号码！</p>";
							continue;	
						}
						$reg_user_passwd=mt_rand(100000,999999);
						$userData['user_password']=md5($userData['user_name'].md5($reg_user_passwd));
						
						//用户短信
						$f = new Seed_Filter_Mobile ();
						$userM = new Seed_Model_User('system');
						$mobileTempM = new Seed_Model_MobileTemplate ( 'system' );
						$mobileOutboxM = new Seed_Model_MobileOutbox ( 'system' );			
						$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'user_create', 'is_actived' => '1' ) );
						if ($mobileTemp ['temp_id'] > 0) {
								$content = $mobileTemp ['temp_content'];
								$patterns = array ();
								$replacements = array ();
								$patterns [] = '/{user_name}/';
								$patterns [] = '/{user_pwd}/';
								$patterns [] = '/{time}/';
								$replacements [] = $userData['user_name'];
								$replacements [] = $reg_user_passwd;
								$replacements [] = date ( 'Y-m-d H:i:s' );
								$content = preg_replace ( $patterns, $replacements, $content );					
								$mobileOutboxM->mobileSend ($userData['user_name'], $content, time ());
								$user_id=$userM->insertRow($userData);
								$mycount+=1;
						}

					}
					$this->view->mycount = $mycount;
					$this->view->myresult = $myresult;		
				}				
			}
		}
		catch (Exception $e) 
		{
			Seed_Browser::tip_show($e->getMessage());
		}
		$storeM = new Shop_Model_Store('shop');
        $stores= $storeM->fetchAllStoreList();
        $this->view->stores = $stores;
	}

}
