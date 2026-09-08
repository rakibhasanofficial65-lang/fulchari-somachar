<?php

session_start();

require_once "../config/config.php";
require_once "../config/database.php";


// =====================================================
// AUTH CHECK
// =====================================================

if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");

    exit;
}


// =====================================================
// CSRF TOKEN
// =====================================================

if (empty($_SESSION["csrf_token"])) {

    $_SESSION["csrf_token"] = bin2hex(
        random_bytes(32)
    );
}

$csrfToken = $_SESSION["csrf_token"];


// =====================================================
// DELETE NEWS
// =====================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["delete_news"])
) {

    $postedToken = $_POST["csrf_token"] ?? "";

    if (
        !hash_equals(
            $_SESSION["csrf_token"],
            $postedToken
        )
    ) {

        die("Invalid CSRF token.");

    }


    $deleteId = filter_input(
        INPUT_POST,
        "delete_id",
        FILTER_VALIDATE_INT
    );


    if ($deleteId) {

        // First find image
        $imageStmt = $pdo->prepare("
            SELECT image
            FROM news
            WHERE id = ?
            LIMIT 1
        ");

        $imageStmt->execute([
            $deleteId
        ]);

        $newsToDelete = $imageStmt->fetch();


        // Delete database row
        $deleteStmt = $pdo->prepare("
            DELETE FROM news
            WHERE id = ?
        ");

        $deleteStmt->execute([
            $deleteId
        ]);


        // Delete image file
        if (
            $newsToDelete
            && !empty($newsToDelete["image"])
        ) {

            $imagePath =
                "../uploads/"
                . basename($newsToDelete["image"]);


            if (is_file($imagePath)) {

                unlink($imagePath);

            }

        }

    }


    header(
        "Location: news-list.php?deleted=1"
    );

    exit;
}


// =====================================================
// FILTERS
// =====================================================

$search = trim(
    $_GET["search"] ?? ""
);

$categoryId = filter_input(
    INPUT_GET,
    "category",
    FILTER_VALIDATE_INT
);

$status = trim(
    $_GET["status"] ?? ""
);


// =====================================================
// CATEGORIES
// =====================================================

$categoryStmt = $pdo->query("
    SELECT
        id,
        name
    FROM categories
    ORDER BY id ASC
");

$categories = $categoryStmt->fetchAll();


// =====================================================
// NEWS QUERY
// =====================================================

$sql = "
    SELECT
        news.*,
        categories.name AS category_name
    FROM news
    LEFT JOIN categories
        ON news.category_id = categories.id
    WHERE 1=1
";

$params = [];


// Search
if ($search !== "") {

    $sql .= "
        AND (
            news.title LIKE ?
            OR news.headline LIKE ?
            OR news.reporter LIKE ?
        )
    ";

    $keyword = "%" . $search . "%";

    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
}


// Category
if ($categoryId) {

    $sql .= "
        AND news.category_id = ?
    ";

    $params[] = $categoryId;
}


// Status
if (
    $status === "published"
    || $status === "draft"
) {

    $sql .= "
        AND news.status = ?
    ";

    $params[] = $status;
}


$sql .= "
    ORDER BY
        news.created_at DESC,
        news.id DESC
    LIMIT 100
";


$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$newsList = $stmt->fetchAll();


// =====================================================
// HELPER
// =====================================================

function e($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
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
        সংবাদ তালিকা | <?php echo e(SITE_NAME); ?>
    </title>

    <link
        rel="stylesheet"
        href="<?php echo SITE_URL; ?>/assets/style.css"
    >


    <style>

        /* =================================================
           ADMIN PAGE
        ================================================= */

        body {
            margin: 0;
            background: #f3f3f3;
        }


        .admin-page {

            max-width: 1250px;

            margin: 35px auto 60px;

            padding: 0 15px;

        }


        .admin-header {

            background: #111;

            color: #fff;

            padding: 20px 25px;

            border-radius: 8px;

            margin-bottom: 20px;

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 15px;

        }


        .admin-header h1 {

            margin: 0;

            font-size: 26px;

        }


        .admin-header-actions {

            display: flex;

            gap: 8px;

            flex-wrap: wrap;

        }


        .admin-btn {

            display: inline-block;

            text-decoration: none;

            border: 0;

            padding: 9px 15px;

            border-radius: 5px;

            cursor: pointer;

            font-size: 14px;

            font-weight: bold;

        }


        .btn-home {

            background: #fff;

            color: #111;

        }


        .btn-add {

            background: #b30000;

            color: #fff;

        }


        .btn-logout {

            background: #555;

            color: #fff;

        }


        /* =================================================
           SUCCESS
        ================================================= */

        .success-message {

            background: #e8f7e8;

            color: #176b17;

            border: 1px solid #a9dca9;

            padding: 13px 16px;

            border-radius: 6px;

            margin-bottom: 20px;

        }


        /* =================================================
           FILTER
        ================================================= */

        .filter-box {

            background: #fff;

            border: 1px solid #ddd;

            padding: 20px;

            border-radius: 8px;

            margin-bottom: 20px;

        }


        .filter-form {

            display: grid;

            grid-template-columns:
                2fr 1fr 1fr auto;

            gap: 10px;

            align-items: end;

        }


        .filter-group label {

            display: block;

            margin-bottom: 6px;

            font-size: 13px;

            font-weight: bold;

            color: #555;

        }


        .filter-input,
        .filter-select {

            width: 100%;

            box-sizing: border-box;

            padding: 10px 12px;

            border: 1px solid #ccc;

            border-radius: 5px;

            background: #fff;

            font-size: 14px;

        }


        .filter-button {

            padding: 10px 18px;

            border: 0;

            border-radius: 5px;

            background: #b30000;

            color: #fff;

            font-weight: bold;

            cursor: pointer;

        }


        .reset-link {

            display: inline-block;

            margin-left: 8px;

            padding: 10px 14px;

            background: #eee;

            color: #333;

            border-radius: 5px;

            text-decoration: none;

            font-size: 14px;

        }


        /* =================================================
           TABLE
        ================================================= */

        .news-table-box {

            background: #fff;

            border: 1px solid #ddd;

            border-radius: 8px;

            overflow-x: auto;

        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 900px;

        }


        th {

            background: #111;

            color: #fff;

            padding: 13px 10px;

            text-align: left;

            font-size: 14px;

            white-space: nowrap;

        }


        td {

            padding: 12px 10px;

            border-bottom: 1px solid #eee;

            vertical-align: middle;

            font-size: 14px;

        }


        tr:hover td {

            background: #fafafa;

        }


        .news-thumb {

            width: 75px;

            height: 55px;

            object-fit: cover;

            border-radius: 5px;

            display: block;

        }


        .no-thumb {

            width: 75px;

            height: 55px;

            border-radius: 5px;

            background: #eee;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #888;

            font-size: 11px;

        }


        .news-title {

            font-weight: bold;

            color: #222;

            line-height: 1.5;

            max-width: 330px;

        }


        .news-headline {

            color: #777;

            font-size: 12px;

            margin-top: 4px;

            max-width: 330px;

        }


        /* =================================================
           STATUS
        ================================================= */

        .status {

            display: inline-block;

            padding: 5px 9px;

            border-radius: 4px;

            font-size: 12px;

            font-weight: bold;

        }


        .status-published {

            background: #dff4df;

            color: #176b17;

        }


        .status-draft {

            background: #fff0d6;

            color: #9a6200;

        }


        /* =================================================
           ACTIONS
        ================================================= */

        .action-buttons {

            display: flex;

            gap: 7px;

            align-items: center;

        }


        .edit-btn {

            background: #222;

            color: #fff;

            padding: 7px 11px;

            border-radius: 4px;

            text-decoration: none;

            font-size: 12px;

        }


        .delete-btn {

            background: #b30000;

            color: #fff;

            padding: 7px 11px;

            border: 0;

            border-radius: 4px;

            cursor: pointer;

            font-size: 12px;

        }


        /* =================================================
           EMPTY
        ================================================= */

        .empty-state {

            text-align: center;

            padding: 60px 20px;

            color: #777;

        }


        .empty-state h2 {

            color: #333;

            margin-bottom: 8px;

        }


        /* =================================================
           MOBILE
        ================================================= */

        @media (max-width: 800px) {

            .admin-header {

                flex-direction: column;

                align-items: flex-start;

            }


            .filter-form {

                grid-template-columns: 1fr;

            }


            .reset-link {

                margin-left: 0;

                margin-top: 7px;

            }

        }

    </style>

</head>


<body>


<div class="admin-page">


    <!-- =================================================
         HEADER
    ================================================= -->

    <div class="admin-header">

        <h1>
            সংবাদ তালিকা
        </h1>


        <div class="admin-header-actions">

            <a
                href="<?php echo SITE_URL; ?>/index.php"
                class="admin-btn btn-home"
            >
                ওয়েবসাইট
            </a>


            <a
                href="add-news.php"
                class="admin-btn btn-add"
            >
                + সংবাদ যোগ করুন
            </a>


            <a
                href="logout.php"
                class="admin-btn btn-logout"
            >
                Logout
            </a>

        </div>

    </div>


    <!-- =================================================
         SUCCESS MESSAGE
    ================================================= -->

    <?php if (isset($_GET["deleted"])): ?>

        <div class="success-message">

            সংবাদ সফলভাবে মুছে ফেলা হয়েছে।

        </div>

    <?php endif; ?>


    <!-- =================================================
         FILTER
    ================================================= -->

    <div class="filter-box">

        <form
            method="GET"
            action="news-list.php"
            class="filter-form"
        >


            <div class="filter-group">

                <label>
                    সংবাদ খুঁজুন
                </label>

                <input
                    type="text"
                    name="search"
                    class="filter-input"
                    value="<?php echo e($search); ?>"
                    placeholder="শিরোনাম / হেডলাইন / রিপোর্টার"
                >

            </div>


            <div class="filter-group">

                <label>
                    ক্যাটাগরি
                </label>

                <select
                    name="category"
                    class="filter-select"
                >

                    <option value="">
                        সব ক্যাটাগরি
                    </option>


                    <?php foreach ($categories as $category): ?>

                        <option
                            value="<?php echo (int)$category["id"]; ?>"
                            <?php
                            echo (
                                $categoryId
                                == $category["id"]
                            )
                            ? "selected"
                            : "";
                            ?>
                        >

                            <?php
                            echo e(
                                $category["name"]
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="filter-group">

                <label>
                    স্ট্যাটাস
                </label>

                <select
                    name="status"
                    class="filter-select"
                >

                    <option value="">
                        সব
                    </option>


                    <option
                        value="published"
                        <?php
                        echo (
                            $status === "published"
                        )
                        ? "selected"
                        : "";
                        ?>
                    >
                        Published
                    </option>


                    <option
                        value="draft"
                        <?php
                        echo (
                            $status === "draft"
                        )
                        ? "selected"
                        : "";
                        ?>
                    >
                        Draft
                    </option>

                </select>

            </div>


            <div class="filter-group">

                <button
                    type="submit"
                    class="filter-button"
                >
                    Filter
                </button>


                <a
                    href="news-list.php"
                    class="reset-link"
                >
                    Reset
                </a>

            </div>


        </form>

    </div>


    <!-- =================================================
         NEWS TABLE
    ================================================= -->

    <div class="news-table-box">


        <?php if (!empty($newsList)): ?>


            <table>

                <thead>

                    <tr>

                        <th>
                            ছবি
                        </th>

                        <th>
                            সংবাদ
                        </th>

                        <th>
                            ক্যাটাগরি
                        </th>

                        <th>
                            রিপোর্টার
                        </th>

                        <th>
                            স্ট্যাটাস
                        </th>

                        <th>
                            তারিখ
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php foreach ($newsList as $news): ?>


                        <tr>


                            <!-- IMAGE -->

                            <td>

                                <?php if (!empty($news["image"])): ?>

                                    <img
                                        src="<?php echo SITE_URL; ?>/uploads/<?php echo e($news["image"]); ?>"
                                        alt=""
                                        class="news-thumb"
                                    >

                                <?php else: ?>

                                    <div class="no-thumb">
                                        No Image
                                    </div>

                                <?php endif; ?>

                            </td>


                            <!-- NEWS -->

                            <td>

                                <div class="news-title">

                                    <?php
                                    echo e(
                                        $news["title"]
                                    );
                                    ?>

                                </div>


                                <?php if (!empty($news["headline"])): ?>

                                    <div class="news-headline">

                                        <?php
                                        echo e(
                                            mb_substr(
                                                $news["headline"],
                                                0,
                                                100
                                            )
                                        );
                                        ?>

                                    </div>

                                <?php endif; ?>

                            </td>


                            <!-- CATEGORY -->

                            <td>

                                <?php
                                echo e(
                                    $news["category_name"]
                                    ?? "-"
                                );
                                ?>

                            </td>


                            <!-- REPORTER -->

                            <td>

                                <?php
                                echo !empty(
                                    $news["reporter"]
                                )
                                ? e(
                                    $news["reporter"]
                                )
                                : "-";
                                ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <?php if (
                                    $news["status"]
                                    === "published"
                                ): ?>

                                    <span
                                        class="status status-published"
                                    >
                                        Published
                                    </span>

                                <?php else: ?>

                                    <span
                                        class="status status-draft"
                                    >
                                        Draft
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- DATE -->

                            <td>

                                <?php

                                if (
                                    !empty(
                                        $news["published_at"]
                                    )
                                ) {

                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $news["published_at"]
                                        )
                                    );

                                } else {

                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $news["created_at"]
                                        )
                                    );

                                }

                                ?>

                            </td>


                            <!-- ACTION -->

                            <td>

                                <div class="action-buttons">


                                    <a
                                        href="edit-news.php?id=<?php echo (int)$news["id"]; ?>"
                                        class="edit-btn"
                                    >
                                        Edit
                                    </a>


                                    <form
                                        method="POST"
                                        action="news-list.php"
                                        onsubmit="return confirm('আপনি কি নিশ্চিত যে এই সংবাদটি মুছে ফেলতে চান?');"
                                        style="margin:0;"
                                    >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?php echo e($csrfToken); ?>"
                                        >


                                        <input
                                            type="hidden"
                                            name="delete_id"
                                            value="<?php echo (int)$news["id"]; ?>"
                                        >


                                        <button
                                            type="submit"
                                            name="delete_news"
                                            class="delete-btn"
                                        >
                                            Delete
                                        </button>

                                    </form>


                                </div>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>

            </table>


        <?php else: ?>


            <div class="empty-state">

                <h2>
                    কোনো সংবাদ পাওয়া যায়নি
                </h2>

                <p>
                    আপনার search/filter পরিবর্তন করে আবার চেষ্টা করুন।
                </p>

            </div>


        <?php endif; ?>


    </div>


</div>


</body>

</html>