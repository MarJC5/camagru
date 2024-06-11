<?php

namespace Camagru\views\user\forms;

use Camagru\routes\Router;
use Camagru\helpers\CSRF;

?>

<div class="grid grid--2">
<form action="<?= Router::to('update_user', ['id' => $user_id]) ?>" method="POST" class="form">
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
    <div class="flex flex-row gap-4">
        <button type="submit" class="button">
            Update
        </button>
    </div>
</form>

<div class="danger-zone">
    <h4 class="the-title">Danger Zone</h4>
    <p>Deleting your account will remove all your data from the system.</p>
    <form action="<?= Router::to('delete_user') ?>" method="POST">
        <?= CSRF::field() ?>
        <input type="hidden" name="id" value="<?= $user_id ?>">
        <button type="submit" class="button button--danger">
            Delete Account
        </button>
    </form>
</div>
</div>

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