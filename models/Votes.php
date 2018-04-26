<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Votes extends CI_Model {
	private $today;
	private $index;
	private $vote;
	private $user;
	private $seed;

	public function __construct(){
		parent::__construct();
		$this->today = date("Y-m-d");
		$this->index = -1;
		$this->vote = '';
		$this->seed = floor(time() / (60*60*24));
		$this->user = null;
		mt_srand($this->seed);
	}

	function initVotes(){
		$db_vote = $this->db->get_where($this->db->dbprefix('vote'), array(
			'vote_day' => $this->today
		));

		if($db_vote->num_rows() == 0){
			$participants = $this->users->getAllIds();
			$need_to_insert = array();
			foreach($participants as $participant){
				$need_to_insert[] = array(
					'vote_user' => $participant,
					'vote_day' => $this->today,
					'vote_vote' => ''
				);
			}

			$this->db->insert_batch($this->db->dbprefix('vote'),$need_to_insert);
		}
	}

	function computeTodaysIndex(){
		if($this->index = -1){
			$this->winner();
		}
	}

	function fetchVote($user){
		if($user && !$user->user_disabled){
			$this->user = $user;
			$this->vote = $this->db->select('*', false)
									->from($this->db->dbprefix('vote'))
									->where('vote_day', $this->today)
									->where('vote_user', $this->user->user_id)
									->get()->row(0)->vote_vote;
		}
		return $this->vote;
	}

	function result(){
		return $this->db->select('*', false)
					    ->from($this->db->dbprefix('vote'))
						->join($this->db->dbprefix('user'), 'vote_user = user_id')
						->where('user_disabled', 0)
						->where('vote_day', $this->today)
						->get()
						->result();
	}

	function winner(){
		$candidates = $this->db->select('*', false)
							   ->from($this->db->dbprefix('vote'))
							   ->join($this->db->dbprefix('user'), 'vote_user = user_id')
							   ->where('user_disabled', 0)
							   ->where('vote_day', $this->today)
							   ->where('vote_vote !=', '')
							   ->order_by('vote_vote')
							   ->get()
							   ->result();
		$size = count($candidates);
		if($size > 0){
			$this->index = mt_rand(1, $size);
			return $candidates[$this->index - 1];
		}else{
			$this->index = -1;
			return null;
		}
	}

	function order(){
		return $this->db->select('*', false)
					    ->from($this->db->dbprefix('vote'))
						->join($this->db->dbprefix('user'), 'vote_user = user_id')
						->where('user_disabled', 0)
						->where('vote_day', $this->today)
						->order_by('vote_vote')
						->get()
						->result();
	}

	function index(){
		return $this->index;
	}

	function seed(){
		return $this->seed;
	}

	function insert($vote){
		if($this->user && !$user->user_disabled){
			$this->vote = $vote;
			$this->db->where('vote_day', $this->today)
				 	 ->where('vote_user', $this->user->user_id)
				 	 ->update($this->db->dbprefix('vote'), array(
					 		  'vote_vote' => $vote
						      ));
		}
	}
}
