<?php
require_once "header_helper.php";
require_once "../config/connect.php";

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(["success" => false]);
    exit;
}

$userId = $_SESSION['user']['id'];

$query = "SELECT id, login, nickname, email, image_id, country, city, bio, level, tech_stack,
          (SELECT COUNT(*) FROM interviews WHERE user_id = users.id AND status = 'completed') as interview_count,
          (SELECT AVG(score) FROM interviews WHERE user_id = users.id AND status = 'completed') as avg_score,
          (SELECT SUM(correct_answers) FROM interviews WHERE user_id = users.id AND status = 'completed') as total_correct,
          (SELECT l.title FROM interviews i JOIN languages l ON i.language_id = l.id WHERE i.user_id = users.id GROUP BY i.language_id ORDER BY COUNT(*) DESC LIMIT 1) as fav_lang
          FROM users WHERE id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user) {
    echo json_encode(["success" => true, "user" => $user]);
} else {
    session_destroy();
    echo json_encode(["success" => false]);
}