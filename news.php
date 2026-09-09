<?php

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/includes/functions.php";


// =====================================================
// GET SLUG
// =====================================================

$slug = trim($_GET["slug"] ?? "");


// =====================================================
// FIND NEWS
// =====================================================

$news = null;

if ($slug !== "") {
    $news = get_news_by_slug($pdo, $slug);
}


// =====================================================
// 404
// =====================================================

if (!$news) {

    http_response_code(404);

    $pageTitle = "সংবাদ পাওয়া যায়নি - " . SITE_NAME;

    $pageDescription =
        "আপনি যে সংবাদটি খুঁজছেন সেটি পাওয়া যায়নি।";

    $canonicalUrl = site_url("404");

    $ogImage = site_url("assets/logo.png");

    require_once __DIR__ . "/includes/header.php";
    require_once __DIR__ . "/includes/navbar.php";

    ?>

    <main class="article-page">

        <div class="not-found-box">

            <h1>
                সংবাদ পাওয়া যায়নি
            </h1>

            <p>
                আপনি যে সংবাদটি খুঁজছেন সেটি পাওয়া যায়নি
                অথবা সংবাদটি বর্তমানে প্রকাশিত নয়।
            </p>

            <a href="<?php echo e(site_url()); ?>">
                হোম পেজে ফিরে যান
            </a>

        </div>

    </main>

    <?php

    require_once __DIR__ . "/includes/footer.php";

    exit;
}


// =====================================================
// RELATED NEWS
// =====================================================

$relatedNews = get_related_news(
    $pdo,
    (int) $news["category_id"],
    (int) $news["id"],
    6
);


// =====================================================
// SEO TITLE
// =====================================================

$rawTitle = trim($news["title"]);

$pageTitle =
    $rawTitle . " - " . SITE_NAME;


// =====================================================
// SEO DESCRIPTION
// =====================================================

$descriptionText = trim(
    strip_tags(
        $news["headline"] ?: $news["content"]
    )
);

$descriptionText = preg_replace(
    "/\s+/u",
    " ",
    $descriptionText
);

$descriptionText = mb_substr(
    $descriptionText,
    0,
    160
);

$pageDescription = $descriptionText;


// =====================================================
// CANONICAL URL
// =====================================================

$canonicalUrl = news_url($news["slug"]);


// =====================================================
// IMAGE URL
// =====================================================

$imageUrl = image_url($news["image"]);


// =====================================================
// DATE
// =====================================================

$datePublished = "";

if (!empty($news["published_at"])) {

    $timestamp = strtotime($news["published_at"]);

    if ($timestamp !== false) {

        $datePublished = date(
            "c",
            $timestamp
        );
    }
}


$dateModified = $datePublished;

if (!empty($news["updated_at"])) {

    $timestamp = strtotime($news["updated_at"]);

    if ($timestamp !== false) {

        $dateModified = date(
            "c",
            $timestamp
        );
    }
}


// =====================================================
// NEWSARTICLE STRUCTURED DATA
// =====================================================

$schemaData = [

    "@context" => "https://schema.org",

    "@type" => "NewsArticle",

    "mainEntityOfPage" => [

        "@type" => "WebPage",

        "@id" => $canonicalUrl

    ],

    "headline" => $rawTitle,

    "description" => $pageDescription,

    "url" => $canonicalUrl,

    "articleSection" =>
        $news["category_name"] ?? "",

    "inLanguage" => "bn-BD",

    "author" => [

        "@type" => "Person",

        "name" =>
            !empty($news["reporter"])
                ? $news["reporter"]
                : SITE_NAME

    ],

    "publisher" => [

        "@type" => "Organization",

        "name" => SITE_NAME,

        "url" => site_url(),

        "logo" => [

            "@type" => "ImageObject",

            "url" =>
                site_url("assets/logo.png")

        ]

    ]

];


if (!empty($datePublished)) {

    $schemaData["datePublished"] =
        $datePublished;
}


if (!empty($dateModified)) {

    $schemaData["dateModified"] =
        $dateModified;
}


if (!empty($imageUrl)) {

    $schemaData["image"] = [
        $imageUrl
    ];
}


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
   ARTICLE PAGE
