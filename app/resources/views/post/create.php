<?php 

namespace Camagru\views\page;

use function Camagru\get_header;
use function Camagru\get_footer;
use function Camagru\partials;

get_header();
?>

<section class="page page--create">
    <div class="row">
        <h1 class="the-title animate__animated animate__slideInUp">New post</h1>
        <?= partials('photobooth/camera.php') ?>
    </div>
</section>

<?php get_footer(); ?>
