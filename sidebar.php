<?php if (htmlspecialchars($user["IsAdmin"]) == 0): ?>
    <div id="sidebar">
        <ul> <!-- Added unordered list to wrap the list items -->
            <li><a href="userBookings.php">Manage Bookings</a></li>
            <li><a href="addBalance.php">Add Balance</a></li>
            <li><a href="manageCars.php">Manage Cars</a></li>
        </ul>
    </div>
<?php elseif (htmlspecialchars($user["IsAdmin"]) > 0): ?>
    <div id="sidebar">
        <ul> <!-- Added unordered list to wrap the list items -->
            <li><a href="displayCars.php">See All Cars</a></li>
            <li><a href="displayBookings.php">See All Bookings</a></li>
            <li><a href="displayAllAccounts.php">See All Accounts</a></li>
            <li><a href="manageParkingLots.php">Manage parking lots</a></li>
            <li><a href="manageSpaces.php">Manage parking spaces</a></li>
        </ul>
    </div>
<?php endif; ?>