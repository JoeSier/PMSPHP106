<?php
include('partial/header.php');
$isAdmin = intval($user["IsAdmin"]) > 0;

if ($isAdmin):
$sel = "SELECT * FROM account";
$mysqlj = require __DIR__ . "/database.php";
$res = $mysqlj->query($sel);
if ($res) {
    // Fetch all rows from the result set
    $accounts = [];
    while ($row = $res->fetch_assoc()) {
        $accounts[] = $row;  // Append the row to the accounts array
    }

    // Display the fetched accounts in a readable format
    print_r( "<br> All accounts: <br>");
    foreach ($accounts as $account) {
        echo "<pre>";
        print_r($account);
        echo "</pre>";
        echo "<br>";
    }
} else {
    // Handle query failure
    echo "Query failed: " . $mysqlj->error;
}
else:
    die ("You are not authorized to access this page.");
endif;
?>

<body>
<p>placeholder</p>







</body>