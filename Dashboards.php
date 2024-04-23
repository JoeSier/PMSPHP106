<?php
include('partial/header.php');
session_start();

if (isset($_SESSION["UserID"])) {

    $mysqli = require __DIR__ . "/database.php";

    $sql = "SELECT * FROM account
            WHERE UserID = {$_SESSION["UserID"]}";

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();

}

?>



<body>
<?php if (isset($user)): ?>
<ul>
    <li><a href="index.php">Home</a></li>
    <li></li>
    <li></li>
    <li><a href="logout.php">Log out</a></li>
</ul>
<p>0 is not admin anything else is admin</p>
    <p>Admin value: <?= htmlspecialchars($user["IsAdmin"]) ?></p>
    <?php if (htmlspecialchars($user["IsAdmin"]) == 0): ?>
        <h1>User Dashboard</h1>
        <p>Hello <?= htmlspecialchars($user["Username"]) ?></p>


        <p> Your bookings:</p>


        <p>current balance: <?= htmlspecialchars($user["Credit"]) ?></p>

        <p><a href="dashboards-sub/addBalance.php">Add Balance</a></p>
        <p><a href="dashboards-sub/manageCars.php">Manage Cars</a></p>

    <?php elseif (htmlspecialchars($user["IsAdmin"]) > 0): ?>
        <h1>Admin Dashboard</h1>
<p>manage parking assignment</p>
    <p>display parking lot data</p>
        <p><a href="dashboards-sub/displayAllAccounts.php">See All Accounts</a></p>
    <?php else: ?>
        <p>couldn't detect if admin</p>
    <?php endif; ?>

<?php else: ?>
    <ul>
        <li><a href="login.php">Log in</a> or <a href="signup.html">sign up</a></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>


<?php endif; ?>

</body>
</html>