<?php

require_once "config/config.php";
require_once "config/database.php";


// =====================================================
// GET CATEGORY SLUG
// =====================================================

$slug = trim($_GET["slug"] ?? "");

if ($slug === "") {
    header("Location: index.php");
    exit;
}


// =====================================================
// GET CATEGORY
// =====================================================

$categoryStmt = $pdo->prepare("
    SELECT id, name, slug
    FROM categories
    WHERE slug = ?
    LIMIT 1
");

$categoryStmt->execute([$slug]);

$category = $categoryStmt->fetch();


if (!$category) {

    http_response_code(404);

    die("Category পাওয়া যায়নি।");

}


// =====================================================
// GET NEWS
// =====================================================

$newsStmt = $pdo->prepare("
    SELECT
        news.*,
        categories.name AS category_name,
        categories.slug AS category_slug
    FROM news
    LEFT JOIN categories
        ON news.category_id = categories.id
    WHERE news.category_id = ?
    AND news.status = 'published'
    ORDER BY news.published_at DESC, news.id DESC
    LIMIT 30
");

$newsStmt->execute([
    $category["id"]
]);

$categoryNews = $newsStmt->fetchAll();

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
    name="description"
    content="<?php
        echo htmlspecialchars(
            $category["name"] .
            " - " .
            SITE_NAME .
            " এর সর্বশেষ সংবাদ"
        );
    ?>"
>

<title>

<?php echo htmlspecialchars(
    $category["name"]
); ?>

|

<?php echo SITE_NAME; ?>

</title>


<link
    rel="stylesheet"
    href="assets/style.css"
>


<style>

/* =====================================================
   CATEGORY PAGE
===================================================== */

.category-page {

    background: #f4f4f4;

    min-height: 100vh;

    padding: 35px 0 50px;

}


/* =====================================================
   CATEGORY HEADER
===================================================== */

.category-header {

    background: #fff;

    border: 1px solid #ddd;

    border-left: 6px solid #b30000;

    padding: 22px 25px;

    margin-bottom: 25px;

}


.category-header h1 {

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 32px;

    color: #111;

    margin-bottom: 5px;

}


.category-header p {

    color: #777;

    font-size: 14px;

}


/* =====================================================
   BREADCRUMB
===================================================== */

.category-breadcrumb {

    margin-bottom: 15px;

    font-size: 13px;

    color: #777;

}


.category-breadcrumb a {

    color: #b30000;

}


/* =====================================================
   NEWS GRID
===================================================== */

.category-news-grid {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 24px;

}


/* =====================================================
   PREMIUM CARD
===================================================== */

.category-card {

    background: #fff;

    border: 1px solid #ddd;

    border-radius: 9px;

    overflow: hidden;

    box-shadow:
        0 4px 18px rgba(0,0,0,.07);

    transition:
        transform .25s ease,
        box-shadow .25s ease;

}


.category-card:hover {

    transform:
        translateY(-5px);

    box-shadow:
        0 10px 28px rgba(0,0,0,.13);

}


/* =====================================================
   IMAGE
===================================================== */

.category-card-image {

    position: relative;

    height: 235px;

    overflow: hidden;

    background: #ddd;

}


.category-card-image img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;

    transition:
        transform .5s ease;

}


.category-card:hover
.category-card-image img {

    transform:
        scale(1.06);

}


/* =====================================================
   IMAGE OVERLAY
===================================================== */

.category-card-image::after {

    content: "";

    position: absolute;

    left: 0;

    right: 0;

    bottom: 0;

    height: 40%;

    background:
        linear-gradient(
            transparent,
            rgba(0,0,0,.72)
        );

    pointer-events: none;

}


/* =====================================================
   LOGO
===================================================== */

.category-card-logo {

    position: absolute;

    top: 12px;

    left: 12px;

    z-index: 5;

    background:
        rgba(255,255,255,.96);

    padding: 5px 8px;

    border-radius: 5px;

    box-shadow:
        0 2px 10px rgba(0,0,0,.25);

}


.category-card-logo img {

    width: 130px;

    height: auto;

    max-height: 48px;

    object-fit: contain;

}


/* =====================================================
   CATEGORY BADGE
===================================================== */

.category-card-badge {

    position: absolute;

    bottom: 13px;

    left: 14px;

    z-index: 5;

    background: #b30000;

    color: #fff;

    padding: 5px 10px;

    border-radius: 3px;

    font-size: 12px;

    font-weight: bold;

}


/* =====================================================
   CONTENT
===================================================== */

.category-card-content {

    padding: 18px;

}


.category-card-title {

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 21px;

    line-height: 1.5;

    margin-bottom: 9px;

}


.category-card-title a {

    color: #171717;

}


.category-card-title a:hover {

    color: #b30000;

}


.category-card-headline {

    color: #666;

    font-size: 14px;

    line-height: 1.7;

    margin-bottom: 13px;

}


.category-card-meta {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 8px;

    padding-top: 11px;

    border-top: 1px solid #eee;

    font-size: 12px;

    color: #888;

}


.category-card-reporter {

    color: #555;

    font-weight: bold;

}


.category-card-read {

    display: inline-block;

    margin-top: 12px;

    color: #b30000;

    font-size: 14px;

    font-weight: bold;

}


/* =====================================================
   NO IMAGE
===================================================== */

.category-no-image {

    height: 100%;

    display: flex;

    align-items: center;

    justify-content: center;

    background:
        linear-gradient(
            135deg,
            #222,
            #555
        );

    color: #fff;

    font-size: 17px;

}


/* =====================================================
   NO NEWS
===================================================== */

.category-empty {

    background: #fff;

    border: 1px solid #ddd;

    padding: 70px 20px;

    text-align: center;

    border-radius: 7px;

}


.category-empty h2 {

    font-size: 28px;

    margin-bottom: 8px;

}


.category-empty p {

    color: #777;

}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 950px) {

    .category-news-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }

}


