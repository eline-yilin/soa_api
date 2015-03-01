<?php
require(APPPATH.'/models/My_model.php');
class inquiry_model extends My_Model {

	var $main_table   = 'inquiry';
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
		$str = "SELECT i.* FROM inquiry i WHERE status = 1 ORDER BY weight DESC,id DESC" ; 
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
		$rst['questions'] = array();
		$rst['greetings'] = array();
		$rst['endings'] = array();
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
		

		$str = "SELECT g.*  FROM inquiry i
		 LEFT JOIN inquiry_ending g ON g.inquiry_id = i.id AND g.status = 1
		 WHERE i.status = 1 AND i.id = ?";
		
		$query = $this->db->query($str, array($id));
		
		foreach ($query->result() as $row)
		{
			
			$rst['endings'][] = $row;
		}
		return $rst;
	}

	function updateDetail($obj)
	{
		
		$request = my_process_db_request($obj, $this->data, false);

		
		$id = $request['id'];
		$remove_request = array('status'=>2);
		$this->db->update('inquiry_greeting', $remove_request, array('inquiry_id' => $id));
		
		$this->db->update('inquiry_ending', $remove_request, array('inquiry_id' => $id));
		$this->db->update('inquiry_question', $remove_request, array('inquiry_id' => $id));
		
		if(isset($obj['questions']))
		{
			$ques_arr = array();
			$questions = explode('###', $obj['questions']);
			foreach ($questions as $question)
			{
				if(empty($question)){
					continue;
				}
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
					if(empty($greeting)){
					continue;
					}
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
		
		if(isset($obj['endings']))
		{
			$ending_arr = array();
			$endings = explode('###', $obj['endings']);
			foreach ($endings as $ending)
			{
				if(empty($ending)){
					continue;
				}	
				$ending_arr[] = array(
						'content'=>$ending,
						'inquiry_id'=>$id,
						'status'=>1
							
				);
			}
			$this->db->insert_batch('inquiry_ending', $ending_arr);
		}

		

		$this->db->update($this->main_table, $request, array('id' => $id));
		return true;
		
	}
	
	function createDetail($obj)
	{
		//$obj = parse_str($obj);
		$request = my_process_db_request($obj, $this->data, false);
		
		$request['id'] = null;
		/* $maxid = 0;
		$row = $this->db->query('SELECT MAX(id) AS `maxid` FROM ' . $this->main_table)->row();
		if ($row) {
			$maxid = $row->maxid;
		}
		$maxid++;
		
		$request['weight'] = $maxid; */
		
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
		
		if(isset($obj['endings']))
		{
			$ending_arr = array();
			$endings = explode('###', $obj['endings']);
			foreach ($endings as $ending)
			{
					
				$ending_arr[] = array(
						'content'=>$ending,
						'inquiry_id'=>$id,
						'status'=>1
							
				);
			}
		}
		$this->db->insert_batch('inquiry_ending', $ending_arr);
		return $id;
		//return $obj;
	}
	
	function deleteDetail($id)
	{
		$id = intval($id);
		$remove_request = array('status'=>2);
		$this->db->update('inquiry_greeting', $remove_request, array('inquiry_id' => $id));
		
		$this->db->update('inquiry_ending', $remove_request, array('inquiry_id' => $id));
		$this->db->update('inquiry_question', $remove_request, array('inquiry_id' => $id));
		$this->db->update('inquiry', $remove_request, array('id' => $id));
		return $id;
		
	}
	
	
	
}
?>