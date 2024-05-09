<?php
include('partial/header.php');
include('sidebar.php');

$isAdmin = intval($user["IsAdmin"]) > 0;
if (!$isAdmin) {
    die("You are not authorized to access this page.");
}

$mysqli = require __DIR__ . "/database.php";

// Fetch the list of users from the database
$stmt = $mysqli->prepare("SELECT UserID, Firstname, Surname, Email,PhoneNumber FROM account");
$stmt->execute();
$result = $stmt->get_result();
$userNames = $result->fetch_all(MYSQLI_ASSOC);

$selectedUserId = ""; // To store the selected user ID
$selectedBookingID = ""; // To store the selected booking ID

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["user_select"])) {
        $selectedUserId = $_POST["user_select"]; // Retrieve the selected user ID

        // Find the selected user from the fetched list
        $selectedUser = array_filter($userNames, function ($user) use ($selectedUserId) {
            return $user["UserID"] == $selectedUserId;
        });

        if (!empty($selectedUser)) { // Check if the selected user is not empty
            $selectedUser = array_values($selectedUser)[0];
            $selectedUserMessage = "You selected: " . $selectedUser["Firstname"] . " " . $selectedUser["Surname"] . " " . $selectedUser["PhoneNumber"] . " " . $selectedUser["Email"];
        }
    }

    // Fetch the list of user's bookings from the database
    $stmb = $mysqli->prepare("SELECT BookingID, ParkingSpaceID, LicensePlate, BookingCost, timeStart, timeEnd, LotName FROM booking WHERE UserID = ?");
    $stmb->bind_param("i", $selectedUserId); // Bind the UserID parameter
    $stmb->execute(); // Execute the query
    $bookingResult = $stmb->get_result(); // Get the result set

    // Fetch all bookings for the selected user
    $bookings = $bookingResult->fetch_all(MYSQLI_ASSOC);

    if (isset($_POST["delete_booking"])) {
        // Retrieve the selected booking ID
        $selectedBookingID = $_POST["booking_select"];

        // Get the booking cost before deleting the booking
        $stmo = $mysqli->prepare("SELECT UserID, BookingCost FROM booking WHERE BookingID=?");
        $stmo->bind_param("i", $selectedBookingID);
        $stmo->execute();
        $costResult = $stmo->get_result();
        if ($costRow = $costResult->fetch_assoc()) {
            $cost = $costRow["BookingCost"];
            $userid = $costRow["UserID"];

            $stme = $mysqli->prepare("SELECT Email FROM account WHERE UserID=?");
            $stme->bind_param("i", $userid);
            $stme->execute();
            $emailResult = $stme->get_result();
            $emailRow = $emailResult->fetch_assoc();
            $email = $emailRow["Email"];
            // Perform deletion operation
            $stmd = $mysqli->prepare("DELETE FROM booking WHERE BookingID = ?");
            $stmd->bind_param("i", $selectedBookingID);
            if ($stmd->execute()) {
                echo "Booking deleted successfully.";
                $mail = require __DIR__ . "/mailer.php";
                $mail->setFrom("parklyuser@outlook.com");
                $mail->addAddress($email); //this sends it to the current admin logged in, not the user
                $mail->Subject = "Booking Cancellation";
                $mail->Body = <<<EOD
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Booking Cancellation</title>
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
                                        <h1>Booking Cancelled</h1>
                                        <p>Your Parking space was cancelled</p>
                                            To manage your booking, log in and go to your dashboard:
                                        </p>
                                        <a href="https://localhost/dashboards.php" style="background:#20e277;text-decoration:none; color:#fff; padding:10px 24px; border-radius:50px; display:inline-block;">Manage Booking</a>
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

                }
                // Update user's credit by adding the booking cost back to their balance
                $stmu = $mysqli->prepare("UPDATE account SET credit = credit + ? WHERE UserID = ?");
                $stmu->bind_param("di", $cost, $userid);
                if ($stmu->execute()) {
                    echo "Credit updated successfully.";
                } else {
                    echo "Error updating credit: " . $mysqli->error;
                }

                // Redirect after successful deletion
                header("Location: Dashboards.php");
                exit();
            } else {
                echo "Error deleting booking: " . $mysqli->error;
            }
        } else {
            echo "Error retrieving booking cost: " . $mysqli->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select User</title>
    <style>
        .message-box {
            padding: 10px;
            background-color: #f0f8ff; /* Light background color for message box */
            border: 1px solid #d0d0d0;
            border-radius: 5px;
            margin-top: 20px; /* Space between form and message box */
        }
    </style>
</head>
<body>
<div class="dashContent">
    <form method="post" action="">
        <label for="user_select">Select a user:</label>
        <select id="user_select" name="user_select" required>
            <option value="">-- Select a user --</option>
            <?php foreach ($userNames as $user) : ?>
                <option value="<?php echo $user["UserID"]; ?>" <?php echo $user["UserID"] == $selectedUserId ? 'selected' : ''; ?>>
                    <?php echo $user["Firstname"] . " " . $user["Surname"] . " (" . $user["Email"] . ")"; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Submit</button>
    </form>

    <!-- New form to display user's bookings and allow deletion -->
    <?php if (!empty($bookings)) : ?>
        <form method="post" action="">
            <input type="hidden" name="booking_select" value="<?php echo $selectedBookingID; ?>">
            <label for="booking_select">Select a Booking:</label>
            <select id="booking_select" name="booking_select" required>
                <option value="">-- Select a booking --</option>
                <?php foreach ($bookings as $booking) : ?>
                    <option value="<?php echo $booking["BookingID"]; ?>"><?php echo $booking["LotName"] . " - " . $booking["timeStart"] . " to " . $booking["timeEnd"]; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="delete_booking">Delete</button>
        </form>
    <?php endif; ?>
</div>


</body>
</html>