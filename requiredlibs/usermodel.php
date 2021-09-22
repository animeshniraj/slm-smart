<?php

	class user{
		private $userid;
		private $fname;
		private $lname;
		private $roleid;
		private $rolename;
		private $profilepic;

		function __construct($id)
		{
			$this->userid=$id; 
		}

		function setFname($val)
		{
			$this->fname = $val;
		}

		function setLname($val)
		{
			$this->lname = $val;
		}

		function setRole($val)
		{
			$result = runQuery("SELECT * FROM roles where roleid='$val'");
			if($result->num_rows>0)
			{
				$this->roleid = $val;
				$this->rolename = $result->fetch_assoc()["rolename"];
				return true;

			}
			else
			{
				return false;
			}
		}

		function setPic()
		{
			$this->profilepic = "/pages/jpg/defaultpic.jpg";

			runQuery("INSERT INTO profilepic VALUES('$this->userid','$this->profilepic')");
		}

		function getUserid()
		{
			return $this->userid;
		}

		function getFname()
		{
			return $this->fname;
		}

		function getLname()
		{
			return $this->lname;
		}

		function getFullname()
		{
			return $this->fname . " ". $this->lname;
		}

		function getRoleid()
		{
			return $this->roleid;
		}

		function getRolename()
		{
			return $this->rolename;
		}


		function getAllData()
		{
			$data = [
				"First Name" => $this->getFname(),
				"Last Name"  => $this->getLname(),
				"User Id"    => $this->userid,
				"Role Id"    => $this->getRoleid(),
				"Role Name"	 => $this->getRolename(),
			];

			return $data;
		}

		function setAll($fname,$lname,$roleid,$updateDb = false)
		{
			if($this->setRole($roleid))
			{
				$this->fname = $fname;
				$this->lname = $lname;

				if($updateDb)
				{
					
					$this->insertDB();
					$this->setPic();
				}
				return true;
			}
			else
			{
				return false;
			}
		}

		function insertDB()
		{
			try
			{
				$result = runQuery("INSERT INTO users VALUE('$this->userid','$this->fname','$this->lname','$this->roleid')");
				$this->setPermission();
				return true;
				
				
			}
			catch(Exception $e)
			{
				return false;
			}
		}

		function updateDB()
		{
			try
			{
				$result = runQuery("UPDATE users SET fname='$this->fname',lname='$this->lname',role='$this->roleid' WHERE userid = '$this->userid'");

				return true;
				

			}
			catch(Exception $e)
			{
				return false;
			}
		}

		function pullInfo()
		{
			try
			{
				$result = runQuery("SELECT * FROM userdetails WHERE userid='$this->userid'");
				$result = $result->fetch_assoc();
				$this->setFname($result["fname"]);
				$this->setLname($result["lname"]);
				$this->roleid = $result["role"];
				$this->rolename = $result["rolename"];

				return true;
				

			}
			catch(Exception $e)
			{
				return false;
			}
		}

		function deleteFromDB()
		{
			try
			{
				$result = runQuery("CALL delete_user('$this->userid')");
				
				return true;
				

			}
			catch(Exception $e)
			{
				return false;
			}
		}

		function setDefaultPassword()
		{
			$this->pullInfo();
			$dumPassword = $this->userid.'@'.mt_rand(1000,9999);
			$hashedPass = password_hash($dumPassword,PASSWORD_DEFAULT);

			try
			{
				$result = runQuery("DELETE FROM userauth WHERE userid = '$this->userid'");
				$resulr = runQuery("INSERT INTO userauth VALUES('$this->userid','$hashedPass','DEFAULT')");
				if($result)
				{
					return $dumPassword;
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


		function setPassword($pass)
		{
			
			
			$hashedPass = password_hash($pass,PASSWORD_DEFAULT);

			try
			{
				$result = runQuery("DELETE FROM userauth WHERE userid = '$this->userid'");
				$resulr = runQuery("INSERT INTO userauth VALUES('$this->userid','$hashedPass','USER_DEFINED')");
				if($result)
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

		function checkUserid()
		{
			$result = runQuery("SELECT * FROM users where userid = '$this->userid'");

			if($result->num_rows==1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function pullByAuth($pass)
		{
			$hashedPass = password_hash($pass,PASSWORD_DEFAULT);
			try
			{
				
				$result = runQuery("SELECT password from userauth WHERE userid = '$this->userid'");

				$result = $result->fetch_assoc();

				$hashedPass = $result['password'];
				
				if(password_verify($pass, $hashedPass))
				{
					$this->pullInfo();
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

		function setPermission()
		{
			$result = runQuery("INSERT INTO ACL SELECT NULL,'$this->userid',defaultpermissions.module,defaultpermissions.permission FROM defaultpermissions WHERE defaultpermissions.role='$this->roleid'");
			
		}

		function setNotification($title,$msg,$type,$url)
		{
			$result = runQuery("INSERT INTO usernotification VALUES(NULL,'$this->userid','$title','$msg','$type','$url','NEW',CURRENT_TIMESTAMP)");
			
		}

		function addLog($msg)
		{
			$result = runQuery("INSERT INTO logs VALUES(NULL,'$this->userid','$this->rolename','$msg',CURRENT_TIMESTAMP)");
			
		}
		
	}

?>