<?php

namespace Camagru\views\user;

use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="user user--reset-password">
    <div class="row">
        <?= $form ?>
    </div>
</section>

<?php get_footer(); ?>