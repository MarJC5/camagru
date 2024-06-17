<?php

namespace Camagru\core\controllers;

use Camagru\routes\Router;
use Camagru\core\models\Media;
use Camagru\helpers\Session;
use Camagru\helpers\Logger;
use Camagru\helpers\CSRF;
use Camagru\core\middlewares\Validation;

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
        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_upload_media'], 'csrf_upload_media')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

        $media = new Media();

        $validation = new Validation();
        $rules = $media->validation();
        $data = [
            'media' => [
                'size' => $_FILES['media']['size'],
                'mimes' => $_FILES['media']['type'],
            ]
        ];
        $validation->validate($data, $rules);

        if ($validation->fails()) {
            $errors = $validation->getErrors();

            Session::set('error', $errors);
            Router::redirect('home');
        } else {
            if ($media->uploadMedia($_FILES['media'])) {
                Session::set('success', 'File uploaded successfully.');
            } else {
                Session::set('error', 'Error uploading file.');
            }
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
        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_delete_media'], 'csrf_delete_media')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

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
