<?php
require_once "header_helper.php";
require_once "../config/connect.php";

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data["email"] ?? '');
$password = trim($data["password"] ?? '');

if (!$email || !$password) {
    echo json_encode(["success" => false, "message" => "Заполните все поля"]);
    exit;
}

$query = "SELECT * FROM users WHERE email = ? OR login = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $email, $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user || !password_verify($password, $user["password"])) {
    echo json_encode(["success" => false, "message" => "Неверная почта или пароль"]);
    exit;
}

$_SESSION["user"] = [
    "id" => $user["id"],
    "login" => $user["login"],
    "email" => $user["email"],
    "image_id" => $user["image_id"]
];

echo json_encode(["success" => true, "user" => $_SESSION["user"]]);