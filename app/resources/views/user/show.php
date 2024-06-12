<?php

namespace Camagru\views\user;

use function Camagru\get_header;
use function Camagru\get_footer;
use function Camagru\partials;

get_header();
?>

<section class="user user--show">
    <div class="row">
        <?= partials('layouts/user/head.php', ['user' => $user]); ?>
    </div>
</section>
<?php get_footer(); ?>