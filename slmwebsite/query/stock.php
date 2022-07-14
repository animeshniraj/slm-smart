<?php
	
	require_once('../../requiredlibs/includeall.php');
    require_once('../../requiredlibs/stock.php');

    function send_error_message($message)
    {
        $message = [
            "response" => false,
            "msg" => $message
        ];

        return $message;
    }


    $session = getPageSession();
    $show_alert = false;
    $alert_message = "";

    
    
    if(!$session)
    {
        $error_response = send_error_message("Auth Error");
        echo json_encode($error_response);
        die();
    }
    
    isAuthenticated($session,'user_module');

    
    if(!isset($_POST["action"]))
	{
        $error_response = send_error_message("No Action");
        echo json_encode($error_response);
		die();
	}

    $action = $_POST["action"];

	switch ($action) {

        case "get_process_properties"       :   get_process_properties($_POST); break;
        case "get_process_grade"            :   get_process_grade($_POST); break;
        case "initDataHandshake"            :   initDataHandshake($_POST); break;
        case "fetch_process_data"           :   fetch_process_data($_POST);break;
        case "test_process_data"            :   test_process_data($_POST);break;


        default: echo json_encode(send_error_message("Unknown Action"));

    }





?>