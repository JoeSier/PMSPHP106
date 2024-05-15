<?php
include('partial/header.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <meta charset="UTF-8">
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js" defer></script>
    <script src="/js/validation.js" defer></script>
</head>
<body>
<div  class="box">
    <div class="form">
        <h1 class="title">Signup</h1>

        <form action="process-signup.php" method="post" id="signup" novalidate>
            <div>
                <label class="label" for="firstname">First Name</label>
                <input class="input" type="text" id="firstname" name="firstname" required>
            </div>

            <div>
                <label class="label" for="lastname">Last Name</label>
                <input class="input" type="text" id="lastname" name="lastname" required>
            </div>

            <div>
                <label class="label" for="username">Username</label>
                <input class="input" type="text" id="username" name="username" required>
            </div>

            <div>
                <label class="label" for="email">Email</label>
                <input class="input" type="email" id="email" name="email" required>
            </div>

            <div>
                <label class="label" for="phonenumber">Phone Number</label>
                <input class="input" type="text" id="phonenumber" name="phonenumber" required>
            </div>

            <div>
                <label class="label" for="password">Password</label>
                <input class="input" type="password" id="password" name="password" required>
            </div>

            <div>
                <label class="label" for="password_confirmation">Repeat password</label>
                <input class="input" type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button class="button">Sign up</button>
        </form>
    </div>
</div>
</body>
</html>








