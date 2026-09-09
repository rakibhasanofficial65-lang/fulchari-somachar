<?php

require_once dirname(__DIR__) . "/config/config.php";


// =====================================================
// HTML ESCAPE
// =====================================================

if (!function_exists("e")) {

    function e($value)
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            "UTF-8"
        );
    }

}


// =====================================================
// SITE URL
// =====================================================

if (!function_exists("site_url")) {

    function site_url($path = "")
    {
        $base = rtrim(SITE_URL, "/");

        $path = trim((string) $path);

        if ($path === "") {
            return $base . "/";
        }

        return $base . "/" . ltrim($path, "/");
    }

}


// =====================================================
// NEWS URL
// =====================================================

if (!function_exists("news_url")) {

    function news_url($slug)
    {
        return site_url(
            "news/" . rawurlencode((string) $slug)
        );
    }

}


// =====================================================
// CATEGORY URL
// =====================================================

if (!function_exists("category_url")) {

    function category_url($slug)
    {
        return site_url(
            "category/" . rawurlencode((string) $slug)
        );
    }

}


// =====================================================
// EPAPER URL
// =====================================================

if (!function_exists("epaper_url")) {

    function epaper_url($date = "")
    {
        $url = site_url("epaper");

        if ($date !== "") {
            $url .= "?date=" . rawurlencode((string) $date);
        }

        return $url;
    }

}


// =====================================================
// SEARCH URL
// =====================================================

if (!function_exists("search_url")) {

    function search_url($query = "")
    {
        if ($query === "") {
            return site_url("search");
        }

        return site_url(
            "search/" . rawurlencode((string) $query)
        );
    }

}


// =====================================================
// IMAGE URL
// =====================================================

if (!function_exists("image_url")) {

    function image_url($image)
    {
        $image = trim((string) $image);

        if ($image === "") {
            return site_url("assets/logo.png");
        }

        // Already an absolute URL
        if (
            preg_match(
                '#^https?://#i',
                $image
            )
        ) {
            return $image;
        }

        return site_url(
            "uploads/" . rawurlencode(basename($image))
        );
    }

}


// =====================================================
// DATE FORMAT
// =====================================================

if (!function_exists("format_date_bn")) {

    function format_date_bn($date)
    {
        if (empty($date)) {
            return "";
        }

        $timestamp = strtotime($date);

        if ($timestamp === false) {
            return "";
        }

        $months = [
            1  => "জানুয়ারি",
            2  => "ফেব্রুয়ারি",
            3  => "মার্চ",
            4  => "এপ্রিল",
            5  => "মে",
            6  => "জুন",
            7  => "জুলাই",
            8  => "আগস্ট",
            9  => "সেপ্টেম্বর",
            10 => "অক্টোবর",
            11 => "নভেম্বর",
            12 => "ডিসেম্বর"
        ];

        $day = date("j", $timestamp);
        $month = $months[(int) date("n", $timestamp)];
        $year = date("Y", $timestamp);

        return $day . " " . $month . " " . $year;
    }

}


// =====================================================
// BANGLA NUMBER FORMAT
// =====================================================

if (!function_exists("bn_number")) {

    function bn_number($number)
    {
        $english = [
            "0",
            "1",
            "2",
            "3",
            "4",
            "5",
            "6",
            "7",
            "8",
            "9"
        ];

        $bangla = [
            "০",
            "১",
            "২",
            "৩",
            "৪",
            "৫",
            "৬",
            "৭",
            "৮",
            "৯"
        ];

        return str_replace(
            $english,
            $bangla,
            (string) $number
        );
    }

}


// =====================================================
// NEWS EXCERPT
// =====================================================

if (!function_exists("news_excerpt")) {

    function news_excerpt($text, $length = 160)
    {
        $text = trim(
            preg_replace(
                '/\s+/u',
                " ",
                strip_tags((string) $text)
            )
        );

        if ($text === "") {
            return "";
        }

        if (mb_strlen($text, "UTF-8") <= $length) {
            return $text;
        }

        return mb_substr(
            $text,
            0,
            $length,
            "UTF-8"
        ) . "…";
    }

}


// =====================================================
// CREATE ASCII SLUG
// =====================================================

if (!function_exists("create_slug")) {

    function create_slug($title)
    {
        $slug = strtolower(
            trim(
                preg_replace(
                    '/[^a-zA-Z0-9]+/',
                    '-',
                    (string) $title
                ),
                '-'
            )
        );

        if ($slug === "") {
            $slug = "news-" . time();
        }

        return $slug;
    }

}


// =====================================================
// SAFE REDIRECT
// =====================================================

