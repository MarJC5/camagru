<?php 

namespace Camagru\views\post;

use Camagru\helpers\Session;
use function Camagru\js_url;
use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="post post--index relative">
    <div class="row">
        <h1 class="the-title flex item-start">Feed<span class="h4"><?= $total ?></span></h1>
        <!-- Infinite scroll Start -->
        <section id="infinit-posts-scroll" class="posts grid grid--3 gap-6">
            <!-- Posts -->
        </section>
        <!-- Infinite scroll End -->
    </div>
</section>

<?php get_footer(); ?>

<script src="<?= js_url('hooks/infinit-loop.js') ?>" type="module" nonce="<?= Session::get('nonce') ?>"></script>