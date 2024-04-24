<?php

include('partial/header.php');

$freeSpaces = $_SESSION['freeSpaces'] ?? [];
$form1_data = $_SESSION['form1_data'] ?? [];

if (empty($freeSpaces) || empty($form1_data)) {
    die("Invalid data. Please start the process again.");
}

?>
<!--shows the available parking spaces and redirects to finalize_booking-->

<form method="post" action="finalize_booking.php"> <!-- Finalize booking in a new page -->
    <input type="hidden" name="form_type" value="form2">

    <label for="parking_space">Select Parking Space (Optional):</label>
    <select name="parking_space" id="parking_space">
        <option value="">Choose a space (optional)</option> <!-- Changed to optional -->
        <?php
        foreach ($freeSpaces as $space) {
            echo "<option value=\"" . htmlspecialchars($space) . "\">" . htmlspecialchars($space) . "</option>";
        }
        ?>
    </select><br>

    <button type="submit">Submit</button>
</form>
