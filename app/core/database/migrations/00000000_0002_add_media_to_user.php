<?php

namespace Camagru\core\database\migrations;

use Camagru\core\database\Migration;

return new class extends Migration
{
    public function up()
    {
        return $this->alterTable('users', [
            'ADD CONSTRAINT fk_media_id FOREIGN KEY (media_id) REFERENCES medias(id) ON DELETE SET NULL'
        ]);
    }

    public function down()
    {
        return $this->alterTable('users', [
            'DROP FOREIGN KEY fk_media_id'
        ]);
    }
};
