<?php

require_once "config/config.php";
require_once "config/database.php";


// =====================================================
// SEARCH
// =====================================================

$search = trim($_GET["q"] ?? "");

$results = [];

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
        ORDER BY news.published_at DESC, news.id DESC
        LIMIT 50
    ");

    $stmt->execute([
        $keyword,
        $keyword,
        $keyword,
        $keyword
    ]);

    $results = $stmt->fetchAll();
}

?>

<!DOCTYPE html>

<html lang="bn">

<head>

<meta charset="UTF-8">

<base href="<?php echo SITE_URL; ?>/">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<meta
    name="description"
    content="ফুলছড়ি সমাচার সংবাদ অনুসন্ধান"
>

<title>
    সংবাদ অনুসন্ধান | <?php echo SITE_NAME; ?>
</title>

<link
    rel="stylesheet"
    href="<?php echo SITE_URL; ?>/assets/style.css"
>


<style>

/* =====================================================
   SEARCH PAGE
===================================================== */

.search-page {

    min-height: 650px;

    background: #f4f4f4;

    padding: 35px 0 55px;

}


/* =====================================================
   SEARCH BOX
===================================================== */

.search-box {

    background: #fff;

    border: 1px solid #ddd;

    padding: 25px;

    margin-bottom: 25px;

}


.search-box h1 {

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 30px;

    margin-bottom: 17px;

}


.search-form {

    display: flex;

    gap: 10px;

}


.search-input {

    flex: 1;

    padding: 13px 15px;

    border: 1px solid #bbb;

    border-radius: 5px;

    font-size: 16px;

    outline: none;

}


.search-input:focus {

    border-color: #b30000;

}


.search-button {

    border: 0;

    background: #b30000;

    color: #fff;

    padding: 0 25px;

    border-radius: 5px;

    font-size: 16px;

    font-weight: bold;

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

    font-size: 16px;

    color: #555;

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
        repeat(3, 1fr);

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
        0 4px 16px rgba(0,0,0,.07);

    transition:
        transform .2s ease,
        box-shadow .2s ease;

}


.search-card:hover {

    transform:
        translateY(-4px);

    box-shadow:
        0 9px 25px rgba(0,0,0,.13);

}


/* =====================================================
   IMAGE
===================================================== */

.search-card-image {

    position: relative;

    height: 220px;

    overflow: hidden;

    background: #ddd;

}


.search-card-image img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;

    transition:
        transform .4s ease;

}


.search-card:hover
.search-card-image img {

    transform:
        scale(1.05);

}


/* =====================================================
   LOGO
===================================================== */

.search-card-logo {

    position: absolute;

    top: 11px;

    left: 11px;

    z-index: 3;

    background: rgba(255,255,255,.95);

    padding: 5px 8px;

    border-radius: 5px;

}


.search-card-logo img {

    width: 115px;

    height: auto;

    display: block;

}


/* =====================================================
   CATEGORY
===================================================== */

.search-card-category {

    position: absolute;

    bottom: 12px;

    left: 12px;

    z-index: 3;

    background: #b30000;

    color: #fff;

    padding: 4px 9px;

    border-radius: 3px;

    font-size: 12px;

    font-weight: bold;

}


/* =====================================================
   CONTENT
===================================================== */

.search-card-content {

    padding: 17px;

}


.search-card-title {

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 20px;

    line-height: 1.5;

    margin-bottom: 8px;

}


.search-card-title a {

    color: #222;

    text-decoration: none;

}


.search-card-title a:hover {

    color: #b30000;

}


.search-card-headline {

    font-size: 14px;

    color: #666;

    line-height: 1.7;

    margin-bottom: 12px;

}


.search-card-meta {

    padding-top: 10px;

    border-top: 1px solid #eee;

    display: flex;

    justify-content: space-between;

    gap: 8px;

    color: #888;

    font-size: 12px;

}


.search-card-read {

    display: inline-block;

    margin-top: 12px;

    color: #b30000;

    font-weight: bold;

    font-size: 14px;

    text-decoration: none;

}


.search-card-read:hover {

    color: #850000;

}


/* =====================================================
   NO IMAGE
===================================================== */

.search-no-image {

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

    font-weight: bold;

}


/* =====================================================
   EMPTY
===================================================== */

.search-empty {

    background: #fff;

    border: 1px solid #ddd;

    text-align: center;

    padding: 70px 20px;

}


.search-empty h2 {

    margin-bottom: 8px;

}


