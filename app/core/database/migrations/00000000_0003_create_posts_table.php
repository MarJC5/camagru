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
            'media_id INT NOT NULL',
            'caption TEXT',
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE',
            'FOREIGN KEY (media_id) REFERENCES medias(id) ON DELETE CASCADE'
        ]);
    }

    public function down()
    {
        return $this->dropTable('posts');
    }
};