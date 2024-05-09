<?php
include('partial/header.php');
include('sidebar.php');
$isAdmin = intval($user["IsAdmin"]) > 0;
if (!isset($user) || $user["IsAdmin"] == 0) {
    die("You are not allowed here.");
}

function getAccounts($mysqli) {
    $query = "SELECT * FROM account";
    return $mysqli->query($query);
}

$accounts = getAccounts($mysqli);
?>

<body>
<div class="dashContent">
    <h1>All Accounts</h1>

    <?php if (isset($errorMessage)): ?>
        <div style="color: red;">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <?php if ($accounts && $accounts->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>User ID</th>
                <th>Admin</th>
                <th>Firstname</th>
                <th>Surname</th>
                <th>Credit</th>
                <th>Username</th>
                <th>Email</th>
                <th>PhoneNumber</th>
            </tr>
            <?php while ($row = $accounts->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['UserID']) ?></td>
                    <td><?= htmlspecialchars($row['IsAdmin']) ?></td>
                    <td><?= htmlspecialchars($row['Firstname']) ?></td>
                    <td><?= htmlspecialchars($row['Surname']) ?></td>
                    <td><?= htmlspecialchars($row['Credit']) ?></td>
                    <td><?= htmlspecialchars($row['Username']) ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= htmlspecialchars($row['PhoneNumber']) ?></td>
<!--                    <td>-->
<!--                        <form method="post">-->
                            <!-- Use LicensePlate to identify the car to be removed -->
<!--                            <input type="hidden" name="UserID" value="--><?php //= htmlspecialchars($row['LicensePlate']) ?><!--">-->
<!--                            <button type="submit">Remove</button>-->
<!--                        </form>-->
<!--                    </td>-->
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No Accounts Found.</p>
    <?php endif; ?>
    <a href="deleteDriver.php"> Delete Driver Account</a>


</div>






</body>