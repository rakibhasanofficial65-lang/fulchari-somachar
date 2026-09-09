<?php

session_start();

require_once dirname(__DIR__) . "/config/config.php";
require_once dirname(__DIR__) . "/config/database.php";

// ================= AUTH CHECK =================

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}


// ================= DASHBOARD COUNTS =================

$totalNews = $pdo->query("
    SELECT COUNT(*) 
    FROM news
")->fetchColumn();


$publishedNews = $pdo->query("
    SELECT COUNT(*) 
    FROM news
    WHERE status = 'published'
")->fetchColumn();


$draftNews = $pdo->query("
    SELECT COUNT(*) 
    FROM news
    WHERE status = 'draft'
")->fetchColumn();


$totalCategories = $pdo->query("
    SELECT COUNT(*) 
    FROM categories
")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="bn">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Admin Dashboard - <?php echo SITE_NAME; ?>
    </title>

    <link rel="stylesheet" href="../assets/style.css">

    <style>

        /* ================= ADMIN ================= */

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


        /* ================= CARDS ================= */

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .dashboard-card {
            background: #fff;
            padding: 25px;
            border-radius: 7px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .dashboard-card h3 {
            font-size: 16px;
            color: #777;
            margin-bottom: 10px;
        }

        .dashboard-number {
            font-size: 34px;
            font-weight: bold;
            color: #b30000;
        }


        /* ================= MENU ================= */

        .admin-menu {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 7px;
            padding: 25px;
        }

        .admin-menu h2 {
            margin-bottom: 20px;
            font-size: 22px;
        }

        .admin-actions {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .admin-action {
            display: block;
            background: #111;
            color: #fff;
            padding: 18px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .admin-action:hover {
            background: #b30000;
        }


        /* ================= MOBILE ================= */

        @media (max-width: 800px) {

            .dashboard-cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .admin-actions {
                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 500px) {

            .admin-header-inner {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .dashboard-cards {
                grid-template-columns: 1fr;
            }

            .admin-title h1 {
                font-size: 25px;
            }

        }

    </style>

</head>

<body>

<div class="admin-page">


    <!-- ================= ADMIN HEADER ================= -->

    <header class="admin-header">

        <div class="container admin-header-inner">

            <div class="admin-logo">
                <?php echo SITE_NAME; ?> — Admin
            </div>

            <div class="admin-user">

                Welcome,
                <?php echo htmlspecialchars($_SESSION["admin_name"]); ?>

                |

                <a href="logout.php">
                    Logout
                </a>

            </div>

        </div>

    </header>


    <!-- ================= CONTENT ================= -->

    <main class="admin-content">

        <div class="container">


            <!-- TITLE -->

            <div class="admin-title">

                <h1>
                    Dashboard
                </h1>

                <p>
                    ওয়েবসাইটের সকল কার্যক্রম এখান থেকে পরিচালনা করুন।
                </p>

            </div>


            <!-- ================= STATISTICS ================= -->

            <div class="dashboard-cards">


                <div class="dashboard-card">

                    <h3>
                        মোট সংবাদ
                    </h3>

                    <div class="dashboard-number">
                        <?php echo $totalNews; ?>
                    </div>

                </div>


                <div class="dashboard-card">

                    <h3>
                        প্রকাশিত সংবাদ
                    </h3>

                    <div class="dashboard-number">
                        <?php echo $publishedNews; ?>
                    </div>

                </div>


                <div class="dashboard-card">

                    <h3>
                        Draft সংবাদ
                    </h3>

                    <div class="dashboard-number">
                        <?php echo $draftNews; ?>
                    </div>

                </div>


                <div class="dashboard-card">

                    <h3>
                        মোট বিভাগ
                    </h3>

                    <div class="dashboard-number">
                        <?php echo $totalCategories; ?>
                    </div>

                </div>


            </div>


            <!-- ================= ADMIN MENU ================= -->

            <div class="admin-menu">

                <h2>
                    Quick Actions
                </h2>

                <div class="admin-actions">

                    <a
                        href="add-news.php"
                        class="admin-action"
                    >
                        + নতুন সংবাদ যোগ করুন
                    </a>


                    <a
                        href="news-list.php"
                        class="admin-action"
                    >
                        📰 সকল সংবাদ
                    </a>


                    <a
                        href="../index.php"
                        class="admin-action"
                    >
                        🌐 ওয়েবসাইট দেখুন
                    </a>

                </div>

            </div>


        </div>

    </main>

</div>

</body>

</html>