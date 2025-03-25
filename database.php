<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "voting_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
?>
