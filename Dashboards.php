<?php
include('partial/header.php');
include('sidebar.php');



?>

<body>

<div class="dashContent">

<!--    code for user-->
    <?php if (isset($user) && $user["IsAdmin"] == 0): ?>
        <h1>Welcome back, <?= htmlspecialchars($user["Username"]) ?></h1>
        <h2>User Dashboard</h2>
        <p><br>Your current balance is: <?= htmlspecialchars($user["Credit"]) ?></p>


<!--    code for admin-->
        <!-- Code for admin -->
   <?php elseif (isset($user) && $user["IsAdmin"] > 0): ?>
        <h1>Welcome back, <?= htmlspecialchars($user["Username"]) ?></h1>
        <h2>Admin Dashboard</h2>

        <!-- Notification for bookings where users haven't arrived yet -->
        <?php
        $query = "SELECT * FROM booking WHERE Active = 0 AND timeStart <= DATE_SUB(NOW(), INTERVAL 1 HOUR)";

        if ($stmt = $mysqli->prepare($query)) {
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Display notification for bookings where users haven't arrived
                echo '<div class="notification">';
                echo '<p>Bookings where users havent arrived an hour after start:</p>';
                echo '<ul>';
                while ($row = $result->fetch_assoc()) {
                    echo '<li>Booking ID: ' . htmlspecialchars($row['BookingID']) . ', Lot Name: ' . htmlspecialchars($row['LotName']) . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }

            $stmt->close();
        } else {
            echo '<p>Unable to retrieve data. Please try again later.</p>';
        }
        ?>

        <!-- Notification for bookings where users are still active an hour after their end time -->
        <?php
        $query = "SELECT * FROM booking WHERE Active = 1 AND timeEnd IS NOT NULL AND timeEnd <= DATE_SUB(NOW(), INTERVAL 1 HOUR)";

        if ($stmt = $mysqli->prepare($query)) {
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Display notification for bookings where users are still active after timeEnd
                echo '<div class="notification">';
                echo '<p>Bookings where users are still active an hour after their end time:</p>';
                echo '<ul>';
                while ($row = $result->fetch_assoc()) {
                    echo '<li>Booking ID: ' . htmlspecialchars($row['BookingID']) . ', Lot Name: ' . htmlspecialchars($row['LotName']) . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }

            $stmt->close();
        } else {
            echo '<p>Unable to retrieve data. Please try again later.</p>';
        }
        ?>

    <?php else: ?>
        <p>Unable to determine user role.</p>
    <?php endif; ?>

</div>

</body>
