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
$selectedUserMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["user_select"])) {
        $selectedUserId = $_POST["user_select"]; // Retrieve the selected user ID

        // Find the selected user from the fetched list
        $selectedUser = array_filter($userNames, function ($user) use ($selectedUserId) {
            return $user["UserID"] == $selectedUserId;
        });

        if ($selectedUser) {
            $selectedUser = array_values($selectedUser)[0];
            $selectedUserMessage = "You selected: " . $selectedUser["Firstname"] . " " . $selectedUser["Surname"] . " " . $selectedUser["PhoneNumber"] . " " . $selectedUser["Email"]  ;
        }
    }

    if (isset($_POST["message"]) && !empty($selectedUserId)) {
        $message = $_POST["message"];
        // Simulate sending a message (you might need additional code to actually send it)
        $selectedUserMessage = "Message sent to " . $selectedUser["Firstname"] . " " . $selectedUser["Surname"] . ": " . $message;
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

    <?php if ($selectedUserMessage) : ?>
        <div class="message-box">
            <p><?php echo $selectedUserMessage; ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($selectedUserId)) : ?>
        <form method="post" action="">
            <input type="hidden" name="user_select" value="<?php echo $selectedUserId; ?>">
            <label for="message">Message for the user:</label>
            <input type="text" id="message" name="message" placeholder="Write your message here" required>
            <button type="submit">Send Message</button>
        </form>
    <?php endif; ?>



</div>


</body>
</html>
