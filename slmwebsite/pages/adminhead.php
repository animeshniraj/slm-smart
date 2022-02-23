<?php


?>

<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from colorlib.com/polygon/admindek/default/menu-header-fixed.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 12 Dec 2019 16:08:34 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
<title><?php echo $PAGE["Page Title"]; ?></title>


<!--[if lt IE 10]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

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

<link rel="stylesheet" href="/pages/css/waves.min.css" type="text/css" media="all">

<link rel="stylesheet" type="text/css" href="/pages/css/feather.css">

<link rel="stylesheet" type="text/css" href="/pages/css/themify-icons.css">

<link rel="stylesheet" type="text/css" href="/pages/css/icofont.css">

<link rel="stylesheet" type="text/css" href="/pages/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/pages/css/datatables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/pages/css/buttons.datatables.min.css">
<link rel="stylesheet" type="text/css" href="/pages/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/pages/css/select2.min.css">
<link rel="stylesheet" type="text/css" href="/pages/css/prism.css">
<link rel="stylesheet" type="text/css" href="/pages/css/bootstrap-multiselect.css">
<link rel="stylesheet" type="text/css" href="/pages/css/multi-select.css">
<link rel="stylesheet" type="text/css" href="/pages/css/style.css">
<link rel="stylesheet" type="text/css" href="/pages/css/widget.css">
<link rel="stylesheet" type="text/css" href="/pages/css/daterangepicker.css">
<script src="/pages/js/sweetalert2.all.min.js"></script>
<link rel="stylesheet" type="text/css" href="/pages/css/sweetalert2.min.css">

</head>

<body>

<div class="loader-bg">
<div class="loader-bar"></div>
</div>

<div id="pcoded" class="pcoded">
<div class="pcoded-overlay-box"></div>
<div class="pcoded-container navbar-wrapper">


<nav class="navbar header-navbar pcoded-header">
<div class="navbar-wrapper">
<div class="navbar-logo">
<a href="<?php echo $PAGE["Home Link"]; ?>">
<img class="img-fluid" src="/pages/png/slmwhite.png" alt="Theme-Logo" />
</a>
<a class="mobile-menu" id="mobile-collapse" href="#!">
<i class="feather icon-menu icon-toggle-right"></i>
</a>
<a class="mobile-options waves-effect waves-light">
<i class="feather icon-more-horizontal"></i>
</a>
</div>
<div class="navbar-container container-fluid">
<ul class="nav-left">

<li class="header-search">
<div class="main-search morphsearch-search">
<div class="input-group">
<span class="input-group-prepend search-close">
<i class="feather icon-x input-group-text" id="mainsearchclose"></i>
</span>
<input type="text" id="mainsearchkey" class="form-control" placeholder="Enter Keyword">
<span class="input-group-append search-btn">
<i class="feather icon-search input-group-text" onclick="mainSearch(this)"></i>
</span>
</div>
</div>
</li>



<script type="text/javascript">
    function mainSearch(inObj)
    {
        
        
        

        if(!inObj.parentNode.parentNode.parentNode.classList.contains('open'))
        {
            return;
        }

        var searchWord = document.getElementById('mainsearchkey').value;

        if(searchWord=="")
        {
            return;
        }
        
    
        

        var postData = new FormData();
           
            postData.append("action","searchProcess");
            postData.append("searchkey",searchWord);
            
             
            var xmlhttp = new XMLHttpRequest();
            
            xmlhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                
                console.log(this.responseText);
                var data = JSON.parse(this.responseText);
                
                if(data.response)
                {
                    var form = document.createElement("form");
                    form.setAttribute("method","POST");
                    form.setAttribute("action",data.url);
                    var input = document.createElement("input");
                    input.setAttribute("type","hidden");
                    input.setAttribute("name",data.name);
                    input.setAttribute("value",searchWord);

                    form.appendChild(input);
                    form.submit();
                    
                      var dumForm   = document.body.appendChild(form);
                      dumForm.submit();

                }
                else
                {
                    Swal.fire({
                        icon: "error",
                        text: "Cannot find any results", 
                    });
                    document.getElementById('mainsearchkey').value = "";
                   document.getElementById('mainsearchclose').click();
                }
                
            
              }
            };
            xmlhttp.open("POST", "/query/search.php", true);
            xmlhttp.send(postData);

        }



</script>

<li>
<a href="#!" onclick="if (!window.__cfRLUnblockHandlers) return false; javascript:toggleFullScreen()" class="waves-effect waves-light" data-cf-modified-aab919c724f78c685c2cf9e5-="">
<i class="full-screen feather icon-maximize"></i>
</a>
</li>

