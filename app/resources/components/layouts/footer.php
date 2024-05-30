<?php 

namespace Camagru\resources\components\layouts;

use Camagru\helpers\Env;
use function Camagru\js_url;

?>

</main>

<footer>
    <p>&copy; <?= date("Y"); ?> <?= Env::get('APP_NAME', 'Camagru') ?></p>
</footer>
<!-- JS -->
<script src="<?= js_url('app.js') ?>" type="text/javascript"></script>
</body>
</html>