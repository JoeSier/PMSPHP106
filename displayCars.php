<?php
include('partial/header.php');
include('sidebar.php');
$isAdmin = intval($user["IsAdmin"]) > 0;

if ($isAdmin):
$sel = "SELECT * FROM car";
$res = $mysqli->query($sel);
if ($res) {
    // Fetch all rows from the result set
    $accounts = [];
    while ($row = $res->fetch_assoc()) {
        $accounts[] = $row;  // Append the row to the accounts array
    }

    // Display the fetched accounts in a readable format
    print_r( "<div class='dashContent'><br> All cars: <br></div>");
    foreach ($accounts as $account) {
        echo "<div class='dashContent'><pre>"; // Optional, makes it easier to format
        print_r($account);
        echo "</pre>";
        echo "<br></div>"; // Move to the next line
    }
} else {
    // Handle query failure
    echo "Query failed: " . $mysqli->error;
}
else:
    die ("You are not authorized to access this page.");
endif;
?>

    <body>
    <div class="dashContent">
    </div>

    </body><?php
