<?php

namespace Camagru\core\controllers;

use Camagru\routes\Router;
use Camagru\core\models\Media;
use Camagru\helpers\Session;
use Camagru\helpers\Logger;
use Camagru\core\models\Post;
use Camagru\helpers\Collage;
use Camagru\helpers\CSRF;
use Camagru\helpers\Config;
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

    public static function sticker($data)
    {
        $media_path = 'storage/collages/stickers/' . $data['filename'];

        // Check if stickers exist
        if (!file_exists(BASE_PATH . '/' . $media_path)) {
            Session::set('error', 'Invalid sticker file');
            Router::redirect('error', ['code' => 404]);
        }

        // Set the content type header to the media's mime type
        header('Content-Type: image/png');

        // Read the file and output it
        echo readfile(BASE_PATH . '/' . $media_path);
    }

    /**
     * Upload a media file.
     * 
     * This method expects a file to be present in the $_FILES array under the 'media' key.
     * It uploads the media file and sets a success or error message based on the result.
     */
    public static function upload()
    {
        // Return json response
        header('Content-Type: application/json');

        // Verify the CSRF token
        if (!isset($_POST['media_mode']) && $_POST['media_mode'] === 'shot') {
            if (!CSRF::verify($_POST['csrf_upload_shot_media'], 'csrf_upload_shot_media')) {
                Session::set('error', 'Invalid CSRF token');
                echo json_encode(['status' => 403, 'message' => 'Invalid CSRF token']);
                return;
            }
        } else if (!isset($_POST['media_mode']) && $_POST['media_mode'] === 'upload') {
            if (!CSRF::verify($_POST['csrf_upload_media'], 'csrf_upload_media')) {
                Session::set('error', 'Invalid CSRF token');
                echo json_encode(['status' => 403, 'message' => 'Invalid CSRF token']);
                return;
            }
        }

        if (!isset($_POST['image']) && !isset($_POST['sticker'])) {
            Session::set('error', 'Invalid request, missing parameters');
            echo json_encode(['status' => 400, 'message' => 'Invalid request, missing parameters']);
            return;
        }

        if (isset($_POST['image']) && isset($_POST['sticker'])) {
            // Validate base64 image
            if (!Collage::isValidBase64Image($_POST['image'])) {
                Session::set('error', 'Invalid base64 image data.');
                echo json_encode(['status' => 400, 'message' => 'Invalid base64 image data.']);
                return;
            }

            // Generate the collage
            $collage = Collage::create([
                [
                    'path' => $_POST['image'], // Base64 image
                    'x' => 0,
                    'y' => 0,
                    'width' => 500,
                    'height' => 500
                ],
                [
                    'path' => BASE_PATH . '/' . Config::get('collage.path') . 'stickers/' . $_POST['sticker'],
                    'x' => 0,
                    'y' => 0,
                    'width' => 500,
                    'height' => 500
                ]
            ], 500, 500);

            if (!$collage) {
                Session::set('error', 'Error creating collage.');
                echo json_encode(['status' => 500]);

                // Clean up
                imagedestroy($collage);

                return;
            }

            $filename = uniqid() . '.png';

            if (imagepng($collage, BASE_PATH . '/' . Config::get('media.path') . $filename)) {
                Session::set('success', 'File uploaded successfully.');
                
                // Save the media file
                $media = new Media();
                $status = $media->insert([
                    'media_path' => Config::get('media.path') . $filename,
                    'user_id' => Session::get('user'),
                    'title' => 'Collage ' . date('Y-m-d H:i:s'),
                    'alt' => 'Collage created on ' . date('Y-m-d H:i:s'),
                    'legende' => 'Collage created on ' . date('Y-m-d H:i:s')
                ]);

                if (!$status) {
                    Session::set('error', 'Error saving file.');

                    // Clean up
                    imagedestroy($collage);

                    // Send http response code
                    echo json_encode(['status' => 500]);

                    return;
                }

                // Clean up
                imagedestroy($collage);

                // Create the post
                $post = new Post();
                // Get the media id
                $media = Media::where('media_path', Config::get('media.path') . $filename)->first();
                $status = $post->insert([
                    'user_id' => Session::get('user'),
                    'media_id' => $media->id(),
                    'caption' => 'Collage created on ' . date('Y-m-d H:i:s')
                ]);

                if (!$status) {
                    Session::set('error', 'Error saving file.');
                }

                // Get the post id
                $post = Post::where('media_id', $media->id())->first();
                // Send http response code
                echo json_encode([
                    'status' => 200,
                    'redirect_url' => '/post/' . $post->id()
                ]);

                return;
            } else {
                Session::set('error', 'Error uploading file.');

                // Clean up
                imagedestroy($collage);

                // Send http response code
                echo json_encode(['status' => 500]);

                return;
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
