<?php 
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once "php/config/connect.php";

    $interviewId = $_GET['id'] ?? 0;
    $userId = $_SESSION['user']['id'];

    $query = "SELECT i.*, l.title as lang_title, r.title as route_title, el.title as level_title 
              FROM interviews i 
              JOIN languages l ON i.language_id = l.id 
              JOIN routes r ON i.route_id = r.id
              JOIN experience_levels el ON i.experience_level_id = el.id
              WHERE i.id = ? AND i.user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $interviewId, $userId);
    mysqli_stmt_execute($stmt);
    $interview = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$interview) { header("Location: interview.php"); exit; }

    if ($interview['status'] === 'in_progress') {
        $scoreQuery = "SELECT COUNT(DISTINCT question_id) as correct_count 
                       FROM interview_answers 
                       WHERE interview_id = ? AND is_correct = 1";
        $stmtS = mysqli_prepare($conn, $scoreQuery);
        mysqli_stmt_bind_param($stmtS, "i", $interviewId);
        mysqli_stmt_execute($stmtS);
        $correctCount = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtS))['correct_count'];

        $finalScore = ($correctCount / $interview['total_questions']) * 100;
        
        $update = mysqli_prepare($conn, "UPDATE interviews SET status = 'completed', score = ?, correct_answers = ?, completed_at = NOW() WHERE id = ?");
        mysqli_stmt_bind_param($update, "iii", $finalScore, $correctCount, $interviewId);
        mysqli_stmt_execute($update);
        
        $interview['score'] = $finalScore;
        $interview['correct_answers'] = $correctCount;
        $interview['status'] = 'completed';
    }

    $qQuery = "SELECT iq.*, 
               (SELECT GROUP_CONCAT(answer_id) FROM interview_answers ia WHERE ia.interview_id = iq.interview_id AND ia.question_id = iq.question_id) as user_answers
               FROM interview_questions iq 
               WHERE iq.interview_id = ? 
               ORDER BY iq.question_order ASC";
    $stmtQ = mysqli_prepare($conn, $qQuery);
    mysqli_stmt_bind_param($stmtQ, "i", $interviewId);
    mysqli_stmt_execute($stmtQ);
    $questionsData = mysqli_fetch_all(mysqli_stmt_get_result($stmtQ), MYSQLI_ASSOC);

    $page_title = "Результаты";
    $extra_css = "resultsstyle.css";
    require_once "components/header.php"; 
?>

<main class="container">
    <div class="results-wrapper">
        <div class="results-details">
            <h2 class="section-title">Разбор вопросов</h2>
            
            <?php foreach ($questionsData as $item): 
                $snapshot = json_decode($item['question_snapshot'], true);
                $userAnsIds = explode(',', $item['user_answers'] ?? '');
                
                $correctClass = 'wrong';
                $checkStatus = mysqli_query($conn, "SELECT is_correct FROM interview_answers WHERE interview_id = $interviewId AND question_id = {$item['question_id']} LIMIT 1");
                $statusRow = mysqli_fetch_assoc($checkStatus);
                if($statusRow && $statusRow['is_correct'] == 1) $correctClass = 'correct';
            ?>
                <div class="res-card <?php echo $correctClass; ?>">
                    <div class="res-card__num">Вопрос <?php echo $item['question_order']; ?></div>
                    <h3><?php echo htmlspecialchars($snapshot['question_text']); ?></h3>
                    
                    <div class="res-answers">
                        <strong>Ваши ответы:</strong>
                        <ul>
                            <?php 
                            $foundAny = false;
                            foreach($snapshot['answers'] as $ans): 
                                if(in_array($ans['id'], $userAnsIds)): $foundAny = true;
                            ?>
                                <li><?php echo htmlspecialchars($ans['answer']); ?></li>
                            <?php endif; endforeach; 
                            if(!$foundAny) echo "<li><i>Ответ не выбран</i></li>";
                            ?>
                        </ul>
                    </div>

                    <?php if($correctClass === 'wrong'): ?>
                    <div class="res-correct">
                        <strong>Правильный ответ:</strong>
                        <p>
                            <?php 
                            $correctStrs = [];
                            foreach($snapshot['answers'] as $ans) {
                                if(isset($ans['is_correct']) && $ans['is_correct'] == 1) $correctStrs[] = htmlspecialchars($ans['answer']);
                            } 
                            echo implode(', ', $correctStrs);
                            ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <aside class="results-sidebar">
            <div class="score-card main-card">
                <div class="progress-ring-wrapper">
                    <svg class="progress-ring" width="160" height="160">
                        <circle class="progress-ring__circle-bg" stroke="#F4F7FF" stroke-width="12" fill="transparent" r="70" cx="80" cy="80"/>
                        <circle class="progress-ring__circle" stroke="#387FF5" stroke-width="12" fill="transparent" r="70" cx="80" cy="80" 
                            style="stroke-dasharray: 440; stroke-dashoffset: <?php echo 440 - (440 * $interview['score'] / 100); ?>;"/>
                    </svg>
                    <div class="score-data">
                        <span class="score-num"><?php echo round($interview['score']); ?>%</span>
                        <span class="score-label">успешно</span>
                    </div>
                </div>
                
                <div class="verdict-badge <?php echo ($interview['score'] >= 70) ? 'pass' : 'fail'; ?>">
                    <?php echo ($interview['score'] >= 70) ? 'Пройдено' : 'Не пройдено'; ?>
                </div>

                <div class="stats-table">
                    <div class="s-row"><span>Направление</span><strong><?php echo $interview['route_title']; ?></strong></div>
                    <div class="s-row"><span>Язык</span><strong><?php echo $interview['lang_title']; ?></strong></div>
                    <div class="s-row"><span>Верно</span><strong><?php echo $interview['correct_answers']; ?> / <?php echo $interview['total_questions']; ?></strong></div>
                </div>

                <div class="action-btns">
                    <a href="interview.php" class="start-btn">К списку тестов</a>
                </div>
            </div>
        </aside>
    </div>
</main>

<?php require_once "components/footer.php"; ?>