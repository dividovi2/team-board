<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Model {
	function getByUUID($uuid){
		$u = $this->db->get_where($this->db->dbprefix('user'), array(
			'user_uuid' => $uuid,
			'user_disabled' => 0
		));
		if($u->num_rows() == 1)
			return $u->row(0);
		else
			return null;
	}

	function get($id){
		$u = $this->db->get_where($this->db->dbprefix('user'), array(
			'user_id' => $id,
			'user_disabled' => 0
		));
		if($u->num_rows() == 1)
			return $u->row(0);
		else
			return null;
	}

	function getAllIds(){
		$ids = array();
		$result = $this->db->where('user_disabled', 0)
						   ->get($this->db->dbprefix('user'))
						   ->result();
		foreach($result as $u){
			$ids[] = $u->user_id;
		}
		return $ids;
	}

	function getAll(){
		$ids = array();
		return $this->db->where('user_disabled', 0)
						->order_by('user_displayname')
						->get($this->db->dbprefix('user'))
						->result();
	}
}
