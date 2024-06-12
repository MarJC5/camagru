<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;
use Camagru\helpers\Config;

class Media extends AModel
{
    protected $table = 'medias';
    protected $fillable = ['media_path', 'user_id', 'title', 'alt', 'legende'];

    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    public function getMediaPath()
    {
        return $this->data->media_path;
    }

    public function validation()
    {
        $config = Config::get('media');
        $allowedExtensions = implode(',', array_keys($config['allowed']));
        return [
            'media_path' => "required|max:{$config['size']}|mimes:{$allowedExtensions}",
        ];
    }

    public function uploadMedia($media): bool
    {
        $mediaPath = $this->uploadFile($media);
        if ($mediaPath) {
            $this->setMediaPath($mediaPath);
            return $this->save();
        }
        return false;
    }

    public function uploadFile($file)
    {
        $config = Config::get('media');
        $allowed = $config['allowed'];
        $maxsize = $config['size'];
        $uploadPath = $config['path'];

        // Check if the file was uploaded without errors
        if ($file['error'] === UPLOAD_ERR_OK) {
            $filename = $file['name'];
            $filetype = $file['type'];
            $filesize = $file['size'];

            // Validate file type and size
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            if (!isset($allowed[$extension])) {
                die("Error: Please select a valid file format.");
            }

            if ($filesize > $maxsize) {
                die("Error: File size is larger than the allowed limit.");
            }

            if (in_array($filetype, $allowed)) {
                $newFilename = uniqid() . "." . $extension;
                $destination = $uploadPath . $newFilename;

                // Ensure the upload path exists
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Move the file to the destination directory
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    return $destination;
                } else {
                    die("Error: There was a problem uploading your file. Please try again.");
                }
            } else {
                die("Error: There was a problem with your upload. Please try again.");
            }
        } else {
            die("Error: " . $file['error']);
        }
        return false;
    }

    public function uploadFromURL($link = null)
    {
        $url = $link ?? $_POST['url'];
        if (!$url) {
            die("Error: Please provide a URL.");
        }

        $config = Config::get('media');
        $allowed = $config['allowed'];
        $maxsize = $config['size'];
        $uploadPath = $config['path'];

        $filename = basename($url);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $filesize = filesize($url);

        // Validate file type and size
        if (!isset($allowed[$extension])) {
            die("Error: Please select a valid file format.");
        }

        if ($filesize > $maxsize) {
            die("Error: File size is larger than the allowed limit.");
        }

        $newFilename = uniqid() . "." . $extension;
        $destination = $uploadPath . $newFilename;

        // Ensure the upload path exists
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Move the file to the destination directory
        if (copy($url, $destination)) {
            return $destination;
        } else {
            die("Error: There was a problem uploading your file. Please try again.");
        }
        return false;
    }

    public function setMediaPath($mediaPath)
    {
        $this->data->media_path = $mediaPath;
    }
}
