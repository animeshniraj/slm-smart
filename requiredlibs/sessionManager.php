<?php

	require_once("dbconfig.php");
	require_once("usermodel.php");


	class UserSession{
		public $user;
		



		function authsession($id,$pass)
		{

			$this->user = new user($id);
			if(!$this->user->checkUserid())
			{
				return false;
			}
			if($this->user->pullByAuth($pass))
			{
				
				if (session_status() == PHP_SESSION_ACTIVE) {
				  session_destroy();
				}

				
				session_start();
				$_SESSION['userData'] = $this;


				return true;

				

			}
			else
			{
				return false;
			}
		}

		function getPermission($module)
		{
			try
			{
				$userid = $this->user->getUserid();
				
				$result = runQuery("SELECT * FROM ACL WHERE userid = '$userid' AND module='$module'");
				
				$result = $result->fetch_assoc();
				if($result['module']==$module && $result['permission']=="ALLOW")
				{
					return true;
				}
				else
				{
					return false;
				}

			}
			catch(Exception $e)
			{
				return false;
			}
		}

		function logout($loc = "Location: /auth/")
		{
			session_destroy();
			header($loc);
			die();
		}



	}

	function getPageSession()
	{
		session_start();
		if(isset($_SESSION['userData']))
		{
			return $_SESSION['userData'];
		}
		else
		{
			return false;
		}
	}


	function isAuthenticated($session,$module)
	{

		if(!$session->getPermission($module))
		{
			header('Location: /auth/');
			die();
		}


	}

	

?>