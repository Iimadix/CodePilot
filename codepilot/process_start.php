<?php
require_once "php/auth/header_helper.php";
require_once "php/config/connect.php";

$userId = $_SESSION['user']['id'] ?? null;

if (!$userId) { 
    header("Location: auth.php"); 
    exit; 
}

$routeId = $_GET['r'] ?? 0;
$langId = $_GET['l'] ?? 0;
$levelId = $_GET['lv'] ?? 0;

$limitQuery = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM interviews WHERE user_id = ?");
mysqli_stmt_bind_param($limitQuery, "i", $userId);
mysqli_stmt_execute($limitQuery);
$limitResult = mysqli_stmt_get_result($limitQuery);
$countData = mysqli_fetch_assoc($limitResult);

if ($countData['total'] >= 10) {
    header("Location: interview.php?error=limit");
    exit;
}

$query = "SELECT id, question FROM questions 
          WHERE route_id = ? AND language_id = ? AND experience_level_id = ? AND is_active = 1 
          ORDER BY RAND() LIMIT 10";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iii", $routeId, $langId, $levelId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$questions = mysqli_fetch_all($res, MYSQLI_ASSOC);

if (count($questions) < 1) {
    die("К сожалению, в этой категории еще нет вопросов. Попробуйте другую!");
}

$totalQuestions = count($questions);
$createInterview = mysqli_prepare($conn, "INSERT INTO interviews (user_id, route_id, language_id, experience_level_id, total_questions) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($createInterview, "iiiii", $userId, $routeId, $langId, $levelId, $totalQuestions);
mysqli_stmt_execute($createInterview);
$interviewId = mysqli_insert_id($conn);

$order = 1;
foreach ($questions as $q) {
    $stmtAnswers = mysqli_prepare($conn, "SELECT id, answer, is_correct FROM question_answers WHERE question_id = ?");
    mysqli_stmt_bind_param($stmtAnswers, "i", $q['id']);
    mysqli_stmt_execute($stmtAnswers);
    $ansRes = mysqli_stmt_get_result($stmtAnswers);
    $answers = mysqli_fetch_all($ansRes, MYSQLI_ASSOC);

    $snapshot = json_encode([
        'question_text' => $q['question'],
        'answers' => $answers 
    ], JSON_UNESCAPED_UNICODE);

    $insQ = mysqli_prepare($conn, "INSERT INTO interview_questions (interview_id, question_id, question_order, question_snapshot) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($insQ, "iiis", $interviewId, $q['id'], $order, $snapshot);
    mysqli_stmt_execute($insQ);
    
    $order++;
}

header("Location: take_interview.php?id=" . $interviewId);
exit;