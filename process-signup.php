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

$mysqli = require __DIR__ . "/database.php";

// SQL statement to insert new user
$sql = "INSERT INTO account (Firstname, Surname, Username, UserPassword, Email, PhoneNumber) VALUES (?,?,?,?,?,?)";

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
if (!$stmt->bind_param("ssssss", $firstName, $lastName, $username, $password_hash, $email, $phoneNumber)) {
    die("Failed to bind parameters: " . $stmt->error);
}

// Execute the statement and handle errors
if (!$stmt->execute()) {
    if ($mysqli->errno === 1062) {
        die("Email or username already taken");
    } else {
        die("SQL Error: " . $mysqli->error . " (Error code: " . $mysqli->errno . ")");
    }
}

// On successful execution, redirect to a success page
header("Location: signup-success.html");
exit;

?>






