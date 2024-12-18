<?php
include('partial/header.php');
include('sidebar.php');
$successMessage=null;
// Variable to track if there was an error
$is_invalid = false;

$mysqli = require __DIR__ . "/database.php";

// Fetch the current user's cars
$userID = $_SESSION['UserID'];
$stmt = $mysqli->prepare("SELECT LicensePlate FROM car WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$cars = $result->fetch_all(MYSQLI_ASSOC);

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $license = $_POST["License"];
    $delete_stmt = $mysqli->prepare("DELETE FROM car WHERE LicensePlate = ? AND UserID = ?");
    $delete_stmt->bind_param("si", $license, $userID);
    $success = $delete_stmt->execute();

    if ($success) {
        $successMessage = "Car removed successfully!";
    } else {
        $successMessage = "Error removing car.";
    }

    $delete_stmt->close();
}
$stmt->close();
$mysqli->close();
?>
<body>
<div class="dashContent">
    <h1>Remove a Car</h1>
    <div id="loginform">
        <form method="post">
            <label for="License">Car license:</label>
            <select name="License" id="License">
                <option value="">Select a car to remove</option>
                <?php
                // Populate the select field with the user's cars
                foreach ($cars as $car) {
                    // Display the car's LicensePlate and optionally the CarModel
                    echo "<option value=\"" . htmlspecialchars($car["LicensePlate"]) . "\">" . htmlspecialchars($car["LicensePlate"] . " - " . $car["CarModel"]) . "</option>";
                }
                ?>
            </select>
            <button type="submit">Remove Car</button>
        </form>
        <?php if ($successMessage): ?>
            <div class="successMessage"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>







