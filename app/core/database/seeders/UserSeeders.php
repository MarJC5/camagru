<?php 

namespace Camagru\core\database\seeders;

use Camagru\core\database\seeders\ASeeders;
use Camagru\helpers\Env;
use Camagru\helpers\CSRF;

class UserSeeders extends ASeeders
{
    protected $table = 'users';
    
    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        $this->createUsers();
    }

    private function createUsers()
    {
        $users = [
            [
                'username' => Env::get('ADMIN_NAME'), 
                'email' => Env::get('ADMIN_EMAIL'),
                'password' => password_hash(Env::get('ADMIN_PASSWORD'), PASSWORD_DEFAULT),
                'role' => 'admin',
                'validated' => 1,
                'token' => password_hash(CSRF::generate(), PASSWORD_DEFAULT),
            ],
        ];

        foreach ($users as $user) {
            $this->db->insertIfNotExists('users', $user, 'email');
        }
    }
}