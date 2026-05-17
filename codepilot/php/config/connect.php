<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "codepilot";

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

if (!$conn) {
    die("Ошибка подключения к БД");
}

mysqli_set_charset($conn, "utf8mb4");