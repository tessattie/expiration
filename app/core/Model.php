<?php 
class Model{
	
	protected $db;
	public function __construct()
	{
		$server_name = 'HOST-STORE';
		$this->db = new PDO( "sqlsrv:server=".$server_name." ; Database = BRDATA", "sa", "BRd@t@123");
	}
}