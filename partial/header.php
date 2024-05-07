<?php
session_start();

if (isset($_SESSION["UserID"])) {

    $mysqli = require __DIR__ . "/database.php";

    $sql = "SELECT * FROM account WHERE UserID = {$_SESSION["UserID"]}";

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();


}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
</head>
<div class="header">
   <a href="index.php">
    <img src="/img/logoLeft.png" class="logo">
   </a>
    <?php if (isset($user)): ?>
<div class="nav-container">
        <nav>
            <ul>
                <li><a href="dashboards.php">Your Dashboard</a></a></li>
                <li><a href="booking.php">Book a space</a></li>
                <li><a href="logout.php">Log out</a></li>
            </ul>

        </nav>
    <?php else: ?>
        <nav>
            <ul>
                <li><a href="login.php">Log in</a> </li>
                <li><a href="signup.php">Sign up</a></li>
            </ul>
        </nav>

    <?php endif; ?>
</div>
</div>