if (!function_exists("redirect")) {

    function redirect($url)
    {
        header(
            "Location: " . $url,
            true,
            302
        );

        exit;
    }

}


// =====================================================
// CURRENT REQUEST PATH
// =====================================================

if (!function_exists("current_path")) {

    function current_path()
    {
        $path = parse_url(
            $_SERVER["REQUEST_URI"] ?? "/",
            PHP_URL_PATH
        );

        return $path ?: "/";
    }

}


// =====================================================
// CURRENT YEAR
// =====================================================

if (!function_exists("current_year")) {

    function current_year()
    {
        return date("Y");
    }

}


// =====================================================
// PUBLISHED NEWS QUERY
// =====================================================

if (!function_exists("get_published_news")) {

    function get_published_news(
        PDO $pdo,
        $limit = 12,
        $offset = 0
    ) {
        $limit = max(1, (int) $limit);
        $offset = max(0, (int) $offset);

        $stmt = $pdo->prepare("
            SELECT
                n.id,
                n.headline,
                n.title,
                n.slug,
                n.category_id,
                n.image,
                n.content,
                n.reporter,
                n.status,
                n.published_at,
                n.created_at,
                n.updated_at,
                c.name AS category_name,
                c.slug AS category_slug
            FROM news n
            INNER JOIN categories c
                ON c.id = n.category_id
            WHERE n.status = 'published'
            ORDER BY n.published_at DESC, n.id DESC
            LIMIT {$limit}
            OFFSET {$offset}
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

}


// =====================================================
// GET SINGLE PUBLISHED NEWS
// =====================================================

if (!function_exists("get_news_by_slug")) {

    function get_news_by_slug(PDO $pdo, $slug)
    {
        $stmt = $pdo->prepare("
            SELECT
                n.id,
                n.headline,
                n.title,
                n.slug,
                n.category_id,
                n.image,
                n.content,
                n.reporter,
                n.status,
                n.published_at,
                n.created_at,
                n.updated_at,
                c.name AS category_name,
                c.slug AS category_slug
            FROM news n
            INNER JOIN categories c
                ON c.id = n.category_id
            WHERE n.slug = ?
              AND n.status = 'published'
            LIMIT 1
        ");

        $stmt->execute([
            $slug
        ]);

        $news = $stmt->fetch();

        return $news ?: null;
    }

}


// =====================================================
// GET NEWS BY CATEGORY
// =====================================================

if (!function_exists("get_news_by_category")) {

    function get_news_by_category(
        PDO $pdo,
        $categorySlug,
        $limit = 30
    ) {
        $limit = max(1, (int) $limit);

        $stmt = $pdo->prepare("
            SELECT
                n.id,
                n.headline,
                n.title,
                n.slug,
                n.category_id,
                n.image,
                n.content,
                n.reporter,
                n.status,
                n.published_at,
                n.created_at,
                n.updated_at,
                c.name AS category_name,
                c.slug AS category_slug
            FROM news n
            INNER JOIN categories c
                ON c.id = n.category_id
            WHERE c.slug = ?
              AND n.status = 'published'
            ORDER BY n.published_at DESC, n.id DESC
            LIMIT {$limit}
        ");

        $stmt->execute([
            $categorySlug
        ]);

        return $stmt->fetchAll();
    }

}


// =====================================================
// GET RELATED NEWS
// =====================================================

if (!function_exists("get_related_news")) {

    function get_related_news(
        PDO $pdo,
        $categoryId,
        $excludeId,
        $limit = 6
    ) {
        $limit = max(1, (int) $limit);

        $stmt = $pdo->prepare("
            SELECT
                n.id,
                n.headline,
                n.title,
                n.slug,
                n.category_id,
                n.image,
                n.content,
                n.reporter,
                n.status,
                n.published_at,
                n.created_at,
                n.updated_at,
                c.name AS category_name,
                c.slug AS category_slug
            FROM news n
            INNER JOIN categories c
                ON c.id = n.category_id
            WHERE n.category_id = ?
              AND n.id != ?
              AND n.status = 'published'
            ORDER BY n.published_at DESC, n.id DESC
            LIMIT {$limit}
        ");

        $stmt->execute([
            $categoryId,
            $excludeId
        ]);

        return $stmt->fetchAll();
    }

}


// =====================================================
// GET CATEGORY
// =====================================================

if (!function_exists("get_category_by_slug")) {

    function get_category_by_slug(PDO $pdo, $slug)
    {
        $stmt = $pdo->prepare("
            SELECT
                id,
                name,
                slug
            FROM categories
            WHERE slug = ?
            LIMIT 1
        ");

        $stmt->execute([
            $slug
        ]);

        $category = $stmt->fetch();

        return $category ?: null;
    }

}
