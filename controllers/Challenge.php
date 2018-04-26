<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Challenge extends MY_Controller {
	function __construct(){
		parent::__construct();
		$this->clngs = $this->challenges->fetchChallenges();
		$this->data(array(
			'challenge_menus' => $this->clngs
		));
		$this->title('Challenge');
	}

	private function setTitle($challenge){
		$title = $this->clngs[(string)$challenge]->challenge_name.' challenge';
		$this->title($title);
		$this->data(array(
			'challenge_name' => $title,
			'chname' => $this->clngs[(string)$challenge]->challenge_name,
			'challenge_id' => $challenge
		));
	}

	public function index($uuid = '') {
		if($uuid != ''){
			$this->setUser($this->users->getByUUID($uuid));
		}
		return redirect('challenge/result/1');
	}

	public function toggleAdminState($url){
		$uri = base64_decode(str_pad(strtr($url, '-_', '+/'), strlen($url) % 4, '=', STR_PAD_RIGHT));
		$_SESSION['adminState'] = isset($_SESSION['adminState']) ? !$_SESSION['adminState'] : true;
		return redirect($uri);
	}

	public function save($challenge){
		if(!$this->user){
			$this->session->set_flashdata('result_error', MSG_UNKNOWN);
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('user', 'User', 'required|is_natural|trim');
		$this->form_validation->set_rules('date', 'Rate', 'required|trim');
		$this->form_validation->set_rules('result', 'Result', 'required|is_natural|trim');
		if ($this->form_validation->run() != FALSE){
			$user = $this->input->post('user', NULL);
			$date = $this->input->post('date', NULL);
			$result = $this->input->post('result', NULL);

			if($user != $this->user->user_id && !($this->user->user_admin && isset($_SESSION['adminState']) && $_SESSION['adminState'])){
				$this->session->set_flashdata('result_error', 'Wrong user');
				return;
			}

			$this->challenges->update($this->users->get($user), $result, (int)$challenge, $date);
			$this->session->set_flashdata('result_success', MSG_SUCC);
		}
	}

	public function result($challenge){
		$tday = strtotime('today');
		$msixday = strtotime('-6 days', $tday);
		$first = strtotime($this->challenges->fetchFirstResult());
		$last = $tday;

		$days = array();
		$cur = $msixday;
		while($cur != $tday){
			$days[] = date('Y-m-d', $cur);
			$cur = strtotime('+1 days', $cur);
		}
		$days[] = date('Y-m-d', $cur);

		$mdays = array();
		$cur = $first;
		while($cur != $last){
			$mdays[] = date('Y-m-d', $cur);
			$cur = strtotime('+1 days', $cur);
		}
		$mdays[] = date('Y-m-d', $cur);

		$this->data(array(
			'participants' => $this->users->getAll(),
			'tday' => date('Y-m-d'),
			'days' => $days,
			'this_week' => $this->challenges->fetchResults($challenge, $days),
			'this_month' => $this->challenges->fetchResults($challenge, $mdays),
		));
		$this->setTitle($challenge);
		$this->render('challenge/result');
	}
}
