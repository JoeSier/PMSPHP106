<?php
include('partial/header.php');
include('sidebar.php');
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
    print_r( " <div class='dashContent'><br> All accounts: <br> </div>");
    foreach ($accounts as $account) {
        echo "<div class='dashContent'> <pre>";
        print_r($account);
        echo "</pre>";
        echo "<br> </div>";
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
<div class="dashContent">
    <a href="deleteDriver.php"> Delete Driver Account</a>


</div>






</body>