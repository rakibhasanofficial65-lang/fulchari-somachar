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
// VARIABLES
// =====================================================

$error = "";
$success = "";


// =====================================================
// CATEGORIES
// =====================================================

$categoryStmt = $pdo->query("
    SELECT id, name, slug
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
    $category_id = (int)($_POST["category_id"] ?? 0);
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
        // SLUG
        // =============================================

        $slug = strtolower(trim(preg_replace(
            '/[^a-zA-Z0-9]+/',
            '-',
            $title
        ), '-'));

        if ($slug === "") {

            $slug = "news-" . time();

        }


        // =============================================
        // CHECK DUPLICATE SLUG
        // =============================================

        $slugCheck = $pdo->prepare("
            SELECT id
            FROM news
            WHERE slug = ?
            LIMIT 1
        ");

        $slugCheck->execute([$slug]);

        if ($slugCheck->fetch()) {

            $slug .= "-" . time();

        }


        // =============================================
        // IMAGE UPLOAD
        // =============================================

        $imageName = "";

        if (
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {

                $error = "ছবি Upload করতে সমস্যা হয়েছে।";

            } else {

                $allowedTypes = [
                    "image/jpeg",
                    "image/png",
                    "image/webp"
                ];

                $maxSize = 5 * 1024 * 1024;

                $tmpName = $_FILES["image"]["tmp_name"];
                $originalName = $_FILES["image"]["name"];
                $fileSize = $_FILES["image"]["size"];


                // -----------------------------------------
                // REAL MIME TYPE CHECK
                // -----------------------------------------

                $finfo = new finfo(FILEINFO_MIME_TYPE);

                $mimeType = $finfo->file($tmpName);


                if (!in_array($mimeType, $allowedTypes, true)) {

                    $error = "শুধু JPG, PNG অথবা WebP ছবি Upload করা যাবে।";

                } elseif ($fileSize > $maxSize) {

                    $error = "ছবির সর্বোচ্চ Size 5MB হতে পারবে।";

                } else {

                    $extensionMap = [
                        "image/jpeg" => "jpg",
                        "image/png" => "png",
                        "image/webp" => "webp"
                    ];

                    $extension = $extensionMap[$mimeType];

                    $imageName = uniqid("news_", true) . "." . $extension;

                    $uploadDirectory = "../uploads/";

                    if (!is_dir($uploadDirectory)) {

                        mkdir($uploadDirectory, 0755, true);

                    }

                    $destination = $uploadDirectory . $imageName;


                    if (!move_uploaded_file($tmpName, $destination)) {

                        $error = "ছবি Save করা যায়নি।";

                    }

                }

            }

        }


        // =============================================
        // INSERT NEWS
        // =============================================

        if ($error === "") {

            if ($status === "published") {

                $publishedAt = date("Y-m-d H:i:s");

            } else {

                $publishedAt = null;

            }


            $stmt = $pdo->prepare("
                INSERT INTO news
                (
                    headline,
                    title,
                    slug,
                    category_id,
                    image,
                    content,
                    reporter,
                    status,
                    published_at
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                )
            ");


            $stmt->execute([
                $headline,
                $title,
                $slug,
                $category_id,
                $imageName,
                $content,
                $reporter,
                $status,
                $publishedAt
            ]);


            $success = "সংবাদ সফলভাবে Save হয়েছে।";


            // Clear form

            $title = "";
            $headline = "";
            $category_id = 0;
            $content = "";
            $reporter = "";

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
        নতুন সংবাদ - <?php echo SITE_NAME; ?>
    </title>

    <link
        rel="stylesheet"
        href="../assets/style.css"
    >

    <style>

        /* =================================================
           ADMIN
        ================================================= */

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


        /* =================================================
           CONTENT
        ================================================= */

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


        /* =================================================
           FORM
        ================================================= */

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
        }


        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #b30000;
        }


        .form-group textarea {
            min-height: 250px;
            resize: vertical;
        }


        .form-help {
            display: block;
            color: #777;
            font-size: 13px;
            margin-top: 5px;
        }


        /* =================================================
           GRID
        ================================================= */

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }


        /* =================================================
           ALERT
        ================================================= */

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


        /* =================================================
           BUTTON
        ================================================= */

        .form-actions {
            display: flex;
            gap: 12px;
            align-items: center;
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


        /* =================================================
           MOBILE
        ================================================= */

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


            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }


            .btn {
                text-align: center;
            }

        }

    </style>

</head>


<body>

<div class="admin-page">


    <!-- =================================================
         ADMIN HEADER
    ================================================= -->

    <header class="admin-header">

        <div class="container admin-header-inner">

            <div class="admin-logo">

                <?php echo SITE_NAME; ?> — Admin

            </div>


            <div class="admin-user">

                <?php echo htmlspecialchars($_SESSION["admin_name"]); ?>

                |

                <a href="index.php">
                    Dashboard
                </a>

                <a href="logout.php">
                    Logout
                </a>

            </div>

        </div>

    </header>


    <!-- =================================================
         MAIN
    ================================================= -->

    <main class="admin-content">

        <div class="container">


            <div class="admin-title">

                <h1>
                    নতুন সংবাদ যোগ করুন
                </h1>

                <p>
                    এখান থেকে নতুন সংবাদ তৈরি ও প্রকাশ করুন।
                </p>

            </div>


            <!-- =================================================
                 ALERT
            ================================================= -->

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


            <!-- =================================================
                 FORM
            ================================================= -->

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
                                value="<?php echo htmlspecialchars($title ?? ""); ?>"
                                placeholder="সংবাদের মূল শিরোনাম"
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
                                        value="<?php echo $category["id"]; ?>"
                                        <?php
                                        echo (
                                            ($category_id ?? 0)
                                            == $category["id"]
                                        )
                                        ? "selected"
                                        : "";
                                        ?>
                                    >

                                        <?php echo htmlspecialchars($category["name"]); ?>

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
                            value="<?php echo htmlspecialchars($headline ?? ""); ?>"
                            placeholder="সংবাদের সংক্ষিপ্ত Headline"
                        >

                    </div>


                    <!-- IMAGE -->

                    <div class="form-group">

                        <label for="image">
                            সংবাদ ছবি
                        </label>

                        <input
                            type="file"
                            id="image"
                            name="image"
                            accept="image/jpeg,image/png,image/webp"
                        >

                        <span class="form-help">
                            JPG, PNG অথবা WebP — সর্বোচ্চ 5MB।
                        </span>

                    </div>


                    <!-- CONTENT -->

                    <div class="form-group">

                        <label for="content">
                            সংবাদ Content *
                        </label>

                        <textarea
                            id="content"
                            name="content"
                            placeholder="এখানে বিস্তারিত সংবাদ লিখুন..."
                            required
                        ><?php echo htmlspecialchars($content ?? ""); ?></textarea>

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
                                value="<?php echo htmlspecialchars($reporter ?? ""); ?>"
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

                                <option value="draft">
                                    Draft
                                </option>

                                <option value="published">
                                    Published
                                </option>

                            </select>

                        </div>


                    </div>


                    <!-- BUTTONS -->

                    <div class="form-actions">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            সংবাদ Save করুন
                        </button>


                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >
                            Dashboard
                        </a>

                    </div>


                </form>

            </div>


        </div>

    </main>

</div>

</body>

</html>