===================================================== */

.article-page {

    max-width: 1100px;

    margin: 30px auto 60px;

    padding: 0 15px;

}


/* =====================================================
   BREADCRUMB
===================================================== */

.article-breadcrumb {

    font-size: 14px;

    color: #777;

    margin-bottom: 18px;

}

.article-breadcrumb a {

    color: #b30000;

    text-decoration: none;

}


/* =====================================================
   CATEGORY
===================================================== */

.article-category {

    display: inline-block;

    background: #b30000;

    color: #fff;

    padding: 6px 14px;

    border-radius: 4px;

    font-size: 14px;

    margin-bottom: 15px;

}


/* =====================================================
   TITLE
===================================================== */

.article-title {

    font-size: 42px;

    line-height: 1.3;

    margin: 0 0 15px;

    color: #111;

    font-weight: 800;

}


/* =====================================================
   HEADLINE
===================================================== */

.article-headline {

    font-size: 21px;

    line-height: 1.7;

    color: #555;

    margin: 0 0 20px;

    font-weight: 500;

}


/* =====================================================
   META
===================================================== */

.article-meta {

    display: flex;

    flex-wrap: wrap;

    gap: 18px;

    align-items: center;

    border-top: 1px solid #eee;

    border-bottom: 1px solid #eee;

    padding: 13px 0;

    margin-bottom: 25px;

    color: #666;

    font-size: 14px;

}


.article-meta strong {

    color: #222;

}


/* =====================================================
   MAIN IMAGE
===================================================== */

.article-image {

    width: 100%;

    max-height: 650px;

    object-fit: cover;

    display: block;

    border-radius: 8px;

    margin-bottom: 30px;

}


/* =====================================================
   ARTICLE CONTENT
===================================================== */

.article-content {

    font-size: 19px;

    line-height: 2;

    color: #222;

    overflow-wrap: break-word;

}


.article-content p {

    margin: 0 0 20px;

}


.article-content img {

    max-width: 100%;

    height: auto;

    display: block;

    margin: 20px auto;

}


.article-content h2,
.article-content h3,
.article-content h4 {

    margin-top: 30px;

    margin-bottom: 15px;

}


/* =====================================================
   SHARE BOX
===================================================== */

.share-box {

    margin-top: 35px;

    padding: 20px;

    background: #f7f7f7;

    border: 1px solid #e5e5e5;

    border-radius: 8px;

}


.share-box h3 {

    margin: 0 0 15px;

    font-size: 18px;

}


.share-buttons {

    display: flex;

    flex-wrap: wrap;

    gap: 10px;

}


.share-buttons a {

    display: inline-block;

    padding: 9px 16px;

    border-radius: 5px;

    color: #fff;

    text-decoration: none;

    font-size: 14px;

}


.share-facebook {

    background: #1877f2;

}


.share-whatsapp {

    background: #25d366;

}


.share-copy {

    background: #333;

    cursor: pointer;

    border: none;

    color: #fff;

    padding: 9px 16px;

    border-radius: 5px;

    font-size: 14px;

}


/* =====================================================
   RELATED NEWS
===================================================== */

.related-section {

    margin-top: 55px;

    padding-top: 30px;

    border-top: 3px solid #111;

}


.related-section h2 {

    margin: 0 0 25px;

    font-size: 28px;

    border-left: 5px solid #b30000;

    padding-left: 12px;

}


.related-grid {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 22px;

}


.related-card {

    background: #fff;

    border: 1px solid #ddd;

    border-radius: 8px;

    overflow: hidden;

    transition: 0.2s ease;

}


.related-card:hover {

    transform: translateY(-3px);

    box-shadow:
        0 8px 25px rgba(0, 0, 0, 0.10);

}


.related-image {

    width: 100%;

    height: 190px;

    object-fit: cover;

    display: block;

}


.related-body {

    padding: 15px;

}


.related-category {

    font-size: 12px;

    color: #b30000;

    font-weight: bold;

    margin-bottom: 8px;

}


.related-title {

    margin: 0 0 8px;

    font-size: 18px;

    line-height: 1.5;

}


.related-title a {

    color: #111;

    text-decoration: none;

}


