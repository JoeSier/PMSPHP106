<?php
// Securely handle the session and user authentication checks


include('partial/header.php');
include('sidebar.php');
if (isset($user) && $user["IsAdmin"] == 0 || $user === null) {
    die("You are not allowed here.");
}
$mysqli = require __DIR__ . "/database.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'form1') {
    $BookingID = filter_var($_POST['BookingID'], FILTER_SANITIZE_NUMBER_INT);
    $UserID = filter_var($_POST['UserID'], FILTER_SANITIZE_NUMBER_INT);
    $ParkingSpaceID = filter_var($_POST['ParkingSpaceID'], FILTER_SANITIZE_NUMBER_INT);
    $LicensePlate = filter_var($_POST['LicensePlate'], FILTER_SANITIZE_STRING);
    $BookingCost = filter_var($_POST['BookingCost'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $userTimeStart = strtotime($_POST['timeStart']);
    $userTimeEnd = strtotime($_POST['timeEnd']);
    $LotName = filter_var($_POST['LotName']);

//$BookingID = filter_var($_POST['BookingID'], FILTER_SANITIZE_NUMBER_INT);
//$UserID = filter_var($_POST['UserID'], FILTER_SANITIZE_NUMBER_INT);
//$ParkingSpaceID = filter_var($_POST['ParkingSpaceID'], FILTER_SANITIZE_NUMBER_INT);
//$LicensePlate = filter_var($_POST['LicensePlate'], FILTER_SANITIZE_STRING);
//$BookingCost = filter_var($_POST['BookingCost'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
//$userTimeStart = strtotime($_POST['timeStart']);
//$userTimeEnd = strtotime($_POST['timeEnd']);
//$LotName = filter_var($_POST['LotName'], FILTER_SANITIZE_STRING);

// Query to get parking lot details
    $stmt = $mysqli->prepare("SELECT TotalSpaces FROM parkingLots WHERE LotName = ?");
    $stmt->bind_param("s", $LotName);
    $stmt->execute();
    $result = $stmt->get_result();
    $TotalSpaces = null;

    if ($row = $result->fetch_assoc()) {
        $TotalSpaces = intval($row['TotalSpaces']);
    } else {
        die("No parking lot found with the specified name.");
    }

// Query to get existing bookings to find occupied spaces
    $occupiedSpaces = [];
    $query = "SELECT ParkingSpaceID, UNIX_TIMESTAMP(timeStart) AS start, UNIX_TIMESTAMP(timeEnd) AS end FROM booking";
    $result = $mysqli->query($query);

    while ($row = $result->fetch_assoc()) {
        if ($userTimeStart < $row['end'] && $userTimeEnd > $row['start']) { // Overlap condition
            $occupiedSpaces[] = $row['ParkingSpaceID'];
        }
    }

    $freeSpaces = [];
    for ($i = 1; $i <= $TotalSpaces; $i++) {
        if (!in_array($i, $occupiedSpaces)) {
            $freeSpaces[] = $i;
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'form2') {
    $newParkingSpaceID = filter_var($_POST['parking_space'], FILTER_SANITIZE_NUMBER_INT);
$BookingID=$_POST['BookingID'];
print_r($BookingID);
    $stmt = $mysqli->prepare("UPDATE booking SET ParkingSpaceID = ? WHERE BookingID = ?");
    $stmt->bind_param("ii", $newParkingSpaceID, $BookingID);
    $stmt->execute();

    header("Location: changebookings.php");
    exit;
}


?>

<div class="box">
    <h1>Edit Booking: <?= htmlspecialchars($BookingID) ?></h1>

    <form class="form" method="post">
        <input class="input" type="hidden" name="form_type" value="form2">
        <input class="input" type="hidden" name="BookingID" value="<?= htmlspecialchars($BookingID) ?>">

        <label class="label" for="parking_space">Select Parking Space:</label>
        <select class="input" name="parking_space" id="parking_space">
            <option value="">Choose a space</option>
            <?php
            foreach ($freeSpaces as $space) {
                echo "<option value=\"" . htmlspecialchars($space) . "\">" . htmlspecialchars($space) . "</option>";
            }
            ?>
        </select><br>

        <button class="button" type="submit">Submit</button>
    </form>
</div>

<?php
// Clean up database resources
$stmt->close();
$mysqli->close();
?>
