<?php

namespace Camagru\core\database\migrations;

use Camagru\core\database\Migration;

return new class extends Migration
{
    public function up()
    {
        return $this->createTable('comments', [
            'id INT AUTO_INCREMENT PRIMARY KEY',
            'user_id INT NOT NULL',
            'post_id INT NOT NULL',
            'comment TEXT NOT NULL',
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);
    }

    public function down()
    {
        return $this->dropTable('comments');
    }
};