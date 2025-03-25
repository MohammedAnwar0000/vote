<?php
session_start();
require_once('database.php');

// جلب جميع الأسئلة من قاعدة البيانات
$sql_questions = "SELECT * FROM questions";
$result_questions = $conn->query($sql_questions);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتائج التصويت</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/indexstyle.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
        <h1 class="text-center mb-4">📊 نتائج التصويت</h1>

        <?php while($row = $result_questions->fetch_assoc()): ?>
            <div class="card shadow-lg mb-4">
                <div class="card-body">
                    <h3 class="card-title text-primary">❓ <?= $row['question_text'] ?></h3>
                    
                    <?php
                    $question_id = $row['id'];
                    $sql_choices = "SELECT * FROM choices WHERE question_id = $question_id";
                    $result_choices = $conn->query($sql_choices);
                    $choices_data = [];
                    $total_votes = 0;
                    
                    while ($choice = $result_choices->fetch_assoc()): 
                        // احصل على عدد الأصوات لكل اختيار
                        $choice_id = $choice['id'];
                        $sql_votes = "SELECT COUNT(*) AS vote_count FROM votes WHERE choice_id = $choice_id";
                        $vote_result = $conn->query($sql_votes);
                        $vote_count = $vote_result->fetch_assoc()['vote_count'] ?? 0;
                        
                        // احصل على قائمة المصوتين لكل اختيار
                        $sql_voters = "SELECT users.name FROM votes 
                                       JOIN users ON votes.user_phone = users.phone
                                       WHERE votes.choice_id = $choice_id";
                        $voter_result = $conn->query($sql_voters);
                        $voters = [];
                        while ($voter = $voter_result->fetch_assoc()) {
                            $voters[] = $voter['name'];
                        }
                        
                        $choices_data[] = [
                            'text' => $choice['choice_text'], 
                            'votes' => $vote_count, 
                            'voters' => $voters
                        ];
                        $total_votes += $vote_count;
                    endwhile;

                    // ترتيب الاختيارات بناءً على عدد الأصوات
                    usort($choices_data, function($a, $b) {
                        return $b['votes'] - $a['votes'];
                    });
                    ?>

                    <canvas id="chart-<?= $question_id ?>" class="my-4"></canvas>

                    <div>
                        <?php foreach ($choices_data as $index => $choice_data): ?>
                            <div class="d-flex align-items-center mb-2 p-2 border rounded <?= $index < 2 ? 'bg-light' : '' ?>">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span class="fw-bold flex-grow-1"><?= $choice_data['text'] ?></span>
                                <span class="badge bg-primary"><?= $choice_data['votes'] ?> صوت</span>
                            </div>
                            
                            <!-- عرض قائمة المصوتين -->
                            <div class="ms-4 mb-2 text-muted">
                                <strong>المصوتين:</strong>
                                <ul>
                                    <?php foreach ($choice_data['voters'] as $voter): ?>
                                        <li><?= $voter ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- عرض ملخص أعلى إجابتين -->
                    <div class="mt-4">
                        <h5>الملخص: أعلى إجابتين</h5>
                        <ul>
                            <?php for ($i = 0; $i < 2; $i++): ?>
                                <li>
                                    <strong><?= $choices_data[$i]['text'] ?>:</strong>
                                    <?= $choices_data[$i]['votes'] ?> صوت
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <script>
                var ctx = document.getElementById("chart-<?= $question_id ?>").getContext("2d");
                var chart = new Chart(ctx, {
                    type: 'bar', // تغيير نوع الرسم إلى شريطي
                    data: {
                        labels: <?= json_encode(array_column($choices_data, 'text')) ?>,
                        datasets: [{
                            label: 'عدد الأصوات',
                            data: <?= json_encode(array_column($choices_data, 'votes')) ?>,
                            backgroundColor: ['#007bff', '#28a745', '#dc3545', '#ffc107', '#6c757d'],
                            borderColor: ['#0056b3', '#218838', '#c82333', '#e0a800', '#5a6268'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return tooltipItem.label + ': ' + tooltipItem.raw + ' صوت';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            },
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        <?php endwhile; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
