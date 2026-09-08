<?php

require_once "config/config.php";
require_once "config/database.php";


// =====================================================
// GET PUBLISHED NEWS
// =====================================================

$stmt = $pdo->prepare("
    SELECT
        news.*,
        categories.name AS category_name,
        categories.slug AS category_slug
    FROM news
    LEFT JOIN categories
        ON news.category_id = categories.id
    WHERE news.status = 'published'
    ORDER BY news.published_at DESC, news.id DESC
    LIMIT 12
");

$stmt->execute();

$latestNews = $stmt->fetchAll();

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
    content="ফুলছড়ি সমাচার - সত্যের পক্ষে, মানুষের পাশে। সর্বশেষ জাতীয়, রাজনীতি, দুর্নীতি, খেলাধুলা ও বিনোদনের সংবাদ।"
>

<title>
    <?php echo SITE_NAME; ?>
</title>

<link
    rel="stylesheet"
  <link rel="stylesheet" href="assets/style.css">
>


<style>

/* =====================================================
   PREMIUM NEWS SECTION
===================================================== */

.premium-news-section {

    padding: 35px 0 50px;

}


/* =====================================================
   SECTION TITLE
===================================================== */

.premium-section-title {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 25px;

    border-bottom: 3px solid #111;

}


.premium-section-title h2 {

    background: #111;

    color: #fff;

    padding: 9px 20px;

    font-size: 23px;

    margin: 0;

}


.premium-section-title a {

    color: #b30000;

    font-weight: bold;

    font-size: 14px;

}


/* =====================================================
   PREMIUM PHOTO CARD GRID
===================================================== */

.premium-news-grid {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 25px;

}


/* =====================================================
   PHOTO CARD
===================================================== */

.premium-photo-card {

    position: relative;

    background: #fff;

    border: 1px solid #ddd;

    border-radius: 10px;

    overflow: hidden;

    box-shadow:
        0 5px 20px rgba(0,0,0,.08);

    transition:
        transform .25s ease,
        box-shadow .25s ease;

}


.premium-photo-card:hover {

    transform:
        translateY(-6px);

    box-shadow:
        0 12px 30px rgba(0,0,0,.15);

}


/* =====================================================
   IMAGE AREA
===================================================== */

.photo-card-image {

    position: relative;

    width: 100%;

    height: 260px;

    overflow: hidden;

    background: #ddd;

}


.photo-card-image img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;

    transition:
        transform .5s ease;

}


.premium-photo-card:hover
.photo-card-image img {

    transform:
        scale(1.06);

}


/* =====================================================
   DARK IMAGE OVERLAY
===================================================== */

.photo-card-image::after {

    content: "";

    position: absolute;

    left: 0;

    right: 0;

    bottom: 0;

    height: 45%;

    background:
        linear-gradient(
            transparent,
            rgba(0,0,0,.72)
        );

    pointer-events: none;

}


/* =====================================================
   LOGO ON PHOTO
===================================================== */

.photo-card-logo {

    position: absolute;

    top: 12px;

    left: 12px;

    z-index: 5;

    background: rgba(255,255,255,.95);

    padding: 6px 9px;

    border-radius: 5px;

    box-shadow:
        0 2px 10px rgba(0,0,0,.25);

}


.photo-card-logo img {

    width: 145px;

    height: auto;

    max-height: 55px;

    object-fit: contain;

    display: block;

}


/* =====================================================
   CATEGORY BADGE
===================================================== */

.photo-card-category {

    position: absolute;

    left: 15px;

    bottom: 15px;

    z-index: 5;

    background: #b30000;

    color: #fff;

    padding: 5px 11px;

    border-radius: 3px;

    font-size: 12px;

    font-weight: bold;

}


/* =====================================================
   CARD CONTENT
===================================================== */

.photo-card-content {

    padding: 19px;

}


/* =====================================================
   TITLE
===================================================== */

.photo-card-title {

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 21px;

    line-height: 1.5;

    margin: 0 0 10px;

}


.photo-card-title a {

    color: #161616;

}


.photo-card-title a:hover {

    color: #b30000;

}


/* =====================================================
   HEADLINE
===================================================== */

.photo-card-headline {

    color: #666;

    font-size: 14px;

    line-height: 1.7;

    margin-bottom: 13px;

}


/* =====================================================
   META
===================================================== */

.photo-card-meta {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 8px;

    padding-top: 12px;

    border-top: 1px solid #eee;

    color: #888;

    font-size: 12px;

}


.photo-card-reporter {

    color: #555;

    font-weight: bold;

}


/* =====================================================
   READ MORE
===================================================== */

.photo-card-read {

    display: inline-block;

    margin-top: 13px;

    color: #b30000;

    font-size: 14px;

    font-weight: bold;

}


.photo-card-read:hover {

    color: #800000;

}


/* =====================================================
   NO IMAGE
===================================================== */

.photo-card-no-image {

    width: 100%;

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

    font-size: 18px;

}


