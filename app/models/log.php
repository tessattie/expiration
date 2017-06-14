<?php

class log extends Model{

	protected $db;

	public function __construct()
	{
		parent::__construct();
		$server_name = 'HOST-STORE';
		$this->db = new PDO( "sqlsrv:server=".$server_name." ; Database = reports", "sa", "BRd@t@123");
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function getLogs()
	{
		$SQL = "SELECT * FROM log WHERE application = 1 ORDER BY date DESC";
		$result = $this->db->query($SQL);
		return $result->fetchAll(PDO::FETCH_BOTH);
	}

	public function saveLog($date, $application, $action)
	{
		$insert = $this->db->prepare("INSERT INTO log (date, application, action)
	    VALUES (:date, :application, :action)");

	    $insert->bindParam(':date', $date);
	    $insert->bindParam(':application', $application);
	    $insert->bindParam(':action', $action);

	    $insert->execute();
	}

	public function deleteLog($id)
	{
		$delete = "DELETE FROM log WHERE id = '" . $id . "'";
		$this->db->query($delete);		
	}
}