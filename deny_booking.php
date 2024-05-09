<?php
include('partial/header.php');
if (!isset($user) || $user["IsAdmin"] != 1) {
    die("You are not allowed here.");
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $booking_id = $_POST['BookingID'];

    $stmt = $mysqli->prepare("DELETE FROM requestedbookings WHERE BookingID = ?");
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        echo "Requested booking removed.";
    } else {
        echo "Error removing requested booking: " . $stmt->error;
    }
}
    ?>
