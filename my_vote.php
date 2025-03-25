<?php
session_start();
require_once('database.php');

// تحقق من أن هناك مستخدم مسجل الدخول
if (!isset($_SESSION['user_phone'])) {
    // إعادة توجيه إلى صفحة الدخول إذا لم يكن هناك جلسة مستخدم
    header("Location: login.php");
    exit();
}

$user_phone = $_SESSION['user_phone']; // أو يمكنك استخدام المعرف الذي يتم تخزينه في الجلسة
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتائج التصويت - شخصي</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./css/indexstyle.css">

</head>
<body style="direction: rtl;">
     <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container container_nav">
            <a class="navbar-brand" href="#"> دايما متجمعين</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="./index.php">التصويت</a></li>
                    <li class="nav-item"><a class="nav-link" href="./results.php">نتائج التصويت</a></li>
                    <li class="nav-item"><a class="nav-link" href="./logout.php"> تسجيل الخروج</a></li>

                </ul>
                <h4 style="color:white; border: blue 1px solid ; padding:5px 15px ; background-color: orangered; border-radius: 10px;cursor:pointer"><a class="nav-link" href="./my_vote.php"> <?php echo $_SESSION['user_name']?></a></h4>

            </div>

        </div>

    </nav>
    
    <div class="container py-4">
        <h1 class="text-center mb-4">📊 نتائج التصويت الشخصية</h1>

        <?php
        // جلب جميع الأسئلة من قاعدة البيانات
        $sql_questions = "SELECT * FROM questions";
        $result_questions = $conn->query($sql_questions);

        while ($row = $result_questions->fetch_assoc()):
            // جلب اختيارات السؤال
            $question_id = $row['id'];
            $sql_choices = "SELECT * FROM choices WHERE question_id = $question_id";
            $result_choices = $conn->query($sql_choices);
            ?>

            <div class="card shadow-lg mb-4">
                <div class="card-body">
                    <h3 class="card-title text-primary">❓ <?= $row['question_text'] ?></h3>

                    <?php
                    while ($choice = $result_choices->fetch_assoc()):
                        // جلب إجابة المستخدم لهذا الاختيار
                        $choice_id = $choice['id'];
                        $sql_user_answer = "SELECT * FROM votes WHERE choice_id = $choice_id AND user_phone = $user_phone";
                        $result_user_answer = $conn->query($sql_user_answer);

                        if ($result_user_answer->num_rows > 0):
                            $user_vote = $result_user_answer->fetch_assoc();
                            ?>
                            <div class="d-flex align-items-center mb-2 p-2 border rounded" style="background-color: goldenrod;">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span class="fw-bold flex-grow-1" ><?= $choice['choice_text'] ?></span>
                                <span class="badge bg-success">تم التصويت</span>
                            </div>
                        <?php else: ?>
                            <div class="d-flex align-items-center mb-2 p-2 border rounded">
                                <i class="fas fa-times-circle text-danger me-2"></i>
                                <span class="fw-bold flex-grow-1"><?= $choice['choice_text'] ?></span>
                                <span class="badge bg-secondary">لم يتم التصويت</span>
                            </div>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </div>
            </div>

        <?php endwhile; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
