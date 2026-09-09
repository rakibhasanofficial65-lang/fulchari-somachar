<?php

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/includes/functions.php";


// =====================================================
// SELECT DATE
// =====================================================

$selectedDate = $_GET["date"] ?? date("Y-m-d");

$dateObject = DateTime::createFromFormat(
    "Y-m-d",
    $selectedDate
);

if (
    !$dateObject ||
    $dateObject->format("Y-m-d") !== $selectedDate
) {
    $selectedDate = date("Y-m-d");
}

$selectedDateObject = new DateTime(
    $selectedDate
);

$today = new DateTime(
    date("Y-m-d")
);


// =====================================================
// PREVIOUS / NEXT DATE
// =====================================================

$previousDateObject =
    clone $selectedDateObject;

$previousDateObject->modify("-1 day");

$nextDateObject =
    clone $selectedDateObject;

$nextDateObject->modify("+1 day");

$previousDate =
    $previousDateObject->format("Y-m-d");

$nextDate =
    $nextDateObject->format("Y-m-d");


// =====================================================
// GET ALL PUBLISHED NEWS OF SELECTED DATE
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
      AND news.published_at IS NOT NULL
      AND DATE(news.published_at) = ?
    ORDER BY
        news.published_at DESC,
        news.id DESC
");

$stmt->execute([
    $selectedDate
]);

$epaperNews =
    $stmt->fetchAll();


// =====================================================
// LEAD NEWS
// =====================================================

$leadNews =
    $epaperNews[0] ?? null;


// =====================================================
// GROUP REMAINING NEWS BY CATEGORY
// =====================================================

$groupedNews = [];

foreach ($epaperNews as $item) {

    if (
        $leadNews &&
        (int) $item["id"] ===
        (int) $leadNews["id"]
    ) {
        continue;
    }

    $categoryName =
        !empty($item["category_name"])
            ? $item["category_name"]
            : "সাধারণ";

    $groupedNews[$categoryName][] =
        $item;
}


// =====================================================
// BANGLA DATE
// =====================================================

$banglaMonths = [

    1 => "জানুয়ারি",
    2 => "ফেব্রুয়ারি",
    3 => "মার্চ",
    4 => "এপ্রিল",
    5 => "মে",
    6 => "জুন",
    7 => "জুলাই",
    8 => "আগস্ট",
    9 => "সেপ্টেম্বর",
    10 => "অক্টোবর",
    11 => "নভেম্বর",
    12 => "ডিসেম্বর"

];


$banglaDays = [

    "Sunday" =>
        "রবিবার",

    "Monday" =>
        "সোমবার",

    "Tuesday" =>
        "মঙ্গলবার",

    "Wednesday" =>
        "বুধবার",

    "Thursday" =>
        "বৃহস্পতিবার",

    "Friday" =>
        "শুক্রবার",

    "Saturday" =>
        "শনিবার"

];


$day =
    $selectedDateObject->format("d");

$month =
    $banglaMonths[
        (int) $selectedDateObject->format("m")
    ];

$year =
    $selectedDateObject->format("Y");

$dayName =
    $banglaDays[
        $selectedDateObject->format("l")
    ];


$displayDate =
    $day . " " .
    $month . " " .
    $year;


$displayFullDate =
    $dayName . ", " .
    $displayDate;


// =====================================================
// PAGE SEO
// =====================================================

$pageTitle =
    "ই-পেপার | " .
    SITE_NAME .
    " | " .
    $displayDate;


$pageDescription =
    SITE_NAME .
    " এর " .
    $displayDate .
    " তারিখের দৈনিক ই-পেপার। " .
    "ফুলছড়ি, জাতীয়, রাজনীতি, দুর্নীতি, " .
    "খেলাধুলা ও বিনোদনের সর্বশেষ সংবাদ।";


$canonicalUrl =
    epaper_url() .
    "?date=" .
    rawurlencode($selectedDate);


$ogImage =
    site_url("assets/logo.png");


// =====================================================
// HELPER: NEWS TIME
// =====================================================

