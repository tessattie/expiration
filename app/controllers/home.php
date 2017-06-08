<?php
session_start();
class home extends Controller{

	protected $brdata;

	protected $users;
	
	private $today;
	
	private $from;
	
	private $to;

	private $queryTitles;

	private $classname;

	private $fileArianne;

	public function __construct()
	{
		parent::__construct();
		$this->today = date('Y-m-d', strtotime("-1 days"));
		if(!isset($_COOKIE["from"]) || empty($_SESSION['report']["date_from"]))
		{
			setCookie("from", date('Y-m-01'));
			$_COOKIE["from"] = date('Y-m-01');
			$_SESSION['report']["date_from"] = date('Y-m-01');
		}
		else
		{
			$this->from = $_COOKIE["from"];
		}
		if(!isset($_COOKIE["to"]) || empty($_SESSION['report']["date_to"]))
		{
			setCookie("to", date('Y-m-d'));
			$_COOKIE["to"] = date('Y-m-d');
			$_SESSION['report']["date_to"] = date('Y-m-d');
		}
		else
		{
			$this->to = $_COOKIE["to"];
		}
		$this->classname = "thereport";
		$this->brdata = $this->model('brdata');
		$this->fileArianne = "NEW REPORT";

	} 

	public function index()
	{
		$addItem = '';
		$data = array("from" => $this->from, "to" => $this->to, "addItems" => $addItem, "action" => "index", "title" => $this->fileArianne);
		$this->view('home', $data);
	}

	public function logout()
	{
		session_unset();
		session_destroy();
		header('Location: /orders/public/login');
	}

	private function renderView($data)
	{
		if(!empty($data))
		{
			$this->view('home', $data);
		}
		else
		{
			$this->view('home');
		}
	}

	public function setDefaultDates($from, $to)
	{
		setCookie("from", $from);
		$_COOKIE["from"] = $from;
		setCookie("to", $to);
		$_COOKIE["to"] = $to;
		if(!empty($from))
		{
			$this->from = $from;
		}
		if(!empty($to))
		{
			$this->to = $to;
		}
	}
}