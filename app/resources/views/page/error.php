<?php 

namespace Camagru\views\page;

use function Camagru\get_header;
use function Camagru\get_footer;
use function Camagru\getHttpStatusMessage;

get_header();

// Get the current HTTP response code
$httpCode = http_response_code();
?>

<section class="page page--error">
    <div class="row">
        <h1 class="the-title animate__animated animate__slideInUp">
            Error <?= $httpCode ?>: <?= getHttpStatusMessage($httpCode); ?>
        </h1>
    </div>
</section>

<?php get_footer(); ?>