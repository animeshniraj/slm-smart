<?php
	
	require_once("variables.php");
	require_once("dbconfig.php");
	require_once("usermodel.php");
	require_once("sessionManager.php");
	require_once("inventory.php");

	

	function showAlert($icon,$title,$msg)
	{
		return "<script>Swal.fire({\nicon: \"".$icon."\",\ntitle: \"".$title."\",\nhtml: \"".$msg."\",\nshowCancelButton: false,\nshowConfirmButton: true,\nallowEscapeKey: true,\nallowOutsideClick: true,\n})</script>";
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