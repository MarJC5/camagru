<?php

namespace Camagru\resources\components\layouts\user;

use Camagru\routes\Router;

$user = $data['user'] ?? null;
?>

<?php if ($user) : ?>
    <a href="<?= Router::to('user', ['id' => $user->id()]) ?>" class="underline-none">
    <div class="flex item-center gap-2 mb-8">
        <div class="avatar-text flex justify-center item-center bg-gray-100 rounded-full w-14 h-14">
            <p class="capitalize text-bold text-2xl text-gray-500 m-0"><?= substr($user->username(), 0, 1) ?></p>
        </div>
        <h1 class="the-title m-0">@<?= $user->username() ?></h1>
    </div>
    </a>
<?php endif; ?>