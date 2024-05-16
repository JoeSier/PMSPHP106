<?php
include('partial/header.php');
include('sidebar.php');
$successMessage=null;
// Variable to track if there was an error
$is_invalid = false;

$mysqli = require __DIR__ . "/database.php";

$stmt = $mysqli->prepare("SELECT LotName FROM parkinglots");
$stmt->execute();
$result = $stmt->get_result();
$parkinglots = $result->fetch_all(MYSQLI_ASSOC);

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $parkingLot = $_POST["parkingLot"];
    $delete_stmt = $mysqli->prepare("DELETE FROM parkinglots WHERE LotName = ?");
    $delete_stmt->bind_param("s", $parkingLot);
    $success = $delete_stmt->execute();

    if ($success) {
        $successMessage = "lot removed successfully!";
    } else {
        $successMessage = "Error removing car.";
    }

    $delete_stmt->close();
}
$stmt->close();
$mysqli->close();
?>
<body>
<div class="box_dash_other">
    <h1 class="h1">Remove a Parking Lot</h1>
    <div id="loginform">
        <form  method="post">
            <label class="label" for="parkingLot">Parking Lot Name:</label>
            <select class="input" name="parkingLot" id="parkingLot">
                <option value="">Select a Parking Lot to remove</option>
                <?php
                // Populate the select field with the user's cars
                foreach ($parkinglots as $parkinglots) {
                    // Display the car's parkingLotPlate and optionally the CarModel
                    echo "<option value=\"" . htmlspecialchars($parkinglots["LotName"]) . "\">" . htmlspecialchars($parkinglots["LotName"] ) . "</option>";
                }
                ?>
            </select>
            <button class="button" type="submit">Remove the lot</button>
        </form>
        <?php if ($successMessage): ?>
            <div class="successMessage"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>







