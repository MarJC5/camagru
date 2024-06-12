<?php

namespace Camagru\views\auth\forms;

use Camagru\routes\Router;
use Camagru\helpers\CSRF;

?>

<form action="<?= Router::to('create_user') ?>" method="POST" class="form w-third">
    <?= CSRF::field() ?>
    <div class="flex flex-column mb-4">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="flex flex-column mb-4">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="flex flex-column mb-4">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div class="flex flex-column">
        <button class="button" type="submit" name="login">Register</button>
        <p>Aleady have an account? <a href="<?= Router::to('login') ?>" class="underline">Login</a></p>
    </div>
</form>