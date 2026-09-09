<?php

require_once dirname(__DIR__) . "/config/config.php";

?>

<footer class="site-footer">

    <div class="container">

        <div class="footer-inner">

            <!-- =================================================
                 BRAND
            ================================================= -->

            <div class="footer-brand">

                <a
                    href="<?php echo SITE_URL; ?>/"
                    class="footer-logo-link"
                >

                    <img
                        src="<?php echo SITE_URL; ?>/assets/logo.png"
                        alt="<?php echo htmlspecialchars(
                            SITE_NAME,
                            ENT_QUOTES,
                            "UTF-8"
                        ); ?>"
                        class="footer-logo"
                    >

                </a>

                <p>
                    <?php echo htmlspecialchars(
                        SITE_NAME,
                        ENT_QUOTES,
                        "UTF-8"
                    ); ?>
                    — সত্য ও বস্তুনিষ্ঠ সংবাদ পরিবেশনে অঙ্গীকারবদ্ধ।
                </p>

            </div>


            <!-- =================================================
                 QUICK LINKS
            ================================================= -->

            <div class="footer-links">

                <h3>
                    গুরুত্বপূর্ণ লিংক
                </h3>

                <a href="<?php echo SITE_URL; ?>/">
                    প্রচ্ছদ
                </a>

                <a href="<?php echo SITE_URL; ?>/epaper">
                    ই-পেপার
                </a>

                <a href="<?php echo SITE_URL; ?>/search">
                    সংবাদ অনুসন্ধান
                </a>

            </div>


            <!-- =================================================
                 ADMIN
            ================================================= -->

            <div class="footer-links">

                <h3>
                    প্রশাসন
                </h3>

                <a href="<?php echo SITE_URL; ?>/admin/login.php">
                    Admin Login
                </a>

            </div>

        </div>


        <!-- =================================================
             COPYRIGHT
        ================================================= -->

        <div class="footer-bottom">

            <p>
                &copy;
                <?php echo date("Y"); ?>
                <?php echo htmlspecialchars(
                    SITE_NAME,
                    ENT_QUOTES,
                    "UTF-8"
                ); ?>

                — সর্বস্বত্ব সংরক্ষিত।
            </p>

        </div>

    </div>

</footer>


<style>

    .site-footer {
        background: #111;
        color: #fff;
        margin-top: 50px;
        padding-top: 40px;
    }


    .footer-inner {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 40px;
        padding-bottom: 35px;
    }


    .footer-brand {
        max-width: 500px;
    }


    .footer-logo-link {
        display: inline-block;
    }


    .footer-logo {
        display: block;
        width: 220px;
        max-width: 100%;
        height: auto;
        margin-bottom: 15px;
    }


    .footer-brand p {
        color: #bbb;
        line-height: 1.8;
        margin: 0;
        font-size: 14px;
    }


    .footer-links h3 {
        font-size: 17px;
        margin: 0 0 15px;
        color: #fff;
        border-left: 3px solid #b30000;
        padding-left: 10px;
    }


    .footer-links a {
        display: block;
        color: #bbb;
        text-decoration: none;
        margin-bottom: 10px;
        font-size: 14px;
        transition: color 0.2s ease;
    }


    .footer-links a:hover {
        color: #fff;
    }


    .footer-bottom {
        border-top: 1px solid #333;
        padding: 18px 0;
        text-align: center;
    }


    .footer-bottom p {
        margin: 0;
        color: #999;
        font-size: 13px;
    }


    @media (max-width: 700px) {

        .footer-inner {
            grid-template-columns: 1fr;
            gap: 25px;
        }


        .footer-brand {
            max-width: 100%;
        }


        .footer-logo {
            width: 190px;
        }

    }

</style>