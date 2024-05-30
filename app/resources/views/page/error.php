<?php 

namespace Camagru\views\page;

use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="page page--error">
    <div class="row">
        <h1 class="the-title">
            <?php if (http_response_code() == 404): ?>
                Page not found
            <?php else: ?>
                Error <?= http_response_code(); ?>
            <?php endif; ?>
        </h1>
    </div>
</section>

<?php get_footer(); ?>
