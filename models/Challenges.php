<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Challenges extends CI_Model {
	private $today;

	public function __construct(){
		parent::__construct();
	}

	function getAllTypeIds(){
		$ids = array();
		$result = $this->db->get($this->db->dbprefix('challenge'))
							->result();
		foreach($result as $c){
			$ids[] = $c->challenge_id;
		}
		return $ids;
	}

	function fetchResults($type, $days=null){
		if(!$days){
			return array();
		}

		$return = array(
			'days' => array(),
			'data' => array()
		);

		$res = $this->db->select('*', false)
				  		->from($this->db->dbprefix('challenge_result'))
				  		->join($this->db->dbprefix('challenge'), 'cr_type = challenge_id')
				  		->join($this->db->dbprefix('user'), 'cr_user = user_id')
				  		->where('cr_type', $type)
				  		->where('cr_day >=', $days[0])
				  		->where('cr_day <=', $days[count($days)-1])
						->order_by('user_displayname')
				  		->order_by('cr_day')
						->order_by('cr_type')
					    ->get()
						->result();
		
		$result = &$return['data'];
		foreach($res as $r){
			if(!in_array($r->cr_day, $return['days'])){
				$return['days'][] = $r->cr_day;
			}
			if(!array_key_exists($r->user_displayname, $result)){
				$result[$r->user_displayname] = array();
				foreach($days as $day){
					$result[$r->user_displayname][$day] = null;
				}
			}
			$result[$r->user_displayname][$r->cr_day] = $r;
		}
		sort($return['days']);
		return $return;
	}
	function fetchTodayUserResults($user, $type){
		if($user && !$user->user_disabled){

			return $this->db->select('*', false)
					        ->from($this->db->dbprefix('challenge_result'))
						    ->where('cr_day', date('Y-m-d'))
						    ->where('cr_user', $user->user_id)
						    ->where('cr_type', $type)
						    ->get()
						    ->row(0);
		}
		return null;
	}

	function fetchFirstResult(){
		$res = $this->db->select('*', false)
					        ->from($this->db->dbprefix('challenge_result'))
							->order_by('cr_day')
							->limit(1)
							->get();
		if($res->num_rows() == 1){
			return $res->row(0)->cr_day;
		}else{
			return date('Y-m-d');
		}
	}

	function fetchChallenges(){
		$r = $this->db->select('*', false)
						->from($this->db->dbprefix('challenge'))
						->get()
						->result();
		
		$result = array();
		foreach($r as $row){
			$result[(string)$row->challenge_id] = $row;
		}

		return $result;
	}

	function update($user, $result, $type, $day){
		if($user && !$user->user_disabled){
			if((int)$result > 0){
				$this->db->replace($this->db->dbprefix('challenge_result'),
					array(
					'cr_day' => $day,
					'cr_user' => $user->user_id,
					'cr_type' => $type,
					'cr_result' => $result
				));
			}else{
				$this->db->where('cr_day', $day)
						 ->where('cr_user', $user->user_id)
						 ->where('cr_type', $type)
						 ->delete($this->db->dbprefix('challenge_result'));
			}
		}else{
			die('OOOOO');
		}
	}
}
