<?php
include('partial/header.php');

$token = $_GET["token"];

$token_hash = hash("sha256", $token);

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT * FROM account
        WHERE account_activation_hash = ?";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    die("token not found");
}

$sql = "UPDATE account
        SET account_activation_hash = NULL
        WHERE UserID = ?";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $user["UserID"]);

$stmt->execute();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Account Activated</title>
    <meta charset="UTF-8">
</head>
<body>

<h1>Account Activated</h1>

<p>Account activated successfully. You can now
    <a href="login.php">log in</a>.</p>

</body>
</html>