<?php

session_start();

if (isset($_SESSION["user_id"])) {

    $mysqli = require __DIR__ . "/database.php";

    $sql = "SELECT * FROM account
            WHERE UserID = {$_SESSION["UserID"]}";

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>

<h1>Manage your cars</h1>
    <p>Hello <?= htmlspecialchars($user["Username"]) ?></p>
    <p><a href="logout.php">Log out</a></p>
<p><a href="addCar.php">add Cars</a></p>
<p><a href="removeCars.php">remove Cars</a></p>


</body>
</html>