<?php

namespace Camagru\views\user;

use Camagru\routes\Router;
use function Camagru\get_header;
use function Camagru\get_footer;
use function Camagru\partials;

get_header();
?>

<section class="user user--profile">
    <div class="row">
        <?= partials('layouts/user/head.php', ['user' => $user]); ?>
        <div class="actions">
            <a href="<?= Router::to('edit_user', ['id' => $user->id()]) ?>">Edit profile</a>
        </div>
    </div>
</section>

<?php get_footer(); ?>