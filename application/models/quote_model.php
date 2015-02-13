<?php
require(APPPATH.'/models/My_model.php');
class quote_model extends My_Model {

	var $main_table   = 'quote';
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
		$str = "SELECT i.* FROM " . $this->main_table . " i WHERE status = 1 ORDER BY id DESC" ; 
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
		
		$rst['clients'] = array();
		
		$str = "SELECT q.name as agent , c.* FROM quote_client c
		 LEFT JOIN quote q
		 ON c.quote_id = q.id AND q.status = 1 
		
		 WHERE c.status = 1  AND q.id = ?"; 
		
		$query = $this->db->query($str, array($id));
		
		foreach ($query->result() as $index => $row)
		{
			$rst['clients'][] = $row;
			if($index == 0)
			{
				$rst['agent'] = $row->agent;
				$rst['id'] = $row->quote_id;
			}
		}
		
		
		return $rst;
	}

	function updateDetail($obj)
	{
		
		$request = my_process_db_request($obj, $this->data, false);

		
		$id = $request['id'];
		$remove_request = array('status'=>2);
		$this->db->update('inquiry_greeting', $remove_request, array('quote_id' => $id));
		
		$this->db->update('inquiry_ending', $remove_request, array('quote_id' => $id));
		$this->db->update('inquiry_question', $remove_request, array('quote_id' => $id));
		
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
						'quote_id'=>$id,
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
							'quote_id'=>$id,
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
						'quote_id'=>$id,
						'status'=>1
							
				);
			}
			$this->db->insert_batch('inquiry_ending', $ending_arr);
		}

		

		$this->db->update('inquiry', $request, array('id' => $id));
		return true;
		
	}
	
	function createDetail($obj)
	{
		//$obj = parse_str($obj);
		$request = my_process_db_request($obj, $this->data, false);
		
		$request['id'] = null;
		$this->db->insert($this->main_table, $request);
		$id = $this->db->insert_id();
		if(isset($obj['clients']))
		{
			$client_arr = array();
			$clients = explode('###', $obj['clients']);
			$contents = explode('###', $obj['client_contents']);
			foreach ($clients as $index => $client)
			{
				
				$client_arr[] = array(
						'name'=>$client,
						'content'=>$contents[$index],
						'quote_id'=>$id,
						'status'=>1
						
				);
			}
			$this->db->insert_batch('quote_client', $client_arr);
		
		}
		
		return $id;
		//return $obj;
	}
	
	function deleteDetail($id)
	{
		$id = intval($id);
		$remove_request = array('status'=>2);
		$this->db->update('inquiry_greeting', $remove_request, array('quote_id' => $id));
		
		$this->db->update('inquiry_ending', $remove_request, array('quote_id' => $id));
		$this->db->update('inquiry_question', $remove_request, array('quote_id' => $id));
		$this->db->update('inquiry', $remove_request, array('id' => $id));
		return $id;
		
	}
}
?>