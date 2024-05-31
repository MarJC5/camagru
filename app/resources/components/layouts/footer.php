<?php 

namespace Camagru\resources\components\layouts;

use Camagru\helpers\Env;
use function Camagru\js_url;

?>

</main>

<footer>
    <div class="row grid">
        <p>&copy; <?= date("Y"); ?> <?= Env::get('APP_NAME', 'Camagru') ?></p>
    </div>
</footer>
<!-- JS -->
<script src="<?= js_url('app.js') ?>" type="module"></script>
</body>
</html>