</ul>
<ul class="nav-right">
    <li>
    <div id="serverTime"></div>
    <script type="text/javascript">
        
        getServerTime();

        setInterval(function(){
            getServerTime();
        },20000)

        function getServerTime()
        {


        
            var postData = new FormData();
           
            postData.append("action","getTime");
            

            var xmlhttp = new XMLHttpRequest();
            
            xmlhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                
                
                var data = JSON.parse(this.responseText);
                
                if(data.response)
                {
                    document.getElementById("serverTime").innerHTML = data.time;
                }
                
            
              }
            };
            xmlhttp.open("POST", "/query/notification.php", true);
            xmlhttp.send(postData);
        }


    </script>
</li>
<li class="header-notification">
<div class="dropdown-primary dropdown">
<div id="userNewNotifications" onclick="readNotification()" class="dropdown-toggle" data-toggle="dropdown">
	<i class="feather icon-bell"></i>
	<span  class="badge bg-c-red">0</span>
</div>
<ul id="userNotifications" class="show-notification notification-view dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" >
<li>
<h6>Notifications</h6>
</li>





 </ul>
</div>
</li>



<li class="user-profile header-notification">
<div class="dropdown-primary dropdown">
<div class="dropdown-toggle" data-toggle="dropdown">
<img src="/pages/jpg/defaultpic.jpg" class="img-radius" alt="User-Profile-Image">
<span><?php echo $session->user->getFullname(); ?></span>
<i class="feather icon-chevron-down"></i>
</div>
<ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">

<li>
<a href="<?php echo $PAGE["Home Link"]; ?>message/inbox.php">
<i class="feather icon-mail"></i> My Messages
</a>
</li>


<li>
<a href="<?php echo $PAGE["Home Link"]; ?>settings.php">
<i class="feather icon-settings"></i> Settings
</a>
</li>



<li>
<a href="/auth/logout.php">
<i class="feather icon-log-out"></i> Logout
</a>
</li>
</ul>
</div>
</li>


</ul>
</div>
</div>
</nav>


<script type="text/javascript">
	getNotification();
	setInterval(function(){
		getNotification();
	},10000)

	var allNotifIds = [];

	function readNotification()
	{

		if(allNotifIds.length==0)
		{
			return;
		}
		
		var postData = new FormData();
       
        postData.append("action","readNotification");
        postData.append("userid","<?php echo $session->user->getUserid(); ?>");
        postData.append("ids",JSON.stringify(allNotifIds));

        var xmlhttp = new XMLHttpRequest();
        
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            
          	
            var data = JSON.parse(this.responseText);
            
            if(data.response)
            {
                document.getElementById("userNewNotifications").innerHTML = "<i class=\"feather icon-bell\"></i>";
            }
            
        
          }
        };
        xmlhttp.open("POST", "/query/notification.php", true);
        xmlhttp.send(postData);
	}

	function getNotification()
	{
		var postData = new FormData();
       
        postData.append("action","getNotification");
        postData.append("userid","<?php echo $session->user->getUserid(); ?>");
        
        var xmlhttp = new XMLHttpRequest();
        var userNotifications = "<li><h6>Notifications</h6></li>";
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            

            var data = JSON.parse(this.responseText);
            
            if(data.response)
            {
                if(data.newNotif==0)
                {
                	document.getElementById("userNewNotifications").innerHTML = "<i class=\"feather icon-bell\"></i>";
                }
                else
                {
                	document.getElementById("userNewNotifications").innerHTML = "<i class=\"feather icon-bell\"></i><span  class=\"badge bg-c-red\">"+data.newNotif+"</span>";
                }
            	

            	var notifications = data.notifications;
                var notCount = 0;
            	for(var i=0; i<notifications.length;i++)
            	{
            		if(notifications[i].status=="NEW")
            		{
            			allNotifIds.push(notifications[i].id);
            		}

                    notCount++;
                    var icon = "";
            		if(notifications[i].type=="MAIL")
                    {
                        icon = "fa fa-send";
                    }
                    else
                    {
                        icon = "fa fa-bell";
                    }

            		userNotifications+="<li onclick=\"location.href='"+notifications[i].url+"'\"><div class=\"media\"><p style=\"font-size: 25px; margin-right: 20px;\"><i class=\""+icon+"\"></i></p><div class=\"media-body\"><h5 class=\"notification-user\">"+notifications[i].title+"</h5><p class=\"notification-msg\">"+notifications[i].msg+"</p><span class=\"notification-time\">"+notifications[i].agomessage+"</span></div></div></li>";

                    if(notCount>=5)
                    {
                        userNotifications += "<li onclick=\"location.href='/admin/showNotifications.php'\"><h6 style=\"text-align: center;\">Show more</h6></a></li>";
                        break;
                    }
            	}

            	document.getElementById("userNotifications").innerHTML = userNotifications;
            }
            
        
          }
        };
        xmlhttp.open("POST", "/query/notification.php", true);
        xmlhttp.send(postData);
	}

</script>