<?php

class brdata extends Model{

	protected $db;

	public function __construct()
	{
		parent::__construct();
	}

	public function get_item($upcNumber, $today, $to, $from)
	{
		$upcNumber = "".$upcNumber;
		$SQL ="SELECT DISTINCT TOP 1 vc.UPC, p.BasePRice AS Retail, vc.VendorItem AS CertCode, vc.CaseCost, i.Brand, i.Description AS ItemDescription,
				i.SizeAlpha, vc.Pack, v.VendorName AS VdrName, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd, i.Department AS SctNo, d.Description AS SctName,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				(SELECT TOP 1 Vendor FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC ORDER BY id.LastUpdated DESC, id.Date DESC) AS VdrNo,
				(SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.VendorCost vc
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Item i ON i.UPC = vc.UPC 
				INNER JOIN dbo.Vendors v ON v.Vendor = (SELECT TOP 1 Vendor FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC ORDER BY id.LastUpdated DESC, id.Date DESC)
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				LEFT JOIN dbo.InventoryDetail id ON id.UPC = vc.UPC
				WHERE vc.UPC = '".$upcNumber."'
				ORDER BY lastReceivingDate, vc.CaseCost;";

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
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated >= (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
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

	public function get_LastReceivingReport($upcNumber)
	{
		$SQL ="SELECT TOP 1 vc.UPC,
				v.VendorName AS VdrName,
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' 
				AND id.UPC=vc.UPC AND id.Vendor=vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R'  AND id.Vendor=vc.Vendor 
				AND id.UPC=vc.UPC AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'R' AND id.UPC=vc.UPC AND id.Vendor=vc.Vendor 
				ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving
				FROM dbo.VendorCost vc
				INNER JOIN dbo.Vendors v ON v.Vendor = vc.Vendor 
				LEFT JOIN dbo.InventoryDetail id ON id.UPC = vc.UPC
				WHERE vc.UPC LIKE '%".$upcNumber."'
				ORDER BY lastReceivingDate DESC";

		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_sectionReport($sectionNumber, $today, $from, $to)
	{
		$SQL = "SELECT vc.UPC, vc.Vendor AS VdrNo, vc.VendorItem AS CertCode, vc.CaseCost, i.Brand, i.Description AS ItemDescription, i.SizeAlpha, vc.Pack, i.Department AS SctNo, i.MajorDept AS DptNo, v.VendorName AS VdrName,
				d.Description AS SctName, md.Description AS DptName, p.BasePrice as Retail, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = vc.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC  AND id.Vendor=vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R'  AND id.Vendor=vc.Vendor AND id.UPC=vc.UPC AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC   AND id.Vendor=vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.VendorCost vc 
				LEFT JOIN dbo.Item i ON i.UPC = vc.UPC
				LEFT JOIN dbo.Vendors v ON v.Vendor = vc.Vendor
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				WHERE i.Department = '".$sectionNumber."' AND p.Store = '00000A'
				ORDER BY v.VendorName ASC, i.Description ASC, vc.Pack DESC, i.SizeAlpha DESC;";

		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_vendorSectionReport($vendorNumber, $sectionNumber, $today, $to, $from)
	{
		$SQL = "SELECT  vc.UPC, v.Vendor AS VdrNo, v.VendorName AS VdrName, vc.VendorItem AS CertCode, vc.CaseCost,
				i.Brand, vc.Pack, i.SizeAlpha, i.Department AS SctNo, i.MajorDept AS DptNo, i.Description AS ItemDescription, p.BasePrice as Retail, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				d.Description AS SctName, md.Description AS DptName, (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC   AND id.Vendor=vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R'  AND id.Vendor=vc.Vendor AND id.UPC=p.UPC AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC   AND id.Vendor=vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.Vendors v 
				RIGHT JOIN dbo.VendorCost vc ON vc.Vendor = v.Vendor
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Item i ON i.UPC = vc.UPC
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				WHERE v.Vendor = '".$vendorNumber."' AND p.Store = '00000A' AND i.Department = '".$sectionNumber."'
				ORDER BY i.Department, i.Description, vc.Pack DESC, i.SizeAlpha DESC;";

		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_vendorReport($vendorNumber, $today, $from, $to)
	{
		$SQL = "SELECT  vc.UPC, v.Vendor AS VdrNo, v.VendorName AS VdrName, vc.VendorItem AS CertCode, vc.CaseCost, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				i.Brand, vc.Pack, i.SizeAlpha, i.Department AS SctNo, i.MajorDept AS DptNo, i.Description AS ItemDescription, p.BasePrice as Retail,
				d.Description AS SctName, md.Description AS DptName, (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC   AND id.Vendor=vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R'  AND id.Vendor=vc.Vendor AND id.UPC=p.UPC AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC   AND id.Vendor=vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.Vendors v 
				RIGHT JOIN dbo.VendorCost vc ON vc.Vendor = v.Vendor
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Item i ON i.UPC = vc.UPC
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				WHERE v.Vendor = '".$vendorNumber."' AND p.Store = '00000A'
				ORDER BY i.Department, i.Description, i.SizeAlpha ASC, vc.Pack DESC;";
		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}
}