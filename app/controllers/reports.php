<?php
session_start();
class reports extends Controller{

	protected $brdata;

	protected $users;

	protected $today;

	protected $report;

	public function __construct()
	{
		parent:: __construct();
		$this->today = date('Y-m-d', strtotime("-1 days"));
		if(empty($_SESSION['report']))
		{
			$_SESSION["report"] = array("name" => "", 
			"date_from" => "", 
			"date_to" => "", 
			"items" => null);
		}
	}

	public function index()
	{
		$reports = $this->report->get_reports();
		$this->view('reports', array("reports" => $reports));
	}

	public function single($id = false, $upc = false)
	{
		$upcPriceCompare = false;
		if($id == false)
		{
			header('Location: /expiration/public/reports');
		}
		$report = $this->report->get_report($id);
		if($upc != false)
		{
			$upcPriceCompare = $this->brdata->get_upcReport($upc, $this->today, $report[0]['date_from'], $report[0]['date_to']);
		}
		$this->view('reports/single', array("report" => $report, "upcPriceCompare" => $upcPriceCompare, "report_id" => $id, "upc" => $upc));
	}

	public function reset()
	{
		unset($_SESSION['report']);
		header('Location: /expiration/public/home');
	}

	public function new_report()
	{
		$_SESSION["report"]["name"] = $_POST["name"];
		$_SESSION["report"]["date_from"] = $_POST["date_from"];
		$_SESSION["report"]["date_to"] = $_POST["date_to"];
		echo "Data saved !";
		die();
	}

	public function add_item()
	{
		if(isset($_POST["newitem"]))
		{
			$item = $this->brdata->get_item($_POST["newitem"], $this->today, $_SESSION["report"]["date_to"], $_SESSION["report"]["date_from"]);
			// Set the item in the session 
			if(!empty($item))
			{
				$item['order'] = null;
				$item['expiration'] = null;
				$item['expiration_date'] = null;
				$_SESSION["report"]["items"][$item["UPC"]] = $item;
			}
		}
		header('Location: /expiration/public/home');
	}

	public function set_itemValue()
	{
		if(!empty($_SESSION))
		{
			$_SESSION['report']['items'][$_POST['ident']][$_POST['name']] = $_POST['value'];
		}
		echo json_encode($_POST);
		die();
	}

	public function removeItem($upc)
	{
		if(!empty($_SESSION['report']['items'][$upc]))
		{
			unset($_SESSION['report']['items'][$upc]);
		}
		header('Location: /expiration/public/home');
	}

	public function save_report()
	{
		unset($_SESSION['error']);
		if(!empty($_SESSION['report']))
		{
			if(!empty($_SESSION['report']["name"]))
			{
				if(!empty($_SESSION['report']["date_from"]))
				{
					if(!empty($_SESSION['report']["date_to"]))
					{
						if($_SESSION['report']["date_to"] > $_SESSION['report']["date_from"])
						{
							if(count($_SESSION['report']["items"]) > 0)
							{
								// save report information
								$report = $this->report->save_report($_SESSION['report']);
								if($report)
								{
									foreach($_SESSION['report']["items"] as $key => $value)
									{
										$saved_items[$key] = $this->report->save_item($value, $report);
									}
									// require_once('export.php');
									// $exportClass = new export();
									// echo "<script>window.open</script>";
									// $exportClass->reportExport($report);
									// echo "<script>window.close</script>";
									$this->reset();
								}
								else
								{
									$_SESSION['error'] = "Something went wrong while saving the report. Please contact support";
								}
							}
							else
							{ 
								$_SESSION['error'] = "Your report must have at least one item";
							}
						}
						else
						{
							$_SESSION['error'] = "The from date must be less than the to date";
						}
					}
					else
					{
						$_SESSION['error'] = "You must choose to sales dates";
					}
				}
				else
				{
					$_SESSION['error'] = "You must choose from sales dates";
				}
			}
			else
			{
				$_SESSION['error'] = "You must choose a name for your report";
			}
		}
		else
		{
			$_SESSION['error'] = "Your report cannot be saved because it was not found";
		}
		header("Location:/expiration/public/home");
	}

	public function reset_error()
	{
		unset($_SESSION['error']);
		die();
	}
}