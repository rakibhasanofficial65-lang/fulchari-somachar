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
// CSRF TOKEN
// =====================================================

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION["csrf_token"];


// =====================================================
// VARIABLES
// =====================================================

$error = "";
$success = "";

$title = "";
$headline = "";
$category_id = 0;
$content = "";
$reporter = "";
$status = "draft";


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

    // =================================================
    // CSRF CHECK
    // =================================================

    $submittedToken = $_POST["csrf_token"] ?? "";

    if (
        !hash_equals(
            $_SESSION["csrf_token"],
            $submittedToken
        )
    ) {

        $error = "Security verification failed. আবার চেষ্টা করুন।";

    } else {

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
            // VERIFY CATEGORY EXISTS
            // =============================================

            $categoryCheck = $pdo->prepare("
                SELECT id
                FROM categories
                WHERE id = ?
                LIMIT 1
            ");

            $categoryCheck->execute([$category_id]);

            if (!$categoryCheck->fetch()) {

                $error = "নির্বাচিত Category পাওয়া যায়নি।";

            }

        }


        // =================================================
        // CREATE SLUG
        // =================================================

        if ($error === "") {

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

                $slug = "news-" . time();

            }


            // =============================================
            // CHECK DUPLICATE SLUG
            // =============================================

            $originalSlug = $slug;
            $counter = 1;

            while (true) {

                $slugCheck = $pdo->prepare("
                    SELECT id
                    FROM news
                    WHERE slug = ?
                    LIMIT 1
                ");

                $slugCheck->execute([$slug]);

                if (!$slugCheck->fetch()) {
                    break;
                }

                $counter++;

                $slug = $originalSlug . "-" . $counter;

            }

        }


        // =================================================
        // IMAGE UPLOAD
        // =================================================

        $imageName = "";

        if (
            $error === "" &&
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {

                $error = "ছবি Upload করতে সমস্যা হয়েছে।";

            } else {

                $allowedTypes = [
                    "image/jpeg" => "jpg",
                    "image/png"  => "png",
                    "image/webp" => "webp"
                ];

                $maxSize = 5 * 1024 * 1024;

                $tmpName = $_FILES["image"]["tmp_name"];
                $fileSize = (int) $_FILES["image"]["size"];


                // -----------------------------------------
                // CHECK FILE EXISTS
                // -----------------------------------------

                if (!is_uploaded_file($tmpName)) {

                    $error = "Invalid image upload.";

                }


                // -----------------------------------------
                // FILE SIZE
                // -----------------------------------------

                if (
                    $error === "" &&
                    $fileSize <= 0
                ) {

                    $error = "ছবির File Size সঠিক নয়।";

                }


                if (
                    $error === "" &&
                    $fileSize > $maxSize
                ) {

                    $error = "ছবির সর্বোচ্চ Size 5MB হতে পারবে।";

                }


                // -----------------------------------------
                // REAL MIME TYPE CHECK
                // -----------------------------------------

                if ($error === "") {

                    $finfo = new finfo(FILEINFO_MIME_TYPE);

                    $mimeType = $finfo->file($tmpName);

                    if (!isset($allowedTypes[$mimeType])) {

                        $error = "শুধু JPG, PNG অথবা WebP ছবি Upload করা যাবে।";

                    }

                }


                // -----------------------------------------
                // CREATE FILE NAME
                // -----------------------------------------

                if ($error === "") {

                    $extension = $allowedTypes[$mimeType];

                    $imageName =
                        bin2hex(random_bytes(16))
                        . "."
                        . $extension;


                    // -------------------------------------
                    // UPLOAD DIRECTORY
                    // -------------------------------------

                    $uploadDirectory =
                        dirname(__DIR__) . "/uploads/";


                    if (!is_dir($uploadDirectory)) {

                        if (
                            !mkdir(
                                $uploadDirectory,
                                0755,
                                true
                            )
                        ) {

                            $error = "Upload directory তৈরি করা যায়নি।";

                        }

                    }


                    // -------------------------------------
                    // SAVE IMAGE
                    // -------------------------------------

                    if ($error === "") {

                        $destination =
                            $uploadDirectory . $imageName;


                        if (
                            !move_uploaded_file(
                                $tmpName,
                                $destination
                            )
                        ) {

                            $error = "ছবি Save করা যায়নি।";

                        }

                    }

                }

            }

        }


        // =================================================
        // INSERT NEWS
        // =================================================

        if ($error === "") {

            if ($status === "published") {

                $publishedAt = date("Y-m-d H:i:s");

            } else {

                $publishedAt = null;

            }


            try {

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


                // =========================================
                // SUCCESS
                // =========================================

                $success = "সংবাদ সফলভাবে Save হয়েছে।";


                // Clear form

                $title = "";
                $headline = "";
                $category_id = 0;
                $content = "";
                $reporter = "";
                $status = "draft";


                // Regenerate CSRF token

                $_SESSION["csrf_token"] =
                    bin2hex(random_bytes(32));

                $csrfToken =
                    $_SESSION["csrf_token"];


            } catch (PDOException $e) {

                // Delete uploaded image if database insert fails

                if (
                    $imageName !== "" &&
                    file_exists(
                        dirname(__DIR__) .
                        "/uploads/" .
                        $imageName
                    )
                ) {

                    @unlink(
                        dirname(__DIR__) .
                        "/uploads/" .
                        $imageName
                    );

                }


                $error =
                    "সংবাদ Save করা যায়নি। Database Error হয়েছে।";

            }

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
        নতুন সংবাদ - <?php echo htmlspecialchars(SITE_NAME); ?>
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
            min-height: 250px;
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

                <?php echo htmlspecialchars(SITE_NAME); ?> — Admin

            </div>


            <div class="admin-user">

                <?php echo htmlspecialchars($_SESSION["admin_name"]); ?>

                |

                <a href="<?php echo SITE_URL; ?>/admin/index.php">
                    Dashboard
                </a>

                <a href="<?php echo SITE_URL; ?>/admin/news-list.php">
                    সকল সংবাদ
                </a>

                <a href="<?php echo SITE_URL; ?>/admin/logout.php">
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

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php echo htmlspecialchars($csrfToken); ?>"
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
                                        value="<?php echo (int)$category["id"]; ?>"
                                        <?php
                                        echo (
                                            $category_id == $category["id"]
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
                            value="<?php echo htmlspecialchars($headline); ?>"
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
                                    <?php echo $status === "draft" ? "selected" : ""; ?>
                                >
                                    Draft
                                </option>

                                <option
                                    value="published"
                                    <?php echo $status === "published" ? "selected" : ""; ?>
                                >
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
                            href="<?php echo SITE_URL; ?>/admin/index.php"
                            class="btn btn-secondary"
                        >
                            Dashboard
                        </a>


                        <a
                            href="<?php echo SITE_URL; ?>/admin/news-list.php"
                            class="btn btn-secondary"
                        >
                            সকল সংবাদ
                        </a>

                    </div>


                </form>

            </div>


        </div>

    </main>

</div>

</body>

</html>