<?php 

namespace Camagru\core\database\seeders;

use Camagru\core\database\seeders\ASeeders;

class UserSeeders extends ASeeders
{
    protected $table = 'users';
    
    public function __construct()
    {
        parent::__construct();
    }
}