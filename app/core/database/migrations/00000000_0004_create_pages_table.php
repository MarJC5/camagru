<?php

namespace Camagru\core\database\migrations;

use Camagru\core\database\Migration;

return new class extends Migration
{
    public function up()
    {
        return $this->createTable('pages', [
            'id INT AUTO_INCREMENT PRIMARY KEY',
            'title VARCHAR(255) NOT NULL',
            'content TEXT',
            'slug VARCHAR(255) NOT NULL UNIQUE',
            'media_id INT',
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'FOREIGN KEY (media_id) REFERENCES medias(id) ON DELETE SET NULL'
        ]);
    }

    public function down()
    {
        return $this->dropTable('pages');
    }
};