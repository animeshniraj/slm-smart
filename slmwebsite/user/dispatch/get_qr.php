<?php
    
	require_once('../../../requiredlibs/includeall.php');
  include('phpqrcode.php');




  if(!isset($_GET["data"]))
  {
    $ERR_TITLE = "Error";
    $ERR_MSG = "You are not authorized to view this page.";
    include("../../pages/error.php");
    die();
  }


    $data = $_GET["data"];

    QRcode::png($data); 

