<?php

require_once dirname(__DIR__) . "/config/config.php";


// =====================================================
// DEFAULT SEO VALUES
// =====================================================

$pageTitle = $pageTitle ?? SITE_NAME;

$pageDescription = $pageDescription
    ?? "ফুলছড়ি সমাচার — ফুলছড়ি, গাইবান্ধা ও দেশের সর্বশেষ সংবাদ।";

$canonicalUrl = $canonicalUrl
    ?? SITE_URL . "/";

$ogImage = $ogImage
    ?? SITE_URL . "/assets/logo.png";


// =====================================================
// ESCAPE HELPER
// =====================================================

if (!function_exists("e")) {

    function e($value)
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            "UTF-8"
        );
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

    <meta
        name="theme-color"
        content="#111111"
    >

    <!-- =================================================
         BASIC SEO
    ================================================= -->

    <title><?php echo e($pageTitle); ?></title>

    <meta
        name="description"
        content="<?php echo e($pageDescription); ?>"
    >

    <meta
        name="robots"
        content="index, follow"
    >

    <link
        rel="canonical"
        href="<?php echo e($canonicalUrl); ?>"
    >

    <!-- =================================================
         OPEN GRAPH
    ================================================= -->

    <meta
        property="og:type"
        content="website"
    >

    <meta
        property="og:locale"
        content="bn_BD"
    >

    <meta
        property="og:site_name"
        content="<?php echo e(SITE_NAME); ?>"
    >

    <meta
        property="og:title"
        content="<?php echo e($pageTitle); ?>"
    >

    <meta
        property="og:description"
        content="<?php echo e($pageDescription); ?>"
    >

    <meta
        property="og:url"
        content="<?php echo e($canonicalUrl); ?>"
    >

    <meta
        property="og:image"
        content="<?php echo e($ogImage); ?>"
    >

    <!-- =================================================
         TWITTER CARD
    ================================================= -->

    <meta
        name="twitter:card"
        content="summary_large_image"
    >

    <meta
        name="twitter:title"
        content="<?php echo e($pageTitle); ?>"
    >

    <meta
        name="twitter:description"
        content="<?php echo e($pageDescription); ?>"
    >

    <meta
        name="twitter:image"
        content="<?php echo e($ogImage); ?>"
    >

    <!-- =================================================
         STYLESHEET
    ================================================= -->

    <link
        rel="stylesheet"
        href="<?php echo e(SITE_URL); ?>/assets/style.css"
    >

    <!-- =================================================
         FAVICON
    ================================================= -->

    <link
        rel="icon"
        type="image/png"
        href="<?php echo e(SITE_URL); ?>/assets/logo.png"
    >

</head>


<body>


<!-- =====================================================
     SITE HEADER
===================================================== -->

<header class="site-header">

    <div class="container">

        <div class="header-inner">

            <div class="logo-area">

                <a
                    href="<?php echo e(SITE_URL); ?>/"
                    class="logo"
                    aria-label="<?php echo e(SITE_NAME); ?>"
                >

                    <img
                        src="<?php echo e(SITE_URL); ?>/assets/logo.png"
                        alt="<?php echo e(SITE_NAME); ?>"
                    >

                </a>

                <div class="tagline">
                    ফুলছড়ি, গাইবান্ধা ও দেশের সর্বশেষ সংবাদ
                </div>

            </div>

        </div>

    </div>

</header>


<?php

// =====================================================
// NAVBAR
// =====================================================

$navbarFile = dirname(__DIR__) . "/includes/navbar.php";

if (file_exists($navbarFile)) {

    require $navbarFile;

}

?>
