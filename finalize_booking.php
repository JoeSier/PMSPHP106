<?php
// Include header file
include('partial/header.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['form_type'] === 'form2') {
    // Retrieve previous form data and selected parking space
    $form1_data = $_SESSION['form1_data'] ?? [];
    $parking_space = $_POST['parking_space'] ?? null;

    if (empty($form1_data)) {
        die("Missing required data. Please try again.");
    }

    if (empty($parking_space)) {
        $parking_space = $_SESSION['freeSpaces'][0];
    }

    // Add the booking to the database
    $userID = $_SESSION['UserID'];
    $license_plate = $form1_data['license_plate'];
    $price = $form1_data['price'];
    $desiredStart = "{$form1_data['start_date']} {$form1_data['start_time']}";
    $desiredEnd = "{$form1_data['end_date']} {$form1_data['end_time']}";

    $stmt = $mysqli->prepare("INSERT INTO booking(UserID, ParkingSpaceID, LicensePlate, BookingCost, timeStart, timeEnd) VALUES(?,?,?,?,?,?)");
    $stmt->bind_param("iisiss", $userID, $parking_space, $license_plate, $price, $desiredStart, $desiredEnd);

    if ($stmt->execute()) {
        echo "Booking successfully made!";
    } else {
        echo "Error adding booking: " . $mysqli->error;
    }

    // Clear session data after the booking
    unset($_SESSION['form1_data']);
    unset($_SESSION['freeSpaces']);
}

$mysqli->close();

?>

<!--$license = $_POST['license_plate'];-->
<!--// Check if the given license plate is already booked for the requested time-->
<!--if (in_array($license, $occupiedLicensePlates)) {-->
<!--die("Car already booked for this time");-->
<!--} else {-->
<!---->
<!--// Sort occupied spaces in ascending order-->
<!--sort($occupiedSpaces);-->
<!---->
<!--$freeParkingSpace = null;-->
<!--for ($i = 1; $i <= 50; $i++) {-->
<!--if (!in_array($i, $occupiedSpaces)) { // Check if the space is free-->
<!--$freeParkingSpace = $i; // Found a free space-->
<!--break;-->
<!--}-->
<!--}-->
<!---->
<!--if ($freeParkingSpace !== null) {-->
<!--echo "First free parking space is: " . $freeParkingSpace;-->
<!--} else {-->
<!--die("No free parking spaces found from 1 to 50.");-->
<!--}-->
<!---->
<!---->
<!--$credit = $user["Credit"];-->
<!--$userID = $_SESSION['UserID'];-->
<!--$desiredStartFormatted = date('Y-m-d H:i:s', $desiredStart);-->
<!--$desiredEndFormatted = date('Y-m-d H:i:s', $desiredEnd);-->
<!---->
<!---->
<!--$price = $_POST['price'];-->
<!---->
<!--if ($credit < $price) {-->
<!--print_r($credit);-->
<!--print_r($price);-->
<!--die("Not enough funds.");-->
<!--}-->
<!---->
<!--$booksql = require __DIR__ . "/database.php";-->
<!--$bookstmt = $booksql->prepare("INSERT INTO booking(-->
<!--UserID,-->
<!--ParkingSpaceID,-->
<!--LicensePlate,-->
<!--BookingCost,-->
<!--timeStart,-->
<!--timeEnd-->
<!--) VALUES(?,?,?,?,?,?)");-->
<!---->
<!--$bookstmt->bind_param("iisiss", $userID, $freeParkingSpace, $license, $price, $desiredStartFormatted, $desiredEndFormatted);-->
<!--$success = $bookstmt->execute();-->
<!---->
<!--if ($success) {-->
<!--echo "Booking added successfully!";-->
<!--} else {-->
<!--echo "Error adding booking: " . $booksql->error;-->
<!--}-->
<!--$bookstmt->close();-->
<!--$booksql->close();-->
<!--}-->
