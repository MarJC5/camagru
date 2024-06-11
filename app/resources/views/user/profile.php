<?php 

namespace Camagru\views\user;

use Camagru\routes\Router;
use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="user user--profile">
    <div class="row">
        <h1 class="the-title">@<?= $user->username() ?></h1>
        <div class="actions">
            <a href="<?= Router::to('edit_user', ['id' => $user->id()]) ?>">Edit profile</a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
