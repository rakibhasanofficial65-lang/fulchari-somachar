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
// SOCIAL
// =====================================================

$facebookPage =
    "https://www.facebook.com/profile.php?id=61593835547926";


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


<main class="main-content">

    <div class="container">


        <!-- =================================================
             FACEBOOK / SOCIAL HERO
        ================================================== -->

        <section class="facebook-premium-banner">

            <div class="facebook-banner-content">

                <div class="facebook-banner-logo">

                    <img
                        src="<?php echo e(
                            site_url("assets/logo.png")
                        ); ?>"
                        alt="<?php echo e(SITE_NAME); ?>"
                    >

                </div>


                <div class="facebook-banner-text">

                    <span class="facebook-small-text">
                        আমাদের সঙ্গে যুক্ত থাকুন
                    </span>

                    <h1>
                        <?php echo e(SITE_NAME); ?>
                    </h1>

                    <p>
                        সর্বশেষ সংবাদ, গুরুত্বপূর্ণ আপডেট
                        ও এলাকার খবর পেতে আমাদের Facebook
                        Page-এ যুক্ত থাকুন।
                    </p>

                </div>


                <div class="facebook-banner-action">

                    <a
                        href="<?php echo e($facebookPage); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="facebook-follow-button"
                    >

                        <span class="facebook-icon">
                            f
                        </span>

                        <span>
                            Facebook Page
                        </span>

                        <span class="facebook-arrow">
                            ↗
                        </span>

                    </a>

                </div>

            </div>

        </section>


        <!-- =================================================
             PREMIUM NEWS SECTION
        ================================================== -->

        <section class="premium-news-section">


            <div class="premium-section-title">

                <div class="premium-title-left">

                    <span class="premium-title-line"></span>

                    <div>

                        <span class="premium-kicker">
                            BREAKING & LATEST
                        </span>

                        <h2>
                            সর্বশেষ সংবাদ
                        </h2>

                    </div>

                </div>


                <a
                    href="<?php echo e(
                        site_url("search")
                    ); ?>"
                    class="premium-see-all"
                >
                    সব সংবাদ
                    <span>→</span>
                </a>

            </div>


            <?php if (!empty($latestNews)): ?>


                <!-- =================================================
                     PREMIUM PHOTO GRID
                ================================================== -->

                <div class="premium-news-grid">


                    <?php foreach (
                        $latestNews
                        as $index => $news
                    ): ?>


                        <article
                            class="
                                premium-photo-card
                                <?php
                                echo $index === 0
                                    ? "premium-photo-card-featured"
                                    : "";
                                ?>
                            "
                        >


                            <!-- =====================================
                                 IMAGE
                            ====================================== -->

                            <div class="photo-card-image">


                                <?php if (
                                    !empty($news["image"])
                                ): ?>

                                    <a
                                        href="<?php echo e(
                                            news_url(
                                                $news["slug"]
                                            )
                                        ); ?>"
                                        class="photo-card-image-link"
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

                                    <div
                                        class="
                                            photo-card-no-image
                                        "
                                    >

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

                                <?php endif; ?>


                                <!-- =================================
                                     IMAGE OVERLAY
                                ================================== -->

                                <div
                                    class="
                                        photo-card-gradient
                                    "
                                ></div>


                                <!-- =================================
                                     TOP BRAND
                                ================================== -->

                                <div class="photo-card-brand">

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

                                    <span>
                                        <?php echo e(
                                            SITE_NAME
                                        ); ?>
                                    </span>

                                </div>


                                <!-- =================================
                                     CATEGORY
                                ================================== -->

                                <?php if (
                                    !empty(
                                        $news["category_name"]
                                    )
                                ): ?>

                                    <a
                                        href="<?php echo e(
                                            category_url(
                                                $news[
                                                    "category_slug"
                                                ]
                                            )
                                        ); ?>"
                                        class="
                                            photo-card-category
                                        "
                                    >

                                        <?php echo e(
                                            $news[
                                                "category_name"
                                            ]
                                        ); ?>

                                    </a>

                                <?php endif; ?>


                                <!-- =================================
                                     FEATURED NUMBER
                                ================================== -->

                                <div class="photo-card-number">

                                    <?php echo str_pad(
                                        $index + 1,
                                        2,
                                        "0",
                                        STR_PAD_LEFT
                                    ); ?>

                                </div>


                                <!-- =================================
                                     IMAGE HEADLINE
                                ================================== -->

                                <a
                                    href="<?php echo e(
                                        news_url(
                                            $news["slug"]
                                        )
                                    ); ?>"
                                    class="
                                        photo-card-overlay-title
                                    "
                                >

                                    <?php echo e(
                                        $news["title"]
                                    ); ?>

                                </a>


                            </div>


                            <!-- =================================================
                                 CARD CONTENT
                            ================================================== -->

                            <div class="photo-card-content">


                                <div class="photo-card-content-top">


                                    <?php if (
                                        !empty(
                                            $news["category_name"]
                                        )
                                    ): ?>

                                        <a
                                            href="<?php echo e(
                                                category_url(
                                                    $news[
                                                        "category_slug"
                                                    ]
                                                )
                                            ); ?>"
                                            class="
                                                card-category-link
                                            "
                                        >

                                            <?php echo e(
                                                $news[
                                                    "category_name"
                                                ]
                                            ); ?>

                                        </a>

                                    <?php endif; ?>


                                    <?php if (
                                        !empty(
                                            $news["published_at"]
                                        )
                                    ): ?>

                                        <time>

                                            <?php echo e(
                                                format_date_bn(
                                                    $news[
                                                        "published_at"
                                                    ]
                                                )
                                            ); ?>

                                        </time>

                                    <?php endif; ?>


                                </div>


                                <!-- TITLE -->

                                <h3 class="photo-card-title">

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

                                </h3>


                                <!-- HEADLINE -->

                                <?php if (
                                    !empty(
                                        $news["headline"]
                                    )
                                ): ?>

                                    <p
                                        class="
                                            photo-card-headline
                                        "
                                    >

                                        <?php echo e(
                                            news_excerpt(
                                                $news["headline"],
                                                145
                                            )
                                        ); ?>

                                    </p>

                                <?php endif; ?>


                                <!-- META -->

                                <div class="photo-card-meta">


                                    <span>

                                        <?php if (
                                            !empty(
                                                $news["reporter"]
                                            )
                                        ): ?>

                                            রিপোর্ট:
                                            <?php echo e(
                                                $news[
                                                    "reporter"
                                                ]
                                            ); ?>

                                        <?php else: ?>

                                            <?php echo e(
                                                SITE_NAME
                                            ); ?>

                                        <?php endif; ?>

                                    </span>


                                </div>


                                <!-- READ MORE -->

                                <a
                                    href="<?php echo e(
                                        news_url(
                                            $news["slug"]
                                        )
                                    ); ?>"
                                    class="
                                        photo-card-read
                                    "
                                >

                                    বিস্তারিত পড়ুন

                                    <span>
                                        →
                                    </span>

                                </a>


                            </div>


                        </article>


                    <?php endforeach; ?>


                </div>


            <?php else: ?>


                <!-- =================================================
                     EMPTY STATE
                ================================================== -->

                <div class="premium-welcome">

                    <div class="premium-welcome-logo">

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

                    <span>
                        <?php echo e(SITE_NAME); ?>
                    </span>

                    <h1>
                        এখনো কোনো সংবাদ প্রকাশিত হয়নি
                    </h1>

                    <p>
                        Admin Panel থেকে সংবাদ Publish করলে
                        এখানে Automatic Premium Photo Card
                        তৈরি হবে।
                    </p>

                    <a
                        href="<?php echo e(
                            site_url(
                                "admin/login.php"
                            )
                        ); ?>"
                        class="premium-admin-button"
                    >
                        Admin Panel
                    </a>

                </div>


            <?php endif; ?>


        </section>


        <!-- =================================================
             FACEBOOK BOTTOM CTA
        ================================================== -->

        <section class="facebook-bottom-card">

            <div class="facebook-bottom-inner">

                <div class="facebook-bottom-icon">
                    f
                </div>

                <div class="facebook-bottom-text">

                    <span>
                        FOLLOW US ON FACEBOOK
                    </span>

                    <strong>
                        <?php echo e(SITE_NAME); ?>
                    </strong>

                    <p>
                        প্রতিদিনের সর্বশেষ খবর ও আপডেট
                        সরাসরি পেতে আমাদের Facebook Page
                        Follow করুন।
                    </p>

                </div>

                <a
                    href="<?php echo e($facebookPage); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="facebook-bottom-button"
                >
                    Follow Page →
                </a>

            </div>

        </section>


    </div>

</main>


<?php

require_once __DIR__ . "/includes/footer.php";

?>
