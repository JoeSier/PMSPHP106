<?php
include('partial/header.php');
include('sidebar.php');
?>
<!--not sure if this cant just be done in the mysql-->
<body>
<div class="dashContent">
<h1> Change Location of a booking</h1>
    <div id="loginform" class="box">
        <form method="post" class="form">
            <label class="label" for="License">Car license:</label>
            <select class="input" name="License" id="License">
                <option value="">Select a car to remove</option>
                <?php
                // Populate the select field with the user's cars
                foreach ($booking as $booking) {
                    // Display the car's LicensePlate and optionally the CarModel
                    echo "<option value=\"" . htmlspecialchars($car["LicensePlate"]) . "\">" . htmlspecialchars($car["LicensePlate"] . " - " . $car["CarModel"]) . "</option>";
                }
                ?>
            </select>
            <button class="button" type="submit">Remove Car</button>
        </form>
        <?php if ($successMessage): ?>
            <div class="successMessage"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
    </div>


</div>

</body>