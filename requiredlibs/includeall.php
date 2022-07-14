<?php
	
	require_once("variables.php");
	require_once("dbconfig.php");
	require_once("usermodel.php");
	require_once("sessionManager.php");
	require_once("inventory.php");

	
	function toServerTime($date,$time=true,$seconds = true)
	{
		if($time)
		{
			if($seconds)
			{
				return Date('Y-m-d H:i:s',strtotime($date));
			}
			else
			{
				return Date('Y-m-d H:i',strtotime($date));
			}
		}
		else
		{
			return Date('Y-M-d',strtotime($date));
		}
		
	}

	function fromServerTimeTo12hr($date)
	{
		return Date('Y-M-d h:i A',strtotime($date));
	}

	function showAlert($icon,$title,$msg)
	{
		return "<script>Swal.fire({\nicon: \"".$icon."\",\ntitle: \"".$title."\",\nhtml: \"".$msg."\",\nshowCancelButton: false,\nshowConfirmButton: true,\nallowEscapeKey: true,\nallowOutsideClick: true,\n})</script>";
	}

	function getInitial($user)
	{
		$initial = "";

		$result = runQuery("SELECT * FROM user_sign WHERE userid='$user'");
		if($result->num_rows==1)
		{
			$initial = $result->fetch_assoc()['initial'];
		}

		return $initial;
	}

	function get_last_id($processname)
	{
		$result = runQuery("SELECT * FROM processentry WHERE processname='$processname' ORDER BY entrytime DESC LIMIT 1");

    	if($result->num_rows==0)
    	{	
    		return "";
    	}
    	else
    	{
    		return $result->fetch_assoc()['processid'];
    	}
	}

	function get_last_premixid($processname)
	{
		$result = runQuery("SELECT * FROM premix_batch  ORDER BY entrydate DESC LIMIT 1");

    	if($result->num_rows==0)
    	{	
    		return "";
    	}
    	else
    	{
    		return $result->fetch_assoc()['premixid'];
    	}
	}

	function getFullName($userid)
	{
		$dumUser = new user($userid);
		$dumUser->pullInfo();

		return $dumUser->getFname()." ". $dumUser->getLname();
	}

	function getFirstName($userid)
	{
		$dumUser = new user($userid);
		$dumUser->pullInfo();

		return $dumUser->getFname();
	}

	function getpropShortname($processname,$propname)
	{
		

		$result = runQuery("SELECT * FROM shortnames WHERE identifier='$processname' AND name='$propname'");

		if($result->num_rows>0)
		{

			$shortname = $result->fetch_assoc()['shortname'];
			if($shortname=="")
			{
				$shortname = $propname;
			}
		}
		else
		{
			$shortname = $propname;
		}


		return $shortname;
	}

	function addprocesslog($type,$id,$user,$log)
	{
		$result = runQuery("INSERT INTO processlog VALUES(NULL,'$type','$id','$user','$log',CURRENT_TIMESTAMP)");

		if($result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}




	


?>