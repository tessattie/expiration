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

	public function batch()
	{
		$this->view('reports/batch', array());
	}

	public function updateBatch(){
		$item = $this->brdata->get_item($_POST['upc'], $this->today, $_SESSION["report"]["date_to"], $_SESSION["report"]["date_from"]);
		// Set the item in the session 
		if(!empty($item))
		{
			$item['order'] = null;
			$item['expiration'] = null;
			$item['expiration_date'] = null;
			$_SESSION["report"]["items"][$item["UPC"]] = $item;
		}
		echo json_encode($_SESSION["report"]); die();
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
		if(count($report) == 0)
		{
			$this->report->delete_report($id);
			header('Location: /expiration/public/home');
		}
		$this->view('reports/single', array("report" => $report, "upcPriceCompare" => $upcPriceCompare, "report_id" => $id, "upc" => $upc));
	}

	public function edit($id)
	{
		if($id == false)
		{

		}
		else
		{
			$report = $this->report->get_report($id);
			unset($_SESSION['error']);
			$_SESSION['report']['id'] = $report[0]['report_id'];
			$_SESSION['report']['name'] = $report[0]['name'];
			$_SESSION['report']['date_from'] = $report[0]['date_from'];
			$_SESSION['report']['date_to'] = $report[0]['date_to'];
			for($i=0;$i<count($report);$i++)
			{
			 	$_SESSION['report']['items'][$i]['UPC'] = $report[$i]['upc'];
			 	$_SESSION['report']['items'][$i]['CertCode'] = $report[$i]['itemcode'];
			 	$_SESSION['report']['items'][$i]['ItemDescription'] = $report[$i]['description'];
			 	$_SESSION['report']['items'][$i]['Pack'] = $report[$i]['pack'];
			 	$_SESSION['report']['items'][$i]['SizeAlpha'] = $report[$i]['size'];
			 	$_SESSION['report']['items'][$i]['Brand'] = $report[$i]['brand'];
			 	$_SESSION['report']['items'][$i]['CaseCost'] = $report[$i]['casecost'];
			 	$_SESSION['report']['items'][$i]['Retail'] = $report[$i]['retail'];
			 	$_SESSION['report']['items'][$i]['onhand'] = $report[$i]['onhand'];
			 	$_SESSION['report']['items'][$i]['lastReceiving'] = $report[$i]['lastorder'];
			 	$_SESSION['report']['items'][$i]['lastReceivingDate'] = $report[$i]['lastorderdate'];
			 	$_SESSION['report']['items'][$i]['sales'] = $report[$i]['sales'];
			 	$_SESSION['report']['items'][$i]['VdrNo'] = $report[$i]['vdrno'];
			 	$_SESSION['report']['items'][$i]['VdrName'] = $report[$i]['vdrname'];
			 	$_SESSION['report']['items'][$i]['tpr'] = $report[$i]['tprprice'];
			 	$_SESSION['report']['items'][$i]['tprStart'] = $report[$i]['tprstart'];
			 	$_SESSION['report']['items'][$i]['tprEnd'] = $report[$i]['tprend'];
			 	$_SESSION['report']['items'][$i]['lastReceivingDate'] = $report[$i]['lastorderdate'];
			 	$_SESSION['report']['items'][$i]['expiration'] = $report[$i]['expiration'];
			 	$_SESSION['report']['items'][$i]['expiration_date'] = $report[$i]['expiration_date'];
			 	$_SESSION['report']['items'][$i]['lastReceivingDate'] = $report[$i]['lastorderdate'];
			 	$_SESSION['report']['items'][$i]['expiration'] = $report[$i]['expiration'];
			 	$_SESSION['report']['items'][$i]['order'] = $report[$i]['orderqty'];
			 	$_SESSION['report']['items'][$i]['SctNo'] = $report[$i]['SctNo'];
			 	$_SESSION['report']['items'][$i]['SctName'] = $report[$i]['SctName'];
			 	$_SESSION['report']['items'][$i]['DptNo'] = $report[$i]['DptNo'];
			 	$_SESSION['report']['items'][$i]['DptName'] = $report[$i]['DptName'];
			}
		}
		header('Location: /expiration/public/home');
	}

	public function duplicate($id)
	{
		if($id == false)
		{

		}
		else
		{
			unset($_SESSION['report']);
			unset($_SESSION['error']);
			$report = $this->report->get_report($id);
			$_SESSION['report']['date_from'] = $report[0]['date_from'];
			$_SESSION['report']['date_to'] = $report[0]['date_to'];
			for($i=0;$i<count($report);$i++)
			{
				$_SESSION['report']['items'][$i]['UPC'] = $report[$i]['upc'];
				$_SESSION['report']['items'][$i]['CertCode'] = $report[$i]['itemcode'];
				$_SESSION['report']['items'][$i]['ItemDescription'] = $report[$i]['description'];
				$_SESSION['report']['items'][$i]['Pack'] = $report[$i]['pack'];
				$_SESSION['report']['items'][$i]['SizeAlpha'] = $report[$i]['size'];
				$_SESSION['report']['items'][$i]['Brand'] = $report[$i]['brand'];
			 	$_SESSION['report']['items'][$i]['CaseCost'] = $report[$i]['casecost'];
			 	$_SESSION['report']['items'][$i]['Retail'] = $report[$i]['retail'];
			 	$_SESSION['report']['items'][$i]['onhand'] = $report[$i]['onhand'];
			 	$_SESSION['report']['items'][$i]['lastReceiving'] = $report[$i]['lastorder'];
			 	$_SESSION['report']['items'][$i]['lastReceivingDate'] = $report[$i]['lastorderdate'];
			 	$_SESSION['report']['items'][$i]['sales'] = $report[$i]['sales'];
			 	$_SESSION['report']['items'][$i]['VdrNo'] = $report[$i]['vdrno'];
			 	$_SESSION['report']['items'][$i]['VdrName'] = $report[$i]['vdrname'];
			 	$_SESSION['report']['items'][$i]['tpr'] = $report[$i]['tprprice'];
			 	$_SESSION['report']['items'][$i]['tprStart'] = $report[$i]['tprstart'];
			 	$_SESSION['report']['items'][$i]['tprEnd'] = $report[$i]['tprend'];
			 	$_SESSION['report']['items'][$i]['lastReceivingDate'] = $report[$i]['lastorderdate'];
			 	$_SESSION['report']['items'][$i]['expiration'] = $report[$i]['expiration'];
			 	$_SESSION['report']['items'][$i]['expiration_date'] = $report[$i]['expiration_date'];
			 	$_SESSION['report']['items'][$i]['lastReceivingDate'] = $report[$i]['lastorderdate'];
			 	$_SESSION['report']['items'][$i]['expiration'] = $report[$i]['expiration'];
			 	$_SESSION['report']['items'][$i]['order'] = $report[$i]['orderqty'];
			 	$_SESSION['report']['items'][$i]['SctNo'] = $report[$i]['SctNo'];
			 	$_SESSION['report']['items'][$i]['SctName'] = $report[$i]['SctName'];
			 	$_SESSION['report']['items'][$i]['DptNo'] = $report[$i]['DptNo'];
			 	$_SESSION['report']['items'][$i]['DptName'] = $report[$i]['DptName'];
			}
		}
		header('Location: /expiration/public/home');
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

	public function addItems()
	{
		foreach($_SESSION["report"]["items"] AS $key => $value)
		{
			$item = $this->brdata->get_item($value['UPC'], $this->today, $_SESSION["report"]["date_to"], $_SESSION["report"]["date_from"]);
			$item['order'] = $_SESSION["report"]["items"][$key]['order'];
			$item['expiration'] = $_SESSION["report"]["items"][$key]['expiration'];
			$item['expiration_date'] = $_SESSION["report"]["items"][$key]['expiration_date'];
			$_SESSION["report"]["items"][$key] = $item;
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

	public function update_itemValue()
	{
		$this->report->update_item($_POST['ident'], $_POST['name'], $_POST['value']);
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
								if(!empty($_SESSION['report']['id']))
								{
									// delete report items
									$this->report->delete_report_items($_SESSION['report']['id']);
									foreach($_SESSION['report']["items"] as $key => $value)
									{
										$saved_items[$key] = $this->report->save_item($value, $_SESSION['report']['id']);
									}
									$this->reset();
									// then save new report items
								}
								else
								{
									$report = $this->report->save_report($_SESSION['report']);
									if($report)
									{
										foreach($_SESSION['report']["items"] as $key => $value)
										{
											$saved_items[$key] = $this->report->save_item($value, $report);
										}
										$this->reset();
									}
									else
									{
										$_SESSION['error'] = "Something went wrong while saving the report. Please contact support";
									}
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

	public function delete_report($id)
	{
		$this->report->delete_report($id);
		$this->report->delete_report_items($id);

		header("Location:/expiration/public/reports");
	}

	public function delete_item($id, $report_id)
	{
		$this->report->delete_item($id);
		header("Location:/expiration/public/reports/single/".$report_id);
	}
}