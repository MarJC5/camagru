<?php

namespace Camagru\views\page;

use Camagru\helpers\CSRF;
use Camagru\helpers\Env;
use Camagru\routes\Router;
use function Camagru\public_url;
use function Camagru\css_url;
use function Camagru\js_url;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Env::get('APP_NAME', 'Camagru') ?><?= !empty($_GET['title']) ? ' | ' . htmlspecialchars($_GET['title']) : '' ?></title>
    <!-- Favicon -->
    <link rel="icon" href="<?= public_url('/favicon/favicon.ico') ?>" type="image/x-icon">
    <!-- CSS -->
    <link href="<?= css_url('app.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Reddit+Mono:wght@200..900&display=swap" rel="stylesheet">
    <!-- Styles -->
    <style>
        body {
            opacity: 0;
            transition: opacity 1s;
        }
    </style>
</head>

<body class="reddit-mono-regular" onload="document.body.style.opacity='1'">
    <header>
        <div class="row flex justify-between">
            <div id="logo">
                <a href="<?= Router::to('home') ?>">
                    <?= Env::get('APP_NAME', 'Camagru') ?>
                </a>
            </div>
    </header>
    <main>

        <section class="page page--install">
            <div class="row">
                <h1 class="the-title">Install</h1>
                <div class="the-content w-half">
                    <p>Camagru is not installed. Please run the following command to install the application:</p>
                    <pre class="mb-4 text-small text-bold">./migrate run</pre>
                    <pre class="text-small text-bold">./seed</pre>

                    <p>Or you can click the button below to install the application:</p>
                    <form action="<?= Router::to('setup') ?>" method="POST">
                        <?= CSRF::field() ?>
                        <input type="hidden" name="install" value="1">
                        <button type="submit" class="button">Install</button>
                    </form>

                    <p>After running the above commands, you can access the application.</p>
                </div>
            </div>
        </section>

    </main>
    <footer>
        <div class="row grid">
            <p>&copy; <?= date("Y"); ?> <?= Env::get('APP_NAME', 'Camagru') ?></p>
        </div>
    </footer>
    <!-- JS -->
    <script src="<?= js_url('app.js') ?>" type="module"></script>
</body>

</html>