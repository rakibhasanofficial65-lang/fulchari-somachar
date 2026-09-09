<?php

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/includes/functions.php";


// =====================================================
// SEO
// =====================================================

$pageTitle =
    SITE_NAME . " — সর্বশেষ বাংলা সংবাদ";

$pageDescription =
    "ফুলছড়ি সমাচার — সত্যের পক্ষে, মানুষের পাশে। "
    . "ফুলছড়ি, গাইবান্ধা, জাতীয়, রাজনীতি, দুর্নীতি, "
    . "খেলাধুলা ও বিনোদনের সর্বশেষ সংবাদ।";

$canonicalUrl = site_url("/");

$ogImage = site_url("assets/logo.png");


// =====================================================
// GET PUBLISHED NEWS
// =====================================================

$stmt = $pdo->prepare("
    SELECT
        news.*,
        categories.name AS category_name,
        categories.slug AS category_slug
    FROM news
    INNER JOIN categories
        ON news.category_id = categories.id
    WHERE news.status = 'published'
    ORDER BY news.published_at DESC, news.id DESC
    LIMIT 12
");

$stmt->execute();

$latestNews = $stmt->fetchAll(PDO::FETCH_ASSOC);


// =====================================================
// HEADER
// =====================================================

require_once __DIR__ . "/includes/header.php";

?>


<!-- =====================================================
     MAIN CONTENT
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

                <a
                    href="<?php echo e(site_url("search")); ?>"
                >
                    সব সংবাদ →
                </a>

            </div>


            <?php if (!empty($latestNews)): ?>


                <!-- =================================================
                     PREMIUM NEWS GRID
                ================================================= -->

                <div class="premium-news-grid">


                    <?php foreach ($latestNews as $index => $news): ?>


                        <article class="premium-photo-card">


                            <!-- =====================================
                                 IMAGE AREA
                            ====================================== -->

                            <div class="photo-card-image">


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
                                                image_url($news["image"])
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

                                    <div class="photo-card-no-image">

                                        <span>
                                            <?php echo e(SITE_NAME); ?>
                                        </span>

                                    </div>

                                <?php endif; ?>


                                <!-- =================================
                                     PREMIUM LOGO
                                ================================== -->

                                <div class="photo-card-logo">

                                    <img
                                        src="<?php echo e(
                                            site_url("assets/logo.png")
                                        ); ?>"
                                        alt="<?php echo e(SITE_NAME); ?>"
                                        loading="lazy"
                                        decoding="async"
                                    >

                                </div>


                                <!-- =================================
                                     CATEGORY
                                ================================== -->

                                <?php if (!empty($news["category_name"])): ?>

                                    <a
                                        href="<?php echo e(
                                            category_url(
                                                $news["category_slug"]
                                            )
                                        ); ?>"
                                        class="photo-card-category"
                                    >

                                        <?php echo e(
                                            $news["category_name"]
                                        ); ?>

                                    </a>

                                <?php endif; ?>


                            </div>


                            <!-- =====================================
                                 CARD CONTENT
                            ====================================== -->

                            <div class="photo-card-content">


                                <!-- =================================
                                     TITLE
                                ================================== -->

                                <h3 class="photo-card-title">

                                    <a
                                        href="<?php echo e(
                                            news_url($news["slug"])
                                        ); ?>"
                                    >

                                        <?php echo e(
                                            $news["title"]
                                        ); ?>

                                    </a>

                                </h3>


                                <!-- =================================
                                     HEADLINE
                                ================================== -->

                                <?php if (!empty($news["headline"])): ?>

                                    <p class="photo-card-headline">

                                        <?php echo e(
                                            news_excerpt(
                                                $news["headline"],
                                                150
                                            )
                                        ); ?>

                                    </p>

                                <?php endif; ?>


                                <!-- =================================
                                     META
                                ================================== -->

                                <div class="photo-card-meta">


                                    <?php if (!empty($news["reporter"])): ?>

                                        <span class="photo-card-reporter">

                                            রিপোর্ট:
                                            <?php echo e(
                                                $news["reporter"]
                                            ); ?>

                                        </span>

                                    <?php else: ?>

                                        <span>
                                            <?php echo e(SITE_NAME); ?>
                                        </span>

                                    <?php endif; ?>


                                    <?php if (
                                        !empty($news["published_at"])
                                    ): ?>

                                        <span>

                                            <?php echo e(
                                                format_date_bn(
                                                    $news["published_at"]
                                                )
                                            ); ?>

                                        </span>

                                    <?php endif; ?>


                                </div>


                                <!-- =================================
                                     READ MORE
                                ================================== -->

                                <a
                                    href="<?php echo e(
                                        news_url($news["slug"])
                                    ); ?>"
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
                     NO NEWS STATE
                ================================================== -->

                <div class="premium-welcome">

                    <h1>
                        এখনো কোনো সংবাদ প্রকাশিত হয়নি
                    </h1>

                    <p>
                        Admin Panel থেকে সংবাদ Publish করলে
                        এখানে Automatic Premium Photo Card তৈরি হবে।
                    </p>

                    <a
                        href="<?php echo e(
                            site_url("admin/login.php")
                        ); ?>"
                        class="photo-card-read"
                    >
                        Admin Panel →
                    </a>

                </div>


            <?php endif; ?>


        </section>

    </div>

</main>


<?php

// =====================================================
// FOOTER
// =====================================================

require_once __DIR__ . "/includes/footer.php";

?>
