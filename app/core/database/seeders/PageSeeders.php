<?php 

namespace Camagru\core\database\seeders;

use Camagru\core\database\seeders\ASeeders;

class PageSeeders extends ASeeders
{
    protected $table = 'pages';
    
    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        $this->createPages();
    }

    private function createPages()
    {
        $pages = [
            [
                'title' => 'Hello, Camagru!', 
                'slug' => 'home', 
                'content' => '<p>Welcome to Camagru! This is a simple photo sharing application that allows you to take pictures, apply filters, and share them with your friends.</p>',
            ],
            [
                'title' => 'About', 
                'slug' => 'about', 
                'content' => '<p>Create a small Instagram-like site allowing users to create and share photo montages. Thus implement, with a bare hands (frameworks are prohibited), the basic functionalities encountered on the majority of sites with a user base.</p>',
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<p>Camagru is a photo sharing application that allows you to take pictures, apply filters, and share them with your friends. We take your privacy seriously and will never share your information with third parties.</p>',
            ],
        ];

        foreach ($pages as $page) {
            $this->db->insertIfNotExists('pages', $page, 'slug');
        }
    }
}