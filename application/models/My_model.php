<?php

class My_model extends CI_Model {

	var $client_id = 0;

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->load->database('default');
		$this->load->helper('db');
		$this->client_id = $this->session->userdata('client_id');
	}
	
	
	
	protected function getUserRoles($uid)
	{
		$string = "SELECT r.*, e.name as entity_name FROM user_role r LEFT JOIN
				entity e
				ON 
				
					(r.entity_type='entity' AND r.entity_id = e.id )

				
		WHERE r.user_id = ? AND r.is_deleted = 0  AND e.is_deleted = 0";

		$query = $this->db->query($string, array($uid));
		
		if($query && $query->result()){
			return $query->result();
		}
		return null;
	}
	
	function updateWeight($obj)
	{
	
		$request = my_process_db_request($obj, $this->data, false);
	
	
		$id = $request['id'];
	
		$this->db->where('id', $id);
		if(isset($obj['action'])){
			if(strtolower($obj['action'] ) == 'add'){
				$this->db->set('weight', '`weight`+ 1', FALSE);
			}
			else{
				$this->db->set('weight', '`weight`- 1', FALSE);
			}
		}
		$this->db->update($this->main_table);
	
		return true;
	
	}

}
?>