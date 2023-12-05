<?php
    include 'connect_db.php';

    if(!empty($_POST)){
        $MCU = $_POST["MCU"];

        $myObj = (object)array();
        $conn = getConnect();
        $sql = 'SELECT * FROM `esp32_table_dht11_leds_record` WHERE board =  "'. $MCU .'" ORDER BY `id` DESC LIMIT 1';
        $result = mysqli_query($conn, $sql);
        foreach ($result as $row){
            $myObj->mcu = $row['board'];
            $myObj->led_01 = $row['LED_01'];
            $myObj->led_02 = $row['LED_02'];
            $myObj->temperature = $row['temperature'];
            $myObj->humidity = $row['humidity'];
            $myObj->lux_status = $row['lux_status'];
        }
        $myJSON = json_encode($myObj);
      
        echo $myJSON;
    }

    
?>