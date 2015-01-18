<?php
require(APPPATH.'/models/My_model.php');
class recommendation_model extends My_Model {

	var $main_table   = 'entity';
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
			
			'description'=> array(
					'type'=>'string'
			),
			'category_id'=> array(
					'type'=>'int'
			),
			'img'=> array(
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
		$user_id = isset($param['user_id']) ? $param['user_id'] : 0;
		$str = "SELECT m.* FROM " . $this->main_table . " m LEFT JOIN user_role r
				ON m.id = r.entity_id AND r.entity_type = 'entity' AND r.is_deleted = 0 AND
				m.is_deleted = 0
				WHERE 1=1 ";
		if($user_id)
		{
			$str .= "AND r.user_id = ?";
		}
		$query = $this->db->query($str, array($user_id));
		$resp = array();
		foreach ($query->result() as $row)
		{
			$resp[] = $row;
		}
		
		return $resp;
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

		$user_id = $obj['user_id'];
		$request = my_process_db_request($obj, $this->data, false);

		$request['id'] = null;
		$this->db->insert($this->main_table, $request);
		
		$entity_id = $this->db->insert_id();
		$admin_role_id = $this->config->item( 'admin_role_id');
		
		$user_role = array(
				'user_id'=>$user_id,
				'role_id'=>$admin_role_id,
				'entity_type'=>'entity',
				'entity_id'=>$entity_id
				
		);
		$this->db->insert('user_role', $user_role);
		return $entity_id;
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