function epaper_time($datetime)
{
    if (empty($datetime)) {
        return "";
    }

    $timestamp =
        strtotime($datetime);

    if ($timestamp === false) {
        return "";
    }

    return date(
        "h:i A",
        $timestamp
    );
}


// =====================================================
// HELPER: NEWS SHORT TEXT
// =====================================================

function epaper_summary(
    $news,
    $length = 180
) {

    $text = "";

    if (!empty($news["headline"])) {

        $text =
            $news["headline"];

    } elseif (!empty($news["content"])) {

        $text =
            strip_tags(
                $news["content"]
            );

    }


    $text = trim(
        preg_replace(
            "/\s+/u",
            " ",
            $text
        )
    );


    if ($text === "") {
        return "";
    }


    return mb_substr(
        $text,
        0,
        $length
    );
}


// =====================================================
// HEADER
// =====================================================

require_once __DIR__ . "/includes/header.php";

?>


<main class="epaper-page">


    <!-- =================================================
         TOP CONTROLS
    ================================================= -->

    <div class="epaper-controls">

        <form
            method="GET"
            action="<?php echo e(epaper_url()); ?>"
            class="date-form"
        >

            <label for="epaper-date">
                ই-পেপার তারিখ
            </label>

            <input
                type="date"
                id="epaper-date"
                name="date"
                value="<?php echo e($selectedDate); ?>"
                max="<?php echo e(date("Y-m-d")); ?>"
            >

            <button
                type="submit"
                class="epaper-btn"
            >
                দেখুন
            </button>

        </form>


        <div class="epaper-navigation">

            <a
                href="<?php echo e(epaper_url()); ?>?date=<?php echo e($previousDate); ?>"
                class="epaper-btn"
            >
                ← আগের দিন
            </a>


            <?php if (
                $selectedDate <
                $today->format("Y-m-d")
            ): ?>

                <a
                    href="<?php echo e(epaper_url()); ?>?date=<?php echo e($nextDate); ?>"
                    class="epaper-btn"
                >
                    পরের দিন →
                </a>

            <?php endif; ?>


            <button
                type="button"
                onclick="window.print()"
                class="epaper-btn print-btn"
            >
                🖨 প্রিন্ট / PDF
            </button>

        </div>

    </div>


    <!-- =================================================
         NEWSPAPER
    ================================================= -->

    <div class="newspaper">


        <!-- =================================================
             NEWSPAPER HEADER
        ================================================= -->

        <header class="newspaper-header">

            <img
                src="<?php echo e(site_url("assets/logo.png")); ?>"
                alt="<?php echo e(SITE_NAME); ?>"
                class="newspaper-logo"
            >


            <div class="newspaper-motto">
                সত্যের পক্ষে, মানুষের পাশে
            </div>


            <div class="newspaper-date">

                দৈনিক ই-পেপার

                <span>|</span>

                <?php echo e($displayFullDate); ?>

            </div>

        </header>


        <?php if ($leadNews): ?>


            <!-- =================================================
                 LEAD NEWS
            ================================================= -->

            <section class="lead-news">


                <div class="lead-news-image-wrap">

                    <?php if (
                        !empty($leadNews["image"])
                    ): ?>

                        <a
                            href="<?php echo e(
                                news_url(
                                    $leadNews["slug"]
                                )
                            ); ?>"
                        >

                            <img
                                src="<?php echo e(
                                    image_url(
                                        $leadNews["image"]
                                    )
                                ); ?>"
                                alt="<?php echo e(
                                    $leadNews["title"]
                                ); ?>"
                                class="lead-image"
                                fetchpriority="high"
                                decoding="async"
                            >

                        </a>

                    <?php else: ?>

                        <div class="lead-no-image">

                            <?php echo e(
                                SITE_NAME
                            ); ?>

                        </div>

                    <?php endif; ?>

                </div>


                <div class="lead-content">


                    <?php if (
                        !empty(
                            $leadNews["category_name"]
                        )
                    ): ?>

                        <div class="lead-category">

                            <?php echo e(
                                $leadNews[
                                    "category_name"
                                ]
                            ); ?>

                        </div>

                    <?php endif; ?>


                    <h1 class="lead-title">

                        <a
                            href="<?php echo e(
                                news_url(
                                    $leadNews["slug"]
                                )
                            ); ?>"
                        >

                            <?php echo e(
                                $leadNews["title"]
                            ); ?>

                        </a>

                    </h1>


                    <?php

                    $leadSummary =
                        epaper_summary(
                            $leadNews,
                            420
                        );

                    ?>


                    <?php if (
                        $leadSummary !== ""
                    ): ?>

                        <p class="lead-headline">

                            <?php echo e(
                                $leadSummary
                            ); ?>

                        </p>

                    <?php endif; ?>


                    <div class="lead-meta">


                        <?php if (
                            !empty(
                                $leadNews["reporter"]
                            )
                        ): ?>

                            <span>

                                রিপোর্ট:

                                <?php echo e(
                                    $leadNews[
                                        "reporter"
                                    ]
                                ); ?>

                            </span>

                            <span>|</span>

                        <?php endif; ?>


                        <span>

                            <?php echo e(
                                epaper_time(
                                    $leadNews[
                                        "published_at"
                                    ]
                                )
                            ); ?>

                        </span>

                    </div>


                    <a
                        href="<?php echo e(
                            news_url(
                                $leadNews["slug"]
                            )
                        ); ?>"
                        class="read-more-btn"
                    >
                        বিস্তারিত পড়ুন →
                    </a>

                </div>

            </section>


            <!-- =================================================
                 NEWS COUNT BAR
            ================================================= -->

            <div class="epaper-info-bar">

                <div>

                    <strong>
                        আজকের সংবাদ
                    </strong>

                    <span>

                        <?php echo e(
                            bn_number(
                                count($epaperNews)
                            )
                        ); ?>টি

                    </span>

                </div>


                <div>

                    <?php echo e(
                        $displayDate
                    ); ?>

                </div>

            </div>


            <!-- =================================================
                 ALL OTHER NEWS
            ================================================= -->

            <?php if (
                !empty($groupedNews)
            ): ?>

                <div class="epaper-grid">


                    <?php foreach (
                        $groupedNews
                        as $categoryName =>
                        $categoryNews
                    ): ?>


                        <section class="epaper-section">


                            <div class="epaper-section-title">

                                <span
                                    class="section-red-line"
                                ></span>

                                <?php echo e(
                                    $categoryName
                                ); ?>

                            </div>


                            <?php foreach (
                                $categoryNews
                                as $item
                            ): ?>


                                <article
                                    class="epaper-item"
                                >


                                    <?php if (
                                        !empty(
                                            $item["image"]
                                        )
                                    ): ?>

                                        <a
                                            href="<?php echo e(
                                                news_url(
                                                    $item["slug"]
                                                )
                                            ); ?>"
                                        >

                                            <img
                                                src="<?php echo e(
                                                    image_url(
                                                        $item["image"]
                                                    )
                                                ); ?>"
                                                alt="<?php echo e(
                                                    $item["title"]
                                                ); ?>"
                                                class="epaper-item-image"
                                                loading="lazy"
                                                decoding="async"
                                            >

                                        </a>

                                    <?php endif; ?>


                                    <div
                                        class="epaper-item-category"
                                    >

                                        <?php echo e(
                                            $categoryName
                                        ); ?>

                                    </div>


                                    <h2
                                        class="epaper-item-title"
                                    >

                                        <a
                                            href="<?php echo e(
                                                news_url(
                                                    $item["slug"]
                                                )
                                            ); ?>"
                                        >

                                            <?php echo e(
                                                $item["title"]
                                            ); ?>

                                        </a>

                                    </h2>


                                    <?php

                                    $shortText =
                                        epaper_summary(
                                            $item,
                                            180
                                        );

                                    ?>


                                    <?php if (
                                        $shortText !== ""
                                    ): ?>

                                        <p
                                            class="epaper-item-text"
                                        >

                                            <?php echo e(
                                                $shortText
                                            ); ?>

                                        </p>

                                    <?php endif; ?>


                                    <div
                                        class="epaper-item-meta"
                                    >


                                        <?php if (
                                            !empty(
                                                $item["reporter"]
                                            )
                                        ): ?>

                                            <span>

                                                রিপোর্ট:

                                                <?php echo e(
                                                    $item[
                                                        "reporter"
                                                    ]
                                                ); ?>

                                            </span>

                                            <span>|</span>

                                        <?php endif; ?>


                                        <span>

                                            <?php echo e(
                                                epaper_time(
                                                    $item[
                                                        "published_at"
                                                    ]
                                                )
                                            ); ?>

                                        </span>

                                    </div>

                                </article>


                            <?php endforeach; ?>


                        </section>


                    <?php endforeach; ?>


                </div>

            <?php endif; ?>


        <?php else: ?>


            <!-- =================================================
                 NO NEWS
            ================================================= -->

            <div class="no-epaper-news">

                <div class="empty-icon">
                    📰
                </div>

                <h2>
                    এই তারিখের কোনো সংবাদ নেই
                </h2>

                <p>

                    <?php echo e(
                        $displayDate
                    ); ?>

                    তারিখে কোনো Published News
                    পাওয়া যায়নি।

                </p>


                <a
                    href="<?php echo e(epaper_url()); ?>"
                    class="epaper-btn"
                >
                    আজকের ই-পেপার দেখুন
                </a>

            </div>

        <?php endif; ?>


        <!-- =================================================
             NEWSPAPER FOOTER
        ================================================= -->

        <footer class="epaper-footer">

            <div class="footer-main-name">

                <?php echo e(
                    SITE_NAME
                ); ?>

            </div>


            <div>
                সত্যের পক্ষে, মানুষের পাশে
            </div>


            <div>

                <?php echo e(
                    $displayDate
                ); ?>

            </div>

        </footer>


    </div>

