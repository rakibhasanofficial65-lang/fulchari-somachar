<?php

require_once "config/database.php";
require_once "config/config.php";

$slug = trim($_GET["slug"] ?? "");

if ($slug === "") {
    http_response_code(404);
    die("News not found");
}

/*
|--------------------------------------------------------------------------
| Main News
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        news.*,
        categories.name AS category_name,
        categories.slug AS category_slug
    FROM news
    INNER JOIN categories
        ON news.category_id = categories.id
    WHERE news.slug = ?
      AND news.status = 'published'
    LIMIT 1
");

$stmt->execute([$slug]);

$news = $stmt->fetch();

if (!$news) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="bn">

    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >

        <title>
            সংবাদ পাওয়া যায়নি - <?php echo htmlspecialchars(SITE_NAME, ENT_QUOTES, "UTF-8"); ?>
        </title>

        <link
            rel="stylesheet"
            href="<?php echo SITE_URL; ?>/assets/style.css"
        >

        <style>
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

            .not-found-box a {
                display: inline-block;
                margin-top: 20px;
                padding: 12px 25px;
                background: #b30000;
                color: #fff;
                text-decoration: none;
                border-radius: 5px;
            }
        </style>

    </head>

    <body>

        <?php include "includes/header.php"; ?>
        <?php include "includes/navbar.php"; ?>

        <main class="container">

            <div class="not-found-box">

                <h1>সংবাদ পাওয়া যায়নি</h1>

                <p>
                    আপনি যে সংবাদটি খুঁজছেন সেটি পাওয়া যায়নি
                    অথবা প্রকাশিত নয়।
                </p>

                <a href="<?php echo SITE_URL; ?>/">
                    হোম পেজে ফিরে যান
                </a>

            </div>

        </main>

        <?php include "includes/footer.php"; ?>

    </body>
    </html>

    <?php
    exit;
}


/*
|--------------------------------------------------------------------------
| Related News
|--------------------------------------------------------------------------
*/

$relatedStmt = $pdo->prepare("
    SELECT
        news.id,
        news.title,
        news.headline,
        news.slug,
        news.image,
        news.reporter,
        news.published_at,
        categories.name AS category_name,
        categories.slug AS category_slug
    FROM news
    INNER JOIN categories
        ON news.category_id = categories.id
    WHERE news.status = 'published'
      AND news.category_id = ?
      AND news.id != ?
    ORDER BY news.published_at DESC
    LIMIT 6
");

$relatedStmt->execute([
    $news["category_id"],
    $news["id"]
]);

$relatedNews = $relatedStmt->fetchAll();


/*
|--------------------------------------------------------------------------
| SEO
|--------------------------------------------------------------------------
*/

$rawTitle = trim($news["title"]);

$seoTitle = htmlspecialchars(
    $rawTitle . " - " . SITE_NAME,
    ENT_QUOTES,
    "UTF-8"
);

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

$seoDescription = htmlspecialchars(
    $descriptionText,
    ENT_QUOTES,
    "UTF-8"
);


/*
|--------------------------------------------------------------------------
| Clean Canonical URL
|--------------------------------------------------------------------------
*/

$canonicalUrl =
    rtrim(SITE_URL, "/") .
    "/news/" .
    rawurlencode($news["slug"]);


/*
|--------------------------------------------------------------------------
| Image URL
|--------------------------------------------------------------------------
*/

$imageUrl = "";

if (!empty($news["image"])) {

    $imageUrl =
        rtrim(SITE_URL, "/") .
        "/uploads/" .
        rawurlencode(basename($news["image"]));
}


/*
|--------------------------------------------------------------------------
| Dates
|--------------------------------------------------------------------------
*/

$datePublished = "";

if (!empty($news["published_at"])) {

    $datePublished = date(
        "c",
        strtotime($news["published_at"])
    );
}

$dateModified = $datePublished;

if (!empty($news["updated_at"])) {

    $dateModified = date(
        "c",
        strtotime($news["updated_at"])
    );
}


/*
|--------------------------------------------------------------------------
| NewsArticle Structured Data
|--------------------------------------------------------------------------
*/

$schemaData = [

    "@context" => "https://schema.org",

    "@type" => "NewsArticle",

    "mainEntityOfPage" => [
        "@type" => "WebPage",
        "@id" => $canonicalUrl
    ],

    "headline" => $rawTitle,

    "description" => $descriptionText,

    "url" => $canonicalUrl,

    "articleSection" => $news["category_name"],

    "inLanguage" => "bn-BD",

    "author" => [

        "@type" => "Person",

        "name" => !empty($news["reporter"])
            ? $news["reporter"]
            : "ফুলছড়ি সমাচার"

    ],

    "publisher" => [

        "@type" => "Organization",

        "name" => SITE_NAME,

        "url" => rtrim(SITE_URL, "/"),

        "logo" => [

            "@type" => "ImageObject",

            "url" =>
                rtrim(SITE_URL, "/") .
                "/assets/logo.png"

        ]

    ]

];


if (!empty($datePublished)) {

    $schemaData["datePublished"] = $datePublished;

}


if (!empty($dateModified)) {

    $schemaData["dateModified"] = $dateModified;

}


if (!empty($imageUrl)) {

    $schemaData["image"] = [
        $imageUrl
    ];

}

?>

<!DOCTYPE html>
<html lang="bn">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php echo $seoTitle; ?>
    </title>

    <meta
        name="description"
        content="<?php echo $seoDescription; ?>"
    >

    <link
        rel="canonical"
        href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, "UTF-8"); ?>"
    >


    <!-- Open Graph -->

    <meta
        property="og:type"
        content="article"
    >

    <meta
        property="og:title"
        content="<?php echo $seoTitle; ?>"
    >

    <meta
        property="og:description"
        content="<?php echo $seoDescription; ?>"
    >

    <meta
        property="og:url"
        content="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, "UTF-8"); ?>"
    >

    <?php if ($imageUrl): ?>

        <meta
            property="og:image"
            content="<?php echo htmlspecialchars($imageUrl, ENT_QUOTES, "UTF-8"); ?>"
        >

    <?php endif; ?>


    <!-- Twitter -->

    <meta
        name="twitter:card"
        content="summary_large_image"
    >

    <meta
        name="twitter:title"
        content="<?php echo $seoTitle; ?>"
    >

    <meta
        name="twitter:description"
        content="<?php echo $seoDescription; ?>"
    >

    <?php if ($imageUrl): ?>

        <meta
            name="twitter:image"
            content="<?php echo htmlspecialchars($imageUrl, ENT_QUOTES, "UTF-8"); ?>"
        >

    <?php endif; ?>


    <!-- NewsArticle Schema -->

    <script type="application/ld+json">
