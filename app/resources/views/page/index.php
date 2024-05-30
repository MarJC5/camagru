<?php 

namespace Camagru\views\page;

use Camagru\helpers\Env;
use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="page page--home">
    <div class="row">
        <h1 class="the-title">Hello <?= Env::get('APP_NAME', '') ?>!</h1>
    </div>
</section>

<?php get_footer(); ?>
