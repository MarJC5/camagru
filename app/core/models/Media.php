<?php

namespace Camagru\core\models;

use Camagru\core\models\AModel;
use Camagru\core\models\User;
use Camagru\helpers\Config;

/**
 * Class Media
 * Model representing media in the application.
 */
class Media extends AModel
{
    protected $table = 'medias';
    protected $fillable = ['media_path', 'user_id', 'title', 'alt', 'legende'];

    /**
     * Media constructor.
     *
     * @param int|null $id The ID of the media to load.
     */
    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    /**
     * Get the path of the media file.
     *
     * @return string
     */
    public function path()
    {
        return $this->data->media_path ?? '';
    }

    /**
     * Get the alt text of the media.
     *
     * @return string
     */
    public function alt()
    {
        return $this->data->alt ?? '';
    }

    /**
     * Get the legende (caption) of the media.
     *
     * @return string
     */
    public function legende()
    {
        return $this->data->legende ?? '';
    }

    /**
     * Get the public URL of the media.
     *
     * @return string
     */
    public function publicURL()
    {
        $config = Config::get('media');
        $siteURL = Config::get('site_url');
        $mediaPath = str_replace($config['path'], '', $this->path());
        return $siteURL . '/medias/' . $mediaPath;
    }

    /**
     * Get the validation rules for the media.
     *
     * @return array
     */
    public function validation()
    {
        $config = Config::get('media');
        $allowedExtensions = implode(',', array_keys($config['allowed']));
        return [
            'media' => "required|size:{$config['size']}|mimes:{$allowedExtensions}",
        ];
    }

     /**
     * Get the validation rules for the media.
     *
     * @return array
     */
    public function validationCollage()
    {
        $config = Config::get('media');
        $allowedExtensions = implode(',', array_keys($config['allowed']));
        return [
            'image' => "required|size:{$config['size']}|mimes:{$allowedExtensions}",
            'sticker' => "required|size:{$config['size']}|mimes:{$allowedExtensions}",
        ];
    }

    /**
     * Upload the media file and save its path.
     *
     * @param array $media The media file to upload.
     * @return bool
     */
    public function uploadMedia($media): bool
    {
        $mediaPath = $this->uploadFile($media);
        if ($mediaPath) {
            $this->setMediaPath($mediaPath);
            return $this->save();
        }
        return false;
    }

    /**
     * Get the user who uploaded the media.
     *
     * @return User
     */
    public function user()
    {
        return new User($this->data->user_id);
    }

    /**
     * Set the media path.
     *
     * @param string $mediaPath The media path to set.
     */
    public function setMediaPath($mediaPath)
    {
        $this->data->media_path = $mediaPath;
    }

    /**
     * Get the MIME type of the media.
     *
     * @return string
     */
    public function mimeType()
    {
        return mime_content_type(BASE_PATH . '/' . $this->path());
    }

    /**
     * Convert the media to a JSON-serializable format.
     *
     * @return array
     */
    public function toJSON()
    {
        if (!$this->id()) {
            return [];
        }
        
        return [
            'path' => $this->path(),
            'src' => $this->publicURL(),
            'alt' => $this->alt() ?? '',
            'legende' => $this->legende() ?? '',
        ];
    }

    /**
     * Upload a file to the server.
     *
     * @param array $file The file to upload.
     * @return string|false The path to the uploaded file, or false on failure.
     */
    public function uploadFile($file)
    {
        $config = Config::get('media');
        $allowed = $config['allowed'];
        $maxsize = $config['size'];
        $uploadPath = BASE_PATH . '/' . $config['path'];

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
                $path = $config['path'] . $newFilename;

                // Ensure the upload path exists
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Move the file to the destination directory
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    unlink($file['tmp_name']); // Remove the temporary file
                    return $path;
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

    /**
     * Upload a media file from a URL.
     *
     * @param string|null $link The URL of the media file to upload.
     * @return string|false The path to the uploaded file, or false on failure.
     */
    public function uploadFromURL($link = null)
    {
        $url = $link ?? $_POST['url'];
        if (!$url) {
            die("Error: Please provide a URL.");
        }

        $config = Config::get('media');
        $allowed = $config['allowed'];
        $maxsize = $config['size'];
        $uploadPath = BASE_PATH . '/' . $config['path'];

        // Query the URL and get the file content
        $fileContent = file_get_contents($url);
        if ($fileContent === false) {
            die("Error: Could not download the file.");
        }

        // Check the headers for the file type
        $headers = get_headers($url, 1);
        $contentType = $headers['Content-Type'];
        $extension = array_search($contentType, $allowed);

        // Check if the extension is allowed
        if (!array_key_exists($extension, $allowed)) {
            die("Error: Please select a valid file format.");
        }

        // Get the file size from the downloaded content
        $filesize = strlen($fileContent);

        // Validate file size
        if ($filesize > $maxsize) {
            die("Error: File size is larger than the allowed limit.");
        }

        // Write the file content to a temporary file to check MIME type
        $tempFile = tempnam(sys_get_temp_dir(), 'camagru_');
        file_put_contents($tempFile, $fileContent);

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tempFile);

        // Validate MIME type
        if ($mimeType !== $allowed[$extension]) {
            unlink($tempFile);
            die("Error: The file MIME type does not match the allowed format.");
        }

        $newFilename = uniqid() . "." . $extension;
        $destination = $uploadPath . $newFilename;
        $path = $config['path'] . $newFilename;

        // Ensure the upload path exists
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Move the file to the destination directory
        if (copy($tempFile, $destination)) {
            unlink($tempFile); // Remove the temporary file
            return $path;
        } else {
            unlink($tempFile); // Remove the temporary file
            die("Error: There was a problem uploading your file. Please try again.");
        }
        return false;
    }
}
