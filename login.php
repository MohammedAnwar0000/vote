<?php
session_start();
require_once('database.php');

$error = ''; // تعريف الرسالة الخطأ

// التحقق من أن البيانات قد تم إرسالها عبر POST وأن الحقل phone يحتوي على قيمة
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['phone'])) {
    $phone = $_POST['phone'];

    // تحضير الاستعلام للبحث عن الهاتف في قاعدة البيانات
    $stmt = $conn->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    // إذا تم العثور على المستخدم
    if ($result->num_rows > 0) {
         $username = mysqli_fetch_assoc($result)['name'];
        // تخزين رقم الهاتف في الجلسة
        $_SESSION['user_phone'] = $phone;
        $_SESSION['user_name'] = $username;
        // تأكد من أنه لا يوجد أي مخرجات قبل هذه السطر
        header("Location: ./index.php");  // إعادة توجيه المستخدم
        exit();  // تأكد من خروج السكربت بعد الإعادة لتجنب تنفيذ أكواد إضافية
    } else {
        // إذا لم يتم العثور على رقم الهاتف في قاعدة البيانات
        $error = "رقم الهاتف غير مسموح به.";
    }

    // إغلاق الاستعلام
    $stmt->close();
} else {
    $error = "يرجى إدخال رقم الهاتف.";
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #e4f0f6; /* لون الخلفية الفاتحة */
            font-family: 'Arial', sans-serif;
            color: #4a4a4a;
        }

        .container {
            max-width: 380px;
            margin-top: 80px;
            padding: 40px 30px;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #006c8e;
            font-weight: 700;
        }

        .form-label {
            font-weight: 500;
            color: #5a5a5a;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #dcdcdc;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        }

        .form-control:focus {
            border-color: #00aaff;
            box-shadow: 0 0 10px rgba(0, 170, 255, 0.5);
        }

        .btn-primary {
            background-color: #00aaff;
            border-color: #00aaff;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0077aa;
            border-color: #006c8e;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 16px;
        }

        .input-group-text {
            background-color: #00aaff;
            color: white;
            border: none;
            border-radius: 10px 0 0 10px;
        }

        .input-group .form-control {
            border-radius: 0 10px 10px 0;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: #777;
            font-size: 14px;
        }

        .footer a {
            color: #006c8e;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>تسجيل الدخول</h2>

        <!-- عرض رسالة الخطأ إن وجدت -->
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- نموذج إدخال رقم الهاتف -->
        <form action="" method="POST">
            <div class="mb-3">
                <label for="phone" class="form-label">رقم الهاتف</label>
                <div class="input-group">
                    <span class="input-group-text">+20</span>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="أدخل رقم هاتفك" >
                </div>
            </div>
            <button type="submit" class="btn btn-primary">تسجيل الدخول</button>
        </form>

        <div class="footer">
            <p>حقوق الطبع والنشر &copy; 2025 جميع الحقوق محفوظة</p>
            <a href="#">الشروط والأحكام</a> | <a href="#">سياسة الخصوصية</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
