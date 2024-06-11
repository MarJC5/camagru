<?php

namespace Camagru\views\user\forms;

use Camagru\routes\Router;
use Camagru\helpers\CSRF;

?>

<form action="<?= Router::to('update_user', ['id' => $user_id]) ?>" method="POST" class="form w-full">
    <?= CSRF::field() ?>
    <input type="number" id="id" name="id" class="hidden" value="<?= $user_id ?>">
    <div class="flex flex-column mb-4">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required value="<?= $old_username ?>">
    </div>
    <div class="flex flex-column mb-4">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="<?= $old_email ?>">
    </div>
    <div class="flex flex-column mb-4">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password">
    </div>
    <div class="flex flex-column mb-4">
        <label for="password_confirmation">Password Confirmation:</label>
        <input type="password" id="password_confirmation" name="password_confirmation">
    </div>
    <div class="flex flex-column">
        <button type="submit" class="button">
            Update
        </button>
    </div>
</form>

<script>
    const form = document.querySelector('form');
    const password = document.querySelector('#password');
    const password_confirmation = document.querySelector('#password_confirmation');

    form.addEventListener('submit', (e) => {
        if (password.value !== password_confirmation.value) {
            e.preventDefault();
            alert('Passwords do not match');
        }
    });
</script>