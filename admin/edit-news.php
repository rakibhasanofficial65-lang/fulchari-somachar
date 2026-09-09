<?php

session_start();

require_once dirname(__DIR__) . "/config/config.php";
require_once dirname(__DIR__) . "/config/database.php";


// =====================================================
// AUTH CHECK
// =====================================================

if (!isset($_SESSION["admin_id"])) {

    header("Location: " . SITE_URL . "/admin/login.php");
    exit;
}


// =====================================================
// GET NEWS ID
// =====================================================

$newsId = (int) ($_GET["id"] ?? 0);

if ($newsId <= 0) {

    header(
        "Location: " . SITE_URL . "/admin/news-list.php"
    );

    exit;
}


// =====================================================
// GET EXISTING NEWS
// =====================================================

$stmt = $pdo->prepare("
    SELECT *
    FROM news
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$newsId]);

$news = $stmt->fetch();

if (!$news) {

    die("সংবাদ পাওয়া যায়নি।");

}


// =====================================================
// VARIABLES
// =====================================================

$error = "";
$success = "";

$title = $news["title"];
$headline = $news["headline"];
$category_id = (int) $news["category_id"];
$content = $news["content"];
$reporter = $news["reporter"];
$status = $news["status"];
$currentImage = $news["image"];


// =====================================================
// GET CATEGORIES
// =====================================================

