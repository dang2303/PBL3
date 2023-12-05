<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location:login.php");
    exit;
}
else {
    $now = time();
  
    if($now > $_SESSION['expire']) {
        session_destroy();
        header("Location: login.php");  
    }
    else {

include 'connect_db.php';
$conn = getConnect();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <link rel="shortcut icon" href="images/favicon.png">
    <title>Admin</title>
    <!--Core CSS -->
    <link href="bs3/css/bootstrap.min.css" rel="stylesheet">
    <link href="js/jquery-ui/jquery-ui-1.10.1.custom.min.css" rel="stylesheet">
    <link href="css/bootstrap-reset.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <link href="font-awesome/css/font-awesome.css" rel="stylesheet"> -->
    <link href="js/jvector-map/jquery-jvectormap-1.2.2.css" rel="stylesheet">
    <link href="css/clndr.css" rel="stylesheet">
    <!--clock css-->
    <link href="js/css3clock/css/style.css" rel="stylesheet">
    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="js/morris-chart/morris.css">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />
    <style>
        .temperatureColor {color: red;}
        .humidityColor {color: #1b78e2;}
        .luxColor {color: yellow;}
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            display: none;
        }

        .sliderTS {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #D3D3D3;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 34px;
        }

        .sliderTS:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: #f7f7f7;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.sliderTS {
            background-color: #00878F;
        }

        input:focus+.sliderTS {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.sliderTS:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        .sliderTS:after {
            content: 'OFF';
            color: white;
            display: block;
            position: absolute;
            transform: translate(-50%, -50%);
            top: 50%;
            left: 70%;
            font-size: 10px;
            font-family: Verdana, sans-serif;
        }

        input:checked+.sliderTS:after {
            left: 25%;
            content: 'ON';
        }

        input:disabled+.sliderTS {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
    <script>
        //------------------------------------------------------------
        //------------------------------------------------------------

        Get_Data("ESP32_01");

        setInterval(myTimer, 1000);

        //------------------------------------------------------------
        function myTimer() {
            Get_Data("ESP32_01");
        }
        //------------------------------------------------------------

        //------------------------------------------------------------
        function Get_Data(mcu) {
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const myObj = JSON.parse(this.responseText);
                    if (myObj.mcu == "ESP32_01") {
                        document.getElementById("ESP32_01_Temp").innerHTML = myObj.temperature;
                        document.getElementById("ESP32_01_Humd").innerHTML = myObj.humidity;
                        document.getElementById("ESP32_01_Lux").innerHTML = myObj.lux_status;
                        //   document.getElementById("ESP32_01_LTRD").innerHTML = "Time : " + myObj.ls_time + " | Date : " + myObj.ls_date + " (dd-mm-yyyy)";
                        if (myObj.led_01 == "ON") {
                            document.getElementById("ESP32_01_TogLED_01").checked = true;
                        } else if (myObj.led_01 == "OFF") {
                            document.getElementById("ESP32_01_TogLED_01").checked = false;
                        }
                        if (myObj.led_02 == "ON") {
                            document.getElementById("ESP32_01_TogLED_02").checked = true;
                        } else if (myObj.led_02 == "OFF") {
                            document.getElementById("ESP32_01_TogLED_02").checked = false;
                        }
                    }
                }
            };
            xmlhttp.open("POST", "get_data_from_db.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send("MCU=" + mcu);
        }
        //------------------------------------------------------------

        //------------------------------------------------------------
        function GetTogBtnLEDState(togbtnid) {
            if (togbtnid == "ESP32_01_TogLED_01") {
                var togbtnchecked = document.getElementById(togbtnid).checked;
                var togbtncheckedsend = "";
                if (togbtnchecked == true) togbtncheckedsend = "ON";
                if (togbtnchecked == false) togbtncheckedsend = "OFF";
                Update_LEDs("esp32_01", "LED_01", togbtncheckedsend);
            }
            if (togbtnid == "ESP32_01_TogLED_02") {
                var togbtnchecked = document.getElementById(togbtnid).checked;
                var togbtncheckedsend = "";
                if (togbtnchecked == true) togbtncheckedsend = "ON";
                if (togbtnchecked == false) togbtncheckedsend = "OFF";
                Update_LEDs("esp32_01", "LED_02", togbtncheckedsend);
            }
        }
        //------------------------------------------------------------

        //------------------------------------------------------------
        function Update_LEDs(id, lednum, ledstate) {
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    //document.getElementById("demo").innerHTML = this.responseText;
                }
            }
            xmlhttp.open("POST", "updates.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send("id=" + id + "&led1=" + lednum + "&ledstate=" + ledstate);
        }
        //------------------------------------------------------------
    </script>
</head>

<body>
    <section id="container">
        <!--header start-->
        <header class="header fixed-top clearfix">
            <!--logo start-->
            <div class="brand">

                <a href="index.php" class="logo">
                    Dang Huynh
                    <!-- <img src="images/logo.png" alt=""> -->
                </a>
                <!-- <div class="sidebar-toggle-box">
        <div class="fa fa-bars"></div>
    </div> -->
            </div>
            <!--logo end-->

            <div class="nav notify-row" id="top_menu">
                <!--  notification start -->
                <ul class="nav top-menu">
                    <!-- settings start -->
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <i class="fa fa-tasks"></i>
                            <span class="badge bg-success">8</span>
                        </a>
                        <ul class="dropdown-menu extended tasks-bar">
                            <li>
                                <p class="">You have 8 pending tasks</p>
                            </li>
                            <li>
                                <a href="#">
                                    <div class="task-info clearfix">
                                        <div class="desc pull-left">
                                            <h5>Target Sell</h5>
                                            <p>25% , Deadline 12 June’13</p>
                                        </div>
                                        <span class="notification-pie-chart pull-right" data-percent="45">
                                            <span class="percent"></span>
                                        </span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div class="task-info clearfix">
                                        <div class="desc pull-left">
                                            <h5>Product Delivery</h5>
                                            <p>45% , Deadline 12 June’13</p>
                                        </div>
                                        <span class="notification-pie-chart pull-right" data-percent="78">
                                            <span class="percent"></span>
                                        </span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div class="task-info clearfix">
                                        <div class="desc pull-left">
                                            <h5>Payment collection</h5>
                                            <p>87% , Deadline 12 June’13</p>
                                        </div>
                                        <span class="notification-pie-chart pull-right" data-percent="60">
                                            <span class="percent"></span>
                                        </span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div class="task-info clearfix">
                                        <div class="desc pull-left">
                                            <h5>Target Sell</h5>
                                            <p>33% , Deadline 12 June’13</p>
                                        </div>
                                        <span class="notification-pie-chart pull-right" data-percent="90">
                                            <span class="percent"></span>
                                        </span>
                                    </div>
                                </a>
                            </li>

                            <li class="external">
                                <a href="#">See All Tasks</a>
                            </li>
                        </ul>
                    </li>
                    <!-- settings end -->
                    <!-- inbox dropdown start-->
                    <li id="header_inbox_bar" class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <i class="fa fa-envelope-o"></i>
                            <span class="badge bg-important">4</span>
                        </a>
                        <ul class="dropdown-menu extended inbox">
                            <li>
                                <p class="red">You have 4 Mails</p>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="photo"></span>
                                    <span class="subject">
                                        <span class="from">Jonathan Smith</span>
                                        <span class="time">Just now</span>
                                    </span>
                                    <span class="message">
                                        Hello, this is an example msg.
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="photo"></span>
                                    <span class="subject">
                                        <span class="from">Jane Doe</span>
                                        <span class="time">2 min ago</span>
                                    </span>
                                    <span class="message">
                                        Nice admin template
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="photo"></span>
                                    <span class="subject">
                                        <span class="from">Tasi sam</span>
                                        <span class="time">2 days ago</span>
                                    </span>
                                    <span class="message">
                                        This is an example msg.
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="photo"></span>
                                    <span class="subject">
                                        <span class="from">Mr. Perfect</span>
                                        <span class="time">2 hour ago</span>
                                    </span>
                                    <span class="message">
                                        Hi there, its a test
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="#">See all messages</a>
                            </li>
                        </ul>
                    </li>
                    <!-- inbox dropdown end -->
                    <!-- notification dropdown start-->
                    <li id="header_notification_bar" class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">

                            <i class="fa fa-bell-o"></i>
                            <span class="badge bg-warning">3</span>
                        </a>
                        <ul class="dropdown-menu extended notification">
                            <li>
                                <p>Notifications</p>
                            </li>
                            <li>
                                <div class="alert alert-info clearfix">
                                    <span class="alert-icon"><i class="fa fa-bolt"></i></span>
                                    <div class="noti-info">
                                        <a href="#"> Server #1 overloaded.</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="alert alert-danger clearfix">
                                    <span class="alert-icon"><i class="fa fa-bolt"></i></span>
                                    <div class="noti-info">
                                        <a href="#"> Server #2 overloaded.</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="alert alert-success clearfix">
                                    <span class="alert-icon"><i class="fa fa-bolt"></i></span>
                                    <div class="noti-info">
                                        <a href="#"> Server #3 overloaded.</a>
                                    </div>
                                </div>
                            </li>

                        </ul>
                    </li>
                    <!-- notification dropdown end -->
                </ul>
                <!--  notification end -->
            </div>
            <div class="top-nav clearfix">
                <!--search & user info start-->
                <ul class="nav pull-right top-menu">
                    <li>
                        <input type="text" class="form-control search" placeholder=" Search">
                    </li>
                    <!-- user login dropdown start-->
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <!-- <img alt="" src="images/avatar1_small.jpg"> -->
                            <span class="username">Dang Huynh</span>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu extended logout">
                            <li><a href="#"><i class=" fa fa-suitcase"></i>Profile</a></li>
                            <li><a href="#"><i class="fa fa-cog"></i> Settings</a></li>
                            <li><a href="logout.php"><i class="fa fa-key"></i> Log Out</a></li>
                        </ul>
                    </li>
                    <!-- user login dropdown end -->
                    <li>
                        <!-- <div class="toggle-right-box">
                <div class="fa fa-bars"></div>
            </div> -->
                    </li>
                </ul>
                <!--search & user info end-->
            </div>
        </header>
        <!--header end-->
        <!--sidebar start-->
        <aside>
            <div id="sidebar" class="nav-collapse">
                <!-- sidebar menu start-->
                <div class="leftside-navigation">
                    <ul class="sidebar-menu" id="nav-accordion">
                        <li>
                            <a class="active" href="index.html">
                                <i class="fa fa-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-laptop"></i>
                                <span>Layouts</span>
                            </a>
                            <!-- <ul class="sub">
                        <li><a href="boxed_page.html">Boxed Page</a></li>
                        <li><a href="horizontal_menu.html">Horizontal Menu</a></li>
                        <li><a href="language_switch.html">Language Switch Bar</a></li>
                    </ul> -->
                        </li>
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-book"></i>
                                <span>UI Elements</span>
                            </a>
                            <!-- <ul class="sub">
                        <li><a href="general.html">General</a></li>
                        <li><a href="buttons.html">Buttons</a></li>
                        <li><a href="typography.html">Typography</a></li>
                        <li><a href="widget.html">Widget</a></li>
                        <li><a href="slider.html">Slider</a></li>
                        <li><a href="tree_view.html">Tree View</a></li>
                        <li><a href="nestable.html">Nestable</a></li>
                        <li><a href="grids.html">Grids</a></li>
                        <li><a href="calendar.html">Calender</a></li>
                        <li><a href="draggable_portlet.html">Draggable Portlet</a></li>
                    </ul> -->
                        </li>
                        <!-- <li>
                    <a href="fontawesome.html">
                        <i class="fa fa-bullhorn"></i>
                        <span>Fontawesome </span>
                    </a>
                </li>
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-th"></i>
                        <span>Data Tables</span>
                    </a>
                    <ul class="sub">
                        <li><a href="basic_table.html">Basic Table</a></li>
                        <li><a href="responsive_table.html">Responsive Table</a></li>
                        <li><a href="dynamic_table.html">Dynamic Table</a></li>
                        <li><a href="editable_table.html">Editable Table</a></li>
                    </ul>
                </li>
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-tasks"></i>
                        <span>Form Components</span>
                    </a>
                    <ul class="sub">
                        <li><a href="form_component.html">Form Elements</a></li>
                        <li><a href="advanced_form.html">Advanced Components</a></li>
                        <li><a href="form_wizard.html">Form Wizard</a></li>
                        <li><a href="form_validation.html">Form Validation</a></li>
                        <li><a href="file_upload.html">Muliple File Upload</a></li>

                        <li><a href="dropzone.html">Dropzone</a></li>
                        <li><a href="inline_editor.html">Inline Editor</a></li>

                    </ul>
                </li>
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-envelope"></i>
                        <span>Mail </span>
                    </a>
                    <ul class="sub">
                        <li><a href="mail.html">Inbox</a></li>
                        <li><a href="mail_compose.html">Compose Mail</a></li>
                        <li><a href="mail_view.html">View Mail</a></li>
                    </ul>
                </li>
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class=" fa fa-bar-chart-o"></i>
                        <span>Charts</span>
                    </a>
                    <ul class="sub">
                        <li><a href="morris.html">Morris</a></li>
                        <li><a href="chartjs.html">Chartjs</a></li>
                        <li><a href="flot_chart.html">Flot Charts</a></li>
                        <li><a href="c3_chart.html">C3 Chart</a></li>
                    </ul>
                </li>
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class=" fa fa-bar-chart-o"></i>
                        <span>Maps</span>
                    </a>
                    <ul class="sub">
                        <li><a href="google_map.html">Google Map</a></li>
                        <li><a href="vector_map.html">Vector Map</a></li>
                    </ul>
                </li>
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-glass"></i>
                        <span>Extra</span>
                    </a>
                    <ul class="sub">
                        <li><a href="blank.html">Blank Page</a></li>
                        <li><a href="lock_screen.html">Lock Screen</a></li>
                        <li><a href="profile.html">Profile</a></li>
                        <li><a href="invoice.html">Invoice</a></li>
                        <li><a href="pricing_table.html">Pricing Table</a></li>
                        <li><a href="timeline.html">Timeline</a></li>                    
                <li><a href="gallery.html">Media Gallery</a></li><li><a href="404.html">404 Error</a></li>
                        <li><a href="500.html">500 Error</a></li>
                        <li><a href="registration.html">Registration</a></li>
                    </ul>
                </li> -->
                        <li>
                            <a href="login.html">
                                <i class="fa fa-user"></i>
                                <span>Login Page</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- sidebar menu end-->
            </div>
        </aside>
        <!--sidebar end-->
        <!--main content start-->
        <section id="main-content">
            <section class="wrapper">

                <!--mini statistics start-->
                <!-- <div class="row">
    <div class="col-md-3">
        <section class="panel">
            <div class="panel-body">
                <div class="top-stats-panel">
                    <div class="gauge-canvas">
                        <h4 class="widget-h">Monthly Expense</h4>
                        <canvas width=160 height=100 id="gauge"></canvas>
                    </div>
                    <ul class="gauge-meta clearfix">
                        <li id="gauge-textfield" class="pull-left gauge-value"></li>
                        <li class="pull-right gauge-title">Safe</li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
    <div class="col-md-3">
        <section class="panel">
            <div class="panel-body">
                <div class="top-stats-panel">
                    <div class="daily-visit">
                        <h4 class="widget-h">Daily Visitors</h4>
                        <div id="daily-visit-chart" style="width:100%; height: 100px; display: block">

                        </div>
                        <ul class="chart-meta clearfix">
                            <li class="pull-left visit-chart-value">3233</li>
                            <li class="pull-right visit-chart-title"><i class="fa fa-arrow-up"></i> 15%</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-md-3">
        <section class="panel">
            <div class="panel-body">
                <div class="top-stats-panel">
                    <h4 class="widget-h">Top Advertise</h4>
                    <div class="sm-pie">
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-md-3">
        <section class="panel">
            <div class="panel-body">
                <div class="top-stats-panel">
                    <h4 class="widget-h">Daily Sales</h4>
                    <div class="bar-stats">
                        <ul class="progress-stat-bar clearfix">
                            <li data-percent="50%"><span class="progress-stat-percent pink"></span></li>
                            <li data-percent="90%"><span class="progress-stat-percent"></span></li>
                            <li data-percent="70%"><span class="progress-stat-percent yellow-b"></span></li>
                        </ul>
                        <ul class="bar-legend">
                            <li><span class="bar-legend-pointer pink"></span> New York</li>
                            <li><span class="bar-legend-pointer green"></span> Los Angels</li>
                            <li><span class="bar-legend-pointer yellow-b"></span> Dallas</li>
                        </ul>
                        <div class="daily-sales-info">
                            <span class="sales-count">1200 </span> <span class="sales-label">Products Sold</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div> -->
                <!--mini statistics end-->
                <div class="row">
                    <div class="col-md-3">
                        <section class="panel">
                            <div class="panel-body">
                                <h4 class="LEDColor"><i class="fas fa-lightbulb"></i> LED 1</h4>
                                <label class="switch">
                                    <input type="checkbox" id="ESP32_01_TogLED_01" onclick="GetTogBtnLEDState('ESP32_01_TogLED_01')">
                                    <div class="sliderTS"></div>
                                </label>
                                <h4 class="LEDColor"><i class="fas fa-lightbulb"></i> LED 2</h4>
                                <label class="switch">
                                    <input type="checkbox" id="ESP32_01_TogLED_02" onclick="GetTogBtnLEDState('ESP32_01_TogLED_02')">
                                    <div class="sliderTS"></div>
                                </label>
                            </div>
                        </section>
                    </div>
                    <div class="col-md-3">
                        <section class="panel">
                            <div class="panel-body">
                                <h4 class="temperatureColor"> TEMPERATURE</h4>
                                <p class="temperatureColor"><span class="reading"><span id="ESP32_01_Temp"></span> &deg;C</span></p>
                                <h4 class="humidityColor">HUMIDITY</h4>
                                <p class="humidityColor"><span class="reading"><span id="ESP32_01_Humd"></span> &percnt;</span></p>
                            </div>
                        </section>
                    </div>
                    <div class="col-md-3">
                        <section class="panel">
                            <div class="panel-body">
                                <h4 class="luxColor">LUX</h4>
                                <p class="luxColor"><span class="reading"><span id="ESP32_01_Lux"></span> &percnt;</span></p>
                            </div>
                        </section>
                    </div>
                </div>
                <!--mini statistics start-->
                <div class="row">

                </div>
                <!--mini statistics end-->


                <div class="row">

                </div>
                <div class="row">

                </div>
                <div class="row">

                </div>
                </div>
            </section>
        </section>
        <!--main content end-->
        <!--right sidebar start-->
        <!--right sidebar end-->
    </section>
    <!-- Placed js at the end of the document so the pages load faster -->
    <!--Core js-->
    <script src="js/jquery.js"></script>
    <script src="js/jquery-ui/jquery-ui-1.10.1.custom.min.js"></script>
    <script src="bs3/js/bootstrap.min.js"></script>
    <script src="js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="js/jquery.scrollTo.min.js"></script>
    <script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
    <script src="js/jquery.nicescroll.js"></script>
    <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot-chart/excanvas.min.js"></script><![endif]-->
    <script src="js/skycons/skycons.js"></script>
    <script src="js/jquery.scrollTo/jquery.scrollTo.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
    <script src="js/calendar/clndr.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js"></script>
    <script src="js/calendar/moment-2.2.1.js"></script>
    <script src="js/evnt.calendar.init.js"></script>
    <script src="js/jvector-map/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="js/jvector-map/jquery-jvectormap-us-lcc-en.js"></script>
    <script src="js/gauge/gauge.js"></script>
    <!--clock init-->
    <script src="js/css3clock/js/css3clock.js"></script>
    <!--Easy Pie Chart-->
    <script src="js/easypiechart/jquery.easypiechart.js"></script>
    <!--Sparkline Chart-->
    <script src="js/sparkline/jquery.sparkline.js"></script>
    <!--Morris Chart-->
    <!-- <script src="js/morris-chart/morris.js"></script>
    <script src="js/morris-chart/raphael-min.js"></script> -->
    <!--jQuery Flot Chart-->
    <!-- <script src="js/flot-chart/jquery.flot.js"></script>
    <script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
    <script src="js/flot-chart/jquery.flot.resize.js"></script>
    <script src="js/flot-chart/jquery.flot.pie.resize.js"></script>
    <script src="js/flot-chart/jquery.flot.animator.min.js"></script>
    <script src="js/flot-chart/jquery.flot.growraf.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="js/jquery.customSelect.min.js"></script> -->
    <!--common script init for all pages-->
    <script src="js/scripts.js"></script>
    <!--script for this page-->
    <?php
    }
}
    ?>
</body>

</html>