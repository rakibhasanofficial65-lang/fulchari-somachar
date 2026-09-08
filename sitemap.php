<?php

require_once "config/database.php";
require_once "config/config.php";

header("Content-Type: application/xml; charset=utf-8");

function xmlEscape($value)
{
    return htmlspecialchars(
        $value,
        ENT_XML1 | ENT_QUOTES,
        "UTF-8"
    );
}

$baseUrl = rtrim(SITE_URL, "/");

$urls = [];

/*
|--------------------------------------------------------------------------
| Homepage
|--------------------------------------------------------------------------
*/
$urls[] = [
    "loc" => $baseUrl . "/",
    "lastmod" => date("c")
];

/*
|--------------------------------------------------------------------------
| Category URLs
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
    SELECT slug
    FROM categories
    ORDER BY id ASC
");

$categories = $stmt->fetchAll();

foreach ($categories as $category) {

    $urls[] = [
        "loc" => $baseUrl . "/category/" . rawurlencode($category["slug"]),
        "lastmod" => date("c")
    ];
}

/*
|--------------------------------------------------------------------------
| Published News URLs
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
    SELECT slug, updated_at, published_at
    FROM news
    WHERE status = 'published'
    ORDER BY published_at DESC
");

$newsList = $stmt->fetchAll();

foreach ($newsList as $news) {

    $lastmod = !empty($news["updated_at"])
        ? $news["updated_at"]
        : $news["published_at"];

    $urls[] = [
        "loc" => $baseUrl . "/news/" . rawurlencode($news["slug"]),
        "lastmod" => date("c", strtotime($lastmod))
    ];
}

/*
|--------------------------------------------------------------------------
| E-Paper
|--------------------------------------------------------------------------
*/
$urls[] = [
    "loc" => $baseUrl . "/epaper",
    "lastmod" => date("c")
];

/*
|--------------------------------------------------------------------------
| XML Output
|--------------------------------------------------------------------------
*/
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
>
<?php foreach ($urls as $url): ?>

    <url>

        <loc>
            <?php echo xmlEscape($url["loc"]); ?>
        </loc>

        <lastmod>
            <?php echo xmlEscape($url["lastmod"]); ?>
        </lastmod>

    </url>

<?php endforeach; ?>

</urlset>