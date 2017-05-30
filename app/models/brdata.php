<?php

class brdata extends Model{

	protected $db;

	public function __construct()
	{
		parent::__construct();
	}

	public function get_item($upcNumber, $today, $to, $from)
	{
		$upc = "4900000217";
		var_dump($upc);
		var_dump($upcNumber);

		$SQL ="SELECT DISTINCT TOP 1 vc.UPC, vc.Vendor AS VdrNo, p.BasePRice AS Retail, vc.VendorItem AS CertCode, vc.CaseCost, i.Brand, i.Description AS ItemDescription,
				i.SizeAlpha, i.Department AS SctNo, i.MajorDept as DptNo, d.Description AS SctName, md.Description AS DptName, vc.Pack, v.VendorName AS VdrName, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC AND id.Vendor=vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R'  AND id.Vendor=vc.Vendor AND id.UPC=vc.UPC AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC   AND id.Vendor=vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' AND id.Date >='".$today."' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT TOP 1 Units FROM dbo.InventoryDetail WHERE RecordType = 'A' AND Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC AND id.Date >='".$today."' ORDER BY Date DESC) AND UPC= p.UPC ORDER BY Date DESC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > '".$today."' AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND Date > '".$today."' AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.VendorCost vc
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Item i ON i.UPC = vc.UPC 
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				INNER JOIN dbo.Vendors v ON v.Vendor = vc.Vendor 
				LEFT JOIN dbo.InventoryDetail id ON id.UPC = vc.UPC
				WHERE vc.UPC LIKE '%".$upcNumber."'
				ORDER BY lastReceivingDate DESC;";

		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetch(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_upcReport($upcNumber, $today, $to, $from)
	{
		$SQL ="SELECT DISTINCT vc.UPC, vc.Vendor AS VdrNo, p.BasePRice AS Retail, vc.VendorItem AS CertCode, vc.CaseCost, i.Brand, i.Description AS ItemDescription,
				i.SizeAlpha, vc.Pack, v.VendorName AS VdrName, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC AND id.Vendor=vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R'  AND id.Vendor=vc.Vendor AND id.UPC=vc.UPC AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC   AND id.Vendor=vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' AND id.Date >='".$today."' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT TOP 1 Units FROM dbo.InventoryDetail WHERE RecordType = 'A' AND Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC AND id.Date >='".$today."' ORDER BY Date DESC) AND UPC= p.UPC ORDER BY Date DESC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > '".$today."' AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND Date > '".$today."' AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.VendorCost vc
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Item i ON i.UPC = vc.UPC 
				INNER JOIN dbo.Vendors v ON v.Vendor = vc.Vendor 
				LEFT JOIN dbo.InventoryDetail id ON id.UPC = vc.UPC
				WHERE vc.UPC LIKE '%".$upcNumber."'
				ORDER BY unitPrice, vc.CaseCost;";

		// Execute query
		$results = $this->db->query($SQL);
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}
}