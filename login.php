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

<div class="box">
    <h1 class="title">Welcome back!</h1>

    <div class="form">


        <form method="post">
            <label class="label" for="email">Email</label>
            <input class="input" type="email" name="email" id="email"
                   value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">

            <label class="label" for="password">Password</label>
            <input class="input" type="password" name="password" id="password">
            <div class="login_buttons_div">
            <button class="button">Log in</button>
        </form>
            <a href="signup.php" id="second_button" class="button">Sign up</a>
        </div>
    </div>
    <?php if ($is_invalid): ?>
        <em>Invalid login</em>
    <?php endif; ?>

    <a id="question" href="forgotPassword.php">Forgot your password?</a>
</div>
</body>