</main>


<style>

/* =====================================================
   E-PAPER PAGE
===================================================== */

.epaper-page {

    background: #e7e7e7;

    padding: 30px 0 60px;

    min-height: 100vh;

}


/* =====================================================
   CONTROLS
===================================================== */

.epaper-controls {

    max-width: 1180px;

    margin: 0 auto 22px;

    padding: 14px 18px;

    background: #fff;

    border: 1px solid #d5d5d5;

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 15px;

    flex-wrap: wrap;

}


.date-form {

    display: flex;

    align-items: center;

    gap: 8px;

    flex-wrap: wrap;

}


.date-form label {

    font-weight: 700;

    font-size: 14px;

}


.date-form input {

    height: 40px;

    border: 1px solid #bbb;

    padding: 0 10px;

    border-radius: 4px;

    background: #fff;

}


.epaper-navigation {

    display: flex;

    align-items: center;

    gap: 7px;

    flex-wrap: wrap;

}


.epaper-btn {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-height: 40px;

    padding: 0 14px;

    border: 0;

    border-radius: 4px;

    background: #111;

    color: #fff;

    text-decoration: none;

    font-size: 13px;

    font-weight: 700;

    cursor: pointer;

}


.epaper-btn:hover {

    background: #b30000;

    color: #fff;

}


