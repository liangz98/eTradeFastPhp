<?php
class CrondController extends Seed_Controller_Action4Admin {
	function indexAction() {
		$crondM = new Seed_Model_Crond ( 'system' );
		
		$perpage = 10;
		$page = intval ( $this->_request->getParam ( 'page' ) );
		$total = $crondM->fetchRowsCount ( null );
		$pageObj = new Seed_Page ( $this->_request, $total, $perpage );
		$this->view->page = $pageObj->getPageArray ();
		if ($page > $this->view->page ['totalpage']) {
			$page = $this->view->page ['totalpage'];
		}
		if ($page < 1) { 
			$page = 1;
		}
		$cronds = $crondM->fetchRows ( array (($page - 1) * $perpage, $perpage ), null, 'order_by ASC' );
		$this->view->cronds = $cronds;
	}
	
	function addAction() {
		//AJAX POST
		if ($this->_request->isPost ()) {
			try {
				$insertData = array ();
				$f1 = new Zend_Filter ();
				$f1->addFilter ( new Zend_Filter_StripTags () )->addFilter ( new Seed_Filter_EscapeQuotes () );
				$insertData ['crond_type'] = $f1->filter ( $this->_request->getPost ( 'crond_type' ) );
				$insertData ['crond_name'] = $f1->filter ( $this->_request->getPost ( 'crond_name' ) );
				$insertData ['crond_title'] = $f1->filter ( $this->_request->getPost ( 'crond_title' ) );
				$insertData ['crond_desc'] = $f1->filter ( $this->_request->getPost ( 'crond_desc' ) );
				$insertData ['crond_content'] = $f1->filter ( $this->_request->getPost ( 'crond_content' ) );
				$insertData ['crond_result'] = $f1->filter ( $this->_request->getPost ( 'crond_result' ) );
				$insertData ['crond_url'] = $f1->filter ( $this->_request->getPost ( 'crond_url' ) );
				
				$f3 = new Zend_Filter_Int ();
				$insertData ['order_by'] = $f3->filter ( $this->_request->getPost ( 'order_by' ) );
				$insertData ['limit_num'] = intval ( $this->_request->getPost ( 'limit_num' ) );
				$insertData ['is_actived'] = intval ( $this->_request->getPost ( 'is_actived' ) );
				$insertData ['crond_status'] = intval ( $this->_request->getPost ( 'crond_status' ) );
				$insertData ['add_time'] = time ();
				
				if (empty ( $insertData ['crond_title'] )) {
					Seed_Browser::tip_show ( '请任务标题！' );
				} else {
					$crondM = new Seed_Model_Crond ( 'system' );
					if ($crondM->insertRow ( $insertData )) {
						Seed_Browser::tip_show ( '添加成功！', $this->view->seed_BaseUrl . '/crond/index' );
					} else {
						Seed_Browser::tip_show ( '添加失败！', $this->view->seed_BaseUrl . '/crond/index' );
					}
				}
				exit ();
			} catch ( Exception $e ) {
				Seed_Browser::tip_show ( $e->getMessage () );
			}
		}
	}
	
