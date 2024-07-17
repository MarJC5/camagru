<?php

namespace Camagru\resources\components\photobooth;

use Camagru\helpers\Session;
use Camagru\helpers\Collage;

use function Camagru\js_url;
use function Camagru\partials;

const MAX_SHOTS = 4;

?>

<div class="display-cover h-full w-full">
    <div class="grid grid--2">
        <div class="camera">
            <video autoplay class="h-full w-full"></video>
            <?php foreach (Collage::getStickers() as $sticker) : ?>
                <img src="<?= $sticker['path'] ?>" class="sticker hidden" height="<?= $sticker['height'] ?>" width="<?= $sticker['width'] ?>" alt="sticker-<?= $sticker['name'] ?>" data-sticker="<?= $sticker['name'] ?>">
            <?php endforeach; ?>
        </div>

        <div class="screenshots">
            <h2 class="mt-0 mb-4 flex">Shots<span class="screenshots-count ml-4 text-small"></span></h2>
            <div id="screenshots-container" class="grid grid--2" data-screenshots="0">
                <?php for ($i = 0; $i < MAX_SHOTS; $i++) : ?>
                    <div class="screenshot">
                        <canvas class="hidden"></canvas>
                        <img class="screenshot-image screenshot-image--<?= $i ?> hidden w-full h-auto" alt="">
                        <img class="screenshot-image-sticker screenshot-image--<?= $i ?>-sticker hidden w-full h-auto" alt="">
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <div class="actions flex flex-column">
        <div class="stickers-selection">
            <h3 class="mt-4 mb-4">Stickers</h3>
            <div class="grid grid--5 grid-rows-auto">
                <?php foreach (Collage::getStickers() as $sticker) : ?>
                    <button class="button button--icon button--sticker" title="<?= $sticker['name'] ?>" data-sticker="<?= $sticker['name'] ?>" data-active="false">
                        <img src="<?= $sticker['path'] ?>" height="50px" width="50px" alt="sticker-<?= $sticker['name'] ?>">
                    </button>
                <?php endforeach; ?>
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
            <button class="button button--icon screenshot hidden button--disabled" title="ScreenShot"><?= partials('svg/take-photo.php') ?></button>
        </div>
    </div>
</div>



<script src="<?= js_url('hooks/photobooth.js') ?>" type="text/javascript" nonce="<?= Session::get('nonce') ?>"></script>