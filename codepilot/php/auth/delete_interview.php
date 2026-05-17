<?php
require_once "header_helper.php";
require_once "../config/connect.php";

$id = $_GET['id'] ?? 0;
$userId = $_SESSION['user']['id'];

$del = mysqli_prepare($conn, "DELETE FROM interviews WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($del, "ii", $id, $userId);

if (mysqli_stmt_execute($del)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}