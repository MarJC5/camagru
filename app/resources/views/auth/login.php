<?php 

namespace Camagru\views\auth;

use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="auth auth--login">
    <div class="row">
        <h1>Login</h1>
        <?= $form ?>
    </div>
</section>

<?php get_footer(); ?>
