<?php
session_start();

if (isset($_SESSION["UserID"])) {

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
<?php if (isset($user)): ?>

    <p>Hello <?= htmlspecialchars($user["Username"]) ?></p>
    <li><a href="index.php">Home</a></li>
    <li><a href="dashboards.php">Your Dashboard</a></a></li>
    <li><a href="logout.php">Log out</a></li>
    </ul>


<?php else: ?>


<li><a href="login.php">Log in</a> </li>
<li><a href="signup.html">sign up</a></li>
<li></li>
<li></li>
</ul>


<?php endif; ?>
</body>
