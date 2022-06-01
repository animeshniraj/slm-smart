<?php
    
    $errFlag = false;

    require_once("../../requiredlibs/includeall.php");
    if (session_status() == PHP_SESSION_ACTIVE) {
      session_destroy();
    }

    if(isset($_POST["userid"]) && isset($_POST["userid"]))
    {
        $userid = $_POST["userid"];
        $password = $_POST["password"];

        
        $session = new UserSession();

        if($session->authsession($userid,$password))
        {

            $lasttime = Date('Y-m-d H:i:s',strtotime('now'));
            $currtime = $lasttime;
            
            $result = runQuery("SELECT * FROM loginlog WHERE userid='$userid' ORDER BY currtime DESC");

            if($result->num_rows>0)
            {
                $lasttime = $result->fetch_assoc()['currtime'];

            }

            runQuery("INSERT INTO loginlog VALUES(NULL,'$userid','$currtime','$lasttime')");



            $result = runQuery("SELECT status FROM userauth WHERE userid='$userid'");
            $passwordStatus = $result->fetch_assoc()["status"];
            if($session->getPermission("admin_module"))
            {   
                if($passwordStatus == "DEFAULT")
                {
                    header("Location: /auth/defaultpassword.php");
                    die();
                }
                else
                {
                    header("Location: /admin/");
                    die();
                }
            }
            else
            {
                if($passwordStatus == "DEFAULT")
                {
                    header("Location: /auth/defaultpassword.php");
                    die();
                }
                else
                {
                    header("Location: /user/");
                    die();
                }
            }
        }
        else
        {
            $errFlag = true;
        }
        


        
    }

?>

<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<title>SLM SMART | Login</title>


<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="description" content="SMART is SLM Technology's proprietary software developed by Amazing Workz Studios to record and track each event followed in their factory." />
<meta name="author" content="Amazing Workz Studios" />

<link rel="icon" href="/pages/jpg/favicon.ico" type="image/x-icon">

<link href="/pages/font/opensan.css" rel="stylesheet">
<link href="/pages/font/opensand.css" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="/pages/css/bootstrap.min.css">

<link rel="stylesheet" href="/pages/css/waves.min.css" type="text/css" media="all"> <link rel="stylesheet" type="text/css" href="/pages/css/feather.css">

<link rel="stylesheet" type="text/css" href="/pages/css/themify-icons.css">

<link rel="stylesheet" type="text/css" href="/pages/css/icofont.css">

<link rel="stylesheet" type="text/css" href="/pages/css/font-awesome.min.css">

<link rel="stylesheet" type="text/css" href="/pages/css/style.css">
<link rel="stylesheet" type="text/css" href="/pages/css/pages.css">
</head>
<body themebg-pattern="theme1" style="background-image: url(/pages/jpg/smart-bg.jpg); background-repeat:none;background-size:cover;">

<div class="theme-loader">
<div class="loader-track">
<div class="preloader-wrapper">
<div class="spinner-layer spinner-blue">
<div class="circle-clipper left">
<div class="circle"></div>
</div>
<div class="gap-patch">
<div class="circle"></div>
</div>
<div class="circle-clipper right">
<div class="circle"></div>
</div>
</div>
<div class="spinner-layer spinner-red">
<div class="circle-clipper left">
<div class="circle"></div>
</div>
<div class="gap-patch">
<div class="circle"></div>
</div>
<div class="circle-clipper right">
<div class="circle"></div>
</div>
</div>
<div class="spinner-layer spinner-yellow">
<div class="circle-clipper left">
<div class="circle"></div>
</div>
<div class="gap-patch">
<div class="circle"></div>
</div>
<div class="circle-clipper right">
<div class="circle"></div>
</div>
</div>
<div class="spinner-layer spinner-green">
<div class="circle-clipper left">
<div class="circle"></div>
</div>
<div class="gap-patch">
<div class="circle"></div>
</div>
<div class="circle-clipper right">
<div class="circle"></div>
</div>
</div>
</div>
</div>
</div>

<section class="login-block">

