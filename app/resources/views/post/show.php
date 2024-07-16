<?php

namespace Camagru\views\post;

use Camagru\core\models\User;
use Camagru\routes\Router;
use Camagru\helpers\CSRF;
use Camagru\helpers\Session;
use function Camagru\get_header;
use function Camagru\get_footer;
use function Camagru\partials;

get_header();
?>

<section class="post post--show">
    <div class="row">
        <div class="grid grid--2">
            <div class="grid__item h-content">
                <div class="post__image relative w-full h-full rounded-md">
                    <?php if (Session::currentUser()) : ?>
                        <?php if ($user['id'] == Session::currentUser()->id() || Session::currentUser()->is_admin()) : ?>
                            <form action="<?= Router::to('delete_post') ?>" method="POST" class="absolute top-4 right-4 z-10">
                                <?= CSRF::field('csrf_delete_post_' . $id) ?>
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <button type="submit" class="button button--danger">
                                    Delete post
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                    <figure class="m-0">
                        <picture>
                            <img class="ofi-image rounded-md shadow" srcset="<?= $media['src'] ?>" src="<?= $media['src']  ?>" alt="<?= $media['alt']  ?>">
                        </picture>
                    </figure>
                </div>
            </div>
            <div class="grid__item h-content flex flex-column justify-center items-center">
                <?= partials('layouts/post/post.php', [
                    'user' => User::find($user['id'])->first(),
                    'comments' => $comments,
                    'id' => $id,
                    'count_likes' => $count_likes,
                    'count_comments' => $count_comments
                ]); ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>