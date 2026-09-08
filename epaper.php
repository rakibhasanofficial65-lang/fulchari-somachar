<?php

require_once "config/config.php";
require_once "config/database.php";


// =====================================================
// SELECT DATE
// =====================================================

$selectedDate = $_GET["date"] ?? date("Y-m-d");


// নিরাপদ Date format check
$dateObject = DateTime::createFromFormat("Y-m-d", $selectedDate);

if (
    !$dateObject ||
    $dateObject->format("Y-m-d") !== $selectedDate
) {
    $selectedDate = date("Y-m-d");
}


// =====================================================
// PREVIOUS / NEXT DATE
// =====================================================

$selectedDateObject = new DateTime($selectedDate);

$previousDate = clone $selectedDateObject;
$previousDate->modify("-1 day");

$nextDate = clone $selectedDateObject;
$nextDate->modify("+1 day");


// =====================================================
// GET PUBLISHED NEWS OF SELECTED DATE
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
    AND DATE(news.published_at) = ?
    ORDER BY news.published_at DESC, news.id DESC
");

$stmt->execute([$selectedDate]);

$epaperNews = $stmt->fetchAll();


// =====================================================
// GROUP NEWS BY CATEGORY
// =====================================================

$groupedNews = [];

foreach ($epaperNews as $item) {

    $categoryName = !empty($item["category_name"])
        ? $item["category_name"]
        : "সাধারণ";

    $groupedNews[$categoryName][] = $item;
}


// =====================================================
// DATE IN BANGLA
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

$day = $selectedDateObject->format("d");
$month = $banglaMonths[(int)$selectedDateObject->format("m")];
$year = $selectedDateObject->format("Y");

$displayDate = $day . " " . $month . " " . $year;


// =====================================================
// PAGE TITLE
// =====================================================

$pageTitle = "ই-পেপার | " . SITE_NAME;

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
    content="<?php echo SITE_NAME; ?> - প্রতিদিনের অনলাইন ই-পেপার"
>

<title>
    <?php echo $pageTitle; ?>
</title>


<link
    rel="stylesheet"
    href="assets/style.css"
>


<style>

/* =====================================================
   E-PAPER PAGE
===================================================== */

.epaper-page {

    background: #e9e9e9;

    padding: 30px 0 60px;

    min-height: 100vh;

}


/* =====================================================
   TOP CONTROL
===================================================== */

.epaper-controls {

    max-width: 1100px;

    margin: 0 auto 20px;

    background: #fff;

    border: 1px solid #ddd;

    padding: 15px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 12px;

    flex-wrap: wrap;

}


.date-form {

    display: flex;

    align-items: center;

    gap: 8px;

}


.date-form label {

    font-weight: bold;

}


.date-form input {

    padding: 9px 10px;

    border: 1px solid #bbb;

    border-radius: 4px;

}


.epaper-btn {

    display: inline-block;

    border: 0;

    background: #111;

    color: #fff;

    padding: 9px 15px;

    border-radius: 4px;

    cursor: pointer;

    font-size: 14px;

    text-decoration: none;

}


.epaper-btn:hover {

    background: #b30000;

}


.print-btn {

    background: #b30000;

}


/* =====================================================
   NEWSPAPER
===================================================== */

.newspaper {

    max-width: 1100px;

    margin: 0 auto;

    background: #fff;

    padding: 30px 35px 45px;

    box-shadow:
        0 5px 30px rgba(0,0,0,.15);

    border: 1px solid #ccc;

}


/* =====================================================
   NEWSPAPER HEADER
===================================================== */

.newspaper-header {

    text-align: center;

    border-bottom: 5px double #111;

    padding-bottom: 18px;

    margin-bottom: 20px;

}


.newspaper-logo {

    width: 260px;

    max-height: 100px;

    object-fit: contain;

    margin: 0 auto 5px;

    display: block;

}


.newspaper-name {

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 45px;

    font-weight: 900;

    color: #111;

    line-height: 1.2;

}