.search-empty p {

    color: #777;

}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 950px) {

    .search-grid {

        grid-template-columns:
            repeat(2, 1fr);

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

        padding: 12px;

    }


    .search-grid {

        grid-template-columns: 1fr;

        gap: 18px;

    }


    .search-card-image {

        height: 220px;

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
                href="<?php echo SITE_URL; ?>/index.php"
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
                <a href="<?php echo SITE_URL; ?>/category/index">
                    হোম
                </a>
            </li>

            <li>
                <a href="<?php echo SITE_URL; ?>/category/fulchari">
                    ফুলছড়ি
                </a>
            </li>


            <li>
                <a href="<?php echo SITE_URL; ?>/category/national">
                    জাতীয়
                </a>
            </li>


            <li>
                <a href="<?php echo SITE_URL; ?>/category/politics">
                    রাজনীতি
                </a>
            </li>


            <li>
                <a href="<?php echo SITE_URL; ?>/category/corruption">
                    দুর্নীতি
                </a>
            </li>


            <li>
                <a href="<?php echo SITE_URL; ?>/category/sports">
                    খেলার খবর
                </a>
            </li>


            <li>
                <a href="<?php echo SITE_URL; ?>/category/entertainment">
                    বিনোদন
                </a>
            </li>


            <li>
                <a href="<?php echo SITE_URL; ?>/category/epaper">
                    ই-পেপার
                </a>
            </li>

        </ul>

    </div>

</nav>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="search-page">

<div class="container">


    <!-- SEARCH BOX -->

    <section class="search-box">

        <h1>
            সংবাদ অনুসন্ধান
        </h1>


        <form
            method="GET"
            action="<?php echo SITE_URL; ?>/search.php"
            class="search-form"
        >

            <input
                type="text"
                name="q"
                class="search-input"
                value="<?php
                    echo htmlspecialchars(
                        $search,
                        ENT_QUOTES,
                        "UTF-8"
                    );
                ?>"
                placeholder="সংবাদের শিরোনাম লিখুন..."
                autocomplete="off"
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


        <div class="search-result-info">

            <strong>
                "<?php
                echo htmlspecialchars(
                    $search,
                    ENT_QUOTES,
                    "UTF-8"
                );
                ?>"
            </strong>

            এর জন্য

            <strong>
                <?php echo count($results); ?>
            </strong>

            টি সংবাদ পাওয়া গেছে।

        </div>


        <?php if (count($results) > 0): ?>


            <div class="search-grid">


                <?php foreach ($results as $news): ?>


                    <article class="search-card">


                        <!-- IMAGE -->

                        <div class="search-card-image">


                            <?php if (!empty($news["image"])): ?>

                                <a
                                    href="<?php echo SITE_URL; ?>/news/<?php echo urlencode($news["slug"]); ?>"
                                >

                                    <img
                                        src="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($news["image"], ENT_QUOTES, "UTF-8"); ?>"
                                        alt="<?php echo htmlspecialchars($news["title"], ENT_QUOTES, "UTF-8"); ?>"
                                        loading="lazy"
                                    >

                                </a>

                            <?php else: ?>

                                <div class="search-no-image">

                                    ফুলছড়ি সমাচার

                                </div>

                            <?php endif; ?>


                            <!-- LOGO -->

                            <div class="search-card-logo">

                                <img
                                    src="<?php echo SITE_URL; ?>/assets/logo.png"
                                    alt="ফুলছড়ি সমাচার"
                                >

                            </div>


                            <!-- CATEGORY -->

                            <?php if (!empty($news["category_name"])): ?>

                                <div class="search-card-category">

                                    <?php
                                    echo htmlspecialchars(
                                        $news["category_name"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );
                                    ?>

                                </div>

                            <?php endif; ?>


                        </div>


                        <!-- CONTENT -->

                        <div class="search-card-content">


                            <h2 class="search-card-title">

                                <a
                                    href="<?php echo SITE_URL; ?>/news/<?php echo urlencode($news["slug"]); ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $news["title"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );
                                    ?>

                                </a>

                            </h2>


                            <?php if (!empty($news["headline"])): ?>

                                <p class="search-card-headline">

                                    <?php
                                    echo htmlspecialchars(
                                        mb_substr(
                                            $news["headline"],
                                            0,
                                            150
                                        ),
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );
                                    ?>

                                </p>

                            <?php else: ?>

                                <p class="search-card-headline">

                                    <?php
                                    echo htmlspecialchars(
                                        mb_substr(
                                            strip_tags(
                                                $news["content"]
                                            ),
                                            0,
                                            150
                                        ),
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );
                                    ?>

                                </p>

                            <?php endif; ?>


                            <div class="search-card-meta">


                                <span>

                                    <?php if (!empty($news["reporter"])): ?>

                                        রিপোর্ট:
                                        <?php
                                        echo htmlspecialchars(
                                            $news["reporter"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );
                                        ?>

                                    <?php else: ?>

                                        <?php echo SITE_NAME; ?>

                                    <?php endif; ?>

                                </span>


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
                                href="<?php echo SITE_URL; ?>/news/<?php echo urlencode($news["slug"]); ?>"
                                class="search-card-read"
                            >

                                বিস্তারিত পড়ুন →

                            </a>


                        </div>


                    </article>


                <?php endforeach; ?>


            </div>


        <?php else: ?>


            <div class="search-empty">

                <h2>
                    কোনো সংবাদ পাওয়া যায়নি
                </h2>

                <p>
                    অন্য কোনো keyword দিয়ে আবার চেষ্টা করুন।
                </p>

            </div>


        <?php endif; ?>


    <?php else: ?>


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