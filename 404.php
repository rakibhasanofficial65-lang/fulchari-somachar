<?php

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/includes/functions.php";

http_response_code(404);


// =====================================================
// SEO
// =====================================================

$pageTitle =
    "পেজ পাওয়া যায়নি | " . SITE_NAME;

$pageDescription =
    "দুঃখিত, আপনি যে পেজটি খুঁজছেন সেটি পাওয়া যায়নি।";

$canonicalUrl =
    site_url("404.php");

$ogImage =
    site_url("assets/logo.png");


// =====================================================
// HEADER
// =====================================================

require_once __DIR__ . "/includes/header.php";

?>

<style>

/* =====================================================
   404 PAGE
===================================================== */

.error-page {

    min-height: 70vh;

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 45px 20px;

    background: #f4f4f4;

}


.error-box {

    width: 100%;

    max-width: 700px;

    background: #fff;

    border: 1px solid #ddd;

    border-top: 5px solid #b30000;

    border-radius: 10px;

    padding: 55px 30px;

    text-align: center;

    box-shadow:
        0 10px 35px rgba(0, 0, 0, .08);

}


/* =====================================================
   LOGO
===================================================== */

.error-logo {

    width: 210px;

    max-width: 70%;

    height: auto;

    display: block;

    margin: 0 auto 25px;

}


/* =====================================================
   ERROR NUMBER
===================================================== */

.error-number {

    margin-bottom: 15px;

    color: #b30000;

    font-family:
        Georgia,
        serif;

    font-size: 90px;

    font-weight: 900;

    line-height: 1;

}


/* =====================================================
   TITLE
===================================================== */

.error-title {

    margin: 0 0 12px;

    color: #111;

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 30px;

    line-height: 1.5;

}


/* =====================================================
   DESCRIPTION
===================================================== */

.error-text {

    max-width: 520px;

    margin: 0 auto 25px;

    color: #777;

    font-size: 17px;

    line-height: 1.8;

}


/* =====================================================
   BUTTONS
===================================================== */

.error-buttons {

    display: flex;

    justify-content: center;

    flex-wrap: wrap;

    gap: 10px;

}


.error-button {

    display: inline-block;

    padding: 12px 22px;

    border-radius: 5px;

    font-size: 15px;

    font-weight: 700;

    text-decoration: none;

    transition:
        background .2s ease,
        transform .2s ease;

}


.error-button:hover {

    transform:
        translateY(-2px);

}


.error-home {

    background: #b30000;

    color: #fff;

}


.error-home:hover {

    background: #850000;

}


.error-search {

    background: #222;

    color: #fff;

}


.error-search:hover {

    background: #000;

}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 600px) {

    .error-page {

        min-height: 65vh;

        padding: 35px 15px;

    }


    .error-box {

        padding: 40px 20px;

    }


    .error-logo {

        width: 180px;

    }


    .error-number {

        font-size: 70px;

    }


    .error-title {

        font-size: 24px;

    }


    .error-text {

        font-size: 16px;

    }


    .error-buttons {

        flex-direction: column;

    }


    .error-button {

        width: 100%;

        box-sizing: border-box;

    }

}

</style>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="error-page">

    <div class="error-box">


        <!-- LOGO -->

        <img
            src="<?php echo e(
                site_url("assets/logo.png")
            ); ?>"
            alt="<?php echo e(SITE_NAME); ?>"
            class="error-logo"
        >


        <!-- ERROR NUMBER -->

        <div
            class="error-number"
            aria-label="404"
        >
            404
        </div>


        <!-- TITLE -->

        <h1 class="error-title">

            পেজটি পাওয়া যায়নি

        </h1>


        <!-- DESCRIPTION -->

        <p class="error-text">

            দুঃখিত, আপনি যে পেজটি খুঁজছেন সেটি
            হয়তো সরিয়ে ফেলা হয়েছে, পরিবর্তন করা হয়েছে
            অথবা URLটি সঠিক নয়।

        </p>


        <!-- BUTTONS -->

        <div class="error-buttons">


            <a
                href="<?php echo e(
                    site_url()
                ); ?>"
                class="error-button error-home"
            >

                🏠 হোম পেজ

            </a>


            <a
                href="<?php echo e(
                    site_url("search.php")
                ); ?>"
                class="error-button error-search"
            >

                🔍 সংবাদ খুঁজুন

            </a>


        </div>


    </div>

</main>


<?php

// =====================================================
// FOOTER
// =====================================================

require_once __DIR__ . "/includes/footer.php";

