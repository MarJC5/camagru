<?php

namespace Camagru\core\controllers;

use Camagru\routes\Router;
use Camagru\core\models\Media;
use Camagru\helpers\Session;
use Camagru\helpers\Logger;

/**
 * Class MediaController
 * Handles actions related to media, such as showing, uploading, and deleting media files.
 */
class MediaController
{
    /**
     * Show the media file.
     * 
     * This method expects 'filename' to be present in the data array.
     * It retrieves the media by its path and outputs the file if it exists.
     * If the media does not exist, it redirects to the error page.
     *
     * @param array $data The data array containing 'filename'.
     */
    public static function show($data)
    {
        $media_path = 'storage/uploads/medias/' . $data['filename'];
        $media = Media::where('media_path', $media_path)->first();
        
        if (empty($media)) {
            Session::set('error', 'Invalid media file');
            Router::redirect('error', ['code' => 404]);
        }

        // Set the content type header to the media's mime type
        header('Content-Type: ' . $media->mimeType());

        // Read the file and output it
        echo readfile(BASE_PATH . '/' . $media->path());
    }

    /**
     * Upload a media file.
     * 
     * This method expects a file to be present in the $_FILES array under the 'media' key.
     * It uploads the media file and sets a success or error message based on the result.
     */
    public static function upload()
    {
        $media = new Media();
        if ($media->uploadMedia($_FILES['media'])) {
            Session::set('success', 'File uploaded successfully.');
        } else {
            Session::set('error', 'Error uploading file.');
        }
    }

    /**
     * Delete a media file.
     * 
     * This method expects 'id' to be present in the POST request.
     * It deletes the media file and sets a success or error message based on the result.
     */
    public static function delete()
    {
        if (!isset($_POST['id'])) {
            Session::set('error', 'Invalid request, missing parameters');
            Router::redirect('profile');
        }

        $media = new Media($_POST['id']);
        if ($media->delete()) {
            Session::set('success', 'File deleted successfully.');
        } else {
            Session::set('error', 'Error deleting file.');
        }
    }
}