.newspaper-tagline {

    font-size: 14px;

    color: #555;

    margin-top: 3px;

}


.newspaper-date {

    margin-top: 10px;

    padding: 7px 0;

    border-top: 1px solid #aaa;

    border-bottom: 1px solid #aaa;

    font-size: 14px;

    font-weight: bold;

}


/* =====================================================
   LEAD NEWS
===================================================== */

.lead-news {

    display: grid;

    grid-template-columns: 1.5fr 1fr;

    gap: 25px;

    padding-bottom: 25px;

    margin-bottom: 25px;

    border-bottom: 2px solid #111;

}


.lead-image {

    width: 100%;

    height: 330px;

    object-fit: cover;

    display: block;

}


.lead-no-image {

    width: 100%;

    height: 330px;

    background: #ddd;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 20px;

    color: #777;

}


.lead-category {

    color: #b30000;

    font-size: 14px;

    font-weight: bold;

    margin-bottom: 8px;

}


.lead-title {

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 31px;

    line-height: 1.45;

    margin-bottom: 12px;

}


.lead-title a {

    color: #111;

}


.lead-title a:hover {

    color: #b30000;

}


.lead-headline {

    font-size: 16px;

    color: #555;

    line-height: 1.8;

    margin-bottom: 12px;

}


.lead-meta {

    font-size: 13px;

    color: #777;

    border-top: 1px solid #ddd;

    padding-top: 10px;

}


/* =====================================================
   NEWS COLUMNS
===================================================== */

.epaper-sections {

    column-count: 3;

    column-gap: 25px;

}


.epaper-section {

    break-inside: avoid;

    margin-bottom: 25px;

}


.epaper-section-title {

    font-size: 19px;

    font-weight: bold;

    background: #111;

    color: #fff;

    padding: 7px 10px;

    border-left: 5px solid #b30000;

    margin-bottom: 12px;

}


.epaper-item {

    border-bottom: 1px solid #ccc;

    padding-bottom: 13px;

    margin-bottom: 14px;

}


.epaper-item:last-child {

    border-bottom: 0;

}


.epaper-item-image {

    width: 100%;

    height: 135px;

    object-fit: cover;

    display: block;

    margin-bottom: 8px;

}


.epaper-item-title {

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 18px;

    line-height: 1.5;

    margin-bottom: 5px;

}


.epaper-item-title a {

    color: #222;

}


.epaper-item-title a:hover {

    color: #b30000;

}


.epaper-item-text {

    font-size: 13px;

    color: #666;

    line-height: 1.7;

}


.epaper-item-meta {

    margin-top: 6px;

    font-size: 11px;

    color: #999;

}


/* =====================================================
   NO NEWS
===================================================== */

.no-epaper-news {

    text-align: center;

    padding: 80px 20px;

    border: 2px dashed #ccc;

    color: #777;

}


.no-epaper-news h2 {

    font-size: 28px;

    color: #333;

    margin-bottom: 8px;

}


/* =====================================================
   FOOTER
===================================================== */

.epaper-footer {

    margin-top: 35px;

    padding-top: 15px;

    border-top: 4px double #111;

    text-align: center;

    font-size: 12px;

    color: #777;

}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 800px) {

    .newspaper {

        padding: 20px;

    }


    .lead-news {

        grid-template-columns: 1fr;

    }


    .lead-image,
    .lead-no-image {

        height: 260px;

    }


    .epaper-sections {

        column-count: 2;

    }


    .newspaper-name {

        font-size: 34px;

    }

}


@media (max-width: 550px) {

    .epaper-page {

        padding: 15px 0 30px;

    }


    .epaper-controls {

        margin: 0 10px 15px;

    }


    .newspaper {

        width: 96%;

        padding: 15px;

    }


    .newspaper-logo {

        width: 190px;

    }


    .newspaper-name {

        font-size: 27px;

    }


    .lead-title {

        font-size: 24px;

    }


    .epaper-sections {

        column-count: 1;

    }


    .date-form {

        width: 100%;

    }


    .date-form input {

        flex: 1;

    }

}


