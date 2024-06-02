<?php

namespace Camagru\views\user\forms;

use Camagru\routes\Router;
use Camagru\helpers\CSRF;

?>

<h1 class="the-title">Set a new password</h1>
<div class="the-content w-half">
    <p class="the-text">Please set a new password for your account.</p>
    <form action="<?= Router::to('new_password') ?>" method="POST" class="form w-third">
        <?= CSRF::field() ?>
        <input type="hidden" name="user_id" value="<?= $user_id ?>" required>
        <div class="flex flex-column mb-4">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="flex flex-column mb-4">
            <label for="password_confirmation">Confirm password:</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
        </div>
        <div class="flex flex-column">
            <button type="submit" class="button">Save new password</button>
        </div>
    </form>
</div>