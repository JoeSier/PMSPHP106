<?php
include('partial/header.php');
include('sidebar.php');

$stmt = $mysqli->prepare("SELECT * FROM booking WHERE userid = ?");
$stmt->bind_param("i", $_SESSION["UserID"]);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $bookingId = $_POST['bookingId'];
    $activeValue = ($_POST['form_type'] === 'arrived') ? 1 : 0;

    $stmt = $mysqli->prepare("UPDATE booking SET Active = ? WHERE BookingID = ?");
    $stmt->bind_param("ii", $activeValue, $bookingId);
    $stmt->execute();

    // Close the statement
    $stmt->close();
}

?>

<body>
<div class="box">
    <h2>Your Bookings</h2>

    <?php if (count($bookings) > 0): ?>
        <div class="bookingGrid">
            <?php foreach ($bookings as $booking): ?>
                <div class="bookingCard">
                    <h3>Your Space: <?php echo htmlspecialchars($booking['ParkingSpaceID'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p>Parking Lot: <?php echo htmlspecialchars($booking['LotName'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p>Start Time: <?php echo htmlspecialchars($booking['timeStart'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p>End Time: <?php echo htmlspecialchars($booking['timeEnd'], ENT_QUOTES, 'UTF-8'); ?></p>

                    <div class="bookingActions">
                        <form action="" method="post">
                            <input type="hidden" name="form_type" value="arrived">
                            <input type="hidden" name="bookingId" value="<?php echo $booking['BookingID']; ?>">
                            <button type="submit">I am here</button>
                        </form>
                        <form action="" method="post">
                            <input type="hidden" name="form_type" value="left">
                            <input type="hidden" name="bookingId" value="<?php echo $booking['BookingID']; ?>">
                            <button type="submit">I have left</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>
</div>
</body>
