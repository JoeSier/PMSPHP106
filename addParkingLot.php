<?php
include('partial/header.php');
include('sidebar.php');
$successMessage=null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST["TotalSpaces"])) {
        die("Total Spaces is required");
    }

    if (empty($_POST["LotName"])) {
        die("Name is required");
    }
    $TotalSpaces = $_POST["TotalSpaces"];
    $Name = $_POST["LotName"];


    $mysqli = require __DIR__ . "/database.php";
    $stmt = $mysqli->prepare("INSERT INTO parkinglots (TotalSpaces,LotName) VALUES (?,?)");

    $stmt->bind_param("is", $TotalSpaces,$Name);
    $success = $stmt->execute();

    if ($success) {
        $successMessage = "Parking lot added successfully!";
    } else {
        $successMessage = "Error adding parking lot.";
    }
    $stmt->close();
    $mysqli->close();
}

?>

<body>
<div class="dashContent">
<h1>Add a New Car</h1>
<div id="loginform">
<form method="post">
    <label for="Name">Enter name of parking lot:</label>
    <input type="text" name="LotName" id="LotName">
    <label for="TotalSpaces">Enter amount of spaces:</label>
    <input type="number" name="TotalSpaces" id="TotalSpaces">

    <button>Add Car</button>
</form>
    <?php if ($successMessage): ?>
        <div class="successMessage"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>
</div>
</div>

</body>
</html>

