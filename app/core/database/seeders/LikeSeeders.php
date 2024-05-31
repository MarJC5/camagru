<?php 

namespace Camagru\core\database\seeders;

use Camagru\core\database\seeders\ASeeders;

class LikeSeeders extends ASeeders
{
    protected $table = 'likes';
    
    public function __construct()
    {
        parent::__construct();
    }
}