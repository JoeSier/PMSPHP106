<?php
include('partial/header.php');
include('sidebar.php');
$successMessage=null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST["License"])) {
        die("License is required");
    }

    if (empty($_POST["carType"])) {
        die("Car type is required");
    }
    $userID = $_SESSION['UserID'];
    $license = $_POST["License"];
    $carType = $_POST["carType"];


    $mysqli = require __DIR__ . "/database.php";
    $stmt = $mysqli->prepare("INSERT INTO car (UserID,LicensePlate,CarType) VALUES (?,?,?)");

    $stmt->bind_param("iss", $userID,$license,$carType);
    $success = $stmt->execute();

    if ($success) {
        $successMessage = "Car added successfully!";
    } else {
        $successMessage = "Error adding car.";
    }
    $stmt->close();
    $mysqli->close();
}

?>

<body>
<div class="dashContent">
<h1>Add a New Car</h1>
<div id="loginform">
<form method="post">
    <label for="License">Enter license plate:</label>
    <input type="text" name="License" id="License">
    <label for="carType">Enter car type:</label>
    <select name="carType" id="carType">
        <option value="Hatchback">Hatchback</option>
        <option value="Saloon">Saloon</option>
        <option value="Estate">Estate</option>
        <option value="MPV">MPV</option>
        <option value="SUV">SUV</option>
        <option value="Coupe">Coupe</option>
        <option value="Convertible">Convertible</option>
        <option value="SportsCar">SportsCar</option>
    </select>
    <button>Add Car</button>
</form>
    <?php if ($successMessage): ?>
        <div class="successMessage"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>
</div>
</div>

</body>
</html>

