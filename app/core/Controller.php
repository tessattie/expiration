<?php
class Controller{

	protected $brdata;

	protected $report;

	protected $users;

	protected $userRole;

	protected $roles;

	public function __construct()
	{
		$this->brdata = $this->model('brdata');
		$this->report = $this->model('report');
		$this->users = $this->model('users');
		$this->userRole = $this->setRole();
		$this->roles = array(5 => "menuAdmin", 6 => "menuOne", 7 => "menuTwo", 8 => "menuZero");
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

	public function phpExcelFactory($fileName)
	{
		if(file_exists('../app/vendors/PHPExcel/Classes/PHPExcel/IOFactory.php'))
		{
			require_once '../app/vendors/PHPExcel/Classes/PHPExcel/IOFactory.php';
		}
		else
		{
			require_once '../app/vendors/PHPExcel/Classes/PHPExcel/IOFactory.php';
		}
		$inputFileType = PHPExcel_IOFactory::identify($fileName);
	    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
	    $objPHPExcel = $objReader->load($fileName);
		return $objPHPExcel;
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
		if(!isset($_SESSION["orders"]['id']))
		{
			header('Location: /orders/public/login');
		}
	}

	public function setRole()
	{
		$role = "";
		$this->roles = array(5 => "menuAdmin", 6 => "menuOne", 7 => "menuTwo", 8 => "menuZero");
		if(isset($_SESSION["orders"]['role']))
		{
			$role = $this->roles[$_SESSION["orders"]['role']];
		}
		else
		{
			if(!isset($_SESSION["orders"]['id']))
			{
				header('Location: /csm/public/login');
			}
		}
		return $role;
	}
}