<?php

namespace Camagru\views\page\forms;

use Camagru\routes\Router;
use Camagru\helpers\CSRF;

?>

<form action="<?= Router::to($type === 'create' ? 'create_page' : 'edit_page') ?>" method="POST" class="form w-half">
    <?= CSRF::field() ?>
    <?php if ($type === 'edit') : ?>
        <input type="number" id="id" name="id" class="hidden" value="<?= $id ?>">
    <?php endif; ?>
    <div class="flex flex-column mb-4">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required value="<?= htmlspecialchars($old['title']) ?>">
    </div>
    <div class="flex flex-column mb-4">
        <label for="slug">Slug:</label>
        <input type="text" id="slug" name="slug" class="disabled" required value="<?= htmlspecialchars($old['slug'])?>" disabled>
    </div>
    <div class="flex flex-column mb-4">
        <label for="content">Content:</label>
        <textarea id="content" name="content" required rows="10" cols="50" placeholder="Enter content here"><?= htmlspecialchars($old['content']) ?></textarea>
    </div>
    <div class="flex flex-row gap-4">
        <button type="submit" class="button button--success">
            <?= $type === 'create' ? 'Create' : 'Update' ?>
        </button>
    </div>
</form>

<script>
    document.getElementById('title').addEventListener('input', function() {
        var title = this.value;
        var slug = title.toLowerCase().trim()
            .replace(/[^\w\s-]/g, '') // Remove all non-word characters
            .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with a hyphen
            .replace(/^-+|-+$/g, ''); // Remove leading or trailing hyphens
        document.getElementById('slug').value = slug;
    });
</script>