@media (max-width: 600px) {

    .category-page {

        padding: 25px 0 35px;

    }


    .category-header {

        padding: 18px;

    }


    .category-header h1 {

        font-size: 27px;

    }


    .category-news-grid {

        grid-template-columns: 1fr;

        gap: 18px;

    }


    .category-card-image {

        height: 230px;

    }


    .category-card-title {

        font-size: 19px;

    }


    .category-card-logo img {

        width: 110px;

    }

}

</style>

</head>


<body>


<!-- =====================================================
     HEADER
===================================================== -->

<header class="site-header">

    <div class="container header-inner">

        <div class="logo-area">

            <a
                href="index.php"
                class="logo"
            >
                ফুলছড়ি সমাচার
            </a>

            <div class="tagline">

                সত্যের পক্ষে, মানুষের পাশে

            </div>

        </div>

    </div>

</header>


<!-- =====================================================
     NAVBAR
===================================================== -->

<nav class="navbar">

    <div class="container">

        <ul class="nav-menu">

            <li>
                <a href="index.php">
                    হোম
                </a>
            </li>

            <li>
                <a href="category.php?slug=fulchari">
                    ফুলছড়ি
                </a>
            </li>

            <li>
                <a href="category.php?slug=national">
                    জাতীয়
                </a>
            </li>

            <li>
                <a href="category.php?slug=politics">
                    রাজনীতি
                </a>
            </li>

            <li>
                <a href="category.php?slug=corruption">
                    দুর্নীতি
                </a>
            </li>

            <li>
                <a href="category.php?slug=sports">
                    খেলার খবর
                </a>
            </li>

            <li>
                <a href="category.php?slug=entertainment">
                    বিনোদন
                </a>
            </li>

            <li>
                <a href="epaper.php">
                    ই-পেপার
                </a>
            </li>

        </ul>

    </div>

</nav>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="category-page">

