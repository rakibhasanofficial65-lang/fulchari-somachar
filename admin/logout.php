<?php

session_start();

// সব session data মুছে ফেলুন
$_SESSION = [];

// Session cookie থাকলে সেটিও মুছে দিন
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Session destroy
session_destroy();

// Login page-এ পাঠিয়ে দিন
header("Location: login.php");
exit;