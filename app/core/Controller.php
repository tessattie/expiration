<?php
class Controller{

	protected $brdata;

	protected $report;

	protected $users;

	public function __construct()
	{
		$this->brdata = $this->model('brdata');
		$this->report = $this->model('report');
		$this->users = $this->model('users');
	}

	public function model($model)
	{
		if(file_exists('../app/models/' . $model . '.php'))
		{
			require_once '../app/models/' . $model . '.php';
			$return = new $model();
		}
		else
		{
			$return = false;
		}
		return $return;
	}

	public function phpExcel()
	{
		if(file_exists('../app/vendors/PHPExcel/Classes/PHPExcel.php'))
		{
			require_once '../app/vendors/PHPExcel/Classes/PHPExcel.php';
		}
		else
		{
			require_once '../app/vendors/PHPExcel/Classes/PHPExcel.php';
		}
		return new PHPExcel();
	}

	public function view($view, $data = [])
	{
		if(file_exists('../app/views/'. $view . '/index.php'))
		{
			require_once '../app/views/'. $view . '/index.php';
		}
		else
		{
			require_once '../app/views/default.php';
		}
	}

	public function checkSession()
	{
		if(!isset($_SESSION['id']))
		{
			header('Location: /expiration/public/login');
		}
	}
}