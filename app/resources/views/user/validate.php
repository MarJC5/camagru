<?php

namespace Camagru\views\user;

use Camagru\routes\Router;
use Camagru\helpers\CSRF;
use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="user user--validate">
    <div class="row">
        <h1 class="the-title">Validate your account</h1>
        <div class="the-content w-half">
            <p class="the-text">We have sent you an email with a link to validate your account. Please check your inbox and click on the link to validate your account.</p>
            <?= $form ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>