<?php
include('partial/header.php');
$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $mysqli = require __DIR__ . "/database.php";

    $sql = sprintf("SELECT * FROM account
                    WHERE email = '%s'",
        $mysqli->real_escape_string($_POST["email"]));

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();


    if ($user && $user["account_activation_hash"] === null) {
        if (password_verify($_POST["password"], $user["UserPassword"])) {
            session_start();

            session_regenerate_id();

            $_SESSION["UserID"] = $user["UserID"];

            header("Location: Dashboards.php");
            exit;
        }
    }


    $is_invalid = true;
}

?>
<body>

<div id="loginbox">
    <h1 class="title">Welcome back!</h1>

    <div id="loginform">


        <form method="post">
            <label class="login_signin_label" for="email">Email</label>
            <input class="login_signin_input" type="email" name="email" id="email"
                   value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">

            <label class="login_signin_label" for="password">Password</label>
            <input class="login_signin_input" type="password" name="password" id="password">
            <div class="login_buttons_div">
            <button class="login_signin_button">Log in</button>
        </form>
            <a href="signup.php"  class="login_signin_button">Sign up</a>
        </div>
    </div>
    <?php if ($is_invalid): ?>
        <em>Invalid login</em>
    <?php endif; ?>

    <a class="topSpace" href="forgotPassword.php">Forgot your password?</a>
</div>
</body>







