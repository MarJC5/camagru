<?php 

namespace Camagru\views\user;

use function Camagru\get_header;
use function Camagru\get_footer;

get_header();
?>

<section class="user user--edit">
    <div class="row">
        <h1 class="the-title animate__animated animate__slideInUp">Edit user - <?= $user->username() ?></h1>
        <div class="the-content w-half">
            <?= $form ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
