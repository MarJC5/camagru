<?php

namespace Camagru\resources\components\forms;

use Camagru\routes\Router;
use Camagru\helpers\CSRF;

?>

<form action="<?= Router::to('upload_media') ?>" method="POST" enctype="multipart/form-data">
    <?= CSRF::field('csrf_upload_media') ?>
    <label for="fileSelect">Filename:</label>
    <input type="file" name="media" id="fileSelect">
    <input type="submit" name="submit" value="Upload">
</form>