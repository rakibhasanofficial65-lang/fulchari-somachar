<?php

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/includes/functions.php";


// =====================================================
// GET CATEGORY SLUG
// =====================================================

$slug = trim($_GET["slug"] ?? "");


// =====================================================
// INVALID SLUG
// =====================================================

if ($slug === "") {

    redirect(site_url());

}


// =====================================================
// GET CATEGORY
// =====================================================

$category = get_category_by_slug(
    $pdo,
    $slug
);


// =====================================================
// CATEGORY NOT FOUND
// =====================================================

if (!$category) {

    http_response_code(404);

    $pageTitle =
        "বিভাগ পাওয়া যায়নি - " . SITE_NAME;

    $pageDescription =
        "আপনি যে সংবাদ বিভাগটি খুঁজছেন সেটি পাওয়া যায়নি।";

    $canonicalUrl =
        site_url("404");

    $ogImage =
        site_url("assets/logo.png");

    require_once __DIR__ . "/includes/header.php";
    require_once __DIR__ . "/includes/navbar.php";

    ?>

    <main class="category-page">

        <div class="container">

            <div class="category-empty">

                <h1>
                    বিভাগ পাওয়া যায়নি
                </h1>

                <p>
                    আপনি যে সংবাদ বিভাগটি খুঁজছেন
                    সেটি পাওয়া যায়নি।
                </p>

                <a
                    href="<?php echo e(site_url()); ?>"
                    class="category-back-button"
                >
                    হোম পেজে ফিরে যান
                </a>

            </div>

        </div>

    </main>

    <?php

    require_once __DIR__ . "/includes/footer.php";

    exit;
}


// =====================================================
// GET CATEGORY NEWS
// =====================================================

$categoryNews = get_news_by_category(
    $pdo,
    $category["slug"],
    30
);


// =====================================================
// SEO
// =====================================================

$pageTitle =
    $category["name"] .
    " — সর্বশেষ সংবাদ | " .
    SITE_NAME;


$pageDescription =
    $category["name"] .
    " বিভাগের সর্বশেষ সংবাদ। " .
    SITE_NAME .
    " এ পড়ুন সর্বশেষ খবর, প্রতিবেদন ও সংবাদ।";


$canonicalUrl =
    category_url($category["slug"]);


$ogImage =
    site_url("assets/logo.png");


// =====================================================
// HEADER
// =====================================================

require_once __DIR__ . "/includes/header.php";


// =====================================================
// NAVBAR
// =====================================================

require_once __DIR__ . "/includes/navbar.php";

?>

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
   BREADCRUMB
===================================================== */

.category-breadcrumb {

    margin-bottom: 15px;

    font-size: 13px;

    color: #777;

}


.category-breadcrumb a {

    color: #b30000;

    text-decoration: none;

}


.category-breadcrumb a:hover {

    text-decoration: underline;

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

    line-height: 1.4;

    color: #111;

    margin: 0 0 5px;

}


