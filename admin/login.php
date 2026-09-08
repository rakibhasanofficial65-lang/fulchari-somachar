<?php

session_start();

require_once dirname(__DIR__) . "/config/config.php";
require_once dirname(__DIR__) . "/config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {

        $error = "Username এবং Password দিন।";

    } else {

        $stmt = $pdo->prepare("
            SELECT id, name, username, password
            FROM users
            WHERE username = ?
            LIMIT 1
        ");

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

    <title>
        Admin Login - <?php echo htmlspecialchars(SITE_NAME); ?>
    </title>

    <link
        rel="stylesheet"
        href="<?php echo SITE_URL; ?>/assets/style.css"
    >

    <style>

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: #f5f5f5;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.10);
        }

        .login-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-title h1 {
            color: #b30000;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .login-title p {
            color: #777;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #b30000;
        }

        .login-button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 5px;
            background: #b30000;
            color: #ffffff;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
        }

        .login-button:hover {
            background: #8b0000;
        }

        .error-message {
            background: #ffe5e5;
            color: #a00000;
            border: 1px solid #ffb3b3;
            padding: 10px 12px;
            border-radius: 5px;
            margin-bottom: 18px;
            text-align: center;
        }

        .back-home {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #555;
            font-size: 14px;
            text-decoration: none;
        }

        .back-home:hover {
            color: #b30000;
        }

    </style>

</head>

<body>

<div class="login-page">

    <div class="login-box">

        <div class="login-title">

            <h1>ফুলছড়ি সমাচার</h1>

            <p>Admin Panel Login</p>

        </div>

        <?php if ($error !== ""): ?>

            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
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
                class="login-button"
            >
                Login
            </button>

        </form>

        <a
            href="<?php echo SITE_URL; ?>/"
            class="back-home"
        >
            ← মূল ওয়েবসাইটে ফিরে যান
        </a>

    </div>

</div>

</body>

</html>