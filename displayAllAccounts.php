<?php
include('partial/header.php');
include('sidebar.php');
$isAdmin = intval($user["IsAdmin"]) > 0;
if (!isset($user) || $user["IsAdmin"] == 0) {
    die("You are not allowed here.");
}

function getAccounts($mysqli) {
    $query = "SELECT * FROM account";
    return $mysqli->query($query);
}

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["UserID"])) {
        $userID = $mysqli->real_escape_string($_POST["UserID"]);

        $stme = $mysqli->prepare("SELECT Email FROM account WHERE UserID=?");
        $stme->bind_param("i", $userID);
        $stme->execute();
        $emailResult = $stme->get_result();
        $emailRow = $emailResult->fetch_assoc();
        $email = $emailRow["Email"];
        $mail = require __DIR__ . "/mailer.php";
        $mail->setFrom("parklyuser@outlook.com");
        $mail->addAddress($email);
        $mail->Subject = "Account Deleted";
        $mail->Body = <<<EOD
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Account Deletion</title>
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
                                        <h1>Account Deletion</h1>
                                        <p>Your Account was Deleted</p>
                                            We're sorry to see you go. You have been removed from our system.</p>
                                            If you would like to contact us about this matter please click the link below:
                                        </p>
                                        <a href="https://localhost/contactDriver.php" style="background:#20e277;text-decoration:none; color:#fff; padding:10px 24px; border-radius:50px; display:inline-block;">Contact Us</a>
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


        $deleteQuery = "DELETE FROM account WHERE UserID = '$userID'";

        $deleteResult = $mysqli->query($deleteQuery);

        if ($deleteResult === false) {
            throw new mysqli_sql_exception("Failed to Delete Account: " . $mysqli->error);
        }


    }

    $accounts = getAccounts($mysqli);

} catch (mysqli_sql_exception $e) {
    $errorMessage = "Error: Unable to delete Account. It might be associated with other records. " . $e->getMessage();
}
?>

<body>
<div class="box_dash_other">
    <h1 class="h1">All Accounts</h1>

    <?php if (isset($errorMessage)): ?>
        <div style="color: red;">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <?php if ($accounts && $accounts->num_rows > 0): ?>
        <table class="table_acc" border="1">
            <tr>
                <th>User ID</th>
                <th>Admin</th>
                <th>Firstname</th>
                <th>Surname</th>
                <th>Credit</th>
                <th>Username</th>
                <th>Email</th>
                <th>PhoneNumber</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $accounts->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['UserID']) ?></td>
                    <td><?= htmlspecialchars($row['IsAdmin']) ?></td>
                    <td><?= htmlspecialchars($row['Firstname']) ?></td>
                    <td><?= htmlspecialchars($row['Surname']) ?></td>
                    <td><?= htmlspecialchars($row['Credit']) ?></td>
                    <td><?= htmlspecialchars($row['Username']) ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= htmlspecialchars($row['PhoneNumber']) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="UserID" value="<?= htmlspecialchars($row['UserID']) ?>">
                            <button type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p class="text">No Accounts Found.</p>
    <?php endif; ?>
</div>






</body>