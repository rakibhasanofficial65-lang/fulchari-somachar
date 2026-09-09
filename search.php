<?php

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/includes/functions.php";


// =====================================================
// SEARCH QUERY
// =====================================================

$search = trim($_GET["q"] ?? "");

$results = [];


// =====================================================
// SEARCH NEWS
// =====================================================

if ($search !== "") {

    $keyword = "%" . $search . "%";

    $stmt = $pdo->prepare("
        SELECT
            news.*,
            categories.name AS category_name,
            categories.slug AS category_slug
        FROM news
        LEFT JOIN categories
            ON news.category_id = categories.id
        WHERE news.status = 'published'
          AND (
                news.title LIKE ?
                OR news.headline LIKE ?
                OR news.content LIKE ?
                OR news.reporter LIKE ?
          )
        ORDER BY
            news.published_at DESC,
            news.id DESC
        LIMIT 50
    ");

    $stmt->execute([
        $keyword,
        $keyword,
        $keyword,
        $keyword
    ]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// =====================================================
// SEO
// =====================================================

if ($search !== "") {

    $pageTitle =
        "“" . $search . "” — সংবাদ অনুসন্ধান | " .
        SITE_NAME;

    $pageDescription =
        SITE_NAME .
        " এ “" .
        $search .
        "” সম্পর্কিত সর্বশেষ প্রকাশিত সংবাদ অনুসন্ধান করুন।";

} else {

    $pageTitle =
        "সংবাদ অনুসন্ধান | " . SITE_NAME;

    $pageDescription =
        SITE_NAME .
        " এ সর্বশেষ সংবাদ অনুসন্ধান করুন।";
}

$canonicalUrl = site_url("search.php");

$ogImage = site_url("assets/logo.png");


// =====================================================
// HEADER
// =====================================================

require_once __DIR__ . "/includes/header.php";

?>

<style>

/* =====================================================
   SEARCH PAGE
===================================================== */

.search-page {

    background: #f4f4f4;

    min-height: 100vh;

    padding: 35px 0 60px;

}


/* =====================================================
   SEARCH HEADER
===================================================== */

.search-box {

    background: #fff;

    border: 1px solid #ddd;

    border-left: 5px solid #b30000;

    padding: 24px;

    margin-bottom: 25px;

    box-shadow:
        0 4px 18px rgba(0, 0, 0, .06);

}


.search-box h1 {

    margin: 0 0 17px;

    color: #111;

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 30px;

    line-height: 1.4;

}


/* =====================================================
   SEARCH FORM
===================================================== */

.search-form {

    display: flex;

    gap: 10px;

}


.search-input {

    flex: 1;

    min-width: 0;

    height: 48px;

    padding: 0 15px;

    border: 1px solid #bbb;

    border-radius: 5px;

    background: #fff;

    color: #222;

    font-size: 16px;

    outline: none;

}


.search-input:focus {

    border-color: #b30000;

    box-shadow:
        0 0 0 2px rgba(179, 0, 0, .08);

}


.search-button {

    min-width: 110px;

    height: 48px;

    padding: 0 20px;

    border: 0;

    border-radius: 5px;

    background: #b30000;

    color: #fff;

    font-size: 15px;

    font-weight: 700;

    cursor: pointer;

}


.search-button:hover {

    background: #850000;

}


/* =====================================================
   RESULT INFO
===================================================== */

.search-result-info {

    margin-bottom: 18px;

    color: #555;

    font-size: 15px;

    line-height: 1.7;

}


.search-result-info strong {

    color: #b30000;

}


/* =====================================================
   RESULT GRID
===================================================== */

.search-grid {

    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 22px;

}


/* =====================================================
   SEARCH CARD
===================================================== */

.search-card {

    background: #fff;

    border: 1px solid #ddd;

    border-radius: 8px;

    overflow: hidden;

    box-shadow:
        0 4px 16px rgba(0, 0, 0, .07);

    transition:
        transform .2s ease,
        box-shadow .2s ease;

}


.search-card:hover {

    transform:
        translateY(-4px);

    box-shadow:
        0 9px 25px rgba(0, 0, 0, .13);

}


/* =====================================================
   IMAGE AREA
===================================================== */

.search-card-image {

    position: relative;

    height: 220px;

    overflow: hidden;

    background: #ddd;

}


.search-card-image > a {

    display: block;

    width: 100%;

    height: 100%;

}


.search-card-image > a > img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;

    transition:
        transform .4s ease;

}


.search-card:hover
.search-card-image > a > img {

    transform:
        scale(1.05);

}


/* =====================================================
   IMAGE OVERLAY
===================================================== */

.search-card-image::after {

    content: "";

    position: absolute;

    left: 0;

    right: 0;

    bottom: 0;

    height: 38%;

    background:
        linear-gradient(
            transparent,
            rgba(0, 0, 0, .70)
        );

    pointer-events: none;

}


/* =====================================================
   LOGO
===================================================== */

.search-card-logo {

    position: absolute;

    top: 11px;

    left: 11px;

    z-index: 5;

    background:
        rgba(255, 255, 255, .96);

    padding: 5px 8px;

    border-radius: 5px;

    box-shadow:
        0 2px 10px rgba(0, 0, 0, .20);

}


.search-card-logo img {

    width: 115px;

    height: auto;

    max-height: 45px;

    object-fit: contain;

    display: block;

}


/* =====================================================
   CATEGORY BADGE
===================================================== */

.search-card-category {

    position: absolute;

    bottom: 12px;

    left: 12px;

    z-index: 5;

    background: #b30000;

    color: #fff;

    padding: 5px 10px;

    border-radius: 3px;

    font-size: 12px;

    font-weight: 700;

}


/* =====================================================
   NO IMAGE
===================================================== */

.search-no-image {

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

    font-weight: 700;

}


/* =====================================================
   CARD CONTENT
===================================================== */

.search-card-content {

    padding: 17px;

}


/* =====================================================
   TITLE
===================================================== */

.search-card-title {

    margin: 0 0 9px;

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 20px;

    line-height: 1.5;

}


.search-card-title a {

    color: #222;

    text-decoration: none;

}


.search-card-title a:hover {

    color: #b30000;

}


/* =====================================================
   HEADLINE
===================================================== */

.search-card-headline {

    margin: 0 0 12px;

    color: #666;

    font-size: 14px;

    line-height: 1.75;

}


/* =====================================================
   META
===================================================== */

.search-card-meta {

    display: flex;

    justify-content: space-between;

    align-items: flex-start;

    gap: 8px;

    padding-top: 10px;

    border-top: 1px solid #eee;

    color: #888;

    font-size: 12px;

    line-height: 1.6;

}


.search-card-reporter {

    color: #555;

    font-weight: 700;

}


/* =====================================================
   READ MORE
===================================================== */

.search-card-read {

    display: inline-block;

    margin-top: 12px;

    color: #b30000;

    font-size: 14px;

    font-weight: 700;

    text-decoration: none;

}


.search-card-read:hover {

    color: #850000;

}


/* =====================================================
   EMPTY STATE
===================================================== */

.search-empty {

    background: #fff;

    border: 1px solid #ddd;

    border-radius: 8px;

    padding: 70px 20px;

    text-align: center;

}


.search-empty h2 {

    margin: 0 0 10px;

    color: #222;

    font-size: 26px;

}


.search-empty p {

    margin: 0;

    color: #777;

    font-size: 15px;

    line-height: 1.8;

}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 950px) {

    .search-grid {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

    }

}


@media (max-width: 600px) {

    .search-page {

        padding: 25px 0 40px;

    }


    .search-box {

        padding: 18px;

    }


    .search-box h1 {

        font-size: 25px;

    }


    .search-form {

        flex-direction: column;

    }


    .search-button {

        width: 100%;

    }


    .search-grid {

        grid-template-columns: 1fr;

        gap: 18px;

    }


    .search-card-image {

        height: 220px;

    }


    .search-card-meta {

        flex-direction: column;

    }


    .search-empty {

        padding: 50px 18px;

    }


    .search-empty h2 {

        font-size: 23px;

    }

}

</style>

<!-- =====================================================
     MAIN
===================================================== -->

<main class="search-page">

```
<div class="container">


    <!-- =================================================
         SEARCH BOX
    ================================================= -->

    <section class="search-box">

        <h1>
            সংবাদ অনুসন্ধান
        </h1>


        <form
            method="GET"
            action="<?php echo e(site_url("search.php")); ?>"
            class="search-form"
        >

            <input
                type="text"
                name="q"
                class="search-input"
                value="<?php echo e($search); ?>"
                placeholder="সংবাদের শিরোনাম, বিষয় বা প্রতিবেদকের নাম লিখুন..."
                autocomplete="off"
                aria-label="সংবাদ অনুসন্ধান"
            >


            <button
                type="submit"
                class="search-button"
            >
                🔍 খুঁজুন
            </button>

        </form>

    </section>


    <?php if ($search !== ""): ?>


        <!-- =================================================
             RESULT INFO
        ================================================= -->

        <div class="search-result-info">

            <strong>
                “<?php echo e($search); ?>”
            </strong>

            এর জন্য

            <strong>
                <?php echo bn_number(count($results)); ?>
            </strong>

            টি সংবাদ পাওয়া গেছে।

        </div>


        <?php if (!empty($results)): ?>


            <!-- =================================================
                 RESULT GRID
            ================================================= -->

            <div class="search-grid">


                <?php foreach ($results as $index => $news): ?>

                    <?php

                    $cardText = "";

                    if (!empty($news["headline"])) {

                        $cardText =
                            $news["headline"];

                    } elseif (!empty($news["content"])) {

                        $cardText =
                            strip_tags(
                                $news["content"]
                            );
                    }

                    ?>


                    <article class="search-card">


                        <!-- =====================================
                             IMAGE
                        ====================================== -->

                        <div class="search-card-image">


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

                                <div class="search-no-image">

                                    <?php echo e(SITE_NAME); ?>

                                </div>

                            <?php endif; ?>


                            <!-- LOGO -->

                            <div class="search-card-logo">

                                <img
                                    src="<?php echo e(
                                        site_url(
                                            "assets/logo.png"
                                        )
                                    ); ?>"
                                    alt="<?php echo e(
                                        SITE_NAME
                                    ); ?>"
                                    loading="lazy"
                                >

                            </div>


                            <!-- CATEGORY -->

                            <?php if (
                                !empty(
                                    $news["category_name"]
                                )
                            ): ?>

                                <div
                                    class="search-card-category"
                                >

                                    <?php echo e(
                                        $news["category_name"]
                                    ); ?>

                                </div>

                            <?php endif; ?>


                        </div>


                        <!-- =====================================
                             CONTENT
                        ====================================== -->

                        <div class="search-card-content">


                            <h2 class="search-card-title">

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


                            <?php if ($cardText !== ""): ?>

                                <p
                                    class="search-card-headline"
                                >

                                    <?php echo e(
                                        news_excerpt(
                                            $cardText,
                                            150
                                        )
                                    ); ?>

                                </p>

                            <?php endif; ?>


                            <!-- META -->

                            <div class="search-card-meta">


                                <?php if (
                                    !empty(
                                        $news["reporter"]
                                    )
                                ): ?>

                                    <span
                                        class="search-card-reporter"
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
                                class="search-card-read"
                            >

                                বিস্তারিত পড়ুন →

                            </a>


                        </div>


                    </article>


                <?php endforeach; ?>


            </div>


        <?php else: ?>


            <!-- =================================================
                 NO RESULT
            ================================================= -->

            <div class="search-empty">

                <h2>
                    কোনো সংবাদ পাওয়া যায়নি
                </h2>

                <p>
                    অন্য কোনো শব্দ দিয়ে আবার অনুসন্ধান করুন।
                </p>

            </div>


        <?php endif; ?>


    <?php else: ?>


        <!-- =================================================
             INITIAL STATE
        ================================================= -->

        <div class="search-empty">

            <h2>
                সংবাদ খুঁজুন
            </h2>

            <p>
                উপরের search box-এ কোনো শব্দ লিখে
                সংবাদ অনুসন্ধান করুন।
            </p>

        </div>


    <?php endif; ?>


</div>
```

</main>

<?php

// =====================================================
// FOOTER
// =====================================================

require_once __DIR__ . "/includes/footer.php";

?>
