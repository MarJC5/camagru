<?php

namespace Camagru\views\auth\forms;

use Camagru\routes\Router;
use Camagru\helpers\CSRF;

?>

<form action="<?= Router::to('connect_user') ?>" method="POST" class="form w-third">
    <?= CSRF::field('csrf_connect_user') ?>
    <div class="flex flex-column mb-4">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="flex flex-column mb-4">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <button class="button" type="submit" name="login">Login</button>
        <p>Don't have an account? <a href="<?= Router::to('register_user') ?>" class="underline">Register</a></p>
        <p>Forgot your password? <a href="<?= Router::to('reset_password') ?>" class="underline">Reset</a></p>
    </div>
</form>