<?php
require_once "header_helper.php";
require_once "../config/connect.php";

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(["success" => false, "message" => "Доступ запрещен"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$userId = $_SESSION['user']['id'];
$oldPass = $data['oldPass'] ?? '';
$newPass = $data['newPass'] ?? '';

if (empty($oldPass) || empty($newPass)) {
    echo json_encode(["success" => false, "message" => "Заполните все поля"]);
    exit;
}

$query = "SELECT password FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);

if (!password_verify($oldPass, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Текущий пароль введен неверно"]);
    exit;
}

$newHash = password_hash($newPass, PASSWORD_DEFAULT);

$update = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
mysqli_stmt_bind_param($update, "si", $newHash, $userId);

if (mysqli_stmt_execute($update)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Ошибка при сохранении в базу данных"]);
}