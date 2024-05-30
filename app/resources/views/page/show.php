<?php

namespace Camagru\views\page;

use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="page page--show">
    <div class="row">
        <h1 class="the-title animate__animated animate__slideInUp"><?= $page->title() ?></h1>
        <div class="the-content w-half">
            <?= $page->content() ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>