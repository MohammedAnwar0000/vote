<?php
session_start();
require_once( 'database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['phone'] ) {
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['user_phone'] = $phone;
        header("Location:./index.php");
        exit();
    } else {
        $error = "رقم الهاتف غير مسموح به.";
    }
}
?>

