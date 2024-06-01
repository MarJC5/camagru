<?php 

namespace Camagru\views\user;

use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="user user--profile">
    <div class="row">
        <h1 class="the-title">@<?= $user->username() ?></h1>
    </div>
</section>

<?php get_footer(); ?>