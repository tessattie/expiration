<?php
class Controller{

	protected $brdata;

	protected $report;

	protected $users;

	protected $userRole;

	protected $logs;

	protected $roles;

	public function __construct()
	{
		$this->brdata = $this->model('brdata');
		$this->report = $this->model('report');
		$this->logs = $this->model('log');
		$this->users = $this->model('users');
		date_default_timezone_set('America/Dominica');
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

	public function deleteOrderLog($order, $id){
		$action = $_SESSION["orders"]['firstname'] . " " . $_SESSION["orders"]['lastname'] . " deleted the order : " . $id . ", name : " . $order;
		$date = date("Y-m-d H:i:s");
		$this->logs->saveLog($date, 1, $action);
	}

	public function exportOrderLog($order, $id){
		$action = $_SESSION["orders"]['firstname'] . " " . $_SESSION["orders"]['lastname'] . " exported the order id : " . $id . ", name : " . $order;
		$date = date("Y-m-d H:i:s");
		$this->logs->saveLog($date, 1, $action);
	}

	public function duplicateOrderLog($order, $id){
		$action = $_SESSION["orders"]['firstname'] . " " . $_SESSION["orders"]['lastname'] . " clicked on 'duplicate' for the order id : " . $id . ", name : " . $order;
		$date = date("Y-m-d H:i:s");
		$this->logs->saveLog($date, 1, $action);
	}

	public function editOrderLog($order, $id){
		$action = $_SESSION["orders"]['firstname'] . " " . $_SESSION["orders"]['lastname'] . " clicked on 'edit' for the order id : " . $id . ", name : " . $order;
		$date = date("Y-m-d H:i:s");
		$this->logs->saveLog($date, 1, $action);
	}

	public function UPCPriceCompareLog($order, $id, $upc){
		$action = $_SESSION["orders"]['firstname'] . " " . $_SESSION["orders"]['lastname'] . " checked the UPCPriceCompare for the UPC : " . $upc . " in the order id : " . $id . ", name : " . $order ;
		$date = date("Y-m-d H:i:s");
		$this->logs->saveLog($date, 1, $action);
	}

	public function currentVendorLog($order_id ,$order, $id, $upc, $fromVendor, $toVendor){
		$action = $_SESSION["orders"]['firstname'] . " " . $_SESSION["orders"]['lastname'] . " changed the current vendor from : " . $fromVendor . "
		to " . $toVendor . " for the item id : ". $id .", UPC : " . $upc . " in the order id : " . $order_id . ", name : " . $order ;
		$date = date("Y-m-d H:i:s");
		$this->logs->saveLog($date, 1, $action);
	}

	public function orderConsultLog($order, $id){
		$action = $_SESSION["orders"]['firstname'] . " " . $_SESSION["orders"]['lastname'] . " consulted the order id : " . $id . ", name : " . $order ;
		$date = date("Y-m-d H:i:s");
		$this->logs->saveLog($date, 1, $action);
	}

	public function saveOrderLog($order, $id){
		$action = $_SESSION["orders"]['firstname'] . " " . $_SESSION["orders"]['lastname'] . " saved a new order id : " . $id . ", name : " . $order;
		$date = date("Y-m-d H:i:s");
		$this->logs->saveLog($date, 1, $action);
	}

	public function closeOrderLog($order, $id, $status){
		if($status == 0){
			$oppositestatus = 1;
		}else
		{
			$oppositestatus = 0;
		}
		$statuses = array(0 => "OPEN", 1 => "CLOSED");
		$action = "<strong>" . $_SESSION["orders"]['firstname'] . " " . $_SESSION["orders"]['lastname'] . " </strong> changed the order id : " . $id . ", name : " . $order . " status from " . $statuses[$oppositestatus] . " to " . $statuses[$status] ;
		$date = date("Y-m-d H:i:s");
		$this->logs->saveLog($date, 1, $action);
	}
}