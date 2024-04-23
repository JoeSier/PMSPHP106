<?php
include('../partial/header.php');
session_start();

if (isset($_SESSION["user_id"])) {

    $mysqli = require __DIR__ . '/database.php';

    $sql = "SELECT * FROM account
            WHERE UserID = {$_SESSION["UserID"]}";

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();
}

?>
<body>

<p>placeholder</p>
</body>
