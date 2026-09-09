<?php

$root = dirname(__DIR__);

$path = parse_url(
    $_SERVER["REQUEST_URI"] ?? "/",
    PHP_URL_PATH
);

$path = "/" . ltrim($path, "/");

if ($path !== "/") {
    $path = rtrim($path, "/");
}


// =====================================================
// HOME
// =====================================================

if ($path === "/") {

    require $root . "/index.php";

    exit;
}


// =====================================================
// NEWS
// /news/slug
// =====================================================

if (
    preg_match(
        '#^/news/([^/]+)$#',
        $path,
        $m
    )
) {

    $_GET["slug"] = urldecode($m[1]);

    require $root . "/news.php";

    exit;
}


// =====================================================
// CATEGORY
// /category/slug
// =====================================================

if (
    preg_match(
        '#^/category/([^/]+)$#',
        $path,
        $m
    )
) {

    $_GET["slug"] = urldecode($m[1]);

    require $root . "/category.php";

    exit;
}


// =====================================================
// SEARCH
// /search
// /search/keyword
// =====================================================

if ($path === "/search") {

    require $root . "/search.php";

    exit;
}


if (
    preg_match(
        '#^/search/(.*)$#',
        $path,
        $m
    )
) {

    $_GET["q"] = urldecode($m[1]);

    require $root . "/search.php";

    exit;
}


// =====================================================
// E-PAPER
// /epaper
// =====================================================

if ($path === "/epaper") {

    require $root . "/epaper.php";

    exit;
}


// =====================================================
// SITEMAP
// /sitemap.xml
// =====================================================

if ($path === "/sitemap.xml") {

    require $root . "/sitemap.php";

    exit;
}


// =====================================================
// 404
// =====================================================

if ($path === "/404") {

    http_response_code(404);

    require $root . "/404.php";

    exit;
}


// =====================================================
// DIRECT PHP PAGES
// =====================================================

$allowed = [

    "/index.php"
        => "/index.php",

    "/news.php"
        => "/news.php",

    "/category.php"
        => "/category.php",

    "/search.php"
        => "/search.php",

    "/epaper.php"
        => "/epaper.php",

    "/404.php"
        => "/404.php",

    "/sitemap.php"
        => "/sitemap.php",


    // =================================================
    // ADMIN
    // =================================================

    "/admin/index.php"
        => "/admin/index.php",

    "/admin/login.php"
        => "/admin/login.php",

    "/admin/logout.php"
        => "/admin/logout.php",

    "/admin/add-news.php"
        => "/admin/add-news.php",

    "/admin/edit-news.php"
        => "/admin/edit-news.php",

    "/admin/news-list.php"
        => "/admin/news-list.php",

];


if (isset($allowed[$path])) {

    require $root . $allowed[$path];

    exit;
}


// =====================================================
// FINAL 404
// =====================================================

http_response_code(404);

require $root . "/404.php";

exit;