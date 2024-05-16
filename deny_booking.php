<?php
include('partial/header.php');
if (!isset($user) || $user["IsAdmin"] != 1) {
    die("You are not allowed here.");
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $booking_id = $_POST['BookingID'];
    $user_id = $_POST['UserID'];

    $stmt = $mysqli->prepare("DELETE FROM requestedbookings WHERE BookingID = ?");
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        echo "Requested booking removed.";
        $stme = $mysqli->prepare("SELECT Email FROM account WHERE UserID=?");
        $stme->bind_param("i", $user_id);
        $stme->execute();
        $emailResult = $stme->get_result();
        $emailRow = $emailResult->fetch_assoc();
        $email = $emailRow["Email"];
        $mail = require __DIR__ . "/mailer.php";
        $mail->setFrom("parklyuser@outlook.com");
        $mail->addAddress($email);
        $mail->Subject = "Booking Unsuccessful";
        $mail->Body = <<<EOD
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Booking Unsuccessful</title>
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
                          <img width="60" src="/img/logoLeft.png" alt="Logo">
                        </td>
                    </tr>
                    <tr><td style="height:20px;">&nbsp;</td></tr>
                    <tr>
                        <td>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                <tr><td style="height:40px;">&nbsp;</td></tr>
                                <tr>
                                    <td style="padding:0 35px;">
                                        <h1>Booking Unsuccessful</h1>
                                        <p>Your Requested Parking space was unfortunately unavailable</p>
                                        <p>
                                            To create a new booking, log in and go to your dashboard:
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
        echo "Error removing requested booking: " . $stmt->error;
}
    ?>
