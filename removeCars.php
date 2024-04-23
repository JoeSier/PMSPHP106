<?php
include('partial/header.php');

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $mysqli = require __DIR__ . "/database.php";

    $sql = sprintf("DELETE FROM car
                    WHERE LicensePlate = '%s' AND UserID=$1" [$_SESSION["UserID"]],
        $mysqli->real_escape_string($_POST["license"]));

    $result = $mysqli->query($sql);

    $car = $result->fetch_assoc();


    if ($car) {
        exit;

    }


    $is_invalid = true;
}

?>
<body>
<p>not done yet</p>
<h1>Remove your cars</h1>
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









