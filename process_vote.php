<?php
require("database.php");
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_phone'])) {
    // إذا تم إرسال التصويت
    $votes = [];
    $phone = $_SESSION['user_phone'] ;

   
    // اجلب كل الخيارات المختارة من الأسئلة
    foreach ($_POST as $question_key => $choice_id) {
        // تحقق إذا كانت القيمة تحتوي على "question-" في البداية
        if (strpos($question_key, 'question-') === 0) {
            // تأكد من أن choice_id هو قيمة صحيحة
            $votes[$question_key] = $choice_id;
        }
    }
    if(!is_completed($votes)){
        $_SESSION['error_vote'] = 'أجب عن كل الأسئلة';
        header("location:./index.php")    ;
        exit();
    }
    if( is_voted($phone)){

        $_SESSION['error_vote'] = 'لقد قمت بالتصويت سابقا يا ' . $_SESSION['user_name'];
        header("location:./index.php")    ;
        exit();
    
       }
    // يمكنك هنا تخزين هذه البيانات في قاعدة البيانات أو التعامل معها

    foreach ($votes as $question_key => $choice_id) {
        $question_id = str_replace('question-', '', $question_key);
        // استعلام لحفظ التصويت في قاعدة البيانات
        $sql = "INSERT INTO `votes` (`user_phone`,`question_id`, `choice_id`) VALUES ('$phone' , '$question_id' , '$choice_id')";
        $result =mysqli_query($conn , $sql);
        if(!$result){
            echo"Error".mysqli_connect_error();
            $_SESSION['error_vote'] = 'لقد قمت بالتصويت سابقا يا ' . $_SESSION['user_name'];

            header("location:./index.php")    ;


        }
    }
       $_SESSION['su_vote'] = ' شكرا لك علي مساهمتك معنا الفطار بإذن الله يوم الخميس يا  '. $_SESSION['user_name']  ;

        header("location:./index.php")    ;

       
}else{

}

function is_voted($phone){
    require("database.php");
   // تحضير الاستعلام للبحث عن الهاتف في قاعدة البيانات
   $stmt = $conn->prepare("SELECT * FROM votes WHERE user_phone = ?");
   $stmt->bind_param("s", $phone);
   $stmt->execute();
   $result = $stmt->get_result();

   // إذا تم العثور على المستخدم
   if ($result->num_rows > 0) {
    return true ;
       exit();  // تأكد من خروج السكربت بعد الإعادة لتجنب تنفيذ أكواد إضافية
   } else {
    return false ;

}

}

function is_completed($votes){
    require("database.php");
   // تحضير الاستعلام للبحث عن الهاتف في قاعدة البيانات
   $stmt = $conn->prepare("SELECT * FROM questions");
   $stmt->execute();
   $result = $stmt->get_result();

   // إذا تم العثور على المستخدم
   if ($result->num_rows > 0) {
    if($result->num_rows == count($votes)){
        return true ;
        exit(); 
    }
    
   } else {
    return false ;

}

}
?>
