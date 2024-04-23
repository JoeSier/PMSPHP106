<?php
include('partial/header.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $credit = intval($_POST['Credit']); // Assuming the value comes from a POST request
    $userID = $_SESSION['UserID'];


    $mysqli = require __DIR__ . "/database.php";
    $stmt = $mysqli->prepare("UPDATE account SET Credit = Credit+ ? WHERE UserID = ?");

    $stmt->bind_param("ii", $credit, $userID);
    $success = $stmt->execute();

    if ($success) {
        echo "Credit updated successfully!";
    } else {
        echo "Error updating credit: " . $mysqli->error;
    }
    $stmt->close();
    $mysqli->close();
}


?>

<body>

<h1>add Funds</h1>
<p>Hello <?= htmlspecialchars($user["Username"]) ?></p>
<p><a href="logout.php">Log out</a></p>
<p><a href="Dashboards.php">Return to Dashboard</a></p>

<form method="post">
    <label for="Credit">Enter Amount:</label>
    <input type="number" name="Credit" id="Credit">
    <button>Add funds</button>
</form>


</body>
</html>