.print-btn {

    background: #b30000;

}


/* =====================================================
   NEWSPAPER
===================================================== */

.newspaper {

    max-width: 1180px;

    margin: 0 auto;

    padding: 30px 38px 40px;

    background: #fff;

    border: 1px solid #c9c9c9;

    box-shadow:
        0 8px 35px rgba(0, 0, 0, .15);

}


/* =====================================================
   NEWSPAPER HEADER
===================================================== */

.newspaper-header {

    text-align: center;

    padding-bottom: 18px;

    margin-bottom: 24px;

    border-bottom: 5px double #111;

}


.newspaper-logo {

    display: block;

    width: 280px;

    max-width: 80%;

    max-height: 105px;

    object-fit: contain;

    margin: 0 auto 7px;

}


.newspaper-motto {

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 14px;

    color: #555;

    margin-bottom: 12px;

}


.newspaper-date {

    border-top: 1px solid #aaa;

    border-bottom: 1px solid #aaa;

    padding: 8px 5px;

    font-size: 14px;

    font-weight: 700;

}


.newspaper-date span {

    margin: 0 8px;

    color: #b30000;

}


/* =====================================================
   LEAD NEWS
===================================================== */

.lead-news {

    display: grid;

    grid-template-columns:
        1.55fr 1fr;

    gap: 28px;

    padding-bottom: 26px;

    border-bottom: 3px solid #111;

}


