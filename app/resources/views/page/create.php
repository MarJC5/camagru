<?php 

namespace Camagru\views\page;

use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="page page--create">
    <div class="row">
        <h1 class="the-title">New page</h1>
        <div class="the-content">
            <?= $form ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
