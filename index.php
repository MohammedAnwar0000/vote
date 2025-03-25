<?php 
session_start();
if(!isset($_SESSION['user_phone'])){
    header("location:./login.php");
}
require_once("./database.php");

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام التصويت</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/indexstyle.css">
</head>
<body>
    <!-- شريط التنقل -->
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
    
    <div class="container my-4">
        <h1 class="text-center">التصويت على الأطعمة</h1>
        <?php if(isset($_SESSION['su_vote'])): ?>
              <div class="alert alert-success text-center" role="alert">
                  <?php echo $_SESSION['su_vote']; 
                  unset($_SESSION['su_vote']);
                  ?>

             </div>
         <?php endif; ?>
         <?php if (isset($_SESSION['error_vote'])): ?>
              <div class="alert alert-warning text-center" role="alert">
                   <?php echo $_SESSION['error_vote']; 
                   unset($_SESSION['error_vote']);
                   ?>
              </div>
         <?php endif; ?>
        <!-- قسم الأسئلة -->
        <form action="process_vote.php" method="POST">
        <div id="voting-section">
        <?php
        $sql_questions = "SELECT * FROM questions";
        $result_questions = $conn->query($sql_questions);

        if ($result_questions->num_rows > 0) {
            while($row = $result_questions->fetch_assoc()) {
                $question_id = $row["id"];
                echo "<div class='question' id='question-" . $question_id . "'>";
                echo "<h3>" . $row["question_text"] . "</h3>";

                $sql_options = "SELECT * FROM `choices` WHERE question_id = $question_id";
                $result_options = $conn->query($sql_options);

                if ($result_options->num_rows > 0) {
                    echo "<div class='options'>";
                    while($option = $result_options->fetch_assoc()) {
                        echo "<div class='option-card' data-option-id='" . $option["id"] . "'>";
                        echo "<input type='radio' name='question-" . $question_id . "' id='option-" . $option["id"] . "' value='" . $option["id"] . "' >";
                        echo "<label for='option-" . $option["id"] . "'>" . $option["choice_text"] . "</label>";
                        echo "</div>";
                    }
                    echo "</div>";
                }
                echo "</div>"; // نهاية div السؤال
            }
        } else {
            echo "لا توجد أسئلة لعرضها.";
        }
        ?>
        </div>

        <button type="submit" class="btn btn-success w-100 mt-3">إرسال التصويت</button>
        </form>
    </div>

    <!-- نافذة إضافة صنف -->
    <div class="modal" tabindex="-1" id="addOptionModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة صنف جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="add_option.php" method="POST">
                        <input type="hidden" name="question_id" id="question_id">
                        <div class="form-group">
                            <label for="choice_text">نص الخيار</label>
                            <input type="text" id="choice_text" name="choice_text" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">حفظ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
