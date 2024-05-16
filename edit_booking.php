<?php
// Securely handle the session and user authentication checks


include('partial/header.php');
include('sidebar.php');
if (isset($user) && $user["IsAdmin"] == 0 || $user === null) {
    die("You are not allowed here.");
}
$mysqli = require __DIR__ . "/database.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'form1') {
    $BookingID = filter_var($_POST['BookingID'], FILTER_SANITIZE_NUMBER_INT);
    $UserID = filter_var($_POST['UserID'], FILTER_SANITIZE_NUMBER_INT);
    $ParkingSpaceID = filter_var($_POST['ParkingSpaceID'], FILTER_SANITIZE_NUMBER_INT);
    $LicensePlate = filter_var($_POST['LicensePlate'], FILTER_SANITIZE_STRING);
    $BookingCost = filter_var($_POST['BookingCost'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $userTimeStart = strtotime($_POST['timeStart']);
    $userTimeEnd = strtotime($_POST['timeEnd']);
    $LotName = filter_var($_POST['LotName']);

//$BookingID = filter_var($_POST['BookingID'], FILTER_SANITIZE_NUMBER_INT);
//$UserID = filter_var($_POST['UserID'], FILTER_SANITIZE_NUMBER_INT);
//$ParkingSpaceID = filter_var($_POST['ParkingSpaceID'], FILTER_SANITIZE_NUMBER_INT);
//$LicensePlate = filter_var($_POST['LicensePlate'], FILTER_SANITIZE_STRING);
//$BookingCost = filter_var($_POST['BookingCost'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
//$userTimeStart = strtotime($_POST['timeStart']);
//$userTimeEnd = strtotime($_POST['timeEnd']);
//$LotName = filter_var($_POST['LotName'], FILTER_SANITIZE_STRING);

// Query to get parking lot details
    $stmt = $mysqli->prepare("SELECT TotalSpaces FROM parkingLots WHERE LotName = ?");
    $stmt->bind_param("s", $LotName);
    $stmt->execute();
    $result = $stmt->get_result();
    $TotalSpaces = null;

    if ($row = $result->fetch_assoc()) {
        $TotalSpaces = intval($row['TotalSpaces']);
    } else {
        die("No parking lot found with the specified name.");
    }

// Query to get existing bookings to find occupied spaces
    $occupiedSpaces = [];
    $query = "SELECT ParkingSpaceID, UNIX_TIMESTAMP(timeStart) AS start, UNIX_TIMESTAMP(timeEnd) AS end FROM booking";
    $result = $mysqli->query($query);

    while ($row = $result->fetch_assoc()) {
        if ($userTimeStart < $row['end'] && $userTimeEnd > $row['start']) { // Overlap condition
            $occupiedSpaces[] = $row['ParkingSpaceID'];
        }
    }

    $freeSpaces = [];
    for ($i = 1; $i <= $TotalSpaces; $i++) {
        if (!in_array($i, $occupiedSpaces)) {
            $freeSpaces[] = $i;
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'form2') {
    $newParkingSpaceID = filter_var($_POST['parking_space'], FILTER_SANITIZE_NUMBER_INT);
$BookingID=$_POST['BookingID'];
$UserID=$_POST['UserID'];
print_r($BookingID);
    $stmt = $mysqli->prepare("UPDATE booking SET ParkingSpaceID = ? WHERE BookingID = ?");
    $stmt->bind_param("ii", $newParkingSpaceID, $BookingID);
    if ($stmt->execute()){
        $stme = $mysqli->prepare("SELECT Email FROM account WHERE UserID=?");
        $stme->bind_param("i", $UserID);
        $stme->execute();
        $emailResult = $stme->get_result();
        $emailRow = $emailResult->fetch_assoc();
        $email = $emailRow["Email"];
        $mail = require __DIR__ . "/mailer.php";
        $mail->setFrom("parklyuser@outlook.com");
        $mail->addAddress($email);
        $mail->Subject = "Booking Update";
        $mail->Body = <<<EOD
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Booking Update</title>
    <meta name="description" content="Booking Update">
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
                                        <h1>Booking Update</h1>
                                        <p>Your Parking space has been updated</p>
                                        <p>New Space: <strong>{$newParkingSpaceID}</strong></p>
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

    header("Location: changebookings.php");
    exit;
}


?>

<div class="box">
    <h1>User ID: <?= htmlspecialchars($UserID) ?></h1>
    <h1>Edit Booking: <?= htmlspecialchars($BookingID) ?></h1>

    <form class="form" method="post">
        <input class="input" type="hidden" name="form_type" value="form2">
        <input class="input" type="hidden" name="BookingID" value="<?= htmlspecialchars($BookingID) ?>">
        <input type="hidden" name="UserID" value="<?= htmlspecialchars($UserID) ?>">

        <label class="label" for="parking_space">Select Parking Space:</label>
        <select class="input" name="parking_space" id="parking_space">
            <option value="">Choose a space</option>
            <?php
            foreach ($freeSpaces as $space) {
                echo "<option value=\"" . htmlspecialchars($space) . "\">" . htmlspecialchars($space) . "</option>";
            }
            ?>
        </select><br>

        <button class="button" type="submit">Submit</button>
    </form>
</div>

<?php
// Clean up database resources
$stmt->close();
$mysqli->close();
?>
