<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vote extends MY_Controller {
	function __construct(){
		parent::__construct();
		$this->title('Food voter');
		$this->data(array(
			'challenge_menus' => $this->challenges->fetchChallenges()
		));
	}

	public function index($uuid = '') {
		if($uuid != ''){
			$this->setUser($this->users->getByUUID($uuid));
		}
		return redirect('vote/result');
	}

	public function vote(){
		if(!$this->user){
			$this->session->set_flashdata('result_error', MSG_UNKNOWN);
			return redirect('vote/result');
		}

		if(time() > strtotime($this->date." ".VOTE_UNTIL)){
			$this->session->set_flashdata('result_error', MSG_NOTALLOWED);
			return redirect('vote/result');
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('vote', 'Where to eat', 'trim|strip_tags');
		if ($this->form_validation->run() != FALSE){
			$vote = $this->input->post('vote', NULL);
			$this->votes->insert($vote);
			$this->session->set_flashdata('result_success', MSG_SUCC);
			return redirect('vote/result');
		}

		$this->data(array(
			'vote' => $this->votes->fetchVote($this->user)
		));

		$this->render('vote/vote'); 
	}

	public function result(){
		$this->data(array(
			'votes' => $this->votes->result(),
			'winner' => $this->votes->winner(),
			'order' => $this->votes->order(),
			'index' => $this->votes->index(),
			'seed' => $this->votes->seed(),
		));
		$this->render('vote/result');
	}
}
