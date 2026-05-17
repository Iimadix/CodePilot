<?php
require_once "../config/connect.php";

$route_id = $_GET['route_id'] ?? 0;
$query = "SELECT id, title FROM languages WHERE route_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $route_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));