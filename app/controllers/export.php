<?php
session_start();
class export extends Controller{
	
	protected $users;

	protected $report;

	private $phpExcel;
	
	protected $brdata;
	
	private $sheet;
		
	private $today;

	private $columns;

	private $columnWidths;

	private $cell_border;

	private $cacheMethod;


	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('America/Port-au-Prince');
		$this->today = date('Y-m-d', strtotime("-1 days"));
		// $this->cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory; 
		$this->phpExcel = $this->phpExcel();
		$this->phpExcel->createSheet();
		$this->sheet = $this->phpExcel->getActiveSheet();
		$this->users = $this->model('users');
		$this->brdata = $this->model('brdata');
		$this->today = date('Y-m-d', strtotime("-1 days"));
		$this->columnWidths = array("UPC" => 13, "VDR ITEM #" => 11, "BRAND" => 8, "ITEM DESCRIPTION" => 30, "PACK" => 6, "SIZE" => 8, 
			"CASE COST" => 10, "RETAIL" => 7, "ON-HAND" => 8, "LAST ORDER" => 12, "LAST ORDER DATE" => 15, "SALES" => 5, "VDR #" => 7, "VDR NAME" => 22, 
			"TPR PRICE" => 7, "TPR START" => 8, "TPR END" => 8, "SCT NO" => 8, "SCT NAME" => 30, "DPT NO" => 8, "DPT NAME" => 30, "UNIT PRICE" => 10, "EXP QTY" => 8, "EXP DATE" => 15, "ORDER" => 8);
		$this->columns = array("UPC" => "upc", "VDR ITEM #" => "itemcode", "BRAND" => "brand", "ITEM DESCRIPTION" => "description", "PACK" => "pack", "SIZE" => "size", "CASE COST" => "casecost", "RETAIL" => "retail", 
			"ON-HAND" => "onhand", "LAST ORDER" => "lastorder", "LAST ORDER DATE" => "lastorderdate", "SALES" => "sales", "VDR #" => "vdrno", "VDR NAME" => "vdrname", "TPR PRICE" => "tprprice", "TPR START" => "tprstart", 
			"TPR END" => "tprend", "SCT NO" => "SctNo", "SCT NAME" => "SctName", "DPT NO" => "DptNo", "DPT NAME" => "DptName", "EXP QTY" => "expiration", "EXP DATE" => "expiration_date", "ORDER" => "orderqty");
	} 

	public function reportExport($id)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H" => "RETAIL", 
						"I" => "ORDER", 
						"J" => "EXP DATE",
						"K" => "EXP QTY",
						"L" => "ON-HAND",
						"M" => "LAST ORDER", 
						"N" => "LAST ORDER DATE", 
						"O" => "SALES", 
						"P" => "TPR PRICE", 
						"Q" => "TPR START", 
						"R" => "TPR END",
						"S" => "VDR #", 
						"T" => "VDR NAME");
		$report = $this->report->get_report($id);
		$this->setSheetName($report[0]['name']);
		$this->exportOrderLog($report[0]['name'], $id);
		$lastItem = count($report) + 4;
		$this->setHeader($report[0]['name'],"[ EXPORT DATE : ".date("Y-m-d")." ] - [ SALES FROM ".$report[0]['date_from']." TO ".$report[0]['date_to']." ] ", $header, 'reportExport', $lastItem);
		$this->setReportWithSection($header, $report);
		$this->saveReport('orders_'.$report[0]['name'].'_'.$this->today);
	}

	private function setSheetName($sheetName)
	{
		$this->sheet->Name = $sheetName;
	}

	private function getItemDescriptionColumn($header)
	{
		$returnValue = '';
		foreach($header as $key => $value)
		{
			$returnValue = $key;
			if($value == "ITEM DESCRIPTION")
			{
				break;
			}
		}
		return $returnValue;
	}

	private function setHeader($title, $subtitle, $header, $reportType, $lastItem)
	{
		$myWorkSheet = new PHPExcel_Worksheet($this->phpExcel, $reportType); 
		// Attach the “My Data” worksheet as the first worksheet in the PHPExcel object 
		$lastKey = $this->getLastArrayKey($header);
		$this->phpExcel->addSheet($myWorkSheet, 0);
		// Set report to landscape 
		$this->phpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

		$this->sheet->mergeCells('A1:' . $lastKey . '1');
		$this->sheet->mergeCells('A2:' . $lastKey . '2');
		$this->sheet->getRowDimension('1')->setRowHeight(35);
		$this->sheet->setCellValue('A1', $title);
		$this->sheet->setCellValue('A2', $subtitle);
		$this->sheet->getStyle('A1:' . $lastKey . '3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->sheet->getRowDimension('2')->setRowHeight(25);
		$this->sheet->getRowDimension('3')->setRowHeight(25);
		$this->sheet->getStyle('A1:' . $lastKey . '3')->getFont()->setBold(true);
		$this->sheet->getStyle('A1:' . $lastKey . '1')->getFont()->setSize(14);
		$this->sheet->getStyle('A1:' . $lastKey . '3') ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$this->sheet->getPageMargins()->setRight(0); 
		$this->sheet->getPageMargins()->setLeft(0);
		$this->sheet->getPageMargins()->setTop(0); 
		$this->sheet->getPageMargins()->setBottom(0);
		$this->sheet->getPageSetup()->setFitToWidth(1);
		$this->sheet->getPageSetup()->setFitToHeight(0);  
		// $this->sheet->getPageSetup()->setPrintArea("A1:" . $lastKey . $lastItem);  

		$this->phpExcel->getProperties()->setCreator("Tess Attie"); 
		$this->phpExcel->getProperties()->setLastModifiedBy("Today"); 
		$this->phpExcel->getProperties()->setTitle($title); 
		$this->phpExcel->getProperties()->setSubject("Office 2005 XLS Test Document"); 
		$this->phpExcel->getProperties()->setDescription("Test document for Office 2005 XLS, generated using PHP classes."); 
		$this->phpExcel->getProperties()->setKeywords("office 2007 openxml php"); 
		$this->phpExcel->getProperties()->setCategory("Test result file");

		$this->phpExcel->getActiveSheet()
		    ->getHeaderFooter()->setOddHeader('&R &P / &N');
		$this->phpExcel->getActiveSheet()
		    ->getHeaderFooter()->setEvenHeader('&R &P / &N');

		foreach($header AS $key => $value)
		{
			if($value == "UPC")
			{
				$this->sheet->getColumnDimension($key)->setWidth('15');
			}
			else
			{
				if($value == "VDR ITEM #")
				{
					$this->sheet->getColumnDimension($key)->setWidth('10');
				}
				else
				{
					if($value == "VDR NAME")
					{
						$this->sheet->getColumnDimension($key)->setWidth('22');
					}
					else
					{
						if($value == "ITEM DESCRIPTION")
						{
							$this->sheet->getColumnDimension($key)->setWidth('26');
						}
						else
						{
							if($value == "TPR END")
							{
								$this->sheet->getColumnDimension($key)->setWidth('8');
							}
						}
					}
				}
				
			}
			$this->sheet->setCellValue($key."3", $value);
		}
	}


	private function getLastArrayKey($header)
	{
		$last = "A";
		foreach($header as $key => $value)
		{
			$last = $key;
		}
		return $last;
	}

	private function setReportWithSection($header, $report)
	{
		$j = 4;
		$lastKey = $this->getLastArrayKey($header);
		$alphabet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
		$start = $alphabet[array_search($this->getItemDescriptionColumn($header), $alphabet) - 1];
		$current =  $this->getItemDescriptionColumn($header);
		$finish = $alphabet[array_search($this->getItemDescriptionColumn($header), $alphabet) + 1];
		$increment = 0;
		$condition = 'ht';
		$vdrcondition = 'vd';
		for ($i=0; $i<count($report); $i++)
		{
			if($increment == 0 || $condition != $report[$i]["SctNo"] || $vdrcondition != $report[$i]["vdrno"])
			{
				$this->sheet->mergeCells('A' . $j . ':' . $start . $j);
				$this->sheet->setCellValue($current . $j, $report[$i]['SctNo'].' - '.$report[$i]['SctName']);
				$condition = $report[$i]["SctNo"];
				$vdrcondition = $report[$i]["vdrno"];
				$this->sheet->getStyle($current . $j)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$this->sheet->getStyle($current . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->sheet->getStyle($current . $j)->getFont()->setBold(true);
				$this->sheet->mergeCells($finish . $j . ':' . $this->getLastArrayKey($header) . $j);
				$this->phpExcel->getActiveSheet()
				    ->getStyle($current . $j)
				    ->getFill()
				    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
				    ->getStartColor()
				    ->setARGB('FFE0E0E0');
				$j = $j + 1;
			}
			foreach($header as $key => $value)
			{
				$this->sheet->getStyle($key . $j) ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				if($this->columns[$value] != "UPC")
				{
					if(($value == "TPR PRICE" && $report[$i]["tprprice"] == ".00")
					|| ($value == "TPR START" && $report[$i]["tprprice"] == ".00") 
					|| ($value == "TPR END" && $report[$i]["tprprice"] == ".00"))
					{
						if($value == "TPR PRICE")
				        {
				        	$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
				        }
						$this->sheet->setCellValue($key . $j, " ");
					}
					else
					{
						if($value == "CASE COST")
				        {
				        	$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
				        	$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
				        }
				        else
				        {
				        	if($value == "UNIT PRICE")
				        	{
				        		$this->sheet->getStyle($key . $j)->getFont()
							    ->getColor()->setRGB('0066CC');
				        		$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
				        		$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
				        	}
				        	else
				        	{
				        		if($value == "RETAIL" || $value == "CASE COST")
						        {
						        	$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
						        }
				        		$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
				        	}
				        }
					}
				}
		        else
		        {
		        	$this->sheet->getStyle($key . $j)->getNumberFormat()->setFormatCode('0000000000000');
		        	$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
		        }
		        if($this->columns[$value] == "CertCode")
				{
					$this->sheet->setCellValue($key . $j, trim($report[$i][$this->columns[$value]]));
				}
		        if($value != "ITEM DESCRIPTION")
		        {
		        	$this->sheet->getStyle($key . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		        }
		        if($value == "ON-HAND")
		        {
		        	if($report[$i][$this->columns[$value]] < 0)
		        	{
		        		$this->sheet->getStyle($key . $j)->getFont()
					    ->getColor()->setRGB('FF0000');
		        		$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
		        	}
		        }
			} 
			$j = $j + 1;
			$increment = 1;
		}
		$j = $j - 1;
		$this->sheet->getStyle('A3:'.$lastKey.$j)->getFont()->setSize(8);
		$styleArray = array( 'borders' => array( 'allborders' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'), ), ), ); 
		$this->phpExcel->getActiveSheet()->getStyle('A1:'.$lastKey.$j)->applyFromArray($styleArray);
	}

	private function SaveReport($documentName)
	{
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="' . $documentName . '.xls"'); 
		header('Cache-Control: max-age=0'); $objWriter = PHPExcel_IOFactory::createWriter($this->phpExcel, 'Excel5'); 
		$objWriter->save('php://output');
	}
}