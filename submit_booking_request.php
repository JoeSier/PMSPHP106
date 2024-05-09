<?php

include('partial/header.php');
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['form_type'] === 'form2') {
    // Validate user input and retrieve session data
    $form1_data = $_SESSION['form1_data'] ?? [];

    if (empty($form1_data)) {
        die("Missing required data. Please try again.");
    }

    // Validate fields
    $userID = $_SESSION['UserID'];
    $license_plate = filter_var($form1_data['license_plate'], FILTER_SANITIZE_STRING);
    $price = (float) $form1_data['price'];
    $desiredStart = "{$form1_data['start_date']} {$form1_data['start_time']}";
    $desiredEnd = "{$form1_data['end_date']} {$form1_data['end_time']}";
    $lotName = filter_var($form1_data['parkingLot'], FILTER_SANITIZE_STRING);
    $parking_space = (int)($_POST['parking_space'] ?? 0);

    // Prepare and bind the SQL query
    $stmt = $mysqli->prepare("INSERT INTO requestedbookings (UserID, ParkingSpaceID, LicensePlate, BookingCost, timeStart, timeEnd, LotName) VALUES (?, ?, ?, ?, ?, ?,?)");

    if ($stmt) {
        $stmt->bind_param("iisdsss", $userID, $parking_space, $license_plate, $price, $desiredStart, $desiredEnd, $lotName);

        // Execute and check for errors
        if ($stmt->execute()) {
            echo "Your parking request has been sent. Our team will review it and get back to you soon.";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $mysqli->error;
    }
}



echo "<pre>"; // Preformatted text to maintain formatting
echo "</pre>";
?>