<?php
echo json_encode(
    $schemaData,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES |
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT |
    JSON_PRETTY_PRINT
);
?>
    </script>


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="<?php echo SITE_URL; ?>/assets/style.css"
    >


    <style>

        /*
        |--------------------------------------------------------------------------
        | Article Page
        |--------------------------------------------------------------------------
        */

        .article-page {
            max-width: 1100px;
            margin: 30px auto 60px;
            padding: 0 15px;
        }


        .article-breadcrumb {
            font-size: 14px;
            color: #777;
            margin-bottom: 18px;
        }

        .article-breadcrumb a {
            color: #b30000;
            text-decoration: none;
        }


        .article-category {
            display: inline-block;
            background: #b30000;
            color: #fff;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 15px;
        }


        .article-title {
            font-size: 42px;
            line-height: 1.3;
            margin: 0 0 15px;
            color: #111;
            font-weight: 800;
        }


        .article-headline {
            font-size: 21px;
            line-height: 1.7;
            color: #555;
            margin: 0 0 20px;
            font-weight: 500;
        }


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


        .article-image {
            width: 100%;
            max-height: 650px;
            object-fit: cover;
            display: block;
            border-radius: 8px;
            margin-bottom: 30px;
        }


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
        }


        .article-content h2,
        .article-content h3,
        .article-content h4 {
            margin-top: 30px;
            margin-bottom: 15px;
        }


        /*
        |--------------------------------------------------------------------------
        | Share
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Related News
        |--------------------------------------------------------------------------
        */

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
            grid-template-columns: repeat(3, 1fr);
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
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.10);
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


        /*
        |--------------------------------------------------------------------------
        | Mobile
        |--------------------------------------------------------------------------
        */

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

</head>