?><?php

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/includes/functions.php";

http_response_code(404);


// =====================================================
// SEO
// =====================================================

$pageTitle =
    "পেজ পাওয়া যায়নি | " . SITE_NAME;

$pageDescription =
    "দুঃখিত, আপনি যে পেজটি খুঁজছেন সেটি পাওয়া যায়নি।";

$canonicalUrl =
    site_url("404.php");

$ogImage =
    site_url("assets/logo.png");


// =====================================================
// HEADER
// =====================================================

require_once __DIR__ . "/includes/header.php";

?>

<style>

/* =====================================================
   404 PAGE
===================================================== */

.error-page {

    min-height: 70vh;

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 45px 20px;

    background: #f4f4f4;

}


.error-box {

    width: 100%;

    max-width: 700px;

    background: #fff;

    border: 1px solid #ddd;

    border-top: 5px solid #b30000;

    border-radius: 10px;

    padding: 55px 30px;

    text-align: center;

    box-shadow:
        0 10px 35px rgba(0, 0, 0, .08);

}


/* =====================================================
   LOGO
===================================================== */

.error-logo {

    width: 210px;

    max-width: 70%;

    height: auto;

    display: block;

    margin: 0 auto 25px;

}


/* =====================================================
   ERROR NUMBER
===================================================== */

.error-number {

    margin-bottom: 15px;

    color: #b30000;

    font-family:
        Georgia,
        serif;

    font-size: 90px;

    font-weight: 900;

    line-height: 1;

}


/* =====================================================
   TITLE
===================================================== */

.error-title {

    margin: 0 0 12px;

    color: #111;

    font-family:
        Georgia,
        "Noto Serif Bengali",
        serif;

    font-size: 30px;

    line-height: 1.5;

}


/* =====================================================
   DESCRIPTION
===================================================== */

.error-text {

    max-width: 520px;

    margin: 0 auto 25px;

    color: #777;

    font-size: 17px;

    line-height: 1.8;

}


/* =====================================================
   BUTTONS
===================================================== */

.error-buttons {

    display: flex;

    justify-content: center;

    flex-wrap: wrap;

    gap: 10px;

}


.error-button {

    display: inline-block;

    padding: 12px 22px;

    border-radius: 5px;

    font-size: 15px;

    font-weight: 700;

    text-decoration: none;

    transition:
        background .2s ease,
        transform .2s ease;

}


.error-button:hover {

    transform:
        translateY(-2px);

}


.error-home {

    background: #b30000;

    color: #fff;

}


.error-home:hover {

    background: #850000;

}


.error-search {

    background: #222;

    color: #fff;

}


.error-search:hover {

    background: #000;

}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 600px) {

    .error-page {

        min-height: 65vh;

        padding: 35px 15px;

    }


    .error-box {

        padding: 40px 20px;

    }


    .error-logo {

        width: 180px;

    }


    .error-number {

        font-size: 70px;

    }


    .error-title {

        font-size: 24px;

    }


    .error-text {

        font-size: 16px;

    }


    .error-buttons {

        flex-direction: column;

    }


    .error-button {

        width: 100%;

        box-sizing: border-box;

    }

}

</style>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="error-page">

    <div class="error-box">


        <!-- LOGO -->

        <img
            src="<?php echo e(
                site_url("assets/logo.png")
            ); ?>"
            alt="<?php echo e(SITE_NAME); ?>"
            class="error-logo"
        >


        <!-- ERROR NUMBER -->

        <div
            class="error-number"
            aria-label="404"
        >
            404
        </div>


        <!-- TITLE -->

        <h1 class="error-title">

            পেজটি পাওয়া যায়নি

        </h1>


        <!-- DESCRIPTION -->

        <p class="error-text">

            দুঃখিত, আপনি যে পেজটি খুঁজছেন সেটি
            হয়তো সরিয়ে ফেলা হয়েছে, পরিবর্তন করা হয়েছে
            অথবা URLটি সঠিক নয়।

        </p>


        <!-- BUTTONS -->

        <div class="error-buttons">


            <a
                href="<?php echo e(
                    site_url()
                ); ?>"
                class="error-button error-home"
            >

                🏠 হোম পেজ

            </a>


            <a
                href="<?php echo e(
                    site_url("search.php")
                ); ?>"
                class="error-button error-search"
            >

                🔍 সংবাদ খুঁজুন

            </a>


        </div>


    </div>

</main>


<?php

// =====================================================
// FOOTER
// =====================================================

require_once __DIR__ . "/includes/footer.php";

?>
