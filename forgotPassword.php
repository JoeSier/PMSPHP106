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
<div class="box">
    <h1 class="h1">Forgot Password</h1>

    <form class="form" method="post" action="passwordReset.php">

        <label class="label" for="email">Email</label>
        <input class="input" type="email" name="email" id="email">

        <button class="button">Send</button>

    </form>
</div>
</body>
</html>