<body>

    <?php include "includes/header.php"; ?>

    <?php include "includes/navbar.php"; ?>


    <main class="article-page">


        <!-- Breadcrumb -->

        <div class="article-breadcrumb">

            <a href="<?php echo SITE_URL; ?>/">
                হোম
            </a>

            &nbsp; / &nbsp;

            <a
                href="<?php echo SITE_URL; ?>/category/<?php echo rawurlencode($news["category_slug"]); ?>"
            >
                <?php
                echo htmlspecialchars(
                    $news["category_name"],
                    ENT_QUOTES,
                    "UTF-8"
                );
                ?>
            </a>

            &nbsp; / &nbsp;

            সংবাদ

        </div>


        <!-- Category -->

        <div class="article-category">

            <?php
            echo htmlspecialchars(
                $news["category_name"],
                ENT_QUOTES,
                "UTF-8"
            );
            ?>

        </div>


        <!-- Title -->

        <h1 class="article-title">

            <?php
            echo htmlspecialchars(
                $news["title"],
                ENT_QUOTES,
                "UTF-8"
            );
            ?>

        </h1>


        <!-- Headline -->

        <?php if (!empty($news["headline"])): ?>

            <div class="article-headline">

                <?php
                echo htmlspecialchars(
                    $news["headline"],
                    ENT_QUOTES,
                    "UTF-8"
                );
                ?>

            </div>

        <?php endif; ?>


        <!-- Meta -->

        <div class="article-meta">

            <?php if (!empty($news["reporter"])): ?>

                <span>

                    প্রতিবেদক:

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $news["reporter"],
                            ENT_QUOTES,
                            "UTF-8"
                        );
                        ?>

                    </strong>

                </span>

            <?php endif; ?>


            <?php if (!empty($news["published_at"])): ?>

                <span>

                    প্রকাশ:

                    <strong>

                        <?php
                        echo date(
                            "d M Y, h:i A",
                            strtotime($news["published_at"])
                        );
                        ?>

                    </strong>

                </span>

            <?php endif; ?>

        </div>


        <!-- Main Image -->

        <?php if (!empty($news["image"])): ?>

            <img
                src="<?php echo $imageUrl; ?>"
                alt="<?php echo htmlspecialchars($news["title"], ENT_QUOTES, "UTF-8"); ?>"
                class="article-image"
            >

        <?php endif; ?>


        <!-- Article -->

        <article class="article-content">

            <?php
            echo $news["content"];
            ?>

        </article>


        <!-- Social Sharing -->

        <div class="share-box">

            <h3>
                সংবাদটি শেয়ার করুন
            </h3>

            <div class="share-buttons">

                <a
                    class="share-facebook"
                    href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($canonicalUrl); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Facebook
                </a>


                <a
                    class="share-whatsapp"
                    href="https://api.whatsapp.com/send?text=<?php echo urlencode($news["title"] . " " . $canonicalUrl); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    WhatsApp
                </a>


                <button
                    type="button"
                    class="share-copy"
                    onclick="copyNewsLink()"
                >
                    লিংক কপি
                </button>

            </div>

        </div>


        <!-- Related News -->

        <?php if (!empty($relatedNews)): ?>

            <section class="related-section">

                <h2>
                    আরও পড়ুন
                </h2>


                <div class="related-grid">

                    <?php foreach ($relatedNews as $item): ?>

                        <article class="related-card">


                            <?php if (!empty($item["image"])): ?>

                                <a
                                    href="<?php echo SITE_URL; ?>/news/<?php echo rawurlencode($item["slug"]); ?>"
                                >

                                    <img
                                        src="<?php echo SITE_URL; ?>/uploads/<?php echo rawurlencode(basename($item["image"])); ?>"
                                        alt="<?php echo htmlspecialchars($item["title"], ENT_QUOTES, "UTF-8"); ?>"
                                        class="related-image"
                                        loading="lazy"
                                    >

                                </a>

                            <?php endif; ?>


                            <div class="related-body">

                                <div class="related-category">

                                    <?php
                                    echo htmlspecialchars(
                                        $item["category_name"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );
                                    ?>

                                </div>


                                <h3 class="related-title">

                                    <a
                                        href="<?php echo SITE_URL; ?>/news/<?php echo rawurlencode($item["slug"]); ?>"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $item["title"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );
                                        ?>

                                    </a>

                                </h3>


                                <div class="related-date">

                                    <?php

                                    if (!empty($item["published_at"])) {

                                        echo date(
                                            "d M Y",
                                            strtotime($item["published_at"])
                                        );

                                    }

                                    ?>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            </section>

        <?php endif; ?>


    </main>


    <?php include "includes/footer.php"; ?>


    <script>

        function copyNewsLink() {

            const url =
                "<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, "UTF-8"); ?>";


            if (navigator.clipboard) {

                navigator.clipboard.writeText(url)

                    .then(function () {

                        alert("সংবাদের লিংক কপি হয়েছে।");

                    })

                    .catch(function () {

                        alert("লিংক কপি করা যায়নি।");

                    });

            }

            else {

                const textarea =
                    document.createElement("textarea");

                textarea.value = url;

                document.body.appendChild(textarea);

                textarea.select();

                document.execCommand("copy");

                textarea.remove();

                alert("সংবাদের লিংক কপি হয়েছে।");

            }

        }

    </script>


</body>
</html>