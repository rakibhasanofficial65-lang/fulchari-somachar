<?php

$root = dirname(__DIR__);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');

if ($path === '') {
    require $root . '/index.php';
    exit;
}

if (preg_match('#^/news/([^/]+)$#', $path, $m)) {
    $_GET['slug'] = $m[1];
    require $root . '/news.php';
    exit;
}

if (preg_match('#^/category/([^/]+)$#', $path, $m)) {
    $_GET['slug'] = $m[1];
    require $root . '/category.php';
    exit;
}

if (preg_match('#^/search/(.*)$#', $path, $m)) {
    $_GET['q'] = urldecode($m[1]);
    require $root . '/search.php';
    exit;
}

if ($path === '/epaper') {
    require $root . '/epaper.php';
    exit;
}

if ($path === '/sitemap.xml') {
    require $root . '/sitemap.php';
    exit;
}

if ($path === '/404') {
    require $root . '/404.php';
    exit;
}

/*
|--------------------------------------------------------------------------
| Direct PHP pages
|--------------------------------------------------------------------------
*/

$allowed = [
    '/index.php' => '/index.php',
    '/news.php' => '/news.php',
    '/category.php' => '/category.php',
    '/search.php' => '/search.php',
    '/epaper.php' => '/epaper.php',
    '/404.php' => '/404.php',
    '/sitemap.php' => '/sitemap.php',

    '/admin/index.php' => '/admin/index.php',
    '/admin/login.php' => '/admin/login.php',
    '/admin/logout.php' => '/admin/logout.php',
    '/admin/add-news.php' => '/admin/add-news.php',
    '/admin/edit-news.php' => '/admin/edit-news.php',
    '/admin/news-list.php' => '/admin/news-list.php',
];

if (isset($allowed[$path])) {
    require $root . $allowed[$path];
    exit;
}

http_response_code(404);
require $root . '/404.php';
exit;