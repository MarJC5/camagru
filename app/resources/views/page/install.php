<?php

namespace Camagru\views\page;

use Camagru\helpers\CSRF;
use Camagru\routes\Router;
use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="page page--install">
    <div class="row">
        <h1 class="the-title">Install</h1>
        <div class="the-content w-half">
            <p>Camagru is not installed. Please run the following command to install the application:</p>
            <pre class="mb-4 text-small text-bold">cd ./app</pre>
            <pre class="mb-4 text-small text-bold">./migrate run</pre>
            <pre class="text-small text-bold">./seed</pre>

            <p>Or you can click the button below to install the application:</p>
            <form action="<?= Router::to('setup') ?>" method="POST">
                <?= CSRF::field('csrf_setup') ?>
                <input type="hidden" name="install" value="1">
                <button type="submit" class="button">Install</button>
            </form>

            <p>After running the above commands, you can access the application.</p>
        </div>
    </div>
</section>

<?php get_footer(); ?>