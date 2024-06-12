<?php 

namespace Camagru\views\user;

use function Camagru\get_header;
use function Camagru\get_footer;
use function Camagru\partials;

get_header();
?>

<section class="user user--index">
    <div class="row">
        <h1 class="the-title">Users</h1>
        <section class="grid grid--6">
            <?php foreach ($users as $user) : ?>
                <?= partials('ui/card/user.php', ['user' => $user]); ?>
            <?php endforeach; ?>
        </section>
    </div>
</section>

<?php get_footer(); ?>