/* =====================================================
   PRINT
===================================================== */

@media print {

    @page {

        size: A4;

        margin: 10mm;

    }


    body {

        background: #fff;

    }


    .site-header,
    .navbar,
    .epaper-controls,
    .site-footer {

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

        box-shadow: none;

        border: 0;

    }


    .newspaper-logo {

        width: 220px;

    }


    .lead-image {

        height: 280px;

    }


    .lead-title {

        font-size: 25px;

    }


    .epaper-sections {

        column-count: 3;

    }


    .epaper-section-title {

        print-color-adjust: exact;

        -webkit-print-color-adjust: exact;

    }


    a {

        text-decoration: none;

        color: #000 !important;

    }

}

</style>

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<nav class="navbar">

    <div class="container">

        <ul class="nav-menu">

            <li>
                <a href="index.php">হোম</a>
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
     E-PAPER
===================================================== -->

<main class="epaper-page">


    <!-- CONTROLS -->

    <div class="epaper-controls">


        <form
            method="GET"
            class="date-form"
        >

            <label for="date">
                ই-পেপার তারিখ:
            </label>

            <input
                type="date"
                id="date"
                name="date"
                value="<?php echo htmlspecialchars($selectedDate); ?>"
            >

            <button
                type="submit"
                class="epaper-btn"
            >
                দেখুন
            </button>

        </form>


        <div>


            <a
                href="epaper.php?date=<?php
                    echo $previousDate->format("Y-m-d");
                ?>"
                class="epaper-btn"
            >
                ← আগের দিন
            </a>


            <?php if ($selectedDate < date("Y-m-d")): ?>

                <a
                    href="epaper.php?date=<?php
                        echo $nextDate->format("Y-m-d");
                    ?>"
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
                🖨 Print / PDF
            </button>


        </div>


    </div>


    <!-- =================================================
         NEWSPAPER
    ================================================= -->

    <div class="newspaper">


        <!-- NEWSPAPER HEADER -->

        <header class="newspaper-header">


            <img
                src="assets/logo.png"
                alt="ফুলছড়ি সমাচার"
                class="newspaper-logo"
            >


            <div class="newspaper-date">

                দৈনিক ই-পেপার |
                <?php echo htmlspecialchars($displayDate); ?>

            </div>


        </header>


        <?php if (count($epaperNews) > 0): ?>


            <!-- =================================================
                 LEAD NEWS
            ================================================= -->

            <?php $leadNews = $epaperNews[0]; ?>


            <section class="lead-news">


                <!-- LEAD IMAGE -->

                <div>


                    <?php if (!empty($leadNews["image"])): ?>

                        <img
                            src="uploads/<?php
                                echo htmlspecialchars(
                                    $leadNews["image"]
                                );
                            ?>"
                            alt="<?php
                                echo htmlspecialchars(
                                    $leadNews["title"]
                                );
                            ?>"
                            class="lead-image"
                        >

                    <?php else: ?>

                        <div class="lead-no-image">
                            ফুলছড়ি সমাচার
                        </div>

                    <?php endif; ?>


                </div>


                <!-- LEAD CONTENT -->

                <div>


                    <?php if (!empty($leadNews["category_name"])): ?>

                        <div class="lead-category">

                            <?php echo htmlspecialchars(
                                $leadNews["category_name"]
                            ); ?>

                        </div>

                    <?php endif; ?>


                    <h1 class="lead-title">

                        <a
                            href="news.php?slug=<?php
                                echo urlencode(
                                    $leadNews["slug"]
                                );
                            ?>"
                        >

                            <?php echo htmlspecialchars(
                                $leadNews["title"]
                            ); ?>

                        </a>

                    </h1>


                    <?php if (!empty($leadNews["headline"])): ?>

                        <div class="lead-headline">

                            <?php echo htmlspecialchars(
                                $leadNews["headline"]
                            ); ?>

                        </div>

                    <?php else: ?>

                        <div class="lead-headline">

                            <?php

                            echo htmlspecialchars(
                                mb_substr(
                                    strip_tags(
                                        $leadNews["content"]
                                    ),
                                    0,
                                    300
                                )
                            );

                            ?>

                        </div>

                    <?php endif; ?>


                    <div class="lead-meta">

                        <?php if (!empty($leadNews["reporter"])): ?>

                            রিপোর্ট:
                            <?php echo htmlspecialchars(
                                $leadNews["reporter"]
                            ); ?>

                            &nbsp; | &nbsp;

                        <?php endif; ?>


                        <?php

                        echo date(
                            "h:i A",
                            strtotime(
                                $leadNews["published_at"]
                            )
                        );

                        ?>

                    </div>


                </div>


            </section>


            <!-- =================================================
                 OTHER NEWS
            ================================================= -->

            <?php if (count($epaperNews) > 1): ?>


                <div class="epaper-sections">


                    <?php foreach (
                        $groupedNews
                        as $categoryName => $categoryNews
                    ): ?>


                        <section class="epaper-section">


                            <div class="epaper-section-title">

                                <?php echo htmlspecialchars(
                                    $categoryName
                                ); ?>

                            </div>


                            <?php foreach (
                                $categoryNews
                                as $item
                            ): ?>


                                <?php

                                // Lead news আবার দেখানো হবে না

                                if (
                                    (int)$item["id"]
                                    ===
                                    (int)$leadNews["id"]
                                ) {
                                    continue;
                                }

                                ?>


                                <article class="epaper-item">


                                    <?php if (!empty($item["image"])): ?>

                                        <a
                                            href="news.php?slug=<?php
                                                echo urlencode(
                                                    $item["slug"]
                                                );
                                            ?>"
                                        >

                                            <img
                                                src="uploads/<?php
                                                    echo htmlspecialchars(
                                                        $item["image"]
                                                    );
                                                ?>"
                                                alt="<?php
                                                    echo htmlspecialchars(
                                                        $item["title"]
                                                    );
                                                ?>"
                                                class="epaper-item-image"
                                            >

                                        </a>

                                    <?php endif; ?>


                                    <h2 class="epaper-item-title">

                                        <a
                                            href="news.php?slug=<?php
                                                echo urlencode(
                                                    $item["slug"]
                                                );
                                            ?>"
                                        >

                                            <?php echo htmlspecialchars(
                                                $item["title"]
                                            ); ?>

                                        </a>

                                    </h2>


                                    <?php

                                    $shortText = "";

                                    if (
                                        !empty(
                                            $item["headline"]
                                        )
                                    ) {

                                        $shortText =
                                            $item["headline"];

                                    } else {

                                        $shortText =
                                            strip_tags(
                                                $item["content"]
                                            );

                                    }

                                    ?>


                                    <div class="epaper-item-text">

                                        <?php echo htmlspecialchars(
                                            mb_substr(
                                                $shortText,
                                                0,
                                                180
                                            )
                                        ); ?>

                                    </div>


                                    <div class="epaper-item-meta">

                                        <?php if (
                                            !empty(
                                                $item["reporter"]
                                            )
                                        ): ?>

                                            রিপোর্ট:
                                            <?php echo htmlspecialchars(
                                                $item["reporter"]
                                            ); ?>

                                            &nbsp; | &nbsp;

                                        <?php endif; ?>


                                        <?php

                                        echo date(
                                            "h:i A",
                                            strtotime(
                                                $item["published_at"]
                                            )
                                        );

                                        ?>

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

                <h2>
                    এই তারিখে কোনো সংবাদ নেই
                </h2>

                <p>
                    এই তারিখে কোনো Published News পাওয়া যায়নি।
                </p>

            </div>


        <?php endif; ?>


        <!-- NEWSPAPER FOOTER -->

        <div class="epaper-footer">

            <?php echo SITE_NAME; ?>

            &nbsp; | &nbsp;

            সত্যের পক্ষে, মানুষের পাশে

            &nbsp; | &nbsp;

            <?php echo htmlspecialchars($displayDate); ?>

        </div>


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