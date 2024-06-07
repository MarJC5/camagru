<?php

namespace Camagru\resources\components\photobooth;

use Camagru\helpers\Session;
use function Camagru\js_url;
use function Camagru\partials;

?>

<div class="display-cover h-full w-full">
    <div class="grid grid--2-1">
        <div class="camera">
            <video autoplay class="h-full w-full"></video>
        </div>

        <div class="screenshots">
            <h2 class="mt-0 mb-4 flex">Shots<span class="screenshots-count ml-4 text-small"></span></h2>
            <div id="screenshots-container" class="grid grid--2" data-screenshots="0">
                <canvas class="hidden"></canvas>
                <img class="screenshot-image screenshot-image--1 hidden w-full h-auto" alt="">
                <canvas class="hidden"></canvas>
                <img class="screenshot-image screenshot-image--2 hidden w-full h-auto" alt="">
                <canvas class="hidden"></canvas>
                <img class="screenshot-image screenshot-image--3 hidden w-full h-auto" alt="">
                <canvas class="hidden"></canvas>
                <img class="screenshot-image screenshot-image--4 hidden w-full h-auto" alt="">
            </div>
        </div>
    </div>

    <div class="controls">
        <div class="video-options">
            <select name="" id="" class="select">
                <option value="">Select camera</option>
            </select>
        </div>
        <button class="button button--icon play" title="Play"><?= partials('svg/camera.php') ?></button>
        <button class="button button--icon pause hidden" title="Pause"><?= partials('svg/hide-camera.php') ?></button>
        <button class="button button--icon screenshot hidden" title="ScreenShot"><?= partials('svg/take-photo.php') ?></button>
    </div>
</div>



<script src="<?= js_url('hooks/photobooth.js') ?>" type="text/javascript" nonce="<?= Session::get('nonce') ?>"></script>