<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('my_process_db_request'))
{
    function my_process_db_request( $param = array(), $target = null, $is_mandatory = false)
    {
    	$CI =& get_instance();
    	$type_default_mapping = array(
    			'int' => 0,
    			'string'=> ''
    	);
    	if($target){
    		$request = array();
		
		    foreach($target as $key => $attr){
		    			//set in param
		    			if(isset($param[$key])){
		    				if(isset($attr['type']))
		    				{
		    					$type = $attr['type'];
		    					switch ($type){
		    						case 'int': 
		    							$request[$key] = intval($param[$key]);
		    							break;
		    						default:
		    							$request[$key] = $param[$key];
		    							break;
		    					}
		    				}
		    				else
		    				{
		    					$request[$key] = $param[$key];
		    				}
		    			}
		    			else{
		    				//has default
		    			   if(isset($attr['default']))
		    			   {
		    			   		$request[$key] = $attr['default'];
		    			   }
		    			   else{

			    			   	//has type default
			    			   	if(isset($attr['type']) && isset($type_default_mapping[$attr['type']])){
			    			   		$request[$key] = $type_default_mapping[$attr['type']];
			    			   }
		    			   else
		    			   {
		    			   	$request[$key] = null;
		    			   	if($is_mandatory){
		    			   		return false;
		    			   	}
		    			   }
		    			}
		    			}
		    			if($key == 'clientid' || $key == 'client_id')
		    			{
		    				$request[$key] =  $CI->session->userdata('client_id');
		    			}
		    			
		    }
	    	return $request;
    	}
    	else
    	{
    		return null;
    	}
       
    }   
    
    function my_process_db_query($request, $action = 'get')
    {
    	$query = "";
	     if($request)
	     {
	     	if($action == 'get')
	     	{
	     		
	     	}
	     	else if($action == 'post'){	
	     		$attrSet = array();
	     		$valSet = array();
	     	}
	     	foreach($request as $key => $val)
	     	{
	     		if($action == 'get')
		     	{
		     		$query .= " AND $key = '$val' ";
		     	}
		     	else if($action == 'post'){
		     		$attrSet[] = $key;
		     		$valSet[] = $val;
		     	}
	     	}
	     	if($action == 'post'){
	     		$escValSet = array_map('mysql_real_escape_string', $valSet);
	     		$escAttrSet = array_map('mysql_real_escape_string', $attrSet);
	     		$query .= "(" . implode("," , $escAttrSet) . ") VALUES ('"
	     				. implode("','" , $escValSet) . "')";
	     		
	     	}
	
	    }
	    return $query;
	 }
}