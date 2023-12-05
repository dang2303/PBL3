<?php 

define('HOST', 'localhost');
define('USERNAME', 'root');
define('PASSWORD', '');
define('DB_NAME', 'pbl3');

function getConnect(){
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_NAME) or die();
    mysqli_query($conn, "set names 'utf8'");
    
    return $conn;
}
?>