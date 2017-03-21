<?php
class error extends Controller{

	public function __construct()
	{
		parent:: __construct();
	}

	public function index()
	{
		$data = array("menu" => $this->userRole);
		$this->view('error', $data);
	}
}