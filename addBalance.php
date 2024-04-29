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

<h1>Add Funds</h1>

<p> Payment Method </p>

<div class="cc-selector">
    <input id="visa" type="radio" name="payment" value="visa" />
    <label class="drinkcard-cc visa" for="visa"></label>
    <input id="mastercard" type="radio" name="payment" value="mastercard" />
    <label class="drinkcard-cc mastercard"for="mastercard"></label>
    <input id="paypal" type="radio" name="payment" value="paypal" />
    <label class="drinkcard-cc paypal"for="paypal"></label>
</div>

<form method="post">
    <label for="Credit">Enter Amount:</label>
    <input type="number" name="Credit" id="Credit" min="1" step="any">
    <button>Add funds</button>
</form>


</body>
</html>

