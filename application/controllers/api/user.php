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

class User extends REST_Controller
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
		$this->load->model('user_model','main');
	}
	
	function list_get()
	{
		
	
		$item = $this->main->getList();
		if($item)
		{
			$this->response($item, 200); // 200 being the HTTP response code
		}
	
		else
		{
			$this->response(array('error' => 'Item could not be found'), 404);
		}
	}
	
	function login_post()
	
	{ 
		if(!$this->post('username') || ! $this->post('password'))
		{
			$this->response(NULL, 400);
		}
	
		$item = $this->main->login($this->post('username'), $this->post('password'));

		if($item)
		{
			$this->response($item, 200); // 200 being the HTTP response code
		}
	
		else
		{
			$this->response(array('error' => 'Item could not be found'), 404);
		}
	}
	
	function register_post()
	{
		if(!($this->post('email') || $this->post('phone')) || ! $this->post('password'))
		{
			$this->response(NULL, 400);
		}
	
		$item = $this->main->register($this->post());
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
	
		$item = $this->main->getDetail($this->get('id'));
		 
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
    	
    	$item = $this->main->updateDetail($request);
    		
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
    	 
    	$item = $this->main->createDetail($request);
    
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
