<?php
include('partial/header.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <meta charset="UTF-8">
</head>
<body>

    <h1>Forgot Password</h1>

    <form method="post" action="passwordReset.php">

        <label for="email">email</label>
        <input type="email" name="email" id="email">

        <button>Send</button>

    </form>

</body>
</html>