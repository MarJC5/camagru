<?php

namespace Camagru\views\user;

use Camagru\helpers\Session;
use function Camagru\js_url;
use function Camagru\get_header;
use function Camagru\get_footer;
use function Camagru\partials;

get_header();
?>

<section class="user user--show">
    <div class="row">
        <?= partials('layouts/user/head.php', ['user' => $user]); ?>
        <!-- Infinite scroll Start -->
        <section id="infinit-posts-scroll" class="posts grid grid--3 gap-6" data-user-id="<?= $user->id() ?>">
            <!-- Posts -->
        </section>
        <!-- Infinite scroll End -->
    </div>
</section>
<?php get_footer(); ?>
<script src="<?= js_url('hooks/infinit-loop.js') ?>" type="module" nonce="<?= Session::get('nonce') ?>"></script>