<div class="container-fluid">
<div class="row justify-content-center">
<div class="col-md-4 mt-3">
<div class="log-screen">
    <form method="POST" class="md-float-material form-material">
    <div class="text-center">
    <img src="/pages/png/slmlogo.png" alt="SLM SMART">
    <hr>
    <h3> WELCOME TO SMART</h3>
    </div>
    <div class="auth-box card">
    <div class="card-block">
    <div class="row m-b-20">
    <div class="col-md-12">
    <h3 class="text-center txt-primary">Login Now</h3>
    <img src="/pages/png/encryption.gif" alt="Login" style="width:60px;height:auto;margin:0 auto;display:block;">

    </div>
    </div>
    <?php
        if($errFlag)
        {


    ?>
    <p class="text-center border-danger" style="">Invalid Credentials</p>
    <?php
        }
    ?>

    <div class="form-group form-primary">
    <input type="text" name="userid" class="form-control" required="">
    <span class="form-bar"></span>
    <label class="float-label">Username</label>
    </div>
    <div class="form-group form-primary">
    <input type="password" name="password" class="form-control" required="">
    <span class="form-bar"></span>
    <label class="float-label">Password</label>
    </div>
    <div class="row m-t-25 text-left">
    <!--<div class="col-12">

    <div class="checkbox-fade fade-in-primary">
    <label>
    <input type="checkbox" value="">
    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
    <span class="text-inverse">Remember me</span>
    </label>
    </div>

    <div class="forgot-phone text-right float-right">
    <a href="#" class="text-right f-w-600"> Forgot Password?</a>
    </div>

    </div> -->

    </div>
    <div class="row m-t-30">
    <div class="col-md-12">
    <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">LOGIN</button>
    </div>
    </div>

    </div>
    </div>
    </form>
    <p class="cent">2022 SLM Material and Reporting Tool. All Rights Reserved. Powered by <a href="https://amazingworkz.com" class="cent">Amazing Workz Studios</a></p>  
</div>

</div>

</div>

</div>

</div>

</section>


<!--[if lt IE 10]>
<div class="ie-warning">
    <h1>Warning!!</h1>
    <p>You are using an outdated version of Internet Explorer, please upgrade <br/>to any of the following web browsers to access this website.</p>
    <div class="iew-container">
        <ul class="iew-download">
            <li>
                <a href="http://www.google.com/chrome/">
                    <img src="../files/assets/images/browser/chrome.png" alt="Chrome">
                    <div>Chrome</div>
                </a>
            </li>
            <li>
                <a href="https://www.mozilla.org/en-US/firefox/new/">
                    <img src="../files/assets/images/browser/firefox.png" alt="Firefox">
                    <div>Firefox</div>
                </a>
            </li>
            <li>
                <a href="http://www.opera.com">
                    <img src="../files/assets/images/browser/opera.png" alt="Opera">
                    <div>Opera</div>
                </a>
            </li>
            <li>
                <a href="https://www.apple.com/safari/">
                    <img src="../files/assets/images/browser/safari.png" alt="Safari">
                    <div>Safari</div>
                </a>
            </li>
            <li>
                <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                    <img src="../files/assets/images/browser/ie.png" alt="">
                    <div>IE (9 & above)</div>
                </a>
            </li>
        </ul>
    </div>
    <p>Sorry for the inconvenience!</p>
</div>
<![endif]-->


<script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="/pages/js/jquery.min.js"></script>
<script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="/pages/js/jquery-ui.min.js"></script>
<script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="/pages/js/popper.min.js"></script>
<script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="/pages/js/bootstrap.min.js"></script>

<script src="/pages/js/waves.min.js" type="4878d7dfa7bc22a8dfa99416-text/javascript"></script>

<script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="/pages/js/jquery.slimscroll.js"></script>

<script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="/pages/js/modernizr.js"></script>
<script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="/pages/js/css-scrollbars.js"></script>
<script type="4878d7dfa7bc22a8dfa99416-text/javascript" src="/pages/js/common-pages.js"></script>


<script src="/pages/js/rocket-loader.min.js" data-cf-settings="4878d7dfa7bc22a8dfa99416-|49" defer=""></script></body>


</html>
