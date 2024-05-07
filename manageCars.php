<?php
include('partial/header.php');
include('sidebar.php');
?>
<div class="dashContent">
<h1>Manage your cars</h1>

<?php

$mysqli = require __DIR__ . "/database.php";

$sec = "SELECT * FROM car WHERE UserID = {$_SESSION["UserID"]}";
$res = $mysqli->query($sec);
if ($res) {
    // Fetch all rows from the result set
    $cars = [];
    while ($row = $res->fetch_assoc()) {
        $cars[] = $row;  // Append the row to the cars array
    }

    // Display the fetched cars in a readable format
    print_r( "<p> Your Cars <p>");
    echo "<p>NoOfCars: " . sizeof($cars) . "</p>";
    foreach ($cars as $car) {
        echo "<pre>"; // Optional, makes it easier to format
        printf("<br>LicensePLate: <br>");
        print_r($car["LicensePlate"]);
        printf("<br>Car Type: <br>");
        print_r($car["CarType"]);
        echo "</pre>";
        echo "<br>"; // Move to the next line
    }
} else {
    echo "Query failed: " . $mysqli->error;
}

?>
</div>
<body>

<p><a href="addCar.php">add Cars</a></p>
<p><a href="removeCars.php">remove Cars</a></p>


</body>
</html>
