<?php
session_start();
class reports extends Controller{

	protected $brdata;

	protected $users;

	protected $today;

	protected $report;

	private $phpExcel;
		
	private $sheet;
		
	private $columns;

	private $columnWidths;

	private $cell_border;

	private $cacheMethod;

	public function __construct()
	{
		parent:: __construct();
		$this->today = date('Y-m-d', strtotime("-1 days"));
		if(empty($_SESSION['report']))
		{
			$_SESSION["report"] = array("name" => "", 
			"date_from" => date("Y-m-01"), 
			"date_to" => date("Y-m-d"), 
			"addItems" => '',
			"type" => 0, 
			"items" => null);
		}
	}

	public function index()
	{
		$reports = $this->report->get_reports();
		$this->view('reports', array("reports" => $reports));
	}

	public function importVendor()
	{
		if(isset($_POST["vendorNumber"]))
		{
			unset($_SESSION["report"]["items"]);
			$items = $this->brdata->get_vendorReport($_POST["vendorNumber"], $this->today, $_SESSION["report"]["date_from"], $_SESSION["report"]["date_to"]);
			for($i=0;$i<count($items);$i++)
			{
				$items[$i]['order'] = null;
				$items[$i]['expiration'] = null;
				$items[$i]['expiration_date'] = null;
				$_SESSION["report"]["items"][$items[$i]["UPC"]] = $items[$i];
			}
			$_SESSION["report"]['name']  = "[ " . $items[0]['VdrNo'] . " - " . $items[0]['VdrName'] . " ]";
			$_SESSION["report"]['addItems'] = 'disabled';
			$_SESSION["report"]['type'] = 1;
		}
		header('Location: /orders/public/home');

	}

	public function importSection()
	{
		if(isset($_POST["sectionNumber"]))
		{
			unset($_SESSION["report"]["items"]);
			$items = $this->brdata->get_sectionReport($_POST["sectionNumber"], $this->today, $_SESSION["report"]["date_from"], $_SESSION["report"]["date_to"]);
			for($i=0;$i<count($items);$i++)
			{
				$items[$i]['order'] = null;
				$items[$i]['expiration'] = null;
				$items[$i]['expiration_date'] = null;
				$_SESSION["report"]["items"][$items[$i]["UPC"]] = $items[$i];
			}
			$_SESSION["report"]['name']  = "[ " . $items[0]['SctNo'] . " - " . $items[0]['SctName'] . " ]";
			$_SESSION["report"]['addItems'] = 'disabled';
			$_SESSION["report"]['type'] = 2;
		}
		header('Location: /orders/public/home');

	}

