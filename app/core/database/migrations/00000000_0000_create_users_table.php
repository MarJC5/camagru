<?php

namespace Camagru\core\database\migrations;

use Camagru\core\database\Migration;

return new class extends Migration
{
    public function up()
    {
        return $this->createTable('users', [
            'id INT AUTO_INCREMENT PRIMARY KEY',
            'username VARCHAR(255) NOT NULL',
            'email VARCHAR(255) NOT NULL UNIQUE',
            'password VARCHAR(255) NOT NULL',
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);
    }

    public function down()
    {
        return $this->dropTable('users');
    }
};