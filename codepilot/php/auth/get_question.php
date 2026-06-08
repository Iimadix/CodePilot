<?php 
require_once "header_helper.php"; 
require_once "../config/connect.php";

$interviewId = intval($_GET['interview_id'] ?? 0); 
$order = intval($_GET['order'] ?? 1); 
$userId = $_SESSION['user']['id'];

$checkQuery = "SELECT id FROM interviews WHERE id = ? AND user_id = ?";
$stmtCheck = mysqli_prepare($conn, $checkQuery);
mysqli_stmt_bind_param($stmtCheck, "ii", $interviewId, $userId);
mysqli_stmt_execute($stmtCheck); 
if (mysqli_num_rows(mysqli_stmt_get_result($stmtCheck)) === 0) { 
    echo json_encode(["success" => false, "message" => "Доступ запрещен"]); 
    exit; 
}

$query = "SELECT iq.question_snapshot, q.difficulty, q.multiple_answers 
          FROM interview_questions iq 
          JOIN questions q ON iq.question_id = q.id 
          WHERE iq.interview_id = ? AND iq.question_order = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $interviewId, $order);
mysqli_stmt_execute($stmt); 
$res = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($res);

if ($data) { 
    $snapshot = json_decode($data['question_snapshot'], true);

    $ansQuery = "SELECT answer_id FROM interview_answers WHERE interview_id = ? AND question_id = (
        SELECT question_id FROM interview_questions WHERE interview_id = ? AND question_order = ?
    )";
    $stmtA = mysqli_prepare($conn, $ansQuery);
    mysqli_stmt_bind_param($stmtA, "iii", $interviewId, $interviewId, $order);
    mysqli_stmt_execute($stmtA);
    $selected = mysqli_fetch_all(mysqli_stmt_get_result($stmtA), MYSQLI_ASSOC);
    $selectedIds = array_map('intval', array_column($selected, 'answer_id'));

    echo json_encode([
        'success' => true,
        'question' => htmlspecialchars($snapshot['question_text'] ?? ''),
        'answers' => array_map(function($a) {
            $a['answer'] = htmlspecialchars($a['answer']);
            return $a;
        }, $snapshot['answers'] ?? []),
        'difficulty' => $data['difficulty'],
        'multiple' => $data['multiple_answers'],
        'selected' => $selectedIds
    ]);

} else { 
    echo json_encode(['success' => false, 'message' => 'Вопрос не найден']); 
}