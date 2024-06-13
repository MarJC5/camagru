<?php

namespace Camagru\core\controllers;

use Camagru\routes\Router;
use Camagru\core\models\Media;
use Camagru\helpers\Session;
use Camagru\helpers\Logger;

class MediaController
{
    public static function show($data)
    {
        $media_path = 'storage/uploads/medias/' . $data['filename'];
        $media = Media::where('media_path', $media_path)->first();
        
        if (empty($media)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        // Set the content type header to the media's mime type
        header('Content-Type: ' . $media->mimeType());

        // Read the file and output it
        echo readfile(BASE_PATH . '/' . $media->path());
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
