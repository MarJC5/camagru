<?php 

namespace Camagru\database\seeders;

use Camagru\database\seeders\Seeders;

class PageSeeders extends Seeders
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
                'content' => 'Welcome to Camagru! This is a simple photo sharing application that allows you to take pictures, apply filters, and share them with your friends.',
            ],
            [
                'title' => 'About', 
                'slug' => 'about', 
                'content' => 'Create a small Instagram-like site allowing users to create and share photo montages. Thus implement, with a bare hands (frameworks are prohibited), the basic functionalities encountered on the majority of sites with a user base.',
            ],
        ];

        foreach ($pages as $page) {
            $this->db->insertIfNotExists('pages', $page, 'slug');
        }
    }
}