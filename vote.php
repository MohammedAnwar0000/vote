<?php
session_start();
include 'database.php';

if (!isset($_SESSION['phone'])) {
    die("يجب عليك تسجيل الدخول أولاً");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_SESSION['phone'];
    $votes = $_POST['votes']; // يجب أن يكون votes مصفوفة تحتوي على [question_id => choice]
    
    foreach ($votes as $question_id => $choice) {
        $question_id = intval($question_id);
        $choice = trim($choice);
        
        // التحقق مما إذا كان المستخدم قد صوت لهذا السؤال بالفعل
        $check_vote = "SELECT * FROM votes WHERE user_phone = ? AND question_id = ?";
        $stmt = $conn->prepare($check_vote);
        $stmt->bind_param("si", $phone, $question_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            // إدخال التصويت في قاعدة البيانات
            $insert_vote = "INSERT INTO votes (user_phone, question_id, choice) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_vote);
            $stmt->bind_param("sis", $phone, $question_id, $choice);
            $stmt->execute();
        }
    }
    echo "تم التصويت بنجاح";
}
?>
