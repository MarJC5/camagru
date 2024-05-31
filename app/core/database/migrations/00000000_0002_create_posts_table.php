<?php

namespace Camagru\core\database\migrations;

use Camagru\core\database\Migration;

return new class extends Migration
{
    public function up()
    {
        return $this->createTable('posts', [
            'id INT AUTO_INCREMENT PRIMARY KEY',
            'user_id INT NOT NULL',
            'image VARCHAR(255) NOT NULL',
            'caption TEXT',
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);
    }

    public function down()
    {
        return $this->dropTable('posts');
    }
};