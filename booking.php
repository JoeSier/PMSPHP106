<?php
// Include header file
include('partial/header.php');

// Include database connection
$mysqli = require __DIR__ . "/database.php";

// Fetch the current user's cars from the database
$userID = $_SESSION['UserID'];
$stmt = $mysqli->prepare("SELECT LicensePlate FROM car WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$cars = $result->fetch_all(MYSQLI_ASSOC);

// Close prepared statement and database connection
$stmt->close();
$mysqli->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $sel = "SELECT ParkingSpaceID,timeStart,timeEnd,LicensePlate FROM booking";
    $mysqlj = require __DIR__ . "/database.php";
    $success = $mysqlj->query($sel);

    if (!$success) {
        echo "Error retrieving parking spaces: " . $mysqlj->error;
        exit;
    }
    $start_date = $_POST['start_date'];
    $start_time = $_POST['start_time'];
    $end_date = $_POST['end_date'];
    $end_time = $_POST['end_time'];

    //Convert into timestamps
    $desiredStart = strtotime("$start_date $start_time");
    $desiredEnd = strtotime("$end_date $end_time");

    if ($desiredStart >= $desiredEnd) {
        echo "End time must be after start time.";
        exit;
    }

    $occupiedSpaces = [];
    $occupiedLicensePlates = [];
    while ($row = $success->fetch_assoc()) {
        $parkingSpaceID = $row['ParkingSpaceID'];
        $licensePlate = $row['LicensePlate'];
        $timeStart = strtotime($row['timeStart']);
        $timeEnd = strtotime($row['timeEnd']);


        // Check if the requested time overlaps with existing bookings
        if (
            ($desiredStart < $timeEnd && $desiredEnd > $timeStart) // Overlap condition
        ) {
            $occupiedSpaces[] = $parkingSpaceID; // Collect overlapping spaces
            $occupiedLicensePlates[] = $licensePlate; // Collect overlapping license plates
        }
    }

    $license = $_POST['license_plate'];
// Check if the given license plate is already booked for the requested time
    if (in_array($license, $occupiedLicensePlates)) {
        die("Car already booked for this time");
    } else {

// Sort occupied spaces in ascending order
        sort($occupiedSpaces);

        $freeParkingSpace = null;
        for ($i = 1; $i <= 50; $i++) {
            if (!in_array($i, $occupiedSpaces)) { // Check if the space is free
                $freeParkingSpace = $i; // Found a free space
                break;
            }
        }

        if ($freeParkingSpace !== null) {
            echo "First free parking space is: " . $freeParkingSpace;
        } else {
            die("No free parking spaces found from 1 to 50.");
        }


        $credit = $user["Credit"];
        $userID = $_SESSION['UserID'];
        $desiredStartFormatted = date('Y-m-d H:i:s', $desiredStart);
        $desiredEndFormatted = date('Y-m-d H:i:s', $desiredEnd);
        print_r($desiredStartFormatted);
        print_r($desiredEndFormatted);


        $price = $_POST['price'];

        if ($credit < $price) {
            print_r($credit);
            print_r($price);
            die("Not enough funds.");
        }

        $booksql = require __DIR__ . "/database.php";
        $bookstmt = $booksql->prepare("INSERT INTO booking(
    UserID,
    ParkingSpaceID,
    LicensePlate,
    BookingCost,
    timeStart,
    timeEnd
) VALUES(?,?,?,?,?,?)");

        $bookstmt->bind_param("iisiss", $userID, $freeParkingSpace, $license, $price, $desiredStartFormatted, $desiredEndFormatted);
        $success = $bookstmt->execute();

        if ($success) {
            echo "Booking added successfully!";
        } else {
            echo "Error adding booking: " . $booksql->error;
        }
        $bookstmt->close();
        $booksql->close();
    }
}
?>

<body>
<h1>Make a booking</h1>
<form method="post" onsubmit="return validateForm()">
    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" min="<?php echo date('Y-m-d'); ?>" required><br>

    <label for="start_time">Start Time:</label>
    <select id="start_time" name="start_time" required>
        <?php
        // Generate options for 30-minute intervals from 00:00 to 23:30
        $start = strtotime('00:00');
        $end = strtotime('23:30');
        $interval = 30 * 60; // 30 minutes in seconds

        for ($time = $start;
        $time <= $end;
        $time += $interval) {
        echo '<option value="' . date('H:i', $time) . '">' . date('H:i', $time) . '</option>';
        }
        ?>
    </select><br>

    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" min="<?php echo date('Y-m-d'); ?>" required><br>

    <label for="end_time">End Time:</label>
    <select id="end_time" name="end_time" required>
        <?php
        // Generate options for 30-minute intervals from 00:00 to 23:30
        for ($time = $start;
        $time <= $end;
        $time += $interval) {
        echo '<option value="' . date('H:i', $time) . '">' . date('H:i', $time) . '</option>';
        }
        ?>
    </select><br>

    <label for="license_plate">Select car:</label>
    <select name="license_plate" id="license_plate" required>
        <option value="">License plate</option>
        <?php
        // Populate the select field with the user's cars
        foreach ($cars as $car) {
        // Display the car's LicensePlate
        echo "<option value=\"" . htmlspecialchars($car["LicensePlate"]) . "\">" . htmlspecialchars($car["LicensePlate"]) . "</option>";
        }
        ?>
    </select><br>

    <label for="price">Session Price:</label>
    <input type="number" id="price" name="price" readonly required><br>

    <button type="submit">Submit</button>
</form>

<script>
    function validateForm() {
        // Validate if all fields are filled out
        var startDate = document.getElementById('start_date').value;
        var startTime = document.getElementById('start_time').value;
        var endDate = document.getElementById('end_date').value;
        var endTime = document.getElementById('end_time').value;
        var licensePlate = document.getElementById('license_plate').value;

        if (startDate === '' || startTime === '' || endDate === '' || endTime === '' || licensePlate === '') {
            alert("Please fill out all fields before submitting the form.");
            return false;
        }

        return true;
    }
</script>

<script>
    function calculatePrice() {
        var startDate = new Date(document.getElementById('start_date').value);
        var startTime = document.getElementById('start_time').value;
        var endDate = new Date(document.getElementById('end_date').value);
        var endTime = document.getElementById('end_time').value;

        // Combine date and time for start and end
        var startDateTime = new Date(startDate.toDateString() + ' ' + startTime);
        var endDateTime = new Date(endDate.toDateString() + ' ' + endTime);

        // Calculate duration in milliseconds
        var duration = endDateTime - startDateTime;

        // If duration is negative, set price to "Error"
        if (duration < 0) {
            document.getElementById('price').value = "Error";
        } else {
            // Convert duration to minutes
            var durationInMinutes = duration / (1000 * 60);

            // Calculate price based on duration
            var price = (durationInMinutes / 30) * 2.50;

            // If price is negative, set it to 0
            if (price < 0) {
                price = 0;
            }

            // Update the price input field
            document.getElementById('price').value =
                // "£" +
                price.toFixed(2);
        }
    }


    // Call calculatePrice function whenever the start or end date/time changes
    document.getElementById('start_date').addEventListener('change', calculatePrice);
    document.getElementById('start_time').addEventListener('change', calculatePrice);
    document.getElementById('end_date').addEventListener('change', calculatePrice);
    document.getElementById('end_time').addEventListener('change', calculatePrice);


    // Call calculatePrice initially to display the initial price based on default selected times
    calculatePrice();
</script>
</body>
