<?php

namespace Camagru\resources\components\layouts;

use Camagru\helpers\Env;
use Camagru\routes\Router;
use function Camagru\css_url;
use function Camagru\public_url;

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
        <div class="row">
            <div id="logo">
                <a href="<?= Router::to('home') ?>">
                    <?= Env::get('APP_NAME', 'Camagru') ?>
                </a>
            </div>
            <div class="nav">
            </div>
        </div>
    </header>
    <main>