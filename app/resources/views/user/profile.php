<?php

namespace Camagru\views\user;

use Camagru\helpers\Session;
use Camagru\routes\Router;
use function Camagru\get_header;
use function Camagru\get_footer;
use function Camagru\partials;
use function Camagru\js_url;

get_header();
?>

<section class="user user--profile">
    <div class="row">
        <?= partials('layouts/user/head.php', ['user' => $user]); ?>
        <div class="actions mb-6">
            <a href="<?= Router::to('edit_user', ['id' => $user->id()]) ?>">Edit profile</a>
        </div>
           <!-- Infinite scroll Start -->
           <section id="infinit-posts-scroll" class="posts grid grid--3 gap-6" data-user-id="<?= $user->id() ?>">
            <!-- Posts -->
        </section>
        <!-- Infinite scroll End -->
    </div>
</section>
<?php get_footer(); ?>
<script src="<?= js_url('hooks/infinit-loop.js') ?>" type="module" nonce="<?= Session::get('nonce') ?>"></script>