.category-header p {

    color: #777;

    font-size: 14px;

    margin: 0;

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

    display: block;

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


/* =====================================================
   TITLE
===================================================== */

.category-card-title {

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 21px;

    line-height: 1.5;

    margin: 0 0 9px;

}


.category-card-title a {

    color: #171717;

    text-decoration: none;

}


.category-card-title a:hover {

    color: #b30000;

}


/* =====================================================
   HEADLINE
===================================================== */

.category-card-headline {

    color: #666;

    font-size: 14px;

    line-height: 1.7;

    margin: 0 0 13px;

}


/* =====================================================
   META
===================================================== */

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


/* =====================================================
   READ MORE
===================================================== */

.category-card-read {

    display: inline-block;

    margin-top: 12px;

    color: #b30000;

    font-size: 14px;

    font-weight: bold;

    text-decoration: none;

}


.category-card-read:hover {

    color: #800000;

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
   EMPTY
===================================================== */

.category-empty {

    background: #fff;

    border: 1px solid #ddd;

    padding: 70px 20px;

    text-align: center;

    border-radius: 7px;

}


.category-empty h1,
.category-empty h2 {

    font-size: 28px;

    margin: 0 0 10px;

}


.category-empty p {

    color: #777;

    line-height: 1.8;

    margin: 0;

}


/* =====================================================
   BACK BUTTON
===================================================== */

.category-back-button {

    display: inline-block;

    margin-top: 20px;

    padding: 11px 22px;

    background: #b30000;

    color: #fff;

    text-decoration: none;

    border-radius: 5px;

    font-weight: bold;

}


.category-back-button:hover {

    background: #800000;

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


    .category-card-meta {

        align-items: flex-start;

        flex-direction: column;

    }


    .category-empty {

        padding: 50px 18px;

    }


    .category-empty h1,
    .category-empty h2 {

        font-size: 23px;

    }

}

</style>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="category-page">

    <div class="container">


        <!-- =================================================
             BREADCRUMB
        ================================================= -->

        <div class="category-breadcrumb">

            <a href="<?php echo e(site_url()); ?>">
                হোম
            </a>

            &nbsp; / &nbsp;

            <?php echo e(
                $category["name"]
            ); ?>

        </div>


        <!-- =================================================
             CATEGORY HEADER
        ================================================= -->

        <div class="category-header">

            <h1>

                <?php echo e(
                    $category["name"]
                ); ?>

            </h1>

            <p>

                <?php echo e(
                    $category["name"]
                ); ?>

                বিভাগের সর্বশেষ সংবাদ

            </p>

        </div>


        <?php if (!empty($categoryNews)): ?>


            <!-- =================================================
                 NEWS GRID
            ================================================= -->

            <div class="category-news-grid">


                <?php foreach (
                    $categoryNews
                    as $index => $news
                ): ?>


                    <article class="category-card">


                        <!-- =====================================
                             IMAGE
                        ====================================== -->

                        <div class="category-card-image">


                            <?php if (!empty($news["image"])): ?>

                                <a
                                    href="<?php echo e(
                                        news_url($news["slug"])
                                    ); ?>"
                                    aria-label="<?php echo e(
                                        $news["title"]
                                    ); ?>"
                                >

                                    <img
                                        src="<?php echo e(
                                            image_url(
                                                $news["image"]
                                            )
                                        ); ?>"
                                        alt="<?php echo e(
                                            $news["title"]
                                        ); ?>"
                                        loading="<?php
                                            echo $index < 3
                                                ? "eager"
                                                : "lazy";
                                        ?>"
                                        decoding="async"
                                    >

                                </a>

                            <?php else: ?>

                                <div class="category-no-image">

                                    ফুলছড়ি সমাচার

                                </div>

                            <?php endif; ?>


                            <!-- =================================
                                 LOGO
                            ================================== -->

                            <div class="category-card-logo">

                                <img
                                    src="<?php echo e(
                                        site_url(
                                            "assets/logo.png"
                                        )
                                    ); ?>"
                                    alt="<?php echo e(
                                        SITE_NAME
                                    ); ?>"
                                >

                            </div>


                            <!-- =================================
                                 BADGE
                            ================================== -->

                            <div class="category-card-badge">

                                <?php echo e(
                                    $category["name"]
                                ); ?>

                            </div>


                        </div>


                        <!-- =====================================
                             CONTENT
                        ====================================== -->

                        <div class="category-card-content">


                            <!-- TITLE -->

                            <h2 class="category-card-title">

                                <a
                                    href="<?php echo e(
                                        news_url(
                                            $news["slug"]
                                        )
                                    ); ?>"
                                >

                                    <?php echo e(
                                        $news["title"]
                                    ); ?>

                                </a>

                            </h2>


                            <!-- HEADLINE -->

                            <?php

                            $cardText = "";

                            if (!empty($news["headline"])) {

                                $cardText =
                                    $news["headline"];

                            } elseif (
                                !empty($news["content"])
                            ) {

                                $cardText =
                                    strip_tags(
                                        $news["content"]
                                    );

                            }

                            ?>

                            <?php if (
                                !empty($cardText)
                            ): ?>

                                <p class="category-card-headline">

                                    <?php echo e(
                                        news_excerpt(
                                            $cardText,
                                            160
                                        )
                                    ); ?>

                                </p>

                            <?php endif; ?>


                            <!-- META -->

                            <div class="category-card-meta">


                                <?php if (
                                    !empty($news["reporter"])
                                ): ?>

                                    <span
                                        class="category-card-reporter"
                                    >

                                        রিপোর্ট:
                                        <?php echo e(
                                            $news["reporter"]
                                        ); ?>

                                    </span>

                                <?php else: ?>

                                    <span>
                                        <?php echo e(
                                            SITE_NAME
                                        ); ?>
                                    </span>

                                <?php endif; ?>


                                <?php if (
                                    !empty(
                                        $news["published_at"]
                                    )
                                ): ?>

                                    <span>

                                        <?php echo e(
                                            format_date_bn(
                                                $news[
                                                    "published_at"
                                                ]
                                            )
                                        ); ?>

                                    </span>

                                <?php endif; ?>


                            </div>


                            <!-- READ MORE -->

                            <a
                                href="<?php echo e(
                                    news_url(
                                        $news["slug"]
                                    )
                                ); ?>"
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
                 EMPTY STATE
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


<?php

// =====================================================
// FOOTER
// =====================================================

require_once __DIR__ . "/includes/footer.php";

?>