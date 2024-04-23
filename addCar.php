<?php
include('partial/header.php');


if ($_SERVER["REQUEST_METHOD"] === "POST") {
//    $credit = intval($_POST['Credit']); // Assuming the value comes from a POST request
    $userID = $_SESSION['UserID'];
    $license = $_POST["License"];
    $carType = $_POST["carType"];


    $mysqli = require __DIR__ . "/database.php";
    $stmt = $mysqli->prepare("INSERT INTO car (UserID,LicensePlate,CarType) VALUES (?,?,?)");

    $stmt->bind_param("iss", $userID,$license,$carType);
    $success = $stmt->execute();

    if ($success) {
        echo "Car added successfully!";
    } else {
        echo "Error adding car: " . $mysqli->error;
    }
    $stmt->close();
    $mysqli->close();
}

?>

<body>

<h1>add your car</h1>
<p>Hello <?= htmlspecialchars($user["Username"]) ?></p>
<p><a href="logout.php">Log out</a></p>
<p><a href="Dashboards.php">Return to Dashboard</a></p>

<form method="post">
    <label for="License">Enter license plate:</label>
    <input type="text" name="License" id="License">
    <label for="carType">Enter car type:</label>
    <select name="carType" id="carType">
        <option value="Hatchback">Hatchback</option>
        <option value="Saloon">Saloob</option>
        <option value="Estate">Estate</option>
        <option value="MPV">MPV</option>
        <option value="SUV">SUV</option>
        <option value="Coupe">Coupe</option>
        <option value="Convertible">Convertible</option>
        <option value="SportsCar">SportsCar</option>
    </select>
    <button>Add Car</button>
</form>


</body>
</html>

