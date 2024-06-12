<?php

namespace Camagru\core\controllers;

use Camagru\routes\Router;
use Camagru\core\models\Media;
use Camagru\helpers\Session;

use function Camagru\loadView;

class MediaController
{
    public static function show($data)
    {
        $id = $data['id'];
        $media = new Media($id );
        
        if (empty($media)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        echo loadView('media/show.php', [
            'media' => $media,
        ]);
    }

    public static function upload()
    {
        $media = new Media();
        if ($media->uploadMedia($_FILES['media'])) {
            Session::set('success', 'File uploaded successfully.');
        } else {
            Session::set('error', 'Error uploading file.');
        }
    }

    public static function delete()
    {
        $media = new Media($_POST['id']);
        if ($media->delete()) {
            Session::set('success', 'File deleted successfully.');
        } else {
            Session::set('error', 'Error deleting file.');
        }
    }
}