.lead-news-image-wrap {

    min-width: 0;

}


.lead-image,
.lead-no-image {

    width: 100%;

    height: 390px;

    object-fit: cover;

    display: block;

}


.lead-no-image {

    background:
        linear-gradient(
            135deg,
            #ddd,
            #aaa
        );

    display: flex;

    align-items: center;

    justify-content: center;

    color: #555;

    font-size: 24px;

    font-weight: 700;

}


.lead-content {

    display: flex;

    flex-direction: column;

    justify-content: center;

}


.lead-category {

    color: #b30000;

    font-size: 14px;

    font-weight: 800;

    margin-bottom: 9px;

}


.lead-title {

    margin: 0 0 13px;

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 34px;

    line-height: 1.45;

}


.lead-title a {

    color: #111;

    text-decoration: none;

}


.lead-title a:hover {

    color: #b30000;

}


.lead-headline {

    margin: 0 0 14px;

    color: #555;

    font-size: 16px;

    line-height: 1.85;

}


.lead-meta {

    display: flex;

    flex-wrap: wrap;

    gap: 7px;

    padding-top: 10px;

    border-top: 1px solid #ddd;

    color: #777;

    font-size: 12px;

}


.read-more-btn {

    align-self: flex-start;

    margin-top: 18px;

    display: inline-block;

    padding: 9px 15px;

    background: #111;

    color: #fff;

    text-decoration: none;

    font-size: 13px;

    font-weight: 700;

}


.read-more-btn:hover {

    background: #b30000;

    color: #fff;

}


/* =====================================================
   INFO BAR
===================================================== */

.epaper-info-bar {

    margin: 20px 0;

    padding: 10px 13px;

    border-top: 1px solid #bbb;

    border-bottom: 1px solid #bbb;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 10px;

    font-size: 13px;

}


.epaper-info-bar div:first-child {

    display: flex;

    gap: 10px;

}


.epaper-info-bar span {

    color: #b30000;

    font-weight: 800;

}


/* =====================================================
   NEWS GRID
===================================================== */

.epaper-grid {

    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 25px;

    align-items: start;

}


/* =====================================================
   SECTION
===================================================== */

.epaper-section {

    min-width: 0;

    border-top: 3px solid #111;

}


.epaper-section-title {

    position: relative;

    padding: 9px 10px;

    margin-bottom: 13px;

    background: #f1f1f1;

    border-bottom: 1px solid #ccc;

    font-size: 19px;

    font-weight: 800;

}


.section-red-line {

    display: inline-block;

    width: 5px;

    height: 19px;

    margin-right: 8px;

    vertical-align: -3px;

    background: #b30000;

}


/* =====================================================
   NEWS ITEM
===================================================== */

.epaper-item {

    padding-bottom: 16px;

    margin-bottom: 16px;

    border-bottom: 1px solid #ccc;

}


.epaper-item:last-child {

    margin-bottom: 0;

}


.epaper-item-image {

    width: 100%;

    height: 175px;

    object-fit: cover;

    display: block;

    margin-bottom: 9px;

}


.epaper-item-category {

    color: #b30000;

    font-size: 11px;

    font-weight: 800;

    margin-bottom: 4px;

}


.epaper-item-title {

    margin: 0 0 7px;

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 19px;

    line-height: 1.5;

}


