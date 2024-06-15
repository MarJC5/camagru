<?php

namespace Camagru\core\controllers;

use function Camagru\loadView;

/**
 * Class ErrorController
 * Handles actions related to errors, such as displaying error pages.
 */
class ErrorController
{
    /**
     * Display the error page.
     * 
     * @param array $data The data array containing 'code'.
     * @return void
     */
    public static function error($data)
    {
        $code = $data['code'];
        http_response_code($code);
        $_GET['title'] = $code;
        echo loadView('error/error.php', [
            'title' => $code
        ]);
    }
}
