<?php
$mysqli = require __DIR__ . "/database.php";

// Prepared statements to prevent SQL injection
$sql = "SELECT * FROM account WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $_GET["email"]);
$stmt->execute();
$result = $stmt->get_result();

$is_available = $result->num_rows === 0;

header("Content-Type: application/json");
echo json_encode(["available" => $is_available]);

$stmt->close();
$mysqli->close();
?>