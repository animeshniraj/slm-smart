<?php
    require_once("../../requiredlibs/includeall.php");

    $errFlag = false;

    $session = getPageSession();

   

    if(!$session)
    {
        header('Location: /auth/');
        die();
    }

    $userid =  $session->user->getUserid();

    $result = runQuery("SELECT status FROM userauth WHERE userid='$userid'");
    $passwordStatus = $result->fetch_assoc()["status"];

    if($passwordStatus=="DEFAULT")
    {
        if(isset($_POST["password"]))
        {
            if($session->user->setPassword($_POST["password"]))
            {
                if($session->getPermission("admin_module"))
                {
                    header("Location: /admin/");
                    die();
                }
                else if($session->getPermission("user_module"))
                {
                    header("Location: /user/");
                    die();
                }
                else
                {
                    header("Location: /auth/");
                    die();
                }
            }
            else
            {
                $errFlag = true;
            }
        }
    }
    else
    {
        $ERR_TITLE = "Error";
        $ERR_MSG = "You are not authorized to view this page.";
        include("../pages/error.php");
        die();
    }


?>

<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<title>SLM | Change Password</title>


<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="description" content="Admindek Bootstrap admin template made using Bootstrap 4 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
<meta name="keywords" content="bootstrap, bootstrap admin template, admin theme, admin dashboard, dashboard template, admin template, responsive" />
<meta name="author" content="colorlib" />

<link rel="icon" href="https://colorlib.com/polygon/admindek/files/assets/images/favicon.ico" type="image/x-icon">

<link href="/pages/font/opensan.css" rel="stylesheet">
<link href="/pages/font/opensand.css" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="/pages/css/bootstrap.min.css">

<link rel="stylesheet" href="/pages/css/waves.min.css" type="text/css" media="all"> <link rel="stylesheet" type="text/css" href="/pages/css/feather.css">

<link rel="stylesheet" type="text/css" href="/pages/css/themify-icons.css">

<link rel="stylesheet" type="text/css" href="/pages/css/icofont.css">

<link rel="stylesheet" type="text/css" href="/pages/css/font-awesome.min.css">

<link rel="stylesheet" type="text/css" href="/pages/css/style.css"><link rel="stylesheet" type="text/css" href="/pages/css/pages.css">
</head>
<body themebg-pattern="theme1">

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
<div class="row">
<div class="col-sm-12">

<form method="POST" class="md-float-material form-material">
<div class="text-center">
<img src="/pages/png/slmlogo.png" alt="slmlogo.png">
</div>
<div class="auth-box card">
<div class="card-block">
<div class="row m-b-20">
<div class="col-md-12">
<h3 class="text-center txt-primary">Change Default Password</h3>
<p id="pmatcherr" class="text-center border-danger" style="display: none;">Passwords do not match</p>
</div>
</div>
<?php
    if($errFlag)
    {


?>
<p class="text-center border-danger" style="">Error. Try again.</p>
<?php
    }
?>



<div class="form-group form-primary">
<input type="password" oninput="checkPassword()" name="password" id="password" class="form-control" required="">
<span class="form-bar"></span>
<label class="float-label">New Password</label>
</div>
<div class="form-group form-primary">
<input type="password" oninput="checkPassword()" name="cpassword" id="cpassword" class="form-control" required="">
<span class="form-bar"></span>
<label class="float-label">Confirm Password</label>
</div>
<div class="row m-t-25 text-left">
<div class="col-12">


</div>
</div>
<div class="row m-t-30">
<div class="col-md-12">
<button disabled id="submitBtn" type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Change Password</button>
</div>
</div>

</div>
</div>
</form>

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

<script type="text/javascript">

    function checkPassword()
    {
        if(document.getElementById("password").value=="" || document.getElementById("cpassword").value=="")
        {
            return false;
        }
        if(document.getElementById("password").value==document.getElementById("cpassword").value)
        {
            document.getElementById("pmatcherr").style.display = 'none';
            document.getElementById("submitBtn").disabled = false;
        }
        else
        {
            document.getElementById("pmatcherr").style.display = 'block';
            document.getElementById("submitBtn").disabled = true;
        }
    }
    
</script>
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