<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lottery extends MY_Controller {
	function __construct(){
		parent::__construct();
		$this->clngs = $this->challenges->fetchChallenges();
		$this->data(array(
			'challenge_menus' => $this->clngs
		));
		$this->title('Lottery');
	}

	public function index($uuid = '') {
		if($uuid != ''){
			$this->setUser($this->users->getByUUID($uuid));
		}

		$seed = floor(strtotime( 'monday this week' ) / (60*60*24*7));
		$weekDays = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday');
		$this->fisherYatesShuffle($weekDays, $seed);

		$this->data(array(
			'weekDays' => $weekDays,
			'seed' => $seed
		));

		$this->render('lottery/index');
	}

	private function fisherYatesShuffle(&$items, $seed)
	{
		@mt_srand($seed);
		for ($i = count($items) - 1; $i > 0; $i--)
		{
			$j = @mt_rand(0, $i);
			$tmp = $items[$i];
			$items[$i] = $items[$j];
			$items[$j] = $tmp;
		}
	}
}
