<?php

namespace Camagru\resources\components\ui;

use Camagru\helpers\Session;

?>

<div class="alerts">
<?php if (Session::has('error')) : ?>
    <div class="alert alert--danger">
        <?= Session::flash('error') ?>
    </div>
<?php endif; ?>

<?php if (Session::has('success')) : ?>
    <div class="alert alert--success">
        <?= Session::flash('success') ?>
    </div>
<?php endif; ?>
</div>