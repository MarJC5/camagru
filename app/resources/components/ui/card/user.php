<?php

namespace Camagru\resources\components\ui\card;

use Camagru\routes\Router;

$user = $data['user'] ?? null;
?>

<?php if ($user) : ?>
    <div class="card card--user flex flex-column gap-4 shadow p-4 rounded-md">
        <a href="<?= Router::to('user', ['id' => $user->id()]) ?>" class="underline-none">
            <div class="card__head flex item-center gap-2">
                <div class="avatar-text flex justify-center item-center bg-gray-100 rounded-full w-8 h-8">
                    <p class="capitalize text-bold text-md text-gray-500 m-0"><?= substr($user->username(), 0, 1) ?></p>
                </div>
                <p class="m-0">@<?= $user->username() ?></p>
            </div>
            <a href="<?= Router::to('user', ['id' => $user->id()]) ?>">View</a>
        </a>
    </div>
<?php endif; ?>