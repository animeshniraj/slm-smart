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


	


?>