.epaper-item-title a {

    color: #222;

    text-decoration: none;

}


.epaper-item-title a:hover {

    color: #b30000;

}


.epaper-item-text {

    margin: 0;

    color: #666;

    font-size: 13px;

    line-height: 1.7;

}


.epaper-item-meta {

    display: flex;

    flex-wrap: wrap;

    gap: 6px;

    margin-top: 8px;

    color: #999;

    font-size: 11px;

}


/* =====================================================
   EMPTY
===================================================== */

.no-epaper-news {

    text-align: center;

    padding: 90px 20px;

    border: 2px dashed #ccc;

}


.empty-icon {

    font-size: 45px;

    margin-bottom: 12px;

}


.no-epaper-news h2 {

    margin: 0 0 8px;

    font-size: 27px;

    color: #222;

}


.no-epaper-news p {

    margin: 0 0 18px;

    color: #777;

}


/* =====================================================
   FOOTER
===================================================== */

.epaper-footer {

    margin-top: 30px;

    padding-top: 15px;

    border-top: 5px double #111;

    text-align: center;

    color: #777;

    font-size: 12px;

    line-height: 1.9;

}


.footer-main-name {

    color: #111;

    font-weight: 800;

    font-size: 15px;

}


/* =====================================================
   TABLET
===================================================== */

@media (max-width: 900px) {

    .newspaper {

        padding: 25px;

    }


    .lead-news {

        grid-template-columns: 1fr;

    }


    .lead-image,
    .lead-no-image {

        height: 320px;

    }


    .epaper-grid {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

    }

}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 600px) {

    .epaper-page {

        padding: 12px 0 30px;

    }


    .epaper-controls {

        margin: 0 10px 15px;

        padding: 12px;

    }


    .date-form {

        width: 100%;

    }


    .date-form input {

        flex: 1;

        min-width: 120px;

    }


    .epaper-navigation {

        width: 100%;

    }


    .epaper-navigation .epaper-btn {

        flex: 1;

    }


    .newspaper {

        width: 96%;

        padding: 16px;

    }


    .newspaper-logo {

        width: 210px;

    }


    .newspaper-motto {

        font-size: 12px;

    }


    .newspaper-date {

        font-size: 12px;

    }


    .lead-image,
    .lead-no-image {

        height: 240px;

    }


    .lead-title {

        font-size: 25px;

    }


    .lead-headline {

        font-size: 14px;

    }


    .epaper-grid {

        grid-template-columns: 1fr;

    }


    .epaper-item-image {

        height: 210px;

    }


    .epaper-info-bar {

        align-items: flex-start;

        flex-direction: column;

    }

}


/* =====================================================
   PRINT / SAVE PDF
===================================================== */

@media print {

    @page {

        size: A4;

        margin: 9mm;

    }


    body {

        background: #fff !important;

    }


    .site-header,
    .navbar,
    .main-navbar,
    .site-footer,
    .epaper-controls {

        display: none !important;

    }


    .epaper-page {

        padding: 0;

        background: #fff;

    }


    .newspaper {

        width: 100%;

        max-width: none;

        margin: 0;

        padding: 0;

        border: 0;

        box-shadow: none;

    }


    .newspaper-header {

        break-inside: avoid;

    }


    .lead-news {

        grid-template-columns:
            1.5fr 1fr;

        break-inside: avoid;

    }


    .lead-image,
    .lead-no-image {

        height: 260px;

    }


    .lead-title {

        font-size: 25px;

    }


    .epaper-grid {

        grid-template-columns:
            repeat(3, minmax(0, 1fr));

        gap: 16px;

    }


    .epaper-section {

        break-inside: avoid;

    }


    .epaper-item {

        break-inside: avoid;

    }


    .epaper-item-image {

        height: 130px;

    }


    .epaper-section-title {

        print-color-adjust: exact;

        -webkit-print-color-adjust: exact;

    }


    a {

        color: #000 !important;

        text-decoration: none !important;

    }

}

</style>


<?php

require __DIR__ . "/includes/footer.php";

?>
