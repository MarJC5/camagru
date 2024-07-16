<?php

namespace Camagru\resources\components\layouts;

use Camagru\core\database\Runner;
use Camagru\helpers\Env;
use Camagru\helpers\Logger;
use Camagru\routes\Router;
use Camagru\helpers\Session;
use function Camagru\js_url;

?>

</main>

<footer>
    <div class="row flex flex-wrap justify-between">
        <div class="flex">
            <p class="mt-0">&copy; <?= date("Y"); ?> <?= Env::get('APP_NAME', 'Camagru') ?>
                <?php if (Runner::isMigrated()) : ?>
                    - <a href="<?= Router::to('page', ['slug' => 'privacy-policy']) ?>">Privacy Policy</a>
                <?php endif; ?>
            </p>
        </div>
        <div class="flex justify-end">
            <p class="mt-0">Developed by <a href="https://github.com/MarJC5" target="_blank">jmartin</a></p>
        </div>
    </div>
</footer>
<!-- JS -->
<script src="<?= js_url('app.js') ?>" type="module" nonce="<?= Session::get('nonce') ?>"></script>
</body>

</html>
















