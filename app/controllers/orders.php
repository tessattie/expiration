<?php
session_start();
class orders extends Controller{

	protected $brdata;

	protected $users;
	
	private $today;

	protected $report;

	public function __construct()
	{
		$this->today = date('Y-m-d', strtotime("-1 days"));
		parent:: __construct();
	}

	public function newOrder()
	{
		// ajax function to save orders
		echo json_encode($_POST); die();
	}
}