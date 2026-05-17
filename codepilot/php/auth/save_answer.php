<?php
require_once "header_helper.php";
require_once "../config/connect.php";

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(["success" => false, "message" => "Auth required"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$interviewId = $data['interview_id'];
$order = $data['order'];
$answerIds = $data['answer_ids'];

$qQuery = "SELECT question_id FROM interview_questions WHERE interview_id = ? AND question_order = ?";
$stmtQ = mysqli_prepare($conn, $qQuery);
mysqli_stmt_bind_param($stmtQ, "ii", $interviewId, $order);
mysqli_stmt_execute($stmtQ);
$questionId = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtQ))['question_id'];

$del = mysqli_prepare($conn, "DELETE FROM interview_answers WHERE interview_id = ? AND question_id = ?");
mysqli_stmt_bind_param($del, "ii", $interviewId, $questionId);
mysqli_stmt_execute($del);

foreach ($answerIds as $ansId) {
    $check = mysqli_prepare($conn, "SELECT is_correct FROM question_answers WHERE id = ?");
    mysqli_stmt_bind_param($check, "i", $ansId);
    mysqli_stmt_execute($check);
    $isCorrect = mysqli_fetch_assoc(mysqli_stmt_get_result($check))['is_correct'];

    $ins = mysqli_prepare($conn, "INSERT INTO interview_answers (interview_id, question_id, answer_id, is_correct) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins, "iiii", $interviewId, $questionId, $ansId, $isCorrect);
    mysqli_stmt_execute($ins);
}

$upd = mysqli_prepare($conn, "UPDATE interviews SET current_question_index = ? WHERE id = ?");
mysqli_stmt_bind_param($upd, "ii", $order, $interviewId);
mysqli_stmt_execute($upd);

echo json_encode(["success" => true]);