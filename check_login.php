<?php 
if(count($_POST) > 0){
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if(checkLogin($username, $password)){
        session_start();
        $_SESSION['username'] = $username;
        // Taking current system Time
        $_SESSION['start'] = time(); 
  
        // Destroying session after ... minute
        $_SESSION['expire'] = $_SESSION['start'] + (1 * 60*500) ;
        
        header("Location:index.php");
    } else {
        header("Location:login.php?username=$username&password=$password");
    }

} else {
    header("Location:login.php");
}

function checkLogin($username, $password) {
    include 'connect_db.php';
    
    $conn = getConnect();

    $result = mysqli_query($conn, "SELECT count(*) as count FROM user where user = '" . $username . "' and password = '" . $password . "'");
    while ($row = mysqli_fetch_array($result)) {
        if ($row['count'] == '1') {
            return true;
        }
    }

    return false;
}