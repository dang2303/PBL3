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
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="bootstrap.min.css" rel="stylesheet">
  <link href="bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="style.css" rel="stylesheet">
  <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
<script>
  //------------------------------------------------------------
  //------------------------------------------------------------
  const clientId = 'mqttjs_webhost'
    const host = 'wss://broker.emqx.io:8084/mqtt'
    const options = {
      keepalive: 60,
      clientId: clientId,
      protocolId: 'MQTT',
      protocolVersion: 4,
      clean: true,
      reconnectPeriod: 1000,
      connectTimeout: 30 * 1000,
      will: {
        topic: 'pbl3test',
        payload: 'Connection Closed abnormally..!',
        qos: 0,
        retain: false
      },
      username : 'dang2303',
      password : 'dang2303',
    }
    console.log('Connecting mqtt client')
    const client = mqtt.connect(host, options)
    client.on('error', (err) => {
      console.log('Connection error: ', err)
      client.end()
    })
    client.on('reconnect', () => {
      console.log('Reconnecting...')
    })
    client.on('connect', () => {
    console.log(`Client connected: ${clientId}`)
    // Subscribe
    client.subscribe('pbl3test', { qos: 0 })
    })
    // Publish
    client.publish('pbl3test', 'ws connection demo...!', { qos: 0, retain: false })
    // Receive
    client.on('message', (topic, message, packet) => {
      const myObj = JSON.parse(message);
      if(myObj.MCU == "ESP32"){
        document.getElementById("ESP32_01_Temp").innerHTML = myObj.temperature;
        document.getElementById("ESP32_01_Humd").innerHTML = myObj.humidity;
        document.getElementById("ESP32_01_Lux").innerHTML = myObj.lux_status;
        // document.getElementById("ESP32_01_Ppm").innerHTML = myObj.ppm;
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
        if (myObj.auto1 == "ON") {
            document.getElementById("ESP32_01_TogLED_01_Auto").checked = true;
        } else if (myObj.auto1 == "OFF") {
            document.getElementById("ESP32_01_TogLED_01_Auto").checked = false;
        }
      }
    })
  //------------------------------------------------------------

  //------------------------------------------------------------
  function GetTogBtnLEDState(togbtnid) {
      if (togbtnid == "ESP32_01_TogLED_01") {
          var togbtnchecked = document.getElementById(togbtnid).checked;
          var togbtncheckedsend = "";
          var togbtn = document.getElementById("ESP32_01_TogLED_02").checked;
          if(togbtn){
            togbtn = "ON";
          }else{
            togbtn = "OFF";
          }
          if (togbtnchecked == true) 
          {
            togbtncheckedsend = "ON"
            document.getElementById("ESP32_01_TogLED_01_Auto").checked = false;
            client.publish('pbl3test_down', `{"led_01" : "ON", "led_02" : "${togbtn}", "auto" : "OFF"}`, { qos: 0, retain: false });
          };
          if (togbtnchecked == false)
          {
            togbtncheckedsend = "OFF"
            document.getElementById("ESP32_01_TogLED_01_Auto").checked = false;
            client.publish('pbl3test_down', `{"led_01" : "OFF", "led_02" : "${togbtn}", "auto" : "OFF"}`, { qos: 0, retain: true })
          };
      }
      if (togbtnid == "ESP32_01_TogLED_02") {
          var togbtnchecked = document.getElementById(togbtnid).checked;
          var togbtncheckedsend = "";
          var togbtn = document.getElementById("ESP32_01_TogLED_01").checked;
          if(togbtn){
            togbtn = "ON";
          }else{
            togbtn = "OFF";
          }
          if (togbtnchecked == true) 
          {
            togbtncheckedsend = "ON"
            document.getElementById("ESP32_01_TogLED_01_Auto").checked = false;
            client.publish('pbl3test_down', `{"led_01" : "${togbtn}", "led_02" : "ON", "auto" : "OFF"}`, { qos: 0, retain: false })
          };
          if (togbtnchecked == false) 
          {
            togbtncheckedsend = "OFF"
            document.getElementById("ESP32_01_TogLED_01_Auto").checked = false;
            client.publish('pbl3test_down', `{"led_01" : "${togbtn}","led_02" : "OFF", "auto" : "OFF"}`, { qos: 0, retain: false });
          } ;
      }
  }
  //------------------------------------------------------------

  //------------------------------------------------------------
  function GetTogBtnAutoState(togbtnid) {
      if (togbtnid == "ESP32_01_TogLED_01_Auto") {
          var togbtnautochecked = document.getElementById(togbtnid).checked;
          var togbtnchecked = document.getElementById("ESP32_01_TogLED_01").checked;
          var togbtnchecked2 = document.getElementById("ESP32_01_TogLED_02").checked;
          if(togbtnchecked){
            togbtnchecked = "ON";
          }else{
            togbtnchecked = "OFF";
          }
          if(togbtnchecked2){
            togbtnchecked2 = "ON";
          }else{
            togbtnchecked2 = "OFF";
          }
          if (togbtnautochecked == true) 
          {
            client.publish('pbl3test_down', `{"auto" : "ON", "led_01" : "${togbtnchecked}", "led_02" : "${togbtnchecked2}"}`, { qos: 0, retain: false });
          };
          if (togbtnautochecked == false)
          {
            client.publish('pbl3test_down', `{"auto" : "OFF", "led_01" : "${togbtnchecked}", "led_02" : "${togbtnchecked2}"}`, { qos: 0, retain: false })
          };
      }
      // if (togbtnid == "ESP32_01_TogLED_02_Auto") {
      //     var togbtnchecked = document.getElementById(togbtnid).checked;
      //     var togbtncheckedsend = "";
      //     if (togbtnchecked == true) 
      //     {
      //       togbtncheckedsend = "ON"
      //       client.publish('pbl3test_down', '{"auto2" : "ON"}', { qos: 0, retain: false });
      //     };
      //     if (togbtnchecked == false) 
      //     {
      //       togbtncheckedsend = "OFF"
      //       client.publish('pbl3test_down', `{"auto2" : "OFF","led_02" : "${togbtnchecked}"}`, { qos: 0, retain: false })
      //     } ;
      // }
  }
  //------------------------------------------------------------