<div class="container">


    <!-- BREADCRUMB -->

    <div class="category-breadcrumb">

        <a href="index.php">
            হোম
        </a>

        &nbsp; / &nbsp;

        <?php echo htmlspecialchars(
            $category["name"]
        ); ?>

    </div>


    <!-- CATEGORY HEADER -->

    <div class="category-header">

        <h1>

            <?php echo htmlspecialchars(
                $category["name"]
            ); ?>

        </h1>

        <p>

            <?php echo htmlspecialchars(
                $category["name"]
            ); ?>

            বিভাগের সর্বশেষ সংবাদ

        </p>

    </div>


    <?php if (count($categoryNews) > 0): ?>


        <!-- =================================================
             NEWS GRID
        ================================================= -->

        <div class="category-news-grid">


            <?php foreach (
                $categoryNews
                as $news
            ): ?>


                <article class="category-card">


                    <!-- IMAGE -->

                    <div class="category-card-image">


                        <?php if (!empty($news["image"])): ?>

                            <a
                                href="news.php?slug=<?php
                                    echo urlencode(
                                        $news["slug"]
                                    );
                                ?>"
                            >

                                <img
                                    src="uploads/<?php
                                        echo htmlspecialchars(
                                            $news["image"]
                                        );
                                    ?>"
                                    alt="<?php
                                        echo htmlspecialchars(
                                            $news["title"]
                                        );
                                    ?>"
                                    loading="lazy"
                                >

                            </a>

                        <?php else: ?>

                            <div class="category-no-image">

                                ফুলছড়ি সমাচার

                            </div>

                        <?php endif; ?>


                        <!-- LOGO -->

                        <div class="category-card-logo">

                            <img
                                src="assets/logo.png"
                                alt="ফুলছড়ি সমাচার"
                            >

                        </div>


                        <!-- BADGE -->

                        <div class="category-card-badge">

                            <?php echo htmlspecialchars(
                                $category["name"]
                            ); ?>

                        </div>


                    </div>


                    <!-- CONTENT -->

                    <div class="category-card-content">


                        <h2 class="category-card-title">

                            <a
                                href="news.php?slug=<?php
                                    echo urlencode(
                                        $news["slug"]
                                    );
                                ?>"
                            >

                                <?php echo htmlspecialchars(
                                    $news["title"]
                                ); ?>

                            </a>

                        </h2>


                        <?php if (!empty($news["headline"])): ?>

                            <p class="category-card-headline">

                                <?php echo htmlspecialchars(
                                    mb_substr(
                                        $news["headline"],
                                        0,
                                        160
                                    )
                                ); ?>

                            </p>

                        <?php else: ?>

                            <p class="category-card-headline">

                                <?php echo htmlspecialchars(
                                    mb_substr(
                                        strip_tags(
                                            $news["content"]
                                        ),
                                        0,
                                        160
                                    )
                                ); ?>

                            </p>

                        <?php endif; ?>


                        <div class="category-card-meta">


                            <?php if (!empty($news["reporter"])): ?>

                                <span
                                    class="category-card-reporter"
                                >

                                    রিপোর্ট:
                                    <?php echo htmlspecialchars(
                                        $news["reporter"]
                                    ); ?>

                                </span>

                            <?php else: ?>

                                <span>
                                    <?php echo SITE_NAME; ?>
                                </span>

                            <?php endif; ?>


                            <?php if (!empty($news["published_at"])): ?>

                                <span>

                                    <?php

                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $news["published_at"]
                                        )
                                    );

                                    ?>

                                </span>

                            <?php endif; ?>


                        </div>


                        <a
                            href="news.php?slug=<?php
                                echo urlencode(
                                    $news["slug"]
                                );
                            ?>"
                            class="category-card-read"
                        >

                            বিস্তারিত পড়ুন →

                        </a>


                    </div>


                </article>


            <?php endforeach; ?>


        </div>


    <?php else: ?>


        <!-- =================================================
             EMPTY
        ================================================= -->

        <div class="category-empty">

            <h2>
                এই বিভাগে এখনো কোনো সংবাদ নেই
            </h2>

            <p>
                নতুন সংবাদ Published হলে
                এখানে automatically দেখা যাবে।
            </p>

        </div>


    <?php endif; ?>


</div>

</main>


<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="site-footer">

    <div class="container">

        <p>

            &copy;
            <?php echo date("Y"); ?>

            <?php echo SITE_NAME; ?>.

            সর্বস্বত্ব সংরক্ষিত।

        </p>

    </div>

</footer>


</body>

</html>