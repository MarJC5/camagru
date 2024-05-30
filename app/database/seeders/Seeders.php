<?php 

namespace Camagru\database\seeders;

use Camagru\database\Database;

abstract class Seeders
{
    protected $db;
    protected $table;
    protected $data;

    public function __construct()
    {
        $this->db = new Database();
    }
}