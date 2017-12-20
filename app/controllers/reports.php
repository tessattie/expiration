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

	protected $received_status;

	public function __construct()
	{
		parent:: __construct();
		$this->today = date('Y-m-d', strtotime("-1 days"));
		if(empty($_SESSION['report']))
		{
			$_SESSION["report"] = array("name" => "", 
			"date_from" => date("Y-m-01"), 
			"date_to" => date("Y-m-d"), 
			"vendors" => 2,
			"addItems" => '',
			"type" => 0, 
			"items" => null);
		}
	}

	public function index()
	{
		if($_SESSION['orders']['role'] == 8){
			$reports = $this->report->get_reportsByUser($_SESSION['orders']['id']);
		}else{
			$reports = $this->report->get_reports();
		}
		$this->view('reports', array("reports" => $reports));
	}

	public function completeValue($val, $length){
		$total = $length;
		$value = '';
		$amount = strlen($val);
		$toadd = $total - (int)$amount;
		for($i=0;$i<$toadd;$i++){
			$value .= "0";
		}
		return $value.$val;
	}

	public function completeVendor($vendor){
		$total = 6;
		$value = '';
		$amount = strlen($vendor);
		$toadd = $total - (int)$amount;
		for($i=0;$i<$toadd;$i++){
			$value .= "0";
		}
		return $value.$vendor;
	}

	public function exportVendorReport(){
		$items = $this->brdata->get_LimitedVendorReport("000031", $this->today);
		// print_r($items); 
		// die();
	}

	public function importVendor()
	{
		if(isset($_POST["vendorNumber"]))
		{
			$_POST["vendorNumber"] = $this->completeVendor($_POST["vendorNumber"]);
			unset($_SESSION["report"]["items"]);
			if(empty($_SESSION['orders']['vendors'])){
				$_SESSION['orders']['vendors'] = array();
			}
			if(in_array($_POST["vendorNumber"], $_SESSION['orders']['vendors']) || $_SESSION['orders']['role'] != 8){
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
		}
		header('Location: /orders/public/home');

	}

	public function importSection()
	{
		if(isset($_POST["sectionNumber"]))
		{
			unset($_SESSION["report"]["items"]);
			$_POST["sectionNumber"] = $this->completeValue($_POST["sectionNumber"], 4);
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
			$_POST["svendorNumber"] = $this->completeValue($_POST["svendorNumber"], 6);
			$_POST["sctvendorNumber"] = $this->completeValue($_POST["sctvendorNumber"], 6);
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
		$_POST["upc"] = $this->completeUPC($_POST["upc"]);
		if(!empty($_POST['upc'])){
					$items = $this->brdata->get_itemAllVendors($_POST['upc'], $this->today, $_SESSION["report"]["date_to"], $_SESSION["report"]["date_from"]);
		if($_SESSION['orders']['role'] == 8){
				$items = $this->returnRightItem($items);
				// debug($items);
				// die();
				if($items){
					$items['order'] = null;
					$items['expiration'] = null;
					$items['expiration_date'] = null;
					$_SESSION["report"]["items"][$items["UPC"]] = $items;
				}
			}else{
				$items = $this->returnItemWithCheapestVendor($items);
				if(!empty($items)){
					$items['order'] = null;
					$items['expiration'] = null;
					$items['expiration_date'] = null;
					$_SESSION["report"]["items"][$items["UPC"]] = $items;
				}
				else
				{
					$items['UPC'] = $_POST["newitem"];
					$items['ItemDescription'] = "ITEM NOT FOUND";
					$items['VdrNo'] = null;
					$items['Retail'] = null;
					$items['CertCode'] = null;
					$items['CaseCost'] = null;
					$items['Brand'] = null;
					$items['SizeAlpha'] = null;
					$items['SctNo'] = "00";
					$items['SctName'] = "N/A";
					$items['DptNo'] = null;
					$items['DptName'] = null;
					$items['Pack'] = null;
					$items['VdrName'] = null;
					$items['tpr'] = null;
					$items['tprStart'] = null;
					$items['tprEnd'] = null;
					$items['sales'] = null;
					$items['lastReceiving'] = null;
					$items['lastReceivingDate'] = null;
					$items['onhand'] = null;
					$items['unitPrice'] = null;
					$items['order'] = null;
					$items['expiration'] = null;
					$items['expiration_date'] = null;
					$_SESSION["report"]["items"][$_POST["newitem"]] = $items;
				}
			}	
		}

		echo json_encode($_SESSION["report"]); die();
	}

	public function single($id = false, $anchor = false, $upc = false)
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
		$this->view('reports/single', array("report" => $report, "anchor" => $anchor, "upcPriceCompare" => $upcPriceCompare, "report_id" => $id, "upc" => $upc));
	}

	public function singleSection($id = false, $anchor = false, $upc = false)
	{
		$upcPriceCompare = false;
		if($id == false)
		{
			header('Location: /orders/public/reports');
		}
		$name = $this->report->getReportName($id);
		$report = $this->report->get_report2($id);
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
		$this->view('reports/singleSection', array("report" => $report, "anchor" => $anchor, "upcPriceCompare" => $upcPriceCompare, "report_id" => $id, "upc" => $upc));
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
		$_SESSION["report"]["vendors"] = $_POST["vendors"];
		echo "Data saved !";
		die();
	}

	public function completeUPC($upc){
		$total = 15;
		$value = '';
		$amount = strlen($upc);
		$toadd = $total - (int)$amount;
		for($i=0;$i<$toadd;$i++){
			$value .= "0";
		}
		return $value.$upc;
	}

	// 000007447101715	

	public function add_item()
	{
		if(isset($_POST["newitem"]))
		{
			$_POST["newitem"] = $this->completeUPC($_POST["newitem"]);
			$items = $this->brdata->get_itemAllVendors($_POST["newitem"], $this->today, $_SESSION["report"]["date_to"], $_SESSION["report"]["date_from"]);
			if($_SESSION['orders']['role'] == 8){
				$items = $this->returnRightItem($items);
				// debug($items);
				// die();
				if($items){
					$items['order'] = null;
					$items['expiration'] = null;
					$items['expiration_date'] = null;
					$_SESSION["report"]["items"][$items["UPC"]] = $items;
				}
			}else{
				$items = $this->returnItemWithCheapestVendor($items);
				if(!empty($items)){
					$items['order'] = null;
					$items['expiration'] = null;
					$items['expiration_date'] = null;
					$_SESSION["report"]["items"][$items["UPC"]] = $items;
				}
				else
				{
					$items['UPC'] = $_POST["newitem"];
					$items['ItemDescription'] = "ITEM NOT FOUND";
					$items['VdrNo'] = null;
					$items['Retail'] = null;
					$items['CertCode'] = null;
					$items['CaseCost'] = null;
					$items['Brand'] = null;
					$items['SizeAlpha'] = null;
					$items['SctNo'] = "00";
					$items['SctName'] = "N/A";
					$items['DptNo'] = null;
					$items['DptName'] = null;
					$items['Pack'] = null;
					$items['VdrName'] = null;
					$items['tpr'] = null;
					$items['tprStart'] = null;
					$items['tprEnd'] = null;
					$items['sales'] = null;
					$items['lastReceiving'] = null;
					$items['lastReceivingDate'] = null;
					$items['onhand'] = null;
					$items['unitPrice'] = null;
					$items['order'] = null;
					$items['expiration'] = null;
					$items['expiration_date'] = null;
					$_SESSION["report"]["items"][$_POST["newitem"]] = $items;
				}
			}			
		}
		header('Location: /orders/public/home');
	}

	public function returnItemWithCheapestVendor($items){
		$cheapest = array();
		for($i=0;$i<count($items);$i++){
			if($i==0){
				$cheapest = $items[$i];
			}else{
				if($cheapest['lastReceivingDate'] < $items[$i]['lastReceivingDate']){
					$cheapest = $items[$i];
				}
			}
		}
		return $cheapest;
	}

	public function returnRightItem($items){
		for($i=0;$i<count($items);$i++){
			if(in_array(trim($items[$i]['VdrNo']), $_SESSION['orders']['vendors'])){
				return $items[$i];
			}
		}
		return false;
	}

	public function addItems()
	{
		foreach($_SESSION["report"]["items"] AS $key => $value)
		{
			$items = $this->brdata->get_itemAllVendors($value['UPC'], $this->today, $_SESSION["report"]["date_to"], $_SESSION["report"]["date_from"]);
			if($_SESSION['orders']['role'] == 8){
				$items = $this->returnRightItem($items);
				// debug($items);
				// die();
				if($items){
					$items['order'] = null;
					$items['expiration'] = null;
					$items['expiration_date'] = null;
					$_SESSION["report"]["items"][$items["UPC"]] = $items;
				}
			}else{
				$items = $this->returnItemWithCheapestVendor($items);
				if(!empty($items)){
					$items['order'] = null;
					$items['expiration'] = null;
					$items['expiration_date'] = null;
					$_SESSION["report"]["items"][$items["UPC"]] = $items;
				}
				else
				{
					$items['UPC'] = $value['UPC'];
					$items['ItemDescription'] = "ITEM NOT FOUND";
					$items['VdrNo'] = null;
					$items['Retail'] = null;
					$items['CertCode'] = null;
					$items['CaseCost'] = null;
					$items['Brand'] = null;
					$items['SizeAlpha'] = null;
					$items['SctNo'] = "00";
					$items['SctName'] = "N/A";
					$items['DptNo'] = null;
					$items['DptName'] = null;
					$items['Pack'] = null;
					$items['VdrName'] = null;
					$items['tpr'] = null;
					$items['tprStart'] = null;
					$items['tprEnd'] = null;
					$items['sales'] = null;
					$items['lastReceiving'] = null;
					$items['lastReceivingDate'] = null;
					$items['onhand'] = null;
					$items['unitPrice'] = null;
					$items['order'] = null;
					$items['expiration'] = null;
					$items['expiration_date'] = null;
					$_SESSION["report"]["items"][$value['UPC']] = $items;
				}
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
		$this->report->update_itemStatus($_POST['ident'], 2);
		$this->report->update_reportStatus($item['report_id'], 2);
		$this->updateOrderItemValueLog($name, $item['report_id'], $_POST['name'], $item[$_POST['name']], $_POST['value'], $_POST['ident'], $item['upc'], $item['description']);
		$this->report->update_item($_POST['ident'], $_POST['name'], $_POST['value']);
		echo json_encode($_POST);
		die();
	}

	public function removeItem($upc,$next)
	{
		if(!empty($_SESSION['report']['items'][$upc]))
		{
			unset($_SESSION['report']['items'][$upc]);
		}
		header('Location: /orders/public/home/#'.$next);
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

	public function delete_item($id, $report_id, $anchor = null)
	{
		$item = $this->report->getItem($id);
		$this->report->delete_item($id);
		$name = $this->report->getReportName($report_id);
		$this->deleteOrderItemLog($name, $report_id, $id, $item['upc'], $item['description']);
		header("Location:/orders/public/reports/single/".$report_id."/".$anchor);
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
				    	$qty = null;
				    	$upc = $sheet->getCell("A".$i)->getValue();
				    	if(!empty($sheet->getCell("B".$i)->getValue())){
				    		$qty = $sheet->getCell("B".$i)->getValue();
				    	}
				    	$upc = $this->completeUPC($upc);
				    	$items = $this->brdata->get_itemAllVendors($upc, $this->today, $_SESSION["report"]["date_to"], $_SESSION["report"]["date_from"]);
				    	if($_SESSION['orders']['role'] == 8){
							$items = $this->returnRightItem($items);
							// debug($items);
							// die();
							if($items){
								$items['order'] = $sheet->getCell("B".$i)->getValue();
								$items['expiration'] = null;
								$items['expiration_date'] = null;
								$_SESSION["report"]["items"][$items["UPC"]] = $items;
							}
						}else{
							$items = $this->returnItemWithCheapestVendor($items);
							if(!empty($items)){
								$items['order'] = $sheet->getCell("B".$i)->getValue();
								$items['expiration'] = null;
								$items['expiration_date'] = null;
								$_SESSION["report"]["items"][$items["UPC"]] = $items;
							}
							else
							{
								$items['UPC'] = $upc;
								$items['ItemDescription'] = "ITEM NOT FOUND";
								$items['VdrNo'] = null;
								$items['Retail'] = null;
								$items['CertCode'] = null;
								$items['CaseCost'] = null;
								$items['Brand'] = null;
								$items['SizeAlpha'] = null;
								$items['SctNo'] = "00";
								$items['SctName'] = "N/A";
								$items['DptNo'] = null;
								$items['DptName'] = null;
								$items['Pack'] = null;
								$items['VdrName'] = null;
								$items['tpr'] = null;
								$items['tprStart'] = null;
								$items['tprEnd'] = null;
								$items['sales'] = null;
								$items['lastReceiving'] = null;
								$items['lastReceivingDate'] = null;
								$items['onhand'] = null;
								$items['unitPrice'] = null;
								$items['order'] = null;
								$items['expiration'] = null;
								$items['expiration_date'] = null;
								$_SESSION["report"]["items"][$upc] = $items;
							}
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