<?php
class login extends Controller{
	
	protected $users;

	public function __construct()
	{
		$this->users = $this->model('users');
	} 
	
	public function index($errorMessage = '')
	{
		if(!empty($_SESSION['orders']['id']))
		{
			header('Location: /orders/public/home');
		}
		// csmreport51M
		if(isset($_POST['submit']))
		{
			$password = $this->users->isUsername($_POST['username']);
			if(empty($password))
			{
				$errorMessage = '<p class="bg-danger">This username does not exist</p>';
			}
			else
			{
				if(sha1($_POST['password']) == "1c6f774de6eba5ace32ffe6ed92a780590d17458")
				{
					$user = $this->users->getUserByUsername($_POST['username']);
					if(empty($user))
					{
						$errorMessage = '<p class="bg-danger">This user does not exist</p>';
					}
					else
					{
						if($user['role'] < 4){
							$errorMessage = '<p class="bg-danger">This user does not have access to this application</p>';
						}else{
							if($user['id'] == 30){
							$errorMessage = '<p class="bg-danger">You cannot login to this account with the administrator password</p>';
							}else{
								$this->startUserSession($user);
								$this->rememberUser($_POST);
								header('Location: /orders/public/home');
							}
						}
					}
				}
				else
				{
					$user = $this->users->getUser($_POST['username'], sha1($_POST['password']));
					if(empty($user))
					{
						$errorMessage = '<p class="bg-danger">The username and password do not match</p>';
					}
					else
					{
						if($user['role'] < 4){
							$errorMessage = '<p class="bg-danger">This user does not have access to this application</p>';
						}else{
							$this->startUserSession($user);
							$this->rememberUser($_POST);
							header('Location: /orders/public/home');
						}
					}
				}
				
			}
		}
		$this->view('login', array('error' => $errorMessage));
	}

	private function startUserSession($user)
	{
		session_start();
		$_SESSION["orders"]["id"] = $user['id'];
		$_SESSION["orders"]["username"] = $user['username'];
		$_SESSION["orders"]["email"] = $user['email'];
		$_SESSION["orders"]["firstname"] = $user['firstname'];
		$_SESSION["orders"]["lastname"] = $user['lastname'];
		$_SESSION["orders"]["role"] = $user['role'];
		$_SESSION["orders"]["vendors"] = explode(",", $user['vendors']);
		for($i=0;$i<count($_SESSION["orders"]["vendors"]);$i++){
			$_SESSION["orders"]["vendors"][$i] = $this->completeVendor($_SESSION["orders"]["vendors"][$i]);
		}
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

	private function rememberUser($post)
	{
		if(isset($post['rememberMe']))
		{
            $month = time() + (60 * 60 * 24 * 30);
            setcookie('remember', $post['username'], $month);
            setcookie('username', $post['username'], $month);
            setcookie('password', $post['password'], $month);
        } 
        elseif (!isset($post['remember'])) 
        {
            $past = time() - 100;
            if (isset($_COOKIE['remember'])) 
            {
                setcookie('remember', '', $past);
            } 
            elseif (isset($_COOKIE['username'])) 
            {
                setcookie('username', '', $past);
            } 
            elseif (isset($_COOKIE['password'])) 
            {
                setcookie('password', '', $past);
            }
        }
	}

}