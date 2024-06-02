<?php

namespace Camagru\views\user\forms;

use Camagru\routes\Router;
use Camagru\helpers\CSRF;

?>

<h1 class="the-title">Reset your password</h1>
<div class="the-content w-half">
    <p class="the-text">Click on the link we sent you by email to reset your password.</p>
    <form action="<?= Router::to('reset_password_request') ?>" method="POST" class="form w-third">
        <?= CSRF::field() ?>
        <div class="flex flex-column mb-4">
            <input type="email" name="email" id="email" required placeholder="email@camagru.local">
        </div>
        <div class="flex flex-column">
            <button type="submit" class="button">Request password reset</button>
        </div>
    </form>
</div>