	public function importVendorSection()
	{
		if(isset($_POST["svendorNumber"]) && isset($_POST["sctvendorNumber"]))
		{
			unset($_SESSION["report"]["items"]);
			$items = $this->brdata->get_vendorSectionReport($_POST["svendorNumber"], $_POST["sctvendorNumber"], $this->today, $_SESSION["report"]["date_to"], $_SESSION["report"]["date_from"]);
			for($i=0;$i<count($items);$i++)
			{
				$items[$i]['order'] = null;
				$items[$i]['expiration'] = null;
				$items[$i]['expiration_date'] = null;
				$_SESSION["report"]["items"][$items[$i]["UPC"]] = $items[$i];
			}
			$_SESSION["report"]['name']  = "[ " . $items[0]['VdrNo'] . " - " . $items[0]['VdrName'] . " ] - [ " . $items[0]['SctNo'] . " - " . $items[0]['SctName'] . " ]";
			$_SESSION["report"]['addItems'] = 'disabled';
			$_SESSION["report"]['type'] = 3;
		}
		header('Location: /orders/public/home');

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
			header('Location: /orders/public/reports');
		}
		$name = $this->report->getReportName($id);
		$report = $this->report->get_report($id);
		if($upc != false)
		{
			$upcPriceCompare = $this->brdata->get_upcReport($upc, $this->today, $report[0]['date_from'], $report[0]['date_to']);
			$this->UPCPriceCompareLog($name, $id, $upc);
		}
		else
		{
			$this->orderConsultLog($name, $id);
		}
		if(count($report) == 0)
		{
			$this->report->delete_report($id);
			header('Location: /orders/public/home');
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
			$this->editOrderLog($report[0]['name'], $id);
			$_SESSION['report']['id'] = $report[0]['report_id'];
			$_SESSION['report']['name'] = $report[0]['name'];
			$_SESSION['report']['type'] = $report[0]['type'];
			$_SESSION['report']['date_from'] = $report[0]['date_from'];
			$_SESSION['report']['date_to'] = $report[0]['date_to'];
			for($i=0;$i<count($report);$i++)
			{
			 	$_SESSION['report']['items'][$report[$i]['upc']]['UPC'] = $report[$i]['upc'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['CertCode'] = $report[$i]['itemcode'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['ItemDescription'] = $report[$i]['description'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['Pack'] = $report[$i]['pack'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['SizeAlpha'] = $report[$i]['size'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['Brand'] = $report[$i]['brand'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['CaseCost'] = $report[$i]['casecost'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['Retail'] = $report[$i]['retail'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['onhand'] = $report[$i]['onhand'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['lastReceiving'] = $report[$i]['lastorder'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['lastReceivingDate'] = $report[$i]['lastorderdate'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['sales'] = $report[$i]['sales'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['VdrNo'] = $report[$i]['vdrno'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['VdrName'] = $report[$i]['vdrname'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['tpr'] = $report[$i]['tprprice'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['tprStart'] = $report[$i]['tprstart'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['tprEnd'] = $report[$i]['tprend'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['lastReceivingDate'] = $report[$i]['lastorderdate'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['expiration'] = $report[$i]['expiration'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['expiration_date'] = $report[$i]['expiration_date'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['lastReceivingDate'] = $report[$i]['lastorderdate'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['expiration'] = $report[$i]['expiration'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['order'] = $report[$i]['orderqty'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['SctNo'] = $report[$i]['SctNo'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['SctName'] = $report[$i]['SctName'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['DptNo'] = $report[$i]['DptNo'];
			 	$_SESSION['report']['items'][$report[$i]['upc']]['DptName'] = $report[$i]['DptName'];
			}
		}
		header('Location: /orders/public/home');
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
			$this->duplicateOrderLog($report[0]['name'], $id);
			$_SESSION['report']['date_from'] = $report[0]['date_from'];
			$_SESSION['report']['type'] = $report[0]['type'];
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
		header('Location: /orders/public/home');
	}



	public function reset()
	{
		unset($_SESSION['report']);
		header('Location: /orders/public/home');
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
		header('Location: /orders/public/home');
	}

	public function addItems()
	{
		foreach($_SESSION["report"]["items"] AS $key => $value)
		{
			$item = $this->brdata->get_item($value['UPC'], $this->today, $_SESSION["report"]["date_to"], $_SESSION["report"]["date_from"]);
			if($item != null)
	    	{
	    		$item['order'] = null;
				$item['expiration'] = null;
				$item['expiration_date'] = null;
				$_SESSION["report"]["items"][$item["UPC"]] = $item;
	    	}
	    	else
	    	{
	    		$item['UPC'] = $value['UPC'];
				$item['ItemDescription'] = "ITEM NOT FOUND";
				$item['VdrNo'] = null;
				$item['Retail'] = null;
				$item['CertCode'] = null;
				$item['CaseCost'] = null;
				$item['Brand'] = null;
				$item['SizeAlpha'] = null;
				$item['SctNo'] = "00";
				$item['SctName'] = "N/A";
				$item['DptNo'] = null;
				$item['DptName'] = null;
				$item['Pack'] = null;
				$item['VdrName'] = null;
				$item['tpr'] = null;
				$item['tprStart'] = null;
				$item['tprEnd'] = null;
				$item['sales'] = null;
				$item['lastReceiving'] = null;
				$item['lastReceivingDate'] = null;
				$item['onhand'] = null;
				$item['unitPrice'] = null;
				$item['order'] = null;
				$item['expiration'] = null;
				$item['expiration_date'] = null;
				$_SESSION["report"]["items"][$value['UPC']] = $item;
	    	}
		}
		header('Location: /orders/public/home');
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
		$item = $this->report->getItem($_POST['ident']);
		$name = $this->report->getReportName($item['report_id']);
		$this->updateOrderItemValueLog($name, $item['report_id'], $_POST['name'], $item[$_POST['name']], $_POST['value'], $_POST['ident'], $item['upc'], $item['description']);
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
		header('Location: /orders/public/home');
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
									if($_SESSION['report']['type'] == null){
										$_SESSION['report']['type'] = 0;
									}
									$report = $this->report->save_report($_SESSION['report']);
									$this->saveOrderLog($_SESSION['report']["name"], $report);
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
		header("Location:/orders/public/home");
	}

	public function reset_error()
	{
		unset($_SESSION['error']);
		die();
	}

	public function delete_report($id)
	{
		$name = $this->report->getReportName($id);
		$this->deleteOrderLog($name, $id);
		$this->report->delete_report($id);
		$this->report->delete_report_items($id);

		header("Location:/orders/public/reports");
	}

	public function delete_item($id, $report_id)
	{
		$item = $this->report->getItem($id);
		$this->report->delete_item($id);
		$name = $this->report->getReportName($report_id);
		$this->deleteOrderItemLog($name, $report_id, $id, $item['upc'], $item['description']);
		header("Location:/orders/public/reports/single/".$report_id);
	}

	public function addExcel(){
		if(isset($_FILES))
		{
			if($_FILES['upcs']['error'] == 0)
			{
				$extension = explode('.',$_FILES['upcs']['name'])[1];
				if($extension == "xlsx" || $extension == "xls")
				{
					$objPHPExcel = $this->phpExcelFactory($_FILES['upcs']['tmp_name']);
					$sheet = $objPHPExcel->getSheet(0);
				    $highestRow = $sheet->getHighestRow();
				    $range = 'A1:A'.$highestRow;
					$objPHPExcel->getActiveSheet()
					    ->getStyle($range)
					    ->getNumberFormat()
					    ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
					$_SESSION["report"]['type'] = 4;
				    for($i=1;$i<=$highestRow;$i++)
				    {
				    	$upc = $sheet->getCell("A".$i)->getValue();
				    	$item = $this->brdata->get_item($upc, $this->today, $_SESSION["report"]["date_to"], $_SESSION["report"]["date_from"]);
				    	if($item != null)
				    	{
				    		$item['order'] = null;
							$item['expiration'] = null;
							$item['expiration_date'] = null;
							$_SESSION["report"]["items"][$item["UPC"]] = $item;
				    	}
				    	else
				    	{
				    		$item['UPC'] = $upc;
							$item['ItemDescription'] = "ITEM NOT FOUND";
							$item['VdrNo'] = null;
							$item['Retail'] = null;
							$item['CertCode'] = null;
							$item['CaseCost'] = null;
							$item['Brand'] = null;
							$item['SizeAlpha'] = null;
							$item['SctNo'] = "00";
							$item['SctName'] = "N/A";
							$item['DptNo'] = null;
							$item['DptName'] = null;
							$item['Pack'] = null;
							$item['VdrName'] = null;
							$item['tpr'] = null;
							$item['tprStart'] = null;
							$item['tprEnd'] = null;
							$item['sales'] = null;
							$item['lastReceiving'] = null;
							$item['lastReceivingDate'] = null;
							$item['onhand'] = null;
							$item['unitPrice'] = null;
							$item['order'] = null;
							$item['expiration'] = null;
							$item['expiration_date'] = null;
							$_SESSION["report"]["items"][$upc] = $item;
				    	}
				    }
				}
				else
				{
				// extension error
				}
			}
			else
			{
				// file upload errors - file is deprecated
			}
		}
		// var_dump($_SESSION['report']); die();
		header("Location:/orders/public/home");
	}

	public function close($id = false, $status = 0)
	{
		if($id == false){
			header("Location:/orders/public/reports");
		}
		$this->report->updateStatus($id, $status);
		$name = $this->report->getReportName($id);
		$this->closeOrderLog($name, $id, $status);
		header("Location:/orders/public/reports/single/".$id);
	}
}