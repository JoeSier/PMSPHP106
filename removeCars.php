<?php

session_start();

if (isset($_SESSION["user_id"])) {

    $mysqli = require __DIR__ . "/database.php";

    $sql = "SELECT * FROM account
            WHERE UserID = {$_SESSION["UserID"]}";

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();
}

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $mysqli = require __DIR__ . "/database.php";

    $sql = sprintf("DELETE FROM car
                    WHERE LicensePlate = '%s'" AND UserID= [$_SESSION["UserID"]],
        $mysqli->real_escape_string($_POST["license"]));

    $result = $mysqli->query($sql);

    $car = $result->fetch_assoc();


    if ($car) {
        exit;

    }


    $is_invalid = true;
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

<form method="post">
    <label for="license">email</label>
    <input type="text" name="license" id="license">
<!--           value="--><?php //= htmlspecialchars($_POST["license"] ?? "") ?><!--">-->
    <button>Remove</button>
</form>


</body>
</html>









