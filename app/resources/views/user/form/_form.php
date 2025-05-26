<?php

namespace Camagru\views\user\forms;

use Camagru\routes\Router;
use Camagru\helpers\CSRF;
use Camagru\core\models\User;
use Camagru\helpers\Session;

?>

<div class="grid grid--2">
    <div class="actions flex flex-column gap-8">
        <?php if (Session::currentUser()->is_admin()) : ?>
            <div class="roles">
                <h4 class="the-title">Roles</h4>
                <p class="w-half">Change the role of this user.</p>
                <form action="<?= Router::to('edit_role') ?>" method="POST" class="form flex flex-column w-half">
                    <?= CSRF::field('csrf_edit_role') ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($user_id) ?>">
                    <select name="role" id="role" class="select mb-4">
                        <?php foreach (User::ROLES as $role) : ?>
                            <option value="<?= $role ?>" <?= $role === $old['role'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($role)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="button">
                        Update Role
                    </button>
                </form>
            </div>
        <?php endif; ?>
        <div class="notification">
            <h4 class="the-title">Notification</h4>
            <p class="w-half">Manage your email notification settings.</p>
            <form action="<?= Router::to('toggle_notification') ?>" method="POST">
                <?= CSRF::field('csrf_toggle_notification') ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($user_id) ?>">
                <button type="submit" class="button">
                    <?= $notification ?> Notifications
                </button>
            </form>
        </div>
        <div class="danger-zone">
            <h4 class="the-title">Danger Zone</h4>
            <p class="w-half">Deleting your account will remove all your data from the system.</p>
            <form action="<?= Router::to('delete_user', ['id' => $user_id]) ?>" method="POST">
                <?= CSRF::field('csrf_delete_user') ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($user_id) ?>">
                <button type="submit" class="button button--danger">
                    Delete Account
                </button>
            </form>
        </div>
    </div>

    <form action="<?= Router::to('update_user', ['id' => $user_id]) ?>" method="POST" class="form">
        <?= CSRF::field('csrf_update_user') ?>
        <input type="number" id="id" name="id" class="hidden" value="<?= htmlspecialchars($user_id) ?>">
        <div class="flex flex-column mb-4">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required value="<?= htmlspecialchars($old['username']) ?>">
        </div>
        <div class="flex flex-column mb-4">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($old['email']) ?>">
        </div>
        <div class="flex flex-column mb-4">
            <label for="old_password">Old Password:</label>
            <input type="password" id="old_password" name="old_password">
        </div>
        <div class="flex flex-column mb-4">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password">
        </div>
        <div class="flex flex-column mb-4">
            <label for="password_confirmation">New Password Confirmation:</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
        </div>
        <div class="flex flex-row gap-4">
            <button type="submit" class="button button--success">
                Update
            </button>
        </div>
    </form>
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