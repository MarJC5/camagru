<?php 

namespace Camagru\views\post;

use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="post post--index">
    <div class="row">
        <h1 class="the-title animate__animated animate__slideInUp flex item-start">Feed<span class="h4"><?= $total ?></span></h1>
    </div>
</section>

<?php get_footer(); ?>
