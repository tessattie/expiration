<?php
class report extends Model{

	protected $db;

	public function __construct()
	{
		parent::__construct();
		$server_name = 'HOST-STORE';
		$this->db = new PDO( "sqlsrv:server=".$server_name." ; Database = reports", "sa", "BRd@t@123");
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function save_report($report)
	{
		$date = date('Y-m-d H:i:s');
		$insert = $this->db->prepare("INSERT INTO dbo.reports (name, date_from, date_to, timestamp, user_firstname, user_lastname, user_id)
	    VALUES (:name, :date_from, :date_to, :timestamp, :user_firstname, :user_lastname, :user_id)");

	    $insert->bindParam(':name', $report['name']);
	    $insert->bindParam(':date_from', $report['date_from']);
	    $insert->bindParam(':date_to', $report['date_to']);
	    $insert->bindParam(':timestamp', $date);
	    $insert->bindParam(':user_firstname', $_SESSION["firstname"]);
	    $insert->bindParam(':user_lastname', $_SESSION["lastname"]);
	    $insert->bindParam(':user_id', $_SESSION["id"]);

	    if($insert->execute())
	    {
	    	return $this->db->lastInsertId(); 
	    }
	    else
	    {
	    	return false;
	    }
	    // print_r($this->db->errorInfo());
	}

	public function get_reports()
	{
		$SQL = "SELECT * FROM reports ORDER BY timestamp DESC";
		$result = $this->db->query($SQL);
		return $result->fetchAll(PDO::FETCH_BOTH);
	}

	public function get_report($id)
	{
		$SQL = "SELECT r.*, i.* 
				FROM dbo.reports r 
				RIGHT JOIN dbo.items i ON i.report_id = r.id
				WHERE r.id = ".$id."
				ORDER BY i.SctNo, i.VdrNo";
		$result = $this->db->query($SQL);
		return $result->fetchAll(PDO::FETCH_BOTH);
	}

	public function save_item($item, $report)
	{
		$item['onhand'] = round($item['onhand']);
		$item['lastReceiving'] = round($item['lastReceiving']);
		$insert = $this->db->prepare("INSERT INTO dbo.items (report_id, upc, itemcode, description, pack, size, brand, casecost, retail, 
			onhand, lastorder, lastorderdate, sales, vdrno, vdrname, tprprice, tprstart, tprend, expiration, expiration_date, orderqty, 
			SctNo, SctName, DptNo, DptName)
	    VALUES (:report_id, :upc, :itemcode, :description, :pack, :size, :brand, :casecost, :retail, 
			:onhand, :lastorder, :lastorderdate, :sales, :vdrno, :vdrname, :tprprice, :tprstart, :tprend, :expiration, :expiration_date, :orderqty, 
			:SctNo, :SctName, :DptNo, :DptName)");

	    $insert->bindParam(':report_id', $report);
	    $insert->bindParam(':upc', $item['UPC']);
	    $insert->bindParam(':itemcode', $item['CertCode']);
	    $insert->bindParam(':description', $item['ItemDescription']);
	    $insert->bindParam(':pack', $item['Pack']);
	    $insert->bindParam(':size', $item['SizeAlpha']);
	    $insert->bindParam(':brand', $item['Brand']);
	    $insert->bindParam(':casecost', $item['CaseCost']);
	    $insert->bindParam(':retail', $item['Retail']);
	    $insert->bindParam(':onhand', $item['onhand']);
	    $insert->bindParam(':lastorder', $item['lastReceiving']);
	    $insert->bindParam(':lastorderdate', $item['lastReceivingDate']);
	    $insert->bindParam(':sales', $item['sales']);
	    $insert->bindParam(':vdrno', $item['VdrNo']);
	    $insert->bindParam(':vdrname', $item['VdrName']);
	    $insert->bindParam(':tprprice', $item['tpr']);
	    $insert->bindParam(':tprstart', $item['tprStart']);
	    $insert->bindParam(':tprend', $item['tprEnd']);
	    $insert->bindParam(':expiration', $item['expiration']);
	    $insert->bindParam(':expiration_date', $item['expiration_date']);
	    $insert->bindParam(':orderqty', $item['order']);
	    $insert->bindParam(':SctNo', $item['SctNo']);
	    $insert->bindParam(':SctName', $item['SctName']);
	    $insert->bindParam(':DptNo', $item['DptNo']);
	    $insert->bindParam(':DptName', $item['DptName']);

	    // $insert->execute();
	    if($insert->execute())
	    {
	    	return "SAVED"; 
	    }
	    else
	    {
	    	return "NOT SAVED";
	    }
	}

	public function update_item($id, $field, $value)
	{
		$update = "UPDATE items SET ".$field." ='" . $value . "' WHERE id =" . $id;
		$this->db->query($update);	
	}

	public function update_report_vendor($id, $vdrno, $vdrname, $casecost, $certcode, $lastorder, $lastorderdate)
	{
		$update = "UPDATE items SET vdrno ='" . $vdrno . "', vdrname = '".$vdrname."', casecost = '".$casecost."', 
		itemcode = '".$certcode."', lastorder = '".$lastorder."', lastorderdate = '".$lastorderdate."'  WHERE id =" . $id;
		$this->db->query($update);	
	}

	public function delete_report($id)
	{
		$delete = "DELETE FROM reports WHERE id = '" . $id . "'";
		$this->db->query($delete);		
	}

	public function delete_report_items($id)
	{
		$delete = "DELETE FROM items WHERE report_id = '" . $id . "'";
		$this->db->query($delete);		
	}

	public function delete_item($id)
	{
		$delete = "DELETE FROM items WHERE id = '" . $id . "'";
		$this->db->query($delete);		
	}
}