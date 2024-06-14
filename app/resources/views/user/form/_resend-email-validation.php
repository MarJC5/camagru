<?php

namespace Camagru\views\user\forms;

use Camagru\routes\Router;
use Camagru\helpers\CSRF;

?>

<form action="<?= Router::to('resend_email_validation') ?>" method="POST">
    <?= CSRF::field('csrf_resend_email_validation') ?>
    <input type="hidden" name="token" value="<?= $token ?>">
    <button type="submit" class="button">Resend validation email</button>
</form>