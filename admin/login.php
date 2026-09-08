<?php

session_start();

require_once dirname(__DIR__) . "/config/config.php";
require_once dirname(__DIR__) . "/config/database.php";

if (isset($_SESSION["admin_id"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Username এবং Password দিন।";
    } else {

        $stmt = $pdo->prepare(
            "SELECT id, name, username, password
             FROM users
             WHERE username = ?
             LIMIT 1"
        );

        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {

            $_SESSION["admin_id"] = $user["id"];
            $_SESSION["admin_name"] = $user["name"];
            $_SESSION["admin_username"] = $user["username"];

            header("Location: index.php");
            exit;

        } else {
            $error = "Username অথবা Password ভুল।";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo htmlspecialchars(SITE_NAME); ?></title>

    <link rel="stylesheet"
          href="<?php echo SITE_URL; ?>/assets/style.css">
</head>

<body>

<div class="admin-login-wrapper">

    <div class="admin-login-box">

        <img
            src="<?php echo SITE_URL; ?>/assets/logo.png"
            alt="<?php echo htmlspecialchars(SITE_NAME); ?>"
            class="admin-login-logo"
        >

        <h1>Admin Login</h1>

        <?php if ($error): ?>
            <div class="admin-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Username</label>
                <input
                    type="text"
                    name="username"
                    required
                    autocomplete="username"
                >
            </div>

            <div class="form-group">
                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="admin-login-button">
                Login
            </button>

        </form>

        <a
            href="<?php echo SITE_URL; ?>/"
            class="back-home"
        >
            ← মূল সাইটে ফিরে যান
        </a>

    </div>

</div>

</body>
</html>
