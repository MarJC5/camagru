<?php

namespace Camagru\database\migrations;

use Camagru\database\Migration;

return new class extends Migration
{
    public function up()
    {
        return $this->createTable('pages', [
            'id INT AUTO_INCREMENT PRIMARY KEY',
            'title VARCHAR(255) NOT NULL',
            'content TEXT NOT NULL',
            'slug VARCHAR(255) NOT NULL UNIQUE',
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);
    }

    public function down()
    {
        return $this->dropTable('pages');
    }
};