<?php

session_start();

if (isset($_SESSION["user_id"])) {

    $mysqli = require __DIR__ . "/database.php";

    $sql = "SELECT * FROM account
            WHERE UserID = {$_SESSION["UserID"]}";

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();

$is_invalid = false;

//if ($_SERVER["REQUEST_METHOD"] === "POST") {
//    $credit = $_POST['Credit']; // Assuming the value comes from a POST request
//    $userID = $_SESSION['UserID'];
//}
//
//    $mysqli = require __DIR__ . "/database.php";
//    $sql = sprintf("UPDATE account SET Credit = :credit WHERE UserID = :userID",
//        $mysqli->real_escape_string($_POST["license"]));
//
//$stmt = $pdo->prepare($sql);
//    $stmt->bindParam(':credit', $credit);
//$stmt->bindParam(':userID', $userID);
//
//
//    $result = $mysqli->query($sql);
//
//    $car = $result->fetch_assoc();
//
//
//    if ($car) {
//        exit;
//
//    }


    $is_invalid = true;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Add funds</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>

<h1>add Funds</h1>
<p>Hello <?= htmlspecialchars($user["Username"]) ?></p>
<p><a href="logout.php">Log out</a></p>
<p><a href="Dashboards.php">Return to Dashboard</a></p>

<form method="post">
    <label for="credit">Enter Amount:</label>
    <input type="text" name="credit" id="credit">
    <button>Add funds</button>
</form>



</body>
</html>

