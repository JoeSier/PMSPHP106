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
            // Execute and check for errors
            if ($stmt->execute()) {
                echo "Your parking request has been sent. Our team will review it and get back to you soon.";
                $stme = $mysqli->prepare("SELECT Email FROM account WHERE UserID=?");
                $stme->bind_param("i", $userID);
                $stme->execute();
                $emailResult = $stme->get_result();
                $emailRow = $emailResult->fetch_assoc();
                $email = $emailRow["Email"];
                $mail = require __DIR__ . "/mailer.php";
                $mail->setFrom("parklyuser@outlook.com");
                $mail->addAddress($email);
                $mail->Subject = "Booking Request Received";
                $mail->Body = <<<EOD
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Booking Request Received</title>
    <meta name="description" content="Booking Request Received" />
    <style type="text/css">
        a:hover {text-decoration: underline !important;}
    </style>
</head>
<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
    <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8" style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;">
        <tr>
            <td>
                <table style="background-color: #f2f3f8; max-width:670px;  margin:0 auto;" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr><td style="height:80px;">&nbsp;</td></tr>
                    <tr>
                        <td style="text-align:center;">
                          <img width="60" src="https://imgur.com/gallery/Nqam9Gi.png" alt="Logo">
                        </td>
                    </tr>
                    <tr><td style="height:20px;">&nbsp;</td></tr>
                    <tr>
                        <td>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                <tr><td style="height:40px;">&nbsp;</td></tr>
                                <tr>
                                    <td style="padding:0 35px;">
                                        <h1>Booking Request Received</h1>
                                        <p>Your have requested a Parking space at: <strong>{$desiredStart}</strong></p>
                                        <p>Space: <strong>{$parking_space}</strong></p>
                                        <p>Lot: <strong>{$lotName}</strong></p>
                                        <p>
                                            Our team will review it and get back to you soon.    
                                        </p>
                                        <p>    
                                            To manage your booking, log in and go to your dashboard:
                                        </p>
                                        <a href="https://localhost/index.php" style="background:#20e277;text-decoration:none; color:#fff; padding:10px 24px; border-radius:50px; display:inline-block;">Manage Booking</a>
                                    </td>
                                </tr>
                                <tr><td style="height:40px;">&nbsp;</td></tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td style="height:20px;">&nbsp;</td></tr>
                    <tr><td style="height:80px;">&nbsp;</td></tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
EOD;
                try {

                    $mail->send();

                } catch (Exception $e) {

                    echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
                    exit;

                }}
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


