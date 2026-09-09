<?php

require_once dirname(__DIR__) . "/config/config.php";


// =====================================================
// NAVIGATION CATEGORIES
// =====================================================

$navCategories = [];

try {

    if (isset($pdo)) {

        $navStmt = $pdo->query("
            SELECT name, slug
            FROM categories
            ORDER BY id ASC
        ");

        $navCategories = $navStmt->fetchAll();

    }

} catch (Throwable $e) {

    $navCategories = [];

}

?>

<nav class="main-navbar">

    <div class="container">

        <div class="navbar-inner">

            <!-- HOME -->

            <a
                href="<?php echo SITE_URL; ?>/"
                class="nav-link nav-home"
            >
                প্রচ্ছদ
            </a>


            <!-- CATEGORIES -->

            <div class="nav-categories">

                <?php foreach ($navCategories as $navCategory): ?>

                    <a
                        href="<?php echo SITE_URL; ?>/category/<?php echo rawurlencode($navCategory["slug"]); ?>"
                        class="nav-link"
                    >
                        <?php echo htmlspecialchars(
                            $navCategory["name"],
                            ENT_QUOTES,
                            "UTF-8"
                        ); ?>
                    </a>

                <?php endforeach; ?>

            </div>


            <!-- RIGHT SIDE -->

            <div class="nav-right">

                <a
                    href="<?php echo SITE_URL; ?>/epaper"
                    class="nav-link epaper-link"
                >
                    📰 ই-পেপার
                </a>


                <a
                    href="<?php echo SITE_URL; ?>/search"
                    class="nav-link search-link"
                    aria-label="Search"
                >
                    🔍
                </a>

            </div>

        </div>

    </div>

</nav>


<style>

    .main-navbar {
        background: #111;
        border-top: 3px solid #b30000;
        border-bottom: 1px solid #333;
        width: 100%;
    }


    .navbar-inner {
        display: flex;
        align-items: center;
        min-height: 48px;
        gap: 0;
    }


    .nav-categories {
        display: flex;
        align-items: center;
        flex: 1;
        overflow-x: auto;
        scrollbar-width: none;
    }


    .nav-categories::-webkit-scrollbar {
        display: none;
    }


    .nav-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 48px;

        padding: 0 15px;

        color: #fff;

        text-decoration: none;

        font-size: 15px;

        font-weight: 600;

        white-space: nowrap;

        transition:
            background-color 0.2s ease,
            color 0.2s ease;
    }


    .nav-link:hover {
        background: #b30000;
        color: #fff;
    }


    .nav-home {
        background: #b30000;
    }


    .nav-right {
        display: flex;
        align-items: center;
        margin-left: auto;
    }


    .epaper-link {
        border-left: 1px solid #333;
    }


    .search-link {
        font-size: 19px;
        border-left: 1px solid #333;
    }


    @media (max-width: 800px) {

        .navbar-inner {
            overflow-x: auto;
            scrollbar-width: none;
        }


        .navbar-inner::-webkit-scrollbar {
            display: none;
        }


        .nav-categories {
            flex: none;
        }


        .nav-right {
            flex: none;
            margin-left: 0;
        }


        .nav-link {
            padding: 0 12px;
            font-size: 14px;
        }

    }


    @media (max-width: 500px) {

        .nav-link {
            padding: 0 10px;
            font-size: 13px;
        }


        .search-link {
            font-size: 17px;
        }

    }

</style>