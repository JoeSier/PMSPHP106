<?php
// Include header file
include('partial/header.php');
if (!isset($user) || $user["IsAdmin"] != 1) {
    die("You are not allowed here.");
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $booking_id = $_POST['BookingID'];
    $user_id = $_POST['UserID'];
    $parking_space_id = $_POST['ParkingSpaceID'];
    $license_plate = $_POST['LicensePlate'];
    $booking_cost = $_POST['BookingCost'];
    $time_start = $_POST['timeStart'];
    $time_end = $_POST['timeEnd'];
    $lot_name = $_POST['LotName'];

    $stmt = $mysqli->prepare("INSERT INTO booking(UserID, ParkingSpaceID, LicensePlate, BookingCost, timeStart, timeEnd, LotName) VALUES(?,?,?,?,?,?,?)");
    $stmt->bind_param("iisdsss", $user_id, $parking_space_id, $license_plate, $booking_cost, $time_start, $time_end, $lot_name);

    if ($stmt->execute()) {
        echo "Booking successfully made!";
        $stmr = $mysqli->prepare("DELETE FROM requestedbookings WHERE BookingID = ?");
        $stmr->bind_param("i", $booking_id);

        if($stmr->execute()){
            $stme = $mysqli->prepare("SELECT Email FROM account WHERE UserID=?");
            $stme->bind_param("i", $user_id);
            $stme->execute();
            $emailResult = $stme->get_result();
            $emailRow = $emailResult->fetch_assoc();
            $email = $emailRow["Email"];
            $mail = require __DIR__ . "/mailer.php";
            $mail->setFrom("parklyuser@outlook.com");
            $mail->addAddress($email);
            $mail->Subject = "Booking Successful";
            $mail->Body = <<<EOD
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Booking Successful</title>
    <meta name="description" content="Booking Successful">
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
                          <img width="120" src="https://i.imgur.com/FAhHQ3G.png" alt="Logo">
                        </td>
                    </tr>
                    <tr><td style="height:20px;">&nbsp;</td></tr>
                    <tr>
                        <td>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                <tr><td style="height:40px;">&nbsp;</td></tr>
                                <tr>
                                    <td style="padding:0 35px;">
                                        <h1>Booking Successful</h1>
                                        <p>Your Parking space is booked for: <strong>{$time_start}</strong></p>
                                        <p>Space: <strong>{$parking_space_id}</strong></p>
                                        <p>Lot: <strong>{$lot_name}</strong></p>
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
        echo "Error adding booking: " . $mysqli->error;
    }
    $stml = $mysqli->prepare("SELECT credit FROM account WHERE UserID=?");
    $stml->bind_param("i", $user_id); // Bind the UserID parameter
    $stml->execute(); // Execute the query
    $result = $stml->get_result(); // Get the result set

    if ($row = $result->fetch_assoc()) {
        $currentCredit = $row['credit']; // Extract the current credit value from the row
        $newCredit = $currentCredit - $booking_cost; // Calculate new credit after subtraction

        // Prepare the SQL query to update the credit
        $stmt = $mysqli->prepare("UPDATE account SET credit = ? WHERE UserID = ?");

        if ($stmt) { // Check if preparation was successful
            // Bind parameters
            $stmt->bind_param("di", $newCredit, $user_id);

            // Execute the query and check for success
            if ($stmt->execute()) {
                echo "Credit updated successfully.";
            } else {
                echo "Error updating credit: " . $stmt->error;
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            echo "Error preparing SQL: " . $mysqli->error;
        }
    } else {
        echo "No user found with the specified UserID.";
    }


    // Clear session data after the booking
    unset($_SESSION['form1_data']);
    unset($_SESSION['freeSpaces']);
}

$mysqli->close();

?>