$categoryStmt = $pdo->query("
    SELECT
        id,
        name,
        slug
    FROM categories
    ORDER BY id ASC
");

$categories = $categoryStmt->fetchAll();


// =====================================================
// FORM SUBMIT
// =====================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $headline = trim($_POST["headline"] ?? "");
    $category_id = (int) ($_POST["category_id"] ?? 0);
    $content = trim($_POST["content"] ?? "");
    $reporter = trim($_POST["reporter"] ?? "");
    $status = $_POST["status"] ?? "draft";


    // =================================================
    // VALIDATION
    // =================================================

    if ($title === "") {

        $error = "সংবাদের Title দিন।";

    } elseif ($category_id <= 0) {

        $error = "একটি Category নির্বাচন করুন।";

    } elseif ($content === "") {

        $error = "সংবাদের Content লিখুন।";

    } elseif (!in_array($status, ["draft", "published"], true)) {

        $error = "Invalid status.";

    } else {


        // =============================================
        // CREATE SLUG
        // =============================================

        $slug = strtolower(
            trim(
                preg_replace(
                    '/[^a-zA-Z0-9]+/',
                    '-',
                    $title
                ),
                '-'
            )
        );


        if ($slug === "") {

            $slug = "news-" . $newsId;

        }


        // =============================================
        // CHECK DUPLICATE SLUG
        // =============================================

        $slugCheck = $pdo->prepare("
            SELECT id
            FROM news
            WHERE slug = ?
            AND id != ?
            LIMIT 1
        ");

        $slugCheck->execute([
            $slug,
            $newsId
        ]);


        if ($slugCheck->fetch()) {

            $slug .= "-" . $newsId;

        }


        // =============================================
        // IMAGE
        // =============================================

        $imageName = $currentImage;


        // =============================================
        // DELETE OLD IMAGE?
        // =============================================

        $removeImage =
            isset($_POST["remove_image"])
            && $_POST["remove_image"] === "1";


        if (
            $removeImage
            && !empty($currentImage)
        ) {

            $oldImagePath =
                dirname(__DIR__)
                . "/uploads/"
                . basename($currentImage);


            if (is_file($oldImagePath)) {

                unlink($oldImagePath);

            }


            $imageName = "";

        }


        // =============================================
        // NEW IMAGE UPLOAD
        // =============================================

        if (
            isset($_FILES["image"])
            && $_FILES["image"]["error"]
            !== UPLOAD_ERR_NO_FILE
        ) {


            if (
                $_FILES["image"]["error"]
                !== UPLOAD_ERR_OK
            ) {

                $error =
                    "ছবি Upload করতে সমস্যা হয়েছে।";

            } else {


                $allowedTypes = [
                    "image/jpeg",
                    "image/png",
                    "image/webp"
                ];


                $maxSize = 5 * 1024 * 1024;

                $tmpName =
                    $_FILES["image"]["tmp_name"];

                $fileSize =
                    $_FILES["image"]["size"];


                // -----------------------------------------
                // MIME TYPE
                // -----------------------------------------

                $finfo =
                    new finfo(FILEINFO_MIME_TYPE);

                $mimeType =
                    $finfo->file($tmpName);


                if (
                    !in_array(
                        $mimeType,
                        $allowedTypes,
                        true
                    )
                ) {

                    $error =
                        "শুধু JPG, PNG অথবা WebP ছবি Upload করা যাবে।";

                } elseif ($fileSize > $maxSize) {

                    $error =
                        "ছবির সর্বোচ্চ Size 5MB হতে পারবে।";

                } else {


                    $extensionMap = [

                        "image/jpeg" => "jpg",

                        "image/png" => "png",

                        "image/webp" => "webp"

                    ];


                    $extension =
                        $extensionMap[$mimeType];


                    $newImageName =
                        uniqid("news_", true)
                        . "."
                        . $extension;


                    // -----------------------------------------
                    // UPLOAD DIRECTORY
                    // -----------------------------------------

                    $uploadDirectory =
                        dirname(__DIR__)
                        . "/uploads/";


                    if (!is_dir($uploadDirectory)) {

                        mkdir(
                            $uploadDirectory,
                            0755,
                            true
                        );

                    }


                    $destination =
                        $uploadDirectory
                        . $newImageName;


                    if (
                        move_uploaded_file(
                            $tmpName,
                            $destination
                        )
                    ) {


                        // -------------------------------------
                        // DELETE PREVIOUS IMAGE
                        // -------------------------------------

                        if (
                            !empty($currentImage)
                            && $currentImage
                            !== $newImageName
                        ) {

                            $oldImagePath =
                                $uploadDirectory
                                . basename($currentImage);


                            if (
                                is_file(
                                    $oldImagePath
                                )
                            ) {

                                unlink(
                                    $oldImagePath
                                );

                            }

                        }


                        $imageName =
                            $newImageName;


                    } else {

                        $error =
                            "নতুন ছবি Save করা যায়নি।";

                    }

                }

            }

        }


        // =============================================
        // UPDATE NEWS
        // =============================================

        if ($error === "") {


            // -----------------------------------------
            // PUBLISHED DATE
            // -----------------------------------------

            if ($status === "published") {

                if (
                    !empty(
                        $news["published_at"]
                    )
                ) {

                    $publishedAt =
                        $news["published_at"];

                } else {

                    $publishedAt =
                        date("Y-m-d H:i:s");

                }

            } else {

                $publishedAt = null;

            }


            // -----------------------------------------
            // UPDATE
            // -----------------------------------------

            $updateStmt = $pdo->prepare("
                UPDATE news
                SET
                    headline = ?,
                    title = ?,
                    slug = ?,
                    category_id = ?,
                    image = ?,
                    content = ?,
                    reporter = ?,
                    status = ?,
                    published_at = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");


            $updateStmt->execute([

                $headline,

                $title,

                $slug,

                $category_id,

                $imageName,

                $content,

                $reporter,

                $status,

                $publishedAt,

                $newsId

            ]);


            $success =
                "সংবাদ সফলভাবে Update হয়েছে।";


            // -----------------------------------------
            // UPDATE CURRENT VALUES
            // -----------------------------------------

            $currentImage =
                $imageName;

            $news["image"] =
                $imageName;

            $news["slug"] =
                $slug;

            $news["published_at"] =
                $publishedAt;

        }

    }

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
        সংবাদ Edit -
        <?php echo htmlspecialchars(SITE_NAME); ?>
    </title>

    <link
        rel="stylesheet"
        href="<?php echo SITE_URL; ?>/assets/style.css"
    >


    <style>

        .admin-page {
            min-height: 100vh;
            background: #f4f4f4;
        }


        .admin-header {
            background: #111;
            color: #fff;
            padding: 18px 0;
        }


        .admin-header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }


        .admin-logo {
            font-size: 24px;
            font-weight: bold;
        }


        .admin-user {
            font-size: 14px;
        }


        .admin-user a {
            color: #fff;
            margin-left: 15px;
        }


        .admin-user a:hover {
            color: #ff4d4d;
        }


        .admin-content {
            padding: 35px 0;
        }


        .admin-title {
            margin-bottom: 25px;
        }


        .admin-title h1 {
            font-size: 30px;
            margin-bottom: 5px;
        }


        .admin-title p {
            color: #777;
        }


        .news-form-box {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 7px;
            padding: 30px;
        }


        .form-group {
            margin-bottom: 20px;
        }


        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }


        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: inherit;
            font-size: 16px;
            box-sizing: border-box;
        }


        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #b30000;
        }


        .form-group textarea {
            min-height: 300px;
            resize: vertical;
        }


        .form-help {
            display: block;
            color: #777;
            font-size: 13px;
            margin-top: 5px;
        }


        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }


        .alert {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }


        .alert-error {
            background: #ffe5e5;
            border: 1px solid #ffb3b3;
            color: #a00000;
        }


        .alert-success {
            background: #e5f8e9;
            border: 1px solid #a8dfb2;
            color: #176b2c;
        }


        .current-image-box {
            margin-top: 12px;
            padding: 12px;
            background: #f7f7f7;
            border: 1px solid #ddd;
            border-radius: 5px;
        }


        .current-image {
            display: block;
            width: 220px;
            height: 140px;
            object-fit: cover;
            border-radius: 5px;
            margin: 10px 0;
        }


        .remove-image-label {
            display: flex !important;
            align-items: center;
            gap: 7px;
            color: #b30000;
            font-weight: normal !important;
        }


        .remove-image-label input {
            width: auto;
        }


        .form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }


        .btn {
            display: inline-block;
            border: none;
            padding: 13px 22px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
        }


        .btn-primary {
            background: #b30000;
            color: #fff;
        }


        .btn-primary:hover {
            background: #8b0000;
        }


        .btn-secondary {
            background: #222;
            color: #fff;
        }


        .btn-secondary:hover {
            background: #444;
        }


        .btn-view {
            background: #176b2c;
            color: #fff;
        }


        .btn-view:hover {
            background: #10521f;
        }


        @media (max-width: 700px) {

            .admin-header-inner {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }


            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }


            .news-form-box {
                padding: 20px;
            }


            .current-image {
                width: 100%;
                height: auto;
            }


            .form-actions {
                flex-direction: column;
            }


            .btn {
                width: 100%;
                text-align: center;
            }

        }

    </style>

