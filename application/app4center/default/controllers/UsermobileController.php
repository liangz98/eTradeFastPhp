<?php
class UsermobileController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$userM = new Seed_Model_User('system');
		$userWechatUserM = new Wechat_Model_User('wechat');
		$userWechatM = new Wechat_Model_Wechat('wechat');
		
		$conditions = array();
		$is_actived=trim($this->_request->getParam('is_actived'));
		$is_admin=trim($this->_request->getParam('is_admin'));
		$user_name=trim($this->_request->getParam('user_name'));
		$user_email=trim($this->_request->getParam('user_email'));

		$this->view->is_actived = $is_actived;
		$this->view->is_admin = $is_admin;
		$this->view->user_name = $user_name;
		$this->view->user_email = $user_email;

		//查询条件
		if($is_actived!='-1' && $is_actived!='')
			$conditions['is_actived']=$is_actived;
		if($is_admin!='-1' && $is_admin!='')
			$conditions['is_admin']=$this->_request->getParam('is_admin');
		if($user_name!='')
			$conditions["user_name like '%".$user_name."%'"]=null;
		if($user_email!='')
			$conditions['user_email']=$user_email;

		$perpage=15;
    	$page=intval($this->_request->getParam('page'));
    	$total = $userM->fetchRowsCount($conditions);
    	$pageObj = new Seed_Page($this->_request,$total,$perpage);
    	$this->view->page = $pageObj->getPageArray();
	   	if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
    	if($page<1)$page=1;
		$users = $userM->fetchRows(array(($page-1)*$perpage,$perpage),$conditions);
		foreach ($users as $k=>$user){
		   $userWechat = $userWechatUserM->fetchRow(array('user_id'=>$user['user_id']));
		   if($userWechat['wu_id']>0){
		      $users[$k]['wc_nick_name'] = $userWechat['nick_name'];
		      $users[$k]['wc_name'] = $userWechatM->fetchOne(array('id'=>$userWechat['wc_id']),'wc_name');
		   }
		  
		}
		$this->view->users = $users;
	}
        
    public function exportAction(){
       	//设置不超时
		set_time_limit(0);
		ini_set('memory_limit', '-1');   
		
        $userM = new Seed_Model_User('system');
		$userWechatUserM = new Wechat_Model_User('wechat');
		$userWechatM = new Wechat_Model_Wechat('wechat');
		
		$conditions = array();
		$users = $userM->fetchRows(array(($page-1)*$perpage,$perpage),$conditions);
		foreach ($users as $k=>$user){
		   $userWechat = $userWechatUserM->fetchRow(array('user_id'=>$user['user_id']));
		   if($userWechat['wu_id']>0){
		      $users[$k]['wc_nick_name'] = $userWechat['nick_name'];
		      $users[$k]['wc_name'] = $userWechatM->fetchOne(array('id'=>$userWechat['wc_id']),'wc_name');
		   }
		  
		}
		$rows = $this->view->users = $users;
        
       /* for ($i = A; $i <= Z; $i++) {
              $mystr .= "$i ";//a b c d e f g h i j k l m n o p q r s t u v w x y z aa ab ac ad ae af ag ah ai aj ak al am an ao ap aq ar as at au av aw ax ay az ba bb bc bd be bf bg bh bi bj bk bl bm bn bo bp bq br bs bt bu bv bw bx by bz ...  yz
        }
        $myarr = explode(" ", $mystr);//标头数组
        $arr = array();
        $arr1 = array();
        for ($i1 = 6; $i1 < 6 + $attrs_cnt; $i1++) {//从G开始计算，总共有多少个用户属性的标头
        	$arr[$myarr[$i1] . "1"] = $attrs[$i1 - 6]['attr_name'];
        	$arr1[$myarr[$i1]] = $attrs[$i1 - 6]['field_name'];
        }
*/
		set_include_path(SEED_LIB_ROOT.'/Plugin/'
    	. PATH_SEPARATOR . get_include_path());
    	
    	/** PHPExcel */
		require_once SEED_LIB_ROOT.'/Plugin/PHPExcel.php';
		
		/** PHPExcel_IOFactory */
		require_once SEED_LIB_ROOT.'/Plugin/PHPExcel/IOFactory.php';
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		
		// Set properties
		$objPHPExcel->getProperties()->setCreator("Guangzhou Seed Studio")
									 ->setLastModifiedBy("Guangzhou Seed Studio")
									 ->setTitle("Guangzhou Seed Studio")
									 ->setSubject("Guangzhou Seed Studio")
									 ->setDescription("Guangzhou Seed Studio")
									 ->setKeywords("Guangzhou Seed Studio")
									 ->setCategory("Guangzhou Seed Studio");
		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A1', "昵称")
			            ->setCellValue('B1', "微信昵称")
			            ->setCellValue('C1', "所属微信号")
			            ->setCellValue('D1', "用户手机");
                    foreach ($arr as $key => $value) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue($key, trim($value));
                    }
 //设置列宽                   
     $objPHPExcel->getActiveSheet(0)->getColumnDimension('A')->setWidth(16);
                    $objPHPExcel->getActiveSheet(0)->getColumnDimension('B')->setWidth(20);
                    $objPHPExcel->getActiveSheet(0)->getColumnDimension('C')->setWidth(20);
                    $objPHPExcel->getActiveSheet(0)->getColumnDimension('D')->setWidth(20);
                    $objPHPExcel->getActiveSheet(0)->getColumnDimension('E')->setWidth(20);
                    $objPHPExcel->getActiveSheet(0)->getColumnDimension('F')->setWidth(20);
                    foreach ($arr1 as $key2 => $value2) {
                        $objPHPExcel->getActiveSheet(0)->getColumnDimension($key2)->setWidth(20);
                    }
                    
		$row=2;
		$row_count = 0 ;
		foreach ($rows as $r => $v) {
			$row_count++;
			$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A'.$row, $v['nick_name']." ")
			            ->setCellValue('B'.$row, $v['wc_nick_name']." ")
			            ->setCellValue('C'.$row, $v['wc_name']." ")
			            ->setCellValue('D'.$row, $v['user_mobile']." ");
		             /* foreach ($arr1 as $key1 => $value1) {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue($key1 . $row, $v[$value1] . " ");
                        }*/        
			$row++;
		}
		$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A'.$row, "合计")
			            ->setCellValue('B'.$row, $row_count." ");
		$row++;
			            
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('用户信息导出');
		
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="user_mobile.xls"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output'); 
		exit;
    }

    
}