	function updateAction() {
		//AJAX POST
		$crond_id = $this->_request->getParam ( 'crond_id' );
		$crondM = new Seed_Model_Crond ( 'system' );
		$this->view->crond = $crondM->fetchRow ( array ('crond_id' => $crond_id ) );
		if ($this->_request->isPost ()) {
			try {
				$updateData = array ();
				$f1 = new Zend_Filter ();
				$f1->addFilter ( new Zend_Filter_StripTags () )->addFilter ( new Seed_Filter_EscapeQuotes () );
				$updateData ['crond_type'] = $f1->filter ( $this->_request->getPost ( 'crond_type' ) );
				$updateData ['crond_name'] = $f1->filter ( $this->_request->getPost ( 'crond_name' ) );
				$updateData ['crond_title'] = $f1->filter ( $this->_request->getPost ( 'crond_title' ) );
				$updateData ['crond_desc'] = $f1->filter ( $this->_request->getPost ( 'crond_desc' ) );
				$updateData ['crond_content'] = $f1->filter ( $this->_request->getPost ( 'crond_content' ) );
				$updateData ['crond_result'] = $f1->filter ( $this->_request->getPost ( 'crond_result' ) );
				$updateData ['crond_url'] = $f1->filter ( $this->_request->getPost ( 'crond_url' ) );
				
				$f3 = new Zend_Filter_Int ();
				$updateData ['order_by'] = $f3->filter ( $this->_request->getPost ( 'order_by' ) );
				$updateData ['limit_num'] = intval ( $this->_request->getPost ( 'limit_num' ) );
				$updateData ['is_actived'] = intval ( $this->_request->getPost ( 'is_actived' ) );
				$updateData ['crond_status'] = intval ( $this->_request->getPost ( 'crond_status' ) );
				
				if (empty ( $updateData ['crond_title'] )) {
					Seed_Browser::tip_show ( '请任务标题！' );
				} else {
					$crondM = new Seed_Model_Crond ( 'system' );
					if ($crondM->updateRow ( $updateData, array ('crond_id' => $crond_id ) )) {
						Seed_Browser::tip_show ( '修改成功！', $this->view->seed_BaseUrl . '/crond/index' );
					} else {
						Seed_Browser::tip_show ( '修改失败！', $this->view->seed_BaseUrl . '/crond/index' );
					}
				}
				exit ();
			} catch ( Exception $e ) {
				Seed_Browser::tip_show ( $e->getMessage () );
			}
		}
	
	}
	
	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost ()) {
			try {
				$f3 = new Zend_Filter_Int ();
				$crond_ids = $this->_request->getPost ( 'crond_id' );
				if (count ( $crond_ids ) == 0) {
					Seed_Browser::tip_show ( '找不到相关的数据!' );
				}
				
				$crondM = new Seed_Model_Crond ( 'system' );
				$crondParamM = new Seed_Model_CrondParam ( 'system' );
				foreach ( $crond_ids as $crond_id ) {
					$crond_id = intval ( $crond_id );
					if ($crond_id > 0) {
						$crond = $crondM->fetchRow ( array ('crond_id' => $crond_id ) );
						if ($crondM->deleteRow ( array ('crond_id' => $crond_id ) )) {
							$crondParamM->deleteRow ( array ('crond_name' => $crond ['crond_name'] ) );
						}
					}
				}
				Seed_Browser::tip_show ( '删除成功！', $this->view->seed_BaseUrl . '/crond/index' );
				exit ();
			} catch ( Exception $e ) {
				Seed_Browser::tip_show ( $e->getMessage () );
			}
		}
		
		$crond_ids = $this->_request->getParam ( 'crond_ids' );
		if (empty ( $crond_ids )) {
			Seed_Browser::error ( '找不到相关的数据!' );
		}
		$crond_ids = explode ( ',', $crond_ids );
		$f3 = new Zend_Filter_Int ();
		$mycrond_arr = array ();
		foreach ( $crond_ids as $crond_id ) {
			$crond_id = $f3->filter ( $crond_id );
			if ($crond_id > 0)
				$mycrond_arr [] = $crond_id;
		}
		if (count ( $mycrond_arr ) > 0) {
			$crondM = new Seed_Model_Crond ( 'system' );
			$cronds = $crondM->fetchRowsByIds ( 'crond_id', $mycrond_arr );
			$this->view->cronds = $cronds;
		
		}
	}
	
	function orderAction() {
		//AJAX POST
		if ($this->_request->isPost ()) {
			try {
				
				$f3 = new Zend_Filter_Alnum ();
				$crond_ids = $this->_request->getPost ( 'crond_ids' );
				$order_bys = $this->_request->getPost ( 'order_bys' );
				
				$updateData = array ();
				$crondM = new Seed_Model_Crond ( 'system' );
				if (is_array ( $crond_ids )) {
					foreach ( $crond_ids as $k => $crond_id ) {
						$crond_id = $f3->filter ( $crond_id );
						$updateData ['order_by'] = $f3->filter ( $order_bys [$k] );
						$crondM->updateRow ( $updateData, array ('crond_id' => $crond_id ) );
					}
				}
				Seed_Browser::tip_show ( '排序成功！', $this->view->seed_BaseUrl . "/crond/index" );
				exit ();
			} catch ( Exception $e ) {
				Seed_Browser::tip_show ( $e->getMessage () );
			}
		}
	}
}