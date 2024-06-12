<?php 

namespace Camagru\views\media;

use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="media media--show">
    <div class="row">
        <h1 class="the-title"><?= $media->title() ?></h1>
    </div>
</section>

<?php get_footer(); ?>
