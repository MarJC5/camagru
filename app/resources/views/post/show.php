<?php

namespace Camagru\views\post;

use Camagru\core\models\User;
use Camagru\helpers\CSRF;
use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\models\Like;
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
                    <figure class="m-0">
                        <picture>
                            <img class="ofi-image rounded-md shadow" srcset="<?= $media['src'] ?>" src="<?= $media['src']  ?>" alt="<?= $media['alt']  ?>">
                        </picture>
                    </figure>
                </div>
            </div>
            <div class="grid__item h-content flex flex-column justify-center items-center">
                <div class="post__content">
                    <?= partials('layouts/user/head.php', ['user' => User::find($user['id'])->first()]); ?>
                    <div class="max-h-half-vh flex flex-column gap-4 p-2 overflow-y-scroll">
                        <?php foreach ($comments as $comment) : ?>
                            <div class="flex gap-4">
                                <?= partials('ui/card/comment.php', [
                                    'user' => $comment['user'],
                                    'comment' => $comment
                                ]); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (Session::currentUser()) : ?>
                        <div class="flex gap-4 px-2 mt-6">
                            <form action="<?= Router::to('comment') ?>" method="POST" class="form flex gap-4 w-full">
                                <?= CSRF::field('csrf_comment') ?>
                                <input type="hidden" name="post_id" value="<?= $id ?>">
                                <input type="hidden" name="user_id" value="<?= Session::currentUser()->id() ?>">
                                <input type="text" name="comment" class="input w-full" placeholder="Add a comment...">
                                <button type="submit" class="button button--primary">Post</button>
                            </form>
                        </div>
                    <?php endif; ?>
                    <div class="flex item-center mt-4 px-2 gap-4">
                        <?php if (Session::currentUser()) : ?>
                            <?php if (Like::hasLiked(Session::currentUser()->id(), $id)) : ?>
                                <form action="<?= Router::to('unlike') ?>" method="POST">
                                    <?= CSRF::field('csrf_unlike') ?>
                                    <input type="hidden" name="id" value="<?= Like::hasLiked(Session::currentUser()->id(), $id) ?>">
                                    <input type="hidden" name="post_id" value="<?= $id ?>">
                                    <button type="submit" class="button button--svg  flex gap-1">
                                        <?= partials('svg/heart-fill.php', ['class' => 'text-red-400']) ?>
                                        <p class="m-0 text-gray-400"><?= $count_likes ?></p>
                                    </button>
                                </form>
                            <?php else : ?>
                                <form action="<?= Router::to('like') ?>" method="POST">
                                    <?= CSRF::field('csrf_like') ?>
                                    <input type="hidden" name="user_id" value="<?= Session::currentUser()->id() ?>">
                                    <input type="hidden" name="post_id" value="<?= $id ?>">
                                    <button type="submit" class="button button--svg flex gap-1">
                                        <?= partials('svg/heart.php', ['class' => 'text-gray-400']) ?>
                                        <p class="m-0 text-gray-400"><?= $count_likes ?></p>
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php else : ?>
                            <a href="<?= Router::to('login') ?>" class="underline-none">
                                <button type="submit" class="button button--svg flex gap-1">
                                    <?= partials('svg/heart.php', ['class' => 'text-gray-400']) ?>
                                    <p class="m-0 text-gray-400"><?= $count_likes ?></p>
                                </button>
                            </a>
                        <?php endif; ?>
                        <button class="button button--svg flex gap-1">
                            <?= partials('svg/comment.php', ['class' => 'text-gray-400']) ?>
                            <p class="m-0 text-gray-400"><?= $count_comments ?></p>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>