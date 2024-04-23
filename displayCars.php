<?php
include('partial/header.php');

$sel = "SELECT * FROM car";
$res = $mysqli->query($sel);
if ($res) {
    // Fetch all rows from the result set
    $accounts = [];
    while ($row = $res->fetch_assoc()) {
        $accounts[] = $row;  // Append the row to the accounts array
    }

    // Display the fetched accounts in a readable format
    print_r( "<br> All bookings: <br>");
    foreach ($accounts as $account) {
        echo "<pre>"; // Optional, makes it easier to format
        print_r($account);
        echo "</pre>";
        echo "<br>"; // Move to the next line
    }
} else {
    // Handle query failure
    echo "Query failed: " . $mysqli->error;
}
?>

    <body>

    </body><?php
