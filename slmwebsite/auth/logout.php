<?php
	
	require_once('../../requiredlibs/includeall.php');

	$session = getPageSession();

	if(!$session)
	{
		header('Location: /auth/');
		die();
	}

	$session->logout();
	

?>