<?php
require (APPPATH . '/models/My_model.php');
class airfreight_model extends My_Model {
	var $main_table = 'airfreight';
	var $content = '';
	var $data = array (
			'id' => array (
					'default' => 0,
					
					// 'required'=>true,
					'type' => 'int' 
			),
			'name' => array (
					'required' => true,
					'type' => 'string' 
			),
			'status' => array (
					'type' => 'int',
					'default' => 1 
			) 
	);
	function __construct() {
		// Call the Model constructor
		parent::__construct ();
	}
	function getList($param = null) {
		$user_id = isset ( $param ['user_id'] ) ? $param ['user_id'] : 0;
		$str = "SELECT main.* FROM " . $this->main_table . " main WHERE status = 1 ORDER BY id DESC";
		$query = $this->db->query ( $str);
		$resp = array ();
		foreach ( $query->result () as $row ) {
			$resp [] = $row;
		}
		
		return $resp;
	}
	function getDetail($id) {
		$rst = array();
		$is_found = false;
		$query = $this->db->get_where($this->main_table, array('id' => $id,'status'=>1));
		foreach ($query->result() as $row)
		{	
			$rst['name'] = $row->name;
			$rst['id'] = $id;
			$is_found = true;
			break;
		}
		if(!$is_found)
		{
			return null;
		}
		$rst['sites'] = array();
		
		$str = "SELECT q.* FROM $this->main_table main
		 LEFT JOIN airfreight_site q
		 ON q.airfreight_id = main.id AND q.status = 1 
		
		 WHERE main.status = 1  AND main.id = ?"; 
		
		$query = $this->db->query($str, array($id));
		
		foreach ($query->result() as $row)
		{
			$sid = $row->id;
			$files = array();
			$str = "SELECT q.* FROM airfreight_site_file q
			
			WHERE q.status = 1  AND q.airfreight_site_id = ?";
			
			$query = $this->db->query($str, array($sid));
			
			foreach ($query->result() as $file)
			{
				
				$files[] = $file->name;
			}
			$row->files = $files;
			$rst['sites'][] = $row;
		}
		
		
		return $rst;
	}
	function updateDetail($obj) {

		$request = my_process_db_request($obj, $this->data, false);

		
		$id = $request['id'];
		$remove_request = array('status'=>2);
		$this->db->update('airfreight_site', $remove_request, array('airfreight_id' => $id));

		if (isset ( $obj ['sites'] )) {
			foreach ( $obj ['sites'] as $site ) {
		
				$site_req = array (
						'name' => $site ['name'],
						'airfreight_id' => $id,
						'status' => 1
				)
				;
				$this->db->insert ( 'airfreight_site', $site_req );
				$sid = $this->db->insert_id ();
				if (isset ( $site ['files'] )) {
					$file_arr = array ();
					$files =  explode(',', $site ['files']);
						
					foreach ( $files as $file ) {
						$file_req = array (
								'name' => $file,
								'airfreight_site_id' => $sid,
								'status' => 1
						)
						;
						$file_arr [] = $file_req;
					}
					$this->db->insert_batch ( 'airfreight_site_file', $file_arr );
				}
			}
		}

		

		$this->db->update($this->main_table, $request, array('id' => $id));
		return true;
	}
	function createDetail($obj) {
		
		// parse_str($obj,$fields);
		$request = my_process_db_request ( $obj, $this->data, false );
		
		$request ['id'] = null;
		$this->db->insert ( $this->main_table, $request );
		$id = $this->db->insert_id ();
		
		// return $obj;
		if (isset ( $obj ['sites'] )) {
			foreach ( $obj ['sites'] as $site ) {
				
				$site_req = array (
						'name' => $site ['name'],
						'airfreight_id' => $id,
						'status' => 1 
				)
				;
				$this->db->insert ( 'airfreight_site', $site_req );
				$sid = $this->db->insert_id ();
				if (isset ( $site ['files'] )) {
					$file_arr = array ();
					$files =  explode(',', $site ['files']);
					
					foreach ( $files as $file ) {	
						$file_req = array (
								'name' => $file,
								'airfreight_site_id' => $sid,
								'status' => 1 
						)
						;
						$file_arr [] = $file_req;
					}
					$this->db->insert_batch ( 'airfreight_site_file', $file_arr );
				}
			}
		}
		
		return $id;
	}
	function deleteDetail($id) {
		return $id;
		$this->title = $_POST ['title'];
		$this->content = $_POST ['content'];
		$this->date = time ();
		
		$this->db->update ( 'entries', $this, array (
				'id' => $_POST ['id'] 
		) );
	}
}
?>