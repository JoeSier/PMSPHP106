<?php
include('partial/header.php');
?>

<body>

<h1>Manage your cars</h1>
    <p>Hello <?= htmlspecialchars($user["Username"]) ?></p>
    <p><a href="logout.php">Log out</a></p>
<p><a href="addCar.php">add Cars</a></p>
<p><a href="removeCars.php">remove Cars</a></p>


</body>
</html>