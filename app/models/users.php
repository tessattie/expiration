<?php

class users extends Model{

	protected $db;

	public function __construct()
	{
		parent::__construct();
		$server_name = 'HOST-STORE';
		$this->db = new PDO( "sqlsrv:server=".$server_name." ; Database = users", "sa", "BRd@t@123");
	}

	public function getUsers()
	{
		$SQL = "SELECT * FROM users WHERE role=5 OR role=6 OR role=7 ORDER BY lastname";
		$result = $this->db->query($SQL);
		// print_r($this->db->errorInfo());
		return $result->fetchall(PDO::FETCH_BOTH);
	}

	public function isUsername($username)
	{
		$SQL = "SELECT password FROM users WHERE username ='" . $username . "'";
		$result = $this->db->query($SQL);
		return $result->fetch(PDO::FETCH_BOTH)['password'];
	}

	public function getUserByUsername($username)
	{
		$SQL = "SELECT * FROM users WHERE username ='" . $username . "'";
		$result = $this->db->query($SQL);
		return $result->fetch(PDO::FETCH_BOTH);
	}

	public function getUser($username, $password)
	{
		$SQL = "SELECT * FROM users WHERE username ='" . $username . "' AND password = '" . $password . "'";
		$result = $this->db->query($SQL);
		return $result->fetch(PDO::FETCH_BOTH);
	}

	public function getUserById($id)
	{
		$SQL = "SELECT * FROM users WHERE id =" . $id ;
		$result = $this->db->query($SQL);
		return $result->fetch(PDO::FETCH_BOTH);
	}

	public function setUser($user)
	{
		$insert = $this->db->prepare("INSERT INTO users (firstname, lastname, username, password, email, role)
	    VALUES (:firstname, :lastname, :username, :password, :email, :role)");

	    $insert->bindParam(':firstname', $user['firstname']);
	    $insert->bindParam(':lastname', $user['lastname']);
	    $insert->bindParam(':username', $user['username']);
	    $insert->bindParam(':password', $user['password']);
	    $insert->bindParam(':email', $user['email']);
	    $insert->bindParam(':role', $user['role']);

	    $insert->execute();
	}

	public function deleteUser($id)
	{
		$delete = "DELETE FROM users WHERE id = '" . $id . "'";
		$this->db->query($delete);		
	}

	public function updateUser($firstname, $lastname, $username, $email, $role, $id)
	{
		$update = "UPDATE users SET firstname ='" . $firstname . "', lastname = '".$lastname."', username = '".$username."', 
		email = '".$email."', role = '".$role."' WHERE id =" . $id;
		$this->db->query($update);	
	}

	public function setPassword($id, $password)
	{
		$update = "UPDATE users SET password ='" . $password . "' WHERE id =" . $id;
		$this->db->query($update);	
	}

}