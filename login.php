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


    if ($user) {
        if (password_verify($_POST["password"], $user["UserPassword"])) {
            session_start();

            session_regenerate_id();

            $_SESSION["UserID"] = $user["UserID"];

            header("Location: index.php");
            exit;
        }
    }


    $is_invalid = true;
}

?>
<body>

<h1>Login</h1>

<?php if ($is_invalid): ?>
    <em>Invalid login</em>
<?php endif; ?>

<form method="post">
    <label for="email">email</label>
    <input type="email" name="email" id="email"
           value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">

    <label for="password">Password</label>
    <input type="password" name="password" id="password">

    <button>Log in</button>
</form>

</body>
</html>








