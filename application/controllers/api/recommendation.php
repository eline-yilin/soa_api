<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Keys Controller
 *
 * This is a basic Key Management REST controller to make and delete keys.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php
require(APPPATH.'/libraries/REST_Controller.php');

class Recommendation extends REST_Controller
{
	protected $methods = array(
		'index_put' => array('level' => 10, 'limit' => 10),
		'index_delete' => array('level' => 10),
		'level_post' => array('level' => 10),
		'regenerate_post' => array('level' => 10),
	);
    
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->load->model('recommendation_model','entity');
	}
	
	function product_get()
	{
		
	   $user_id = $this->get('user_id');
	   $limit =  $this->get('limit') ? $this->get('limit') : 4;
		$item = $this->entity->getList(array('user_id'=>$user_id));
		if($item)
		{
			$this->response($item, 200); // 200 being the HTTP response code
		}
	
		else
		{
			$this->response(array('error' => 'Item could not be found'), 404);
		}
	}
	
	function detail_get()
	{
		if(!$this->get('id'))
		{
			$this->response(NULL, 400);
		}
	
		$item = $this->product->getDetail($this->get('id'));
		 
		if($item)
		{
			$this->response($item, 200); // 200 being the HTTP response code
		}
	
		else
		{
			$this->response(array('error' => 'Item could not be found'), 404);
		}
	}
	 
	/**
	 * Key Create
	 *
	 * Insert a key into the database.
	 *
	 * @access	public
	 * @return	void
	 */
	public function detail_put()
    {
    	$request = $this->put();
    	/* if(!$this->put('id'))
    	{
    		$this->response(NULL, 400);
    	} */
    	
    	$item = $this->product->updateDetail($request);
    		
    	if($item)
    	{
    		$this->response($item, 200); // 200 being the HTTP response code
    	}
    	
    	else
    	{
    		$this->response(array('error' => 'Item could not be found'), 404);
    	}
    }

    public function detail_post()
    {
    	$request = $this->post();
    	
    	/* if(!$this->put('id'))
    	 {
    	 $this->response(NULL, 400);
    	} */
    	$item = $this->entity->createDetail($request);

    	if($item)
    	{
    		$this->response($item, 200); // 200 being the HTTP response code
    	}
    	 
    	else
    	{
    		$this->response(array('error' => 'Item could not be found'), 404);
    	}
    }
    

    function detail_delete()
    {
    	if(!$this->get('id'))
    	{
    		$this->response(NULL, 400);
    	}
    
    	$item = $this->product->deleteDetail($this->get('id'));
    		
    	if($item)
    	{
    		$this->response($item, 200); // 200 being the HTTP response code
    	}
    
    	else
    	{
    		$this->response(array('error' => 'Item could not be found'), 404);
    	}
    }
    
	
}
