<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	protected $user;
	protected $model = array();
	protected $date;

	function __construct(){
		parent::__construct();
		$this->date = date("Y-m-d");

		$this->votes->initVotes();
		$this->votes->computeTodaysIndex();

		if(isset($_SESSION['user_id'])){
			$this->setUser($this->users->get($_SESSION['user_id']));
		}else{
			$this->setUser(null);
		}

		/* Basic View datas */
		$this->model = array(
			'user'              => $this->user,
			'date'              => $this->date,
			'adminState'		=> $this->user && $this->user->user_admin && isset($_SESSION['adminState']) && $_SESSION['adminState'],
			'_title'            => 'TeamBoard - ',
			'scripts'	        => array(),
			'additional_css'	=>''
		);
	}

	protected function setUser($user){
		$this->user = $user;
		if($user){
			$this->session->set_userdata('user_id', $user->user_id);
		}else{
			unset($_SESSION['user_id']);
		}
		$this->data(array(
			'user' => $this->user
		));
		$this->votes->fetchVote($this->user);
	}

	/* Place data into model */
	protected function data(array $array)
	{
		if(!empty($array))
		{
			foreach($array as $key => $value)
			{
				if(!preg_match('/^_/', $key)) $this->model[$key] = $value;
			}
		}
	}

	/* Set Title */
	protected function title($title = '')
	{
		$this->model['_title'] = 'TeamBoard - '.$title;
	}


	/* Render View */
	protected function render($view, $template = 'default', $return = FALSE)
	{
		$this->output->set_header('Content-type: text/html; charset=UTF-8');
		$this->model['_view'] = $view;
		return $ptr = $this->load->view('template/'.$template, $this->model, $return);
	}
}
