<?php

namespace Camagru\views\page;

use Camagru\routes\Router;
use Camagru\core\models\Post;
use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="page page--home">
    <div class="row">
        <h1 class="the-title animate__animated animate__slideInUp"><?= $page->title() ?></h1>
        <div class="the-content w-half">
            <?= $page->content() ?>
            <section>
                <a href="<?= Router::to('page', ['slug' => 'about']) ?>" class="button">About</a>
                <a href="<?= Router::to('posts') ?>" class="button button--secondary">View feed</a>
                <a href="<?= Router::to('users') ?>" class="button button--secondary">See the community</a>
            </section>
        </div>
    </div>
</section>

<?php get_footer(); ?>