</head>


<body>

<div class="admin-page">


    <!-- =================================================
         HEADER
    ================================================= -->

    <header class="admin-header">

        <div class="container admin-header-inner">


            <div class="admin-logo">

                <?php echo htmlspecialchars(SITE_NAME); ?>
                — Admin

            </div>


            <div class="admin-user">

                <?php echo htmlspecialchars(
                    $_SESSION["admin_name"]
                ); ?>


                |


                <a
                    href="<?php echo SITE_URL; ?>/admin/index.php"
                >
                    Dashboard
                </a>


                <a
                    href="<?php echo SITE_URL; ?>/admin/news-list.php"
                >
                    News List
                </a>


                <a
                    href="<?php echo SITE_URL; ?>/admin/logout.php"
                >
                    Logout
                </a>

            </div>

        </div>

    </header>


    <!-- =================================================
         MAIN CONTENT
    ================================================= -->

    <main class="admin-content">

        <div class="container">


            <!-- TITLE -->

            <div class="admin-title">

                <h1>
                    সংবাদ Edit করুন
                </h1>

                <p>
                    পুরোনো সংবাদ পরিবর্তন বা Update করুন।
                </p>

            </div>


            <!-- ALERT -->

            <?php if ($error !== ""): ?>

                <div class="alert alert-error">

                    <?php echo htmlspecialchars($error); ?>

                </div>

            <?php endif; ?>


            <?php if ($success !== ""): ?>

                <div class="alert alert-success">

                    <?php echo htmlspecialchars($success); ?>

                </div>

            <?php endif; ?>


            <!-- FORM -->

            <div class="news-form-box">

                <form
                    method="POST"
                    action=""
                    enctype="multipart/form-data"
                >


                    <!-- TITLE + CATEGORY -->

                    <div class="form-row">


                        <div class="form-group">

                            <label for="title">
                                সংবাদ Title *
                            </label>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="<?php echo htmlspecialchars($title); ?>"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="category_id">
                                Category *
                            </label>

                            <select
                                id="category_id"
                                name="category_id"
                                required
                            >

                                <option value="">
                                    -- Category নির্বাচন করুন --
                                </option>


                                <?php foreach ($categories as $category): ?>

                                    <option
                                        value="<?php echo (int)$category["id"]; ?>"
                                        <?php
                                        echo (
                                            $category_id
                                            === (int)$category["id"]
                                        )
                                        ? "selected"
                                        : "";
                                        ?>
                                    >

                                        <?php echo htmlspecialchars(
                                            $category["name"]
                                        ); ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                    </div>


                    <!-- HEADLINE -->

                    <div class="form-group">

                        <label for="headline">
                            Headline
                        </label>

                        <input
                            type="text"
                            id="headline"
                            name="headline"
                            value="<?php echo htmlspecialchars($headline); ?>"
                            placeholder="সংবাদের সংক্ষিপ্ত Headline"
                        >

                    </div>


                    <!-- IMAGE -->

                    <div class="form-group">

                        <label for="image">
                            নতুন ছবি
                        </label>

                        <input
                            type="file"
                            id="image"
                            name="image"
                            accept="image/jpeg,image/png,image/webp"
                        >

                        <span class="form-help">
                            নতুন ছবি দিলে পুরোনো ছবিটি Replace হবে।
                            JPG, PNG অথবা WebP — সর্বোচ্চ 5MB।
                        </span>


                        <?php if (!empty($currentImage)): ?>

                            <div class="current-image-box">

                                <strong>
                                    বর্তমান ছবি:
                                </strong>


                                <img
                                    src="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars(basename($currentImage)); ?>"
                                    alt=""
                                    class="current-image"
                                >


                                <label class="remove-image-label">

                                    <input
                                        type="checkbox"
                                        name="remove_image"
                                        value="1"
                                    >

                                    বর্তমান ছবি Remove করুন

                                </label>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- CONTENT -->

                    <div class="form-group">

                        <label for="content">
                            সংবাদ Content *
                        </label>

                        <textarea
                            id="content"
                            name="content"
                            required
                        ><?php echo htmlspecialchars($content); ?></textarea>

                    </div>


                    <!-- REPORTER + STATUS -->

                    <div class="form-row">


                        <div class="form-group">

                            <label for="reporter">
                                Reporter
                            </label>

                            <input
                                type="text"
                                id="reporter"
                                name="reporter"
                                value="<?php echo htmlspecialchars($reporter); ?>"
                                placeholder="সাংবাদিকের নাম"
                            >

                        </div>


                        <div class="form-group">

                            <label for="status">
                                Status
                            </label>

                            <select
                                id="status"
                                name="status"
                            >

                                <option
                                    value="draft"
                                    <?php
                                    echo $status === "draft"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Draft
                                </option>


                                <option
                                    value="published"
                                    <?php
                                    echo $status === "published"
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Published
                                </option>

                            </select>

                        </div>


                    </div>


                    <!-- ACTIONS -->

                    <div class="form-actions">


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Update করুন
                        </button>


                        <a
                            href="<?php echo SITE_URL; ?>/admin/news-list.php"
                            class="btn btn-secondary"
                        >
                            News List
                        </a>


                        <?php if (!empty($news["slug"])): ?>

                            <a
                                href="<?php echo SITE_URL; ?>/news/<?php echo urlencode($news["slug"]); ?>"
                                target="_blank"
                                class="btn btn-view"
                            >
                                সংবাদ দেখুন
                            </a>

                        <?php endif; ?>


                    </div>


                </form>

            </div>


        </div>

    </main>

</div>

</body>

</html>