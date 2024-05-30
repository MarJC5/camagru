<?php 

namespace Camagru\resources\components\layouts;

use Camagru\helpers\Env;
use function Camagru\css_url;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Env::get('APP_NAME', 'Camagru') ?></title>
    <!-- CSS -->
    <link href="<?= css_url('app.css') ?>" rel="stylesheet">
</head>
<body>

<main>