<?php

// Ensure all required POST fields are present
$requiredFields = ["firstname", "lastname", "username", "email", "phonenumber", "password", "password_confirmation"];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        die(ucfirst($field) . " is required");
    }
}

$password = $_POST["password"];
if (strlen($password) < 8) {
    die("Password must be at least 8 characters");
}
if (!preg_match("/[a-z]/i", $password)) {
    die("Password must contain at least one letter");
}
if (!preg_match("/[0-9]/", $password)) {
    die("Password must contain at least one number");
}
if ($password !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

// Hash the password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$activation_token = bin2hex(random_bytes(16));

$activation_token_hash = hash("sha256", $activation_token);

$mysqli = require __DIR__ . "/database.php";

// SQL statement to insert new user
$sql = "INSERT INTO account (Firstname, Surname, Username, UserPassword, Email, PhoneNumber,account_activation_hash) VALUES (?,?,?,?,?,?,?)";

if (!$stmt = $mysqli->stmt_init()) {
    die("Failed to initialize statement: " . $mysqli->error);
}

if (!$stmt->prepare($sql)) {
    die("Failed to prepare SQL statement: " . $mysqli->error);
}

$firstName = $_POST["firstname"];
$lastName = $_POST["lastname"];
$username = $_POST["username"];
$email = $_POST["email"];
$phoneNumber = $_POST["phonenumber"];


// Check if the email already exists
$sqlCheckEmail = "SELECT COUNT(*) as count FROM account WHERE Email = ?";
$stmtCheckEmail = $mysqli->prepare($sqlCheckEmail);

$stmtCheckEmail->bind_param("s", $email);
$stmtCheckEmail->execute();
$stmtCheckEmail->bind_result($emailCount);
$stmtCheckEmail->fetch();
$stmtCheckEmail->close();

if ($emailCount > 0) {
    die("Email  already taken. Please use a different email.");
}
// Check if the phone number already exists
$sqlCheckphone = "SELECT COUNT(*) as count FROM account WHERE phoneNumber = ?";
$stmtCheckphone = $mysqli->prepare($sqlCheckphone);

$stmtCheckphone->bind_param("s", $phoneNumber);
$stmtCheckphone->execute();
$stmtCheckphone->bind_result($phoneCount);
$stmtCheckphone->fetch();
$stmtCheckphone->close();


if ($phoneCount > 0) {
    die("Phone number already taken. Please use a different phone number.");
}

// Bind parameters
if (!$stmt->bind_param("sssssss", $firstName, $lastName, $username, $password_hash, $email, $phoneNumber,$activation_token_hash)) {
    die("Failed to bind parameters: " . $stmt->error);
}

if ($stmt->execute()) {

    $mail = require __DIR__ . "/mailer.php";

    $mail->setFrom("parklyuser@outlook.com");
    $mail->addAddress($_POST["email"]);
    $mail->Subject = "Account Activation";
    $mail->Body = <<<EOD
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Account Activation</title>
    <meta name="description" content="Account Activation Email.">
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
                                        <h1>Activate your account</h1>
                                        <p>
                                            To activate your account, click the following link:
                                        </p>
                                        <a href="https://localhost/activateAccount.php?token=$activation_token" style="background:#20e277;text-decoration:none; color:#fff; padding:10px 24px; border-radius:50px; display:inline-block;">Activate Account</a>
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

} else {

    if ($mysqli->errno === 1062) {
        die("email already taken");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}

header("Location: signup-success.php");
exit;






