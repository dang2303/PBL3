<?php
    include 'connect_db.php';
    global $led_1;
    if(!empty($_POST)){
        $led_1 = $_POST["ledstate"];
        $conn = getConnect();

        $result = mysqli_query($conn, "UPDATE `update_db` SET `led1` = '$led_1' WHERE id = (SELECT MAX(id) FROM `update_db`)");
    }
?>