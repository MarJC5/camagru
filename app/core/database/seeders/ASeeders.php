<?php 

namespace Camagru\core\database\seeders;

use Camagru\core\database\Database;

abstract class ASeeders
{
    protected $db;
    protected $table;
    protected $data;

    public function __construct()
    {
        $this->db = new Database();
    }
}