<?php
include('partial/header.php');
include('sidebar.php');
?>

<body>

<div class="dashContent">
    <?php if (htmlspecialchars($user["IsAdmin"]) == 0): ?>
        <h1>Welcome back, <?= htmlspecialchars($user["Username"]) ?></h1>
        <h2>User Dashboard</h2>
        <p><br>Your current balance is: <?= htmlspecialchars($user["Credit"]) ?></p>


    <?php elseif (htmlspecialchars($user["IsAdmin"]) > 0): ?>
        <h1>Welcome back, <?= htmlspecialchars($user["Username"]) ?></h1>
        <h2>Admin Dashboard</h2>
    <?php else: ?>
        <p>couldn't detect if admin</p>
    <?php endif; ?>


</div>



</body>

<?php
$mysqli = require __DIR__ . "/database.php";

$seb = "SELECT * FROM booking WHERE UserID = {$_SESSION["UserID"]}";
$res = $mysqli->query($seb);
if ($res) {
    // Fetch all rows from the result set
    $bookings = [];
    while ($row = $res->fetch_assoc()) {
        $bookings[] = $row;  // Append the row to the bookings array
    }

    // Display the fetched bookings in a readable format
    print_r( "<p> Booking History <p>");
    echo "<p>NoOfBookings: " . sizeof($bookings) . "</p>";
//    print_r(sizeof($bookings));
    foreach ($bookings as $booking) {
        echo "<pre>"; // Optional, makes it easier to format
        printf("<br>ParkingID: <br>");
        print_r($booking["ParkingSpaceID"]);
        printf("<br>License Plate: <br>");
        print_r($booking["LicensePlate"]);
        printf("<br>Booking Cost: <br>");
        print_r($booking["BookingCost"]);
        printf("<br>Time Start: <br>");
        print_r($booking["timeStart"]);
        printf("<br>Time End: <br>");
        print_r($booking["timeEnd"]);
        echo "</pre>";
        echo "<br>"; // Move to the next line
    }
} else {
    echo "Query failed: " . $mysqli->error;
}


?>