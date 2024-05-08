<?php
include('partial/header.php');
include('sidebar.php');

if (!$_SESSION['UserID']) {
    die("you are not logged in");
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $credit = intval($_POST['Credit']); // Assuming the value comes from a POST request
    $userID = $_SESSION['UserID'];


    $mysqli = require __DIR__ . "/database.php";
    $stmt = $mysqli->prepare("UPDATE account SET Credit = Credit+ ? WHERE UserID = ?");

    $stmt->bind_param("ii", $credit, $userID);
    $exec = $stmt->execute();

    $stmt->close();
    $mysqli->close();
    if ($exec) {
        // If the execution was successful, reload the page
        header("Location: " . $_SERVER['PHP_SELF']); // Redirects to the same page
        exit; // Ensure no other code is executed after the header
    } else {
        echo "Error updating account credit.";
    }
}


?>

<body>
<div class="dashContent">
    <h1>Add Funds</h1>
    <p><br>Your current balance is: <?= htmlspecialchars($user["Credit"]) ?></p>

    <p><br>Choose a payment method </p>
    <div class="cc-selector">
        <input id="visa" type="radio" name="payment" value="visa"/>
        <label class="drinkcard-cc visa" for="visa"></label>
        <input id="mastercard" type="radio" name="payment" value="mastercard"/>
        <label class="drinkcard-cc mastercard" for="mastercard"></label>
        <input id="paypal" type="radio" name="payment" value="paypal"/>
        <label class="drinkcard-cc paypal" for="paypal"></label>
    </div>
    <div id="loginform">
        <form method="post">
            <label for="Credit">Enter Amount:</label>
            <input type="number" name="Credit" id="Credit" min="1" step="any">
            <button>Add funds</button>
        </form>
    </div>

</div>
</body>
</html>

