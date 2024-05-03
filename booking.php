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

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['form_type'] === 'form1') {
// This gets all of the free parking spaces at the time entered
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

    // Determine free spaces
    $freeSpaces = [];
    for ($i = 1; $i <= 50; $i++) {
        if (!in_array($i, $occupiedSpaces)) {
            $freeSpaces[] = $i;
        }
    }

    $license = $_POST['license_plate'];
// Check if the given license plate is already booked for the requested time
    if (in_array($license, $occupiedLicensePlates)) {
        die("Car already booked for this time");
    }



// Prepare the statement to get the credit from the account based on USERID
    $stml = $mysqli->prepare("SELECT credit FROM account WHERE USERID=?");
    $stml->bind_param("i", $_SESSION['UserID']); // Bind the UserID parameter
    $stml->execute(); // Execute the query
    $result = $stml->get_result(); // Get the result set

// Fetch the first row from the result set
    if ($row = $result->fetch_assoc()) {
        $credit = $row['credit']; // Extract the credit value from the row

        // Check if the credit is less than the price from POST data
        if ($credit < $_POST['price']) {
            die("Not enough credit"); // Terminate if not enough credit
        } else {
            echo "Sufficient credit"; // Optional: A message if there's enough credit
        }
    } else {
        die("Error: No data found for this UserID."); // Handle cases where there's no data
    }

// Optional: Additional code to proceed with the transaction if there's enough credit


    // Store data in session to transition to the second form
    $_SESSION['freeSpaces'] = $freeSpaces;
    $_SESSION['form1_data'] = $_POST; // Store the initial form data

    // Redirect to a new page with the second form
    header("Location: booking-chooseSpace.php"); // The new page with the second form
    exit;

}

?>

<body>
<h1>Make a booking</h1>
<form method="post" onsubmit="return validateForm()">
    <input type="hidden" name="form_type" value="form1">
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
