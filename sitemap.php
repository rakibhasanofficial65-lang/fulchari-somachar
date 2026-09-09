<?php

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/config/database.php";

header("Content-Type: application/xml; charset=utf-8");


// =====================================================
// XML ESCAPE
// =====================================================

function xml_escape($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_XML1 | ENT_QUOTES,
        "UTF-8"
    );
}


// =====================================================
// SAFE LASTMOD
// =====================================================

function sitemap_lastmod($date)
{
    if (empty($date)) {
        return date("c");
    }

    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return date("c");
    }

    return date("c", $timestamp);
}


// =====================================================
// BASE URL
// =====================================================

$baseUrl = rtrim(SITE_URL, "/");


// =====================================================
// URL COLLECTION
// =====================================================

$urls = [];


// =====================================================
// HOMEPAGE
// =====================================================

$urls[$baseUrl . "/"] = [
    "loc" => $baseUrl . "/",
    "lastmod" => date("c")
];


// =====================================================
// CATEGORY URLS
// =====================================================

$stmt = $pdo->query("
    SELECT
        slug
    FROM categories
    WHERE slug IS NOT NULL
      AND slug <> ''
    ORDER BY id ASC
");

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($categories as $category) {

    $slug = trim($category["slug"]);

    if ($slug === "") {
        continue;
    }

    $url =
        $baseUrl .
        "/category/" .
        rawurlencode($slug);

    $urls[$url] = [
        "loc" => $url,
        "lastmod" => date("c")
    ];
}


// =====================================================
// PUBLISHED NEWS URLS
// =====================================================

$stmt = $pdo->query("
    SELECT
        slug,
        updated_at,
        published_at
    FROM news
    WHERE status = 'published'
      AND slug IS NOT NULL
      AND slug <> ''
    ORDER BY
        published_at DESC,
        id DESC
");

$newsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($newsList as $news) {

    $slug = trim($news["slug"]);

    if ($slug === "") {
        continue;
    }

    $url =
        $baseUrl .
        "/news/" .
        rawurlencode($slug);

    $lastmodDate =
        !empty($news["updated_at"])
            ? $news["updated_at"]
            : $news["published_at"];

    $urls[$url] = [
        "loc" => $url,
        "lastmod" => sitemap_lastmod($lastmodDate)
    ];
}


// =====================================================
// E-PAPER
// =====================================================

$epaperUrl = $baseUrl . "/epaper";

$urls[$epaperUrl] = [
    "loc" => $epaperUrl,
    "lastmod" => date("c")
];


// =====================================================
// XML HEADER
// =====================================================

echo '<?xml version="1.0" encoding="UTF-8"?>';

?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<?php foreach ($urls as $url): ?>

    <url>

        <loc><?php echo xml_escape($url["loc"]); ?></loc>

        <lastmod><?php echo xml_escape($url["lastmod"]); ?></lastmod>

    </url>

<?php endforeach; ?>

</urlset>
