<?php
session_start();
class logs extends Controller{

	protected $log;

	public function __construct()
	{
		parent:: __construct();
		$this->log = $this->model('log');
	}

	public function index()
	{
		$logs = $this->log->getLogs();
		$data = array("menu" => $this->userRole, "logs" => $logs);
		$this->view('logs', $data);
	}
}