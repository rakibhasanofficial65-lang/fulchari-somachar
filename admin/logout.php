<?php

session_start();

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

$_SESSION = [];

// Session cookie remove
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Session destroy
session_destroy();

// Login page-এ redirect
header("Location: ../admin/login.php");
exit;
