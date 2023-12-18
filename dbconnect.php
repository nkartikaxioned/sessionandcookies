<?php
$errorMsg = '';
$dbhost = "localhost";
$dbusername = "phpmyadmin";
$dbpassword = "root";
$dbname = "cookieandsession";
try {
    $dbconnection = mysqli_connect($dbhost, $dbusername, $dbpassword, $dbname);
    if (!$dbconnection) {
        throw new Exception("connection failed" . mysqli_connect_error());
    }
} catch (Exception $e) {
    echo "Exception" . $e->getMessage();
}
?>