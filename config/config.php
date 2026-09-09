<?php

define("SITE_NAME", "ফুলছড়ি সমাচার");

/*
|--------------------------------------------------------------------------
| Site URL
|--------------------------------------------------------------------------
| Localhost হলে:
| http://localhost/fulchari-somachar
|
| Vercel হলে:
| Vercel-এর production URL automatically নেওয়া হবে।
|--------------------------------------------------------------------------
*/

$vercelProductionUrl = getenv("VERCEL_PROJECT_PRODUCTION_URL");
$vercelUrl = getenv("VERCEL_URL");

if (!empty($vercelProductionUrl)) {

    $siteUrl = "https://" . $vercelProductionUrl;

} elseif (!empty($vercelUrl)) {

    $siteUrl = "https://" . $vercelUrl;

} else {

    $siteUrl = "http://localhost/fulchari-somachar";
}

define("SITE_URL", rtrim($siteUrl, "/"));
