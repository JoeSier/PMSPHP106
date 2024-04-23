<?php

if (empty($_POST["firstname"])) {
    die("First name is required");
}

if (empty($_POST["lastname"])) {
    die("Last name is required");
}

if (empty($_POST["username"])) {
    die("Username is required");
}

//if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
//    die("Valid email is required");
//}

if (empty($_POST["email"])) {
    die("Valid email is required");
}

if (empty($_POST["phonenumber"])) {
    die("Phone number is required");
}

if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters");
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}

if (!preg_match("/[0-9]/", $_POST["password"])) {
    die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$mysqli = require __DIR__ . "/database.php";

//print_r($_POST);
//var_dump($password_hash);

//allows us to avoid an sql injection attack or something

$sql ="INSERT INTO account(Firstname, Surname, Username, UserPassword, Email, PhoneNumber)
VALUES (?,?,?,?,?,?)";



//$stmt = $mysqli->stmt_init();

if (!$stmt = $mysqli->stmt_init()) {
    die("Failed to initialize statement: " . $mysqli->error);
}


if (!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}
//
//$stmt->bind_param("ssssss",
//    $_POST["firstName"],
//    $_POST["lastName"],
//    $_POST["username"],
//    $password_hash,
//    $_POST["email"],
//    $_POST["phonenumber"]);

$firstName = $_POST["firstname"] ?? null; // Using null coalescing operator for safety
$lastName = $_POST["lastname"] ?? null;
$username = $_POST["username"] ?? null;
$email = $_POST["email"] ?? null;
$phonenumber = $_POST["phonenumber"] ?? null;

// Ensure these variables are not null
if (!$firstName || !$lastName || !$username || !$email || !$phonenumber) {
    die("Missing required fields");
}

$stmt->bind_param("ssssss", $firstName, $lastName, $username, $password_hash, $email, $phonenumber);


if ($stmt->execute()) {

    header("Location: signup-success.html");
    exit;

} else {

    if ($mysqli->errno === 1062) {
        die("email already taken");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}








