<?php

namespace Camagru\resources\components\layouts;

use Camagru\helpers\Env;
use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\database\Runner;
use function Camagru\css_url;
use function Camagru\partials;
use function Camagru\public_url;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Env::get('APP_NAME', 'Camagru') ?><?= !empty($_GET['title']) ? ' | ' . htmlspecialchars($_GET['title']) : '' ?></title>
    <!-- Favicon -->
    <link nonce="<?= Session::get('nonce') ?>" rel="icon" href="<?= public_url('/favicon/favicon.ico') ?>" type="image/x-icon">
    <!-- CSS -->
    <link nonce="<?= Session::get('nonce') ?>" href="<?= css_url('app.css') ?>" rel="stylesheet">
    <link nonce="<?= Session::get('nonce') ?>" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- Fonts -->
    <link nonce="<?= Session::get('nonce') ?>" rel="preconnect" href="https://fonts.googleapis.com">
    <link nonce="<?= Session::get('nonce') ?>"rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link nonce="<?= Session::get('nonce') ?>" href="https://fonts.googleapis.com/css2?family=Reddit+Mono:wght@200..900&display=swap" rel="stylesheet">
    <!-- Styles -->
    <style nonce="<?= Session::get('nonce') ?>">
        body {
            opacity: 0;
            transition: opacity 1s;
        }
    </style>
    <!-- Scripts -->
    <script nonce="<?= Session::get('nonce') ?>">
        document.addEventListener("DOMContentLoaded", function() {
            document.body.style.opacity = '1';
        });

        const APP_URL = '<?= Env::get('APP_URL') ?>';
        const APP_NAME = '<?= Env::get('APP_NAME') ?>';
    </script>
</head>

<body class="reddit-mono-regular">
    <?= partials('ui/alert.php') ?>
    <header>
        <div class="row flex justify-between">
            <div id="logo">
                <a href="<?= Router::to('home') ?>">
                    <?= Env::get('APP_NAME', 'Camagru') ?>
                </a>
            </div>
            <?php if (Runner::isMigrated()) : ?>
                <div class="nav">
                    <ul class="flex item-center justify-end reset-ul gap-4">
                        <li><a href="<?= Router::to('posts') ?>">Feed</a></li>
                        <?php if (Session::isLogged()) : ?>
                            <li><a href="<?= Router::to('profile') ?>">Profile</a></li>
                            <li><a href="<?= Router::to('logout') ?>">Logout</a></li>
                            <li>
                                <a href="<?= Router::to('create_post') ?>" class="button button--header button--icon flex gap-2">
                                    <?= partials('svg/plus.php') ?> Post
                                </a>
                            </li>
                        <?php else : ?>
                            <li><a href="<?= Router::to('login') ?>">Login</a></li>
                            <li><a href="<?= Router::to('register_user') ?>">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <main>