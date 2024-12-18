
<?php

include('partial/header.php');
include('sidebar.php');

if (!isset($user) || $user["IsAdmin"] == 0) {
    die("You are not allowed here.");
}
$query = "SELECT * FROM requestedbookings";
$result = $mysqli->query($query);

if ($result === false) {
    die("Database query failed: " . $mysqli->error);
}
?>


<body>
<div class="box_dash_other">

<h1 class="h1">Pending Booking Requests</h1>

<!-- Check if there are any bookings -->
<?php if ($result->num_rows > 0): ?>
    <table border="1">
        <tr>
            <th>Booking ID</th>
            <th>User ID</th>
            <th>Parking Space ID</th>
            <th>License Plate</th>
            <th>Booking Cost</th>
            <th>Time Start</th>
            <th>Time End</th>
            <th>Lot Name</th>
            <th>Action</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()):?>
            <tr>
                <td><?= $row['BookingID'] ?></td>
                <td><?= $row['UserID'] ?></td>
                <td><?= $row['ParkingSpaceID'] ?></td>
                <td><?= $row['LicensePlate'] ?></td>
                <td><?= $row['BookingCost'] ?></td>
                <td><?= $row['timeStart'] ?></td>
                <td><?= $row['timeEnd'] ?></td>
                <td><?= $row['LotName'] ?></td>
                <td>
                    <form action="finalize_booking.php" method="post">
                        <input type="hidden" name="BookingID" value="<?= $row['BookingID'] ?>">
                        <input type="hidden" name="UserID" value="<?= $row['UserID'] ?>">
                        <input type="hidden" name="ParkingSpaceID" value="<?= $row['ParkingSpaceID'] ?>">
                        <input type="hidden" name="LicensePlate" value="<?= $row['LicensePlate'] ?>">
                        <input type="hidden" name="BookingCost" value="<?= $row['BookingCost'] ?>">
                        <input type="hidden" name="timeStart" value="<?= $row['timeStart'] ?>">
                        <input type="hidden" name="timeEnd" value="<?= $row['timeEnd'] ?>">
                        <input type="hidden" name="LotName" value="<?= $row['LotName'] ?>">
                        <button type="submit" value="approve">Approve</button>
                    </form>
                    <td>
                    <form action="deny_booking.php" method="post">
                        <input type="hidden" name="BookingID" value="<?= $row['BookingID'] ?>">
                        <input type="hidden" name="UserID" value="<?= $row['UserID'] ?>">
                        <button type="submit" value="deny">Deny</button>
                    </form>
                </td>

                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p class="text">No booking requests found.</p>
<?php endif; ?>
</div>
</body>
</html>

<?php
$result->close();
?>