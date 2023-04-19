<?php
    
	require_once('../../../requiredlibs/includeall.php');

	
	$session = getPageSession();
  	$show_alert = false;
  	$alert_message = "";
	
	if(!$session)
	{
		header('Location: /auth/');
		die();
	}

	isAuthenticated($session,'user_module');

	
	$data = unserialize($_GET['data']);

	


	//Background Image - The Image To Write Text On
	if($_GET['company'] == "SLM Metal")
	{
		$image = imagecreatefrompng('slm_stamp_metal.png');
	}
	else
	{
		$image = imagecreatefrompng('slm_stamp_tech.png');
	}
    
    imagesavealpha($image, true);
    $width  = imagesx($image);
	$height = imagesy($image);
    
    $black = imagecolorallocate($image, 0, 0, 0);
    $blue = imagecolorallocate($image, 0, 54,111);


    $font = "./SCRIPTIN.ttf";
    $fontsize = 55;

  	list($left,, $right) = imageftbbox($fontsize, 0, $font, $data["approved-by-inital"]);
  	$dwidth = $right - $left;
  	$posx = $width/2 - $dwidth/2;
  	$posy = $height/2;

    imagettftext($image, $fontsize, 0, $posx,$posy, $blue, $font, $data["approved-by-inital"]);


    $font = "./Lucida_Console_Regular.ttf";
    $fontsize = 12;
    list($left,, $right) = imageftbbox($fontsize, 0, $font, $data["approved-date"]);
  	$dwidth = $right - $left;
  	$posx = $width/2 - $dwidth/2;
  	$posy = $height*2/3;

    imagettftext($image, $fontsize, 0, $posx,$posy, $black, $font,$data["approved-date"]);


    list($left,, $right) = imageftbbox($fontsize, 0, $font, "Approved By: ".$data["approved-by"]);
  	$dwidth = $right - $left;
  	$posx = $width/2 - $dwidth/2;
  	$posy = $height-20;

    imagettftext($image, $fontsize, 0, $posx,$posy, $black, $font,"Approved By: ".$data["approved-by"]);


    $fontsize = 10;
    list($left,, $right) = imageftbbox($fontsize, 0, $font, $data["hash"]);
  	$dwidth = $right - $left;
  	$posx = $width/2 - $dwidth/2;
  	$posy = $height-2;

	 
    imagettftext($image, $fontsize, 0, $posx,$posy, $black, $font,$data["hash"]);

    //Set Browser Content Type
    header('Content-type: image/png');
    
    //Send Image To Browser
    imagepng($image);
    
    //Clear Image From Memory
    imagedestroy($image);

?>