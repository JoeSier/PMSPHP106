<?php
include('partial/header.php');
?>



<body>

<p>0 is not admin anything else is admin</p>
    <p>Admin value: <?= htmlspecialchars($user["IsAdmin"]) ?></p>
    <?php if (htmlspecialchars($user["IsAdmin"]) == 0): ?>
        <h1>User Dashboard</h1>
        <p>Hello <?= htmlspecialchars($user["Username"]) ?></p>


        <p> Your bookings:</p>


        <p>current balance: <?= htmlspecialchars($user["Credit"]) ?></p>

        <p><a href="addBalance.php">Add Balance</a></p>
        <p><a href="manageCars.php">Manage Cars</a></p>

    <?php elseif (htmlspecialchars($user["IsAdmin"]) > 0): ?>
        <h1>Admin Dashboard</h1>
        <p><a href="displayCars.php">See All Cars</a></p>
        <p><a href="displayBookings.php">See All Bookings</a></p>
        <p><a href="displayAllAccounts.php">See All Accounts</a></p>
    <?php else: ?>
        <p>couldn't detect if admin</p>
    <?php endif; ?>






</body>
</html>