/* =====================================================
   FEATURED FIRST CARD
===================================================== */

.premium-photo-card:first-child {

    grid-column: span 2;

}


.premium-photo-card:first-child
.photo-card-image {

    height: 350px;

}


.premium-photo-card:first-child
.photo-card-title {

    font-size: 28px;

}


/* =====================================================
   WELCOME BOX
===================================================== */

.premium-welcome {

    background: #fff;

    border: 1px solid #ddd;

    border-radius: 8px;

    text-align: center;

    padding: 60px 25px;

}


.premium-welcome h1 {

    margin-bottom: 10px;

}


.premium-welcome p {

    color: #777;

}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 900px) {

    .premium-news-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }


    .premium-photo-card:first-child {

        grid-column: span 2;

    }

}


@media (max-width: 600px) {

    .premium-news-section {

        padding: 25px 0 35px;

    }


    .premium-section-title h2 {

        font-size: 19px;

        padding: 8px 14px;

    }


    .premium-news-grid {

        grid-template-columns: 1fr;

        gap: 18px;

    }


    .premium-photo-card:first-child {

        grid-column: span 1;

    }


    .photo-card-image,
    .premium-photo-card:first-child
    .photo-card-image {

        height: 230px;

    }


    .premium-photo-card:first-child
    .photo-card-title {

        font-size: 22px;

    }


    .photo-card-title {

        font-size: 19px;

    }


    .photo-card-logo img {

        width: 115px;

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

<main class="main-content">

<div class="container">


<section class="premium-news-section">


    <!-- =================================================
         SECTION HEADER
    ================================================= -->

    <div class="premium-section-title">

        <h2>
            সর্বশেষ সংবাদ
        </h2>

        <a href="search.php">
            সব সংবাদ →
        </a>

    </div>


    <?php if (count($latestNews) > 0): ?>


        <!-- =================================================
             PREMIUM NEWS GRID
        ================================================= -->

        <div class="premium-news-grid">


            <?php foreach ($latestNews as $index => $news): ?>


                <article class="premium-photo-card">


                    <!-- =====================================
                         IMAGE
                    ====================================== -->

                    <div class="photo-card-image">


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
                                    loading="<?php
                                        echo $index < 3
                                            ? "eager"
                                            : "lazy";
                                    ?>"
                                >

                            </a>

                        <?php else: ?>

                            <div class="photo-card-no-image">

                                ফুলছড়ি সমাচার

                            </div>

                        <?php endif; ?>


                        <!-- =================================
                             PREMIUM LOGO
                        ================================== -->

                        <div class="photo-card-logo">

                            <img
                                src="assest/logo.png"
                                alt="ফুলছড়ি সমাচার"
                            >

                        </div>


                        <!-- =================================
                             CATEGORY
                        ================================== -->

                        <?php if (!empty($news["category_name"])): ?>

                            <a
                                href="category.php?slug=<?php
                                    echo urlencode(
                                        $news["category_slug"]
                                    );
                                ?>"
                                class="photo-card-category"
                            >

                                <?php echo htmlspecialchars(
                                    $news["category_name"]
                                ); ?>

                            </a>

                        <?php endif; ?>


                    </div>


                    <!-- =====================================
                         CARD CONTENT
                    ====================================== -->

                    <div class="photo-card-content">


                        <!-- TITLE -->

                        <h3 class="photo-card-title">

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

                        </h3>


                        <!-- HEADLINE -->

                        <?php if (!empty($news["headline"])): ?>

                            <p class="photo-card-headline">

                                <?php

                                echo htmlspecialchars(
                                    mb_substr(
                                        $news["headline"],
                                        0,
                                        150
                                    )
                                );

                                ?>

                            </p>

                        <?php endif; ?>


                        <!-- META -->

                        <div class="photo-card-meta">


                            <?php if (!empty($news["reporter"])): ?>

                                <span class="photo-card-reporter">

                                    রিপোর্ট:
                                    <?php echo htmlspecialchars(
                                        $news["reporter"]
                                    ); ?>

                                </span>

                            <?php else: ?>

                                <span>
                                    ফুলছড়ি সমাচার
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


                        <!-- READ MORE -->

                        <a
                            href="news.php?slug=<?php
                                echo urlencode(
                                    $news["slug"]
                                );
                            ?>"
                            class="photo-card-read"
                        >

                            বিস্তারিত পড়ুন →

                        </a>


                    </div>


                </article>


            <?php endforeach; ?>


        </div>


    <?php else: ?>


        <!-- =================================================
             NO NEWS
        ================================================== -->

        <div class="premium-welcome">

            <h1>
                এখনো কোনো সংবাদ প্রকাশিত হয়নি
            </h1>

            <p>
                Admin Panel থেকে সংবাদ Publish করলে
                এখানে Automatic Premium Photo Card তৈরি হবে।
            </p>

        </div>


    <?php endif; ?>


</section>


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