<?php

namespace Camagru\core\database\migrations;

use Camagru\core\database\Migration;

return new class extends Migration
{
    public function up()
    {
        return $this->createTable('medias', [
            'id INT AUTO_INCREMENT PRIMARY KEY',
            'media_path VARCHAR(255) NOT NULL',
            'user_id INT NOT NULL',
            'title VARCHAR(255) NOT NULL',
            'alt VARCHAR(255) NOT NULL',
            'legende TEXT',
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE'
        ]);
    }

    public function down()
    {
        return $this->dropTable('medias');
    }
};