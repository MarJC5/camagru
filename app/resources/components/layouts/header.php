<?php 

namespace Camagru\resources\components\layouts;

use Camagru\helpers\Env;
use function Camagru\css_url;
use function Camagru\public_url;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Env::get('APP_NAME', 'Camagru') ?></title>
    <!-- Favicon -->
    <link rel="icon" href="<?= public_url('/favicon/favicon.ico') ?>" type="image/x-icon">
    <!-- CSS -->
    <link href="<?= css_url('app.css') ?>" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Reddit+Mono:wght@200..900&display=swap" rel="stylesheet">
</head>
<body class="reddit-mono-regular">
<header>
</header>
<main>