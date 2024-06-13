<?php

namespace Camagru\resources\components\ui\modal;

use function Camagru\partials;

?>


<div class="modal-overlay modal-overlay-share absolute w-screen h-screen top-0 left-0 flex item-center">
    <div class="modal modal--share bg-white m-auto w-third p-4">
        <div class="modal__head flex justify-between item-center text-main mb-4">
            <h2 class="modal__title m-0">Share</h2>
            <button class="modal__close button button--reset button--svg">
                <?= partials('svg/x-mark.php'); ?>
            </button>
        </div>
        <div class="modal__body">
            <div class="modal__body__content flex flex-wrap gap-4">
                <div class="modal__body__content__item">
                    <a href="#" class="button button--primary">Facebook</a>
                </div>
                <div class="modal__body__content__item">
                    <a href="#" class="button button--primary">Twitter</a>
                </div>
                <div class="modal__body__content__item">
                    <a href="#" class="button button--primary">WhatsApp</a>
                </div>
                <div class="modal__body__content__item">
                    <a href="#" class="button button--primary">Email</a>
                </div>
                <div class="modal__body__content__item">
                    <a href="#" class="button button--primary">Copy link</a>
                </div>
            </div>
        </div>
    </div>
</div>