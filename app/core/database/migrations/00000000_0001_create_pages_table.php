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
            'thumbnail VARCHAR(255)',
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);
    }

    public function down()
    {
        return $this->dropTable('pages');
    }
};