.related-title a:hover {

    color: #b30000;

}


.related-date {

    color: #777;

    font-size: 12px;

}


/* =====================================================
   404 BOX
===================================================== */

.not-found-box {

    max-width: 700px;

    margin: 80px auto;

    padding: 50px 25px;

    text-align: center;

    background: #fff;

    border: 1px solid #ddd;

    border-radius: 12px;

}


.not-found-box h1 {

    margin-bottom: 15px;

    color: #b30000;

}


.not-found-box p {

    color: #666;

    line-height: 1.8;

}


.not-found-box a {

    display: inline-block;

    margin-top: 20px;

    padding: 12px 25px;

    background: #b30000;

    color: #fff;

    text-decoration: none;

    border-radius: 5px;

}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 768px) {

    .article-page {

        margin-top: 20px;

    }


    .article-title {

        font-size: 30px;

    }


    .article-headline {

        font-size: 18px;

    }


    .article-content {

        font-size: 17px;

        line-height: 1.9;

    }


    .article-image {

        max-height: 400px;

    }


    .related-grid {

        grid-template-columns: 1fr;

    }


    .related-image {

        height: 220px;

    }

}


@media (max-width: 480px) {

    .article-title {

        font-size: 26px;

    }


    .article-headline {

        font-size: 17px;

    }


    .article-meta {

        gap: 8px;

        flex-direction: column;

        align-items: flex-start;

    }


    .article-content {

        font-size: 16px;

    }

}

</style>


<!-- =====================================================
     MAIN ARTICLE
===================================================== -->

