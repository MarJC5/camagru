<?php

namespace Camagru\database\migrations;

use Camagru\database\Migration;

return new class extends Migration
{
    public function up()
    {
        return $this->createTable('users', [
            'id INT AUTO_INCREMENT PRIMARY KEY',
            'username VARCHAR(255) NOT NULL',
            'email VARCHAR(255) NOT NULL',
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