</script>
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <span class="d-none d-lg-block">Admin</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number">4</span>
          </a><!-- End Notification Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
              You have 4 new notifications
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-exclamation-circle text-warning"></i>
              <div>
                <h4>Lorem Ipsum</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>30 min. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-x-circle text-danger"></i>
              <div>
                <h4>Atque rerum nesciunt</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>1 hr. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-check-circle text-success"></i>
              <div>
                <h4>Sit rerum fuga</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>2 hrs. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-info-circle text-primary"></i>
              <div>
                <h4>Dicta reprehenderit</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>4 hrs. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>
            <li class="dropdown-footer">
              <a href="#">Show all notifications</a>
            </li>

          </ul><!-- End Notification Dropdown Items -->

        </li><!-- End Notification Nav -->

        <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-chat-left-text"></i>
            <span class="badge bg-success badge-number">3</span>
          </a><!-- End Messages Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
            <li class="dropdown-header">
              You have 3 new messages
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <div>
                  <h4>Maria Hudson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>4 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <div>
                  <h4>Anna Nelson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>6 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <div>
                  <h4>David Muldon</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>8 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="dropdown-footer">
              <a href="#">Show all messages</a>
            </li>

          </ul><!-- End Messages Dropdown Items -->

        </li><!-- End Messages Nav -->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <!-- <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle"> -->
            <span class="d-none d-md-block dropdown-toggle ps-2">Admin</span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>Admin</h6>
              <span>Web Designer</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="index.html">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <!-- <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Tables</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="tables-general.html">
              <i class="bi bi-circle"></i><span>General Tables</span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span>Data Tables</span>
            </a>
          </li>
        </ul>
      </li>End Tables Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-bar-chart"></i><span>Charts</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="charts-chartjs.html">
              <i class="bi bi-circle"></i><span>Chart.js</span>
            </a>
          </li>
          <!-- <li>
            <a href="charts-apexcharts.html">
              <i class="bi bi-circle"></i><span>ApexCharts</span>
            </a>
          </li>
          <li>
            <a href="charts-echarts.html">
              <i class="bi bi-circle"></i><span>ECharts</span>
            </a>
          </li> -->
        </ul>
      </li><!-- End Charts Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.html">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-register.html">
          <i class="bi bi-card-list"></i>
          <span>Register</span>
        </a>
      </li><!-- End Register Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="logout.php">
          <i class="bi bi-box-arrow-in-right"></i>
          <span>Logout</span>
        </a>
      </li><!-- End Login Page Nav -->

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row">
             <!-- Revenue Card -->
             <div class="col-xxl-4 col-md-4">
              <div class="card info-card revenue-card">

                <div class="card-body">
                  <h5 class="card-title">Control <span>| SYSTEM</span></h5>
                    <h4 class="LEDColor"><i class="fas fa-lightbulb"></i> AUTO</h4>
                    <label class="switch">
                        <input type="checkbox" id="ESP32_01_TogLED_01_Auto" onclick="GetTogBtnAutoState('ESP32_01_TogLED_01_Auto')">
                        <div class="sliderTS slideManual"></div>
                    </label>
                </div>

              </div>
            </div><!-- End Revenue Card -->

            <!-- Sales Card -->
            <div class="col-xxl-4 col-md-4">
              <div class="card info-card sales-card">

                <div class="card-body">
                  <h5 class="card-title">Control <span>| LED</span></h5>
                  <h4 class="LEDColor"><i class="fas fa-lightbulb"></i> LED 1</h4>
                    <label class="switch">
                        <input type="checkbox" id="ESP32_01_TogLED_01" onclick="GetTogBtnLEDState('ESP32_01_TogLED_01')">
                        <div class="sliderTS"></div>
                    </label>
                    
                </div>

              </div>
            </div><!-- End Sales Card -->

            <!-- Revenue Card -->
            <div class="col-xxl-4 col-md-4">
              <div class="card info-card revenue-card">

                <div class="card-body">
                  <h5 class="card-title">Control <span>| LED</span></h5>
                  
                    <h4 class="LEDColor"><i class="fas fa-lightbulb"></i> LED 2</h4>
                    <label class="switch">
                        <input type="checkbox" id="ESP32_01_TogLED_02" onclick="GetTogBtnLEDState('ESP32_01_TogLED_02')">
                        <div class="sliderTS"></div>
                    </label>
                    
                </div>

              </div>
            </div><!-- End Revenue Card -->

            <!-- Customers Card -->
            <div class="col-xxl-4 col-md-3">

              <div class="card info-card customers-card">

                <div class="card-body">
                  <h5 class="card-title">Temperature <span>| Now</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-thermometer-half"></i>
                    </div>
                    <div class="ps-3">
                      <p class="temperatureColor"><span class="reading"><span id="ESP32_01_Temp"></span> &deg;C</span></p>
                    </div>
                  </div>

                </div>
              </div>

            </div><!-- End Customers Card -->

            <div class="col-xxl-4 col-md-3">

              <div class="card info-card customers-card">

                <div class="card-body">
                  <h5 class="card-title">Humidity <span>| Now</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-moisture"></i>
                    </div>
                    <div class="ps-3">
                    <p class="humidityColor"><span class="reading"><span id="ESP32_01_Humd"></span> &percnt;</span></p>

                    </div>
                  </div>

                </div>
              </div>

            </div>

            <div class="col-xxl-4 col-md-3">

              <div class="card info-card customers-card">

                <div class="card-body">
                  <h5 class="card-title">Lux <span>| Now</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-lightbulb"></i>
                    </div>
                    <div class="ps-3">
                    <p class="luxColor"><span class="reading"><span id="ESP32_01_Lux"></span> &percnt;</span></p>
                    </div>
                  </div>

                </div>
              </div>

            </div>

            <div class="col-xxl-4 col-md-3">

              <div class="card info-card customers-card">

                <div class="card-body">
                  <h5 class="card-title">PPM <span>| Now</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="20" viewBox="0 0 640 512">
                      <path d="M32 144c0 79.5 64.5 144 144 144H299.3c22.6 19.9 52.2 32 84.7 32s62.1-12.1 84.7-32H496c61.9 0 112-50.1 112-112s-50.1-112-112-112c-10.7 0-21 1.5-30.8 4.3C443.8 27.7 401.1 0 352 0c-32.6 0-62.4 12.2-85.1 32.3C242.1 12.1 210.5 0 176 0C96.5 0 32 64.5 32 144zM616 368H280c-13.3 0-24 10.7-24 24s10.7 24 24 24H616c13.3 0 24-10.7 24-24s-10.7-24-24-24zm-64 96H440c-13.3 0-24 10.7-24 24s10.7 24 24 24H552c13.3 0 24-10.7 24-24s-10.7-24-24-24zm-192 0H24c-13.3 0-24 10.7-24 24s10.7 24 24 24H360c13.3 0 24-10.7 24-24s-10.7-24-24-24zM224 392c0-13.3-10.7-24-24-24H96c-13.3 0-24 10.7-24 24s10.7 24 24 24H200c13.3 0 24-10.7 24-24z"/>
                    </svg>
                    </div>
                    <div class="ps-3">
                    <p style="color: black;" class="ESP32_01_Ppm"><span class="reading"><span id="ESP32_01_Lux"></span> &percnt;</span></p>
                    </div>
                  </div>

                </div>
              </div>

            </div>

          </div>
        </div><!-- End Left side columns -->


      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Admin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed by <a href="https://facebook.com/danghuynh2303" target="_blank">Dang Huynh</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="apexcharts.min.js"></script>
  <script src="bootstrap.bundle.min.js"></script>
  <!-- <script src="assets/vendor/chart.js/chart.umd.js"></script> -->
  <!-- <script src="assets/vendor/echarts/echarts.min.js"></script> -->
  <!-- <script src="assets/vendor/quill/quill.min.js"></script> -->
  <!-- <script src="assets/vendor/simple-datatables/simple-datatables.js"></script> -->
  <!-- <script src="assets/vendor/tinymce/tinymce.min.js"></script> -->
  <!-- <script src="assets/vendor/php-email-form/validate.js"></script> -->

  <!-- Template Main JS File -->
  <script src="main.js"></script>

</body>
<?php
    }
  }
 ?>
</html>