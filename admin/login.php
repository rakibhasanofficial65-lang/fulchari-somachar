<?php

session_start();

require_once dirname(__DIR__) . "/config/config.php";
require_once dirname(__DIR__) . "/config/database.php";
require_once dirname(__DIR__) . "/includes/functions.php";

if (isset($_SESSION["admin_id"])) {
    header("Location: " . SITE_URL . "/admin/index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {

        $error = "Username এবং Password দিন।";

    } else {

        try {

            $stmt = $pdo->prepare("
                SELECT id, name, username, password
                FROM users
                WHERE username = ?
                LIMIT 1
            ");

            $stmt->execute([$username]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user["password"])) {

                session_regenerate_id(true);

                $_SESSION["admin_id"] = $user["id"];
                $_SESSION["admin_name"] = $user["name"];
                $_SESSION["admin_username"] = $user["username"];

                header("Location: " . SITE_URL . "/admin/index.php");
                exit;

            } else {

                $error = "Username অথবা Password ভুল।";
            }

        } catch (PDOException $e) {

            $error = "Database connection error।";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="bn">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Login | <?php echo e(SITE_NAME); ?></title>

    <link
        rel="stylesheet"
        href="<?php echo e(SITE_URL); ?>/assets/style.css"
    >

</head>

<body class="admin-login-page">

    <div class="login-wrapper">

        <div class="login-box">

            <div class="login-logo">

                <a href="<?php echo e(SITE_URL); ?>/">

                    <img
                        src="<?php echo e(SITE_URL); ?>/assets/logo.png"
                        alt="<?php echo e(SITE_NAME); ?>"
                    >

                </a>

            </div>

            <h1>Admin Login</h1>

            <p class="login-subtitle">
                <?php echo e(SITE_NAME); ?> প্রশাসনিক প্যানেল
            </p>

            <?php if ($error !== ""): ?>

                <div class="alert alert-danger">
                    <?php echo e($error); ?>
                </div>

            <?php endif; ?>

            <form method="POST" action="">

                <div class="form-group">

                    <label for="username">
                        Username
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Username লিখুন"
                        autocomplete="username"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Password লিখুন"
                        autocomplete="current-password"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="btn btn-primary btn-block"
                >
                    Login
                </button>

            </form>

            <div class="login-back">

                <a href="<?php echo e(SITE_URL); ?>/">
                    ← Home Page
                </a>

            </div>

        </div>

    </div>

</body>
</html>
