<?php
require(APPPATH.'/models/My_model.php');
class user_model extends My_Model {

	var $main_table   = 'user';
	var $content = '';
	var $data    = array(
			'id' => array(
			'default'=> 0,
			//'required'=>true,
			'type'=>'int'		
			),
			'name' => array(
			'required'=>true,
			'type'=>'string'		
			),
			'phone' => array(
					'required'=>true,
					'type'=>'string'
			),
			'password'=> array(
					'type'=>'string'
			),
			'clientid'=> array(
					'type'=>'int'
			),
			'birthday'=> array(
					'type'=>'string'
			),
			'is_deleted'=> array(
					'type'=>'int',
					'default' => 0
			)
	);

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
	function getList($param = null)
	{
		$query = $this->db->get_where('product', array('is_deleted'=>0));
		$resp = array();
		foreach ($query->result() as $row)
		{
			$resp[] = $row;
		}
		
		return $resp;
	}
	
  
	function login($username, $password)
	{
		$client_id = $this->client_id;
		$string = "SELECT * FROM user u 
		WHERE u.password= ? AND u.is_deleted= 0 AND (u.phone = ? OR u.email=?) AND clientid = ? LIMIT 1";
		//$query = $this->db->get_where('user', array('phone' => $username,'password' => $password));
		/* $query = $this->db->select("*")
		->from("user")
		->where($where); */
		$query = $this->db->query($string, array($password,$username,$username, $client_id));
		
		if($query && $query->result()){
			foreach ($query->result() as $row)
			{
				$row->roles = array();
				if($roles = $this->getUserRoles($row->id))
				{
					$row->roles = $roles;
				}
				return $row;
			}
		}
		return false;
	}
	
	function register($obj)
	{
		$client_id = $this->client_id;
		$request = my_process_db_request($obj, $this->data, false);
		$request['id'] = null;
		$this->db->insert('user', $request);
		$uid =  $this->db->insert_id();
		
		return $uid;
	
		if($uid){
			foreach ($query->result() as $row)
			{
				$row->roles = array();
				if($roles = $this->getUserRoles($row->id))
				{
					$row->roles = $roles;
				}
				return $row;
			}
		}
		return false;
	}
	
	function getDetail($id)
	{
		$query = $this->db->get_where('entity', array('id' => $id,'is_deleted'=>0));
		foreach ($query->result() as $row)
		{
			return $row;
		}
	}

	function updateDetail($obj)
	{
		$request = my_process_db_request($obj, $this->data);
		return $request;
		

		$this->db->update('entity', $request, array('id' => $_POST['id']));
	}
	
	function createDetail($obj)
	{

		$request = my_process_db_request($obj, $this->data, false);

		$request['id'] = null;
		$this->db->insert('product', $request);
		return $this->db->insert_id();
		//return $obj;
	}
	
	function deleteDetail($id)
	{
		return $id;
		$this->title   = $_POST['title'];
		$this->content = $_POST['content'];
		$this->date    = time();
	
		$this->db->update('entries', $this, array('id' => $_POST['id']));
	}
	
}
?>