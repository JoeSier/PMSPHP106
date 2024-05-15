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
<div  id="loginbox">
    <div id="loginform">
        <h1 class="title">Signup</h1>

        <form action="process-signup.php" method="post" id="signup" novalidate>
            <div>
                <label class="login_signin_label" for="firstname">First Name</label>
                <input class="login_signin_input" type="text" id="firstname" name="firstname" required>
            </div>

            <div>
                <label class="login_signin_label" for="lastname">Last Name</label>
                <input class="login_signin_input" type="text" id="lastname" name="lastname" required>
            </div>

            <div>
                <label class="login_signin_label" for="username">Username</label>
                <input class="login_signin_input" type="text" id="username" name="username" required>
            </div>

            <div>
                <label class="login_signin_label" for="email">Email</label>
                <input class="login_signin_input" type="email" id="email" name="email" required>
            </div>

            <div>
                <label class="login_signin_label" for="phonenumber">Phone Number</label>
                <input class="login_signin_input" type="text" id="phonenumber" name="phonenumber" required>
            </div>

            <div>
                <label class="login_signin_label" for="password">Password</label>
                <input class="login_signin_input" type="password" id="password" name="password" required>
            </div>

            <div>
                <label class="login_signin_label" for="password_confirmation">Repeat password</label>
                <input class="login_signin_input" type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button class="login_signin_button">Sign up</button>
        </form>
    </div>
</div>
</body>
</html>








