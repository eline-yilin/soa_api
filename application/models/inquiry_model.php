<?php
require(APPPATH.'/models/My_model.php');
class inquiry_model extends My_Model {

	var $main_table   = 'product';
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
			
			'status'=> array(
					'type'=>'int',
					'default' => 1
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
		$str = "SELECT i.* FROM inquiry i" ; 
		/* " p LEFT JOIN user_role r
				ON p.entity_id = r.entity_id AND r.entity_type = 'entity' AND r.is_deleted = 0 AND
				p.is_deleted = 0
				LEFT JOIN entity e ON p.entity_id = e.id
				WHERE 1=1 "; */
		if($user_id)
		{
			//$str .= "AND r.user_id = ?";
		}
		$query = $this->db->query($str);
		$resp = array();
		foreach ($query->result() as $row)
		{
			$resp[] = $row;
		}
		
		return $resp;
	}
	

	function getDetail($id)
	{
		/* if($user_id)
		{
			//$str .= "AND r.user_id = ?";
		} */
		
		$rst = array();
		$is_found = false;
		$query = $this->db->get_where('inquiry', array('id' => $id,'status'=>1));
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
		$rst['questions'] = array();
		$rst['greetings'] = array();
		
		$str = "SELECT q.* FROM inquiry i
		 LEFT JOIN inquiry_question q
		 ON q.inquiry_id = i.id AND q.status = 1 
		
		 WHERE i.status = 1  AND i.id = ?"; 
		
		$query = $this->db->query($str, array($id));
		
		foreach ($query->result() as $row)
		{
			$rst['questions'][] = $row;
		}
		
		$str = "SELECT g.*  FROM inquiry i
		 LEFT JOIN inquiry_greeting g ON g.inquiry_id = i.id AND g.status = 1
		 WHERE i.status = 1 AND i.id = ?";
		
		$query = $this->db->query($str, array($id));
		
		foreach ($query->result() as $row)
		{
			$rst['greetings'][] = $row;
		}
		return $rst;
	}

	function updateDetail($obj)
	{
		$request = my_process_db_request($obj, $this->data);
		return $request;
		

		$this->db->update('entity', $request, array('id' => $_POST['id']));
	}
	
	function createDetail($obj)
	{
		//$obj = parse_str($obj);
		$request = my_process_db_request($obj, $this->data, false);
		
		$request['id'] = null;
		$this->db->insert('inquiry', $request);
		$id = $this->db->insert_id();
		if(isset($obj['questions']))
		{
			$ques_arr = array();
			$questions = explode('###', $obj['questions']);
			foreach ($questions as $question)
			{
				
				$ques_arr[] = array(
						'question'=>$question,
						'inquiry_id'=>$id,
						'status'=>1
						
				);
			}

			if(isset($obj['greetings']))
			{
				$greeting_arr = array();
				$greetings = explode('###', $obj['greetings']);
				foreach ($greetings as $greeting)
				{
			
					$greeting_arr[] = array(
							'content'=>$greeting,
							'inquiry_id'=>$id,
							'status'=>1
			
					);
				}
			}
			$this->db->insert_batch('inquiry_question', $ques_arr);
			$this->db->insert_batch('inquiry_greeting', $greeting_arr);
		
		}
		return $id;
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