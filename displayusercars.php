<?php
include('partial/header.php');
include('sidebar.php');


// Check if the user is allowed to be here
//if (!isset($user) || $user["IsAdmin"] == 0) {
//    die("You are not allowed here.");
//}

function getCars($mysqli) {
    $query = "SELECT * FROM car Where UserID = ".$_SESSION["UserID"];
    return $mysqli->query($query);
}

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["LicensePlate"])) {
        $licensePlate = $mysqli->real_escape_string($_POST["LicensePlate"]);
        $deleteQuery = "DELETE FROM car WHERE LicensePlate = '$licensePlate'";

        $deleteResult = $mysqli->query($deleteQuery);

        if ($deleteResult === false) {
            throw new mysqli_sql_exception("Failed to remove car: " . $mysqli->error);
        }
    }

    $cars = getCars($mysqli);
} catch (mysqli_sql_exception $e) {
    $errorMessage = "Error: Unable to remove the car. It might be associated with other records. " . $e->getMessage();
}
?>

<body>
<div class="box_dash_other">

    <h1>All Cars</h1>

    <?php if (isset($errorMessage)): ?>
        <div style="color: red;">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <?php if ($cars && $cars->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>User ID</th>
                <th>License Plate</th>
                <th>Car Type</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $cars->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['UserID']) ?></td>
                    <td><?= htmlspecialchars($row['LicensePlate']) ?></td>
                    <td><?= htmlspecialchars($row['CarType']) ?></td>
                    <td>
                        <form class="form" method="post">
                            <!-- Use LicensePlate to identify the car to be removed -->
                            <input class="input" type="hidden" name="LicensePlate" value="<?= htmlspecialchars($row['LicensePlate']) ?>">
                            <button class="button" type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No Cars Found.</p>
    <?php endif; ?>
    <li><a id="add_car" href="addCar.php">add Car</a></li>
    <li><a id="remove_car_user" href="removeCars.php">Remove Car</a></li>
</div>
</body>
</html>

<?php
$cars->close(); // Close the result set after processing
?>
