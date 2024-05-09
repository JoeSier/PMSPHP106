<?php if (htmlspecialchars($user["IsAdmin"]) == 0): ?>
    <div id="sidebar">
        <ul> <!-- Added unordered list to wrap the list items -->
            <li><a href="userBookings.php">Manage Bookings</a></li>
            <li><a href="addBalance.php">Add Balance</a></li>
            <li><a href="addCar.php">add Car</a></li>
            <li><a href="removeCars.php">add Car</a></li>
            <li><a href="Forum.php">Forum</a></li>
            <li><a href="map.php">Map</a></li>
        </ul>
    </div>
<?php elseif (htmlspecialchars($user["IsAdmin"]) > 0): ?>
    <div id="sidebar">
        <ul> <!-- Added unordered list to wrap the list items -->
            <li><a href="displayCars.php">See All Cars</a></li>
            <li><a href="displayBookings.php">See Parking Lot Usage</a></li>
            <li><a href="displayAllAccounts.php">See All Accounts</a></li>
            <li><a href="manageSpaces.php">Manage parking spaces</a></li>
            <li><a href="changebookings.php">Change User Booking</a></li>
            <li><a href="addParkingLot.php">Add parking Lot</a></li>
            <li><a href="removeParkingLot.php">Remove parking lot</a></li>
<!--            <li><a href="contactDriver.php">Contact User</a></li>-->
            <li><a href="Forum.php">Forum</a></li>
            <li><a href="approveBooking.php">Pending bookings</a></li>
            <li><a href="map.php">Where is my parking lot?</a></li>
        </ul>
    </div>
<?php endif; ?>




