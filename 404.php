<?php

require_once "config/config.php";

http_response_code(404);

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
        পেজ পাওয়া যায়নি | <?php echo SITE_NAME; ?>
    </title>

    <meta
        name="robots"
        content="noindex, nofollow"
    >

    <link
        rel="stylesheet"
        href="<?php echo SITE_URL; ?>/assets/style.css"
    >


    <style>

        body {

            margin: 0;

            background: #f4f4f4;

        }


        .error-page {

            min-height: 75vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 40px 20px;

            box-sizing: border-box;

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
                0 10px 35px rgba(0,0,0,.08);

        }


        .error-logo {

            width: 210px;

            max-width: 70%;

            height: auto;

            margin-bottom: 25px;

        }


        .error-number {

            font-family:
                Georgia,
                serif;

            font-size: 90px;

            line-height: 1;

            font-weight: 900;

            color: #b30000;

            margin-bottom: 15px;

        }


        .error-title {

            font-size: 30px;

            margin: 0 0 12px;

            color: #111;

        }


        .error-text {

            font-size: 17px;

            line-height: 1.8;

            color: #777;

            margin: 0 auto 25px;

            max-width: 520px;

        }


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

            text-decoration: none;

            font-size: 15px;

            font-weight: bold;

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


        @media (max-width: 600px) {

            .error-box {

                padding: 40px 20px;

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

        }

    </style>

</head>


<body>


<?php include "includes/header.php"; ?>

<?php include "includes/navbar.php"; ?>


<main class="error-page">

    <div class="error-box">


        <!-- LOGO -->

        <img
            src="<?php echo SITE_URL; ?>/assets/logo.png"
            alt="<?php echo htmlspecialchars(
                SITE_NAME,
                ENT_QUOTES,
                "UTF-8"
            ); ?>"
            class="error-logo"
        >


        <!-- ERROR NUMBER -->

        <div class="error-number">
            404
        </div>


        <!-- TITLE -->

        <h1 class="error-title">

            পেজটি পাওয়া যায়নি

        </h1>


        <!-- TEXT -->

        <p class="error-text">

            দুঃখিত, আপনি যে পেজটি খুঁজছেন সেটি
            হয়তো সরিয়ে ফেলা হয়েছে, পরিবর্তন করা হয়েছে
            অথবা URLটি সঠিক নয়।

        </p>


        <!-- BUTTONS -->

        <div class="error-buttons">


            <a
                href="<?php echo SITE_URL; ?>/index.php"
                class="error-button error-home"
            >

                🏠 হোম পেজ

            </a>


            <a
                href="<?php echo SITE_URL; ?>/search.php"
                class="error-button error-search"
            >

                🔍 সংবাদ খুঁজুন

            </a>


        </div>


    </div>

</main>


<?php include "includes/footer.php"; ?>


</body>

</html>