<main class="article-page">


    <!-- =================================================
         BREADCRUMB
    ================================================= -->

    <div class="article-breadcrumb">

        <a href="<?php echo e(site_url()); ?>">
            হোম
        </a>

        &nbsp; / &nbsp;

        <a
            href="<?php echo e(
                category_url($news["category_slug"])
            ); ?>"
        >

            <?php echo e(
                $news["category_name"]
            ); ?>

        </a>

        &nbsp; / &nbsp;

        সংবাদ

    </div>


    <!-- =================================================
         CATEGORY
    ================================================= -->

    <div class="article-category">

        <?php echo e(
            $news["category_name"]
        ); ?>

    </div>


    <!-- =================================================
         TITLE
    ================================================= -->

    <h1 class="article-title">

        <?php echo e(
            $news["title"]
        ); ?>

    </h1>


    <!-- =================================================
         HEADLINE
    ================================================= -->

    <?php if (!empty($news["headline"])): ?>

        <div class="article-headline">

            <?php echo e(
                $news["headline"]
            ); ?>

        </div>

    <?php endif; ?>


    <!-- =================================================
         META
    ================================================= -->

    <div class="article-meta">


        <?php if (!empty($news["reporter"])): ?>

            <span>

                প্রতিবেদক:

                <strong>

                    <?php echo e(
                        $news["reporter"]
                    ); ?>

                </strong>

            </span>

        <?php endif; ?>


        <?php if (!empty($news["published_at"])): ?>

            <span>

                প্রকাশ:

                <strong>

                    <?php echo e(
                        format_date_bn(
                            $news["published_at"]
                        )
                    ); ?>

                </strong>

            </span>

        <?php endif; ?>


    </div>


    <!-- =================================================
         MAIN IMAGE
    ================================================= -->

    <?php if (!empty($news["image"])): ?>

        <img
            src="<?php echo e($imageUrl); ?>"
            alt="<?php echo e($news["title"]); ?>"
            class="article-image"
            fetchpriority="high"
        >

    <?php endif; ?>


    <!-- =================================================
         ARTICLE CONTENT
    ================================================= -->

    <article class="article-content">

        <?php

        /*
         * Content field intentionally supports HTML
         * for article formatting.
         *
         * IMPORTANT:
         * Admin/editor input should be trusted or
         * sanitized before storing HTML.
         */

        echo $news["content"];

        ?>

    </article>


    <!-- =================================================
         SOCIAL SHARING
    ================================================= -->

    <div class="share-box">

        <h3>
            সংবাদটি শেয়ার করুন
        </h3>


        <div class="share-buttons">


            <!-- Facebook -->

            <a
                class="share-facebook"
                href="https://www.facebook.com/sharer/sharer.php?u=<?php
                    echo rawurlencode($canonicalUrl);
                ?>"
                target="_blank"
                rel="noopener noreferrer"
            >
                Facebook
            </a>


            <!-- WhatsApp -->

            <a
                class="share-whatsapp"
                href="https://api.whatsapp.com/send?text=<?php
                    echo rawurlencode(
                        $news["title"] .
                        " " .
                        $canonicalUrl
                    );
                ?>"
                target="_blank"
                rel="noopener noreferrer"
            >
                WhatsApp
            </a>


            <!-- Copy -->

            <button
                type="button"
                class="share-copy"
                onclick="copyNewsLink()"
            >
                লিংক কপি
            </button>


        </div>

    </div>


    <!-- =================================================
         RELATED NEWS
    ================================================= -->

    <?php if (!empty($relatedNews)): ?>

        <section class="related-section">

            <h2>
                আরও পড়ুন
            </h2>


            <div class="related-grid">


                <?php foreach ($relatedNews as $item): ?>


                    <article class="related-card">


                        <!-- IMAGE -->

                        <?php if (!empty($item["image"])): ?>

                            <a
                                href="<?php echo e(
                                    news_url($item["slug"])
                                ); ?>"
                            >

                                <img
                                    src="<?php echo e(
                                        image_url($item["image"])
                                    ); ?>"
                                    alt="<?php echo e(
                                        $item["title"]
                                    ); ?>"
                                    class="related-image"
                                    loading="lazy"
                                    decoding="async"
                                >

                            </a>

                        <?php endif; ?>


                        <!-- BODY -->

                        <div class="related-body">


                            <!-- CATEGORY -->

                            <?php if (
                                !empty($item["category_name"])
                            ): ?>

                                <div class="related-category">

                                    <?php echo e(
                                        $item["category_name"]
                                    ); ?>

                                </div>

                            <?php endif; ?>


                            <!-- TITLE -->

                            <h3 class="related-title">

                                <a
                                    href="<?php echo e(
                                        news_url($item["slug"])
                                    ); ?>"
                                >

                                    <?php echo e(
                                        $item["title"]
                                    ); ?>

                                </a>

                            </h3>


                            <!-- DATE -->

                            <?php if (
                                !empty($item["published_at"])
                            ): ?>

                                <div class="related-date">

                                    <?php echo e(
                                        format_date_bn(
                                            $item["published_at"]
                                        )
                                    ); ?>

                                </div>

                            <?php endif; ?>


                        </div>


                    </article>


                <?php endforeach; ?>


            </div>

        </section>

    <?php endif; ?>


</main>


<?php

// =====================================================
// FOOTER
// =====================================================

require_once __DIR__ . "/includes/footer.php";

?>


<!-- =====================================================
     COPY LINK SCRIPT
===================================================== -->

<script>

function copyNewsLink() {

    const url =
        <?php echo json_encode(
            $canonicalUrl,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        ); ?>;


    if (
        navigator.clipboard &&
        window.isSecureContext
    ) {

        navigator.clipboard
            .writeText(url)
            .then(function () {

                alert(
                    "সংবাদের লিংক কপি হয়েছে।"
                );

            })
            .catch(function () {

                fallbackCopy(url);

            });

    } else {

        fallbackCopy(url);

    }

}


function fallbackCopy(url) {

    const textarea =
        document.createElement("textarea");

    textarea.value = url;

    textarea.style.position = "fixed";

    textarea.style.left = "-9999px";

    document.body.appendChild(textarea);

    textarea.focus();

    textarea.select();

    try {

        document.execCommand("copy");

        alert(
            "সংবাদের লিংক কপি হয়েছে।"
        );

    } catch (error) {

        alert(
            "লিংক কপি করা যায়নি।"
        );

    }

    textarea.remove();

}

</script>


<!-- =====================================================
     NEWSARTICLE JSON-LD
===================================================== -->

<script type="application/ld+json">

<?php

echo json_encode(
    $schemaData,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES |
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT
);

?>

</script>