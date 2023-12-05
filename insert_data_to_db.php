<?php
    include 'connect_db.php';
    global $LED_01;
    if(!empty($_POST)){
        $LED_01 = $_POST["LED_01"];
    }

    $conn = getConnect();

    $result = mysqli_query($conn, "INSERT INTO `update_db`(`MCU`, `led1`) VALUES ('ESP32_01','{$LED_01}')");
?>