<?php

namespace Camagru\helpers;

use Camagru\helpers\Config;

class Collage
{
    protected static $stickers = [];
    protected static $filters = [];

    public static function initialize()
    {
        self::$stickers = [
            [
                'name' => 'hand',
                'path' => BASE_URL . '/' . Config::get('collage.path') . 'stickers/hand.png',
                'x' => 0,
                'y' => 0,
                'width' => 500,
                'height' => 500
            ],
            [
                'name' => 'heart',
                'path' => BASE_URL . '/' . Config::get('collage.path') . 'stickers/heart.png',
                'x' => 0,
                'y' => 0,
                'width' => 500,
                'height' => 500
            ],
            [
                'name' => 'love',
                'path' => BASE_URL . '/' . Config::get('collage.path') . 'stickers/love.png',
                'x' => 0,
                'y' => 0,
                'width' => 500,
                'height' => 500
            ],
            [
                'name' => 'pow',
                'path' => BASE_URL . '/' . Config::get('collage.path') . 'stickers/pow.png',
                'x' => 0,
                'y' => 0,
                'width' => 500,
                'height' => 500
            ],
            [
                'name' => 'smiley',
                'path' => BASE_URL . '/' . Config::get('collage.path') . 'stickers/smiley.png',
                'x' => 0,
                'y' => 0,
                'width' => 500,
                'height' => 500
            ]
        ];

        self::$filters = [];
    }

    /**
     * Collage constructor - Initialize the stickers and filters.
     *
     */
    public function __construct()
    {
        self::initialize();
    }

    public static function getStickers()
    {
        self::initialize();
        return self::$stickers;
    }

    public static function getFilters()
    {
        self::initialize();
        return self::$filters;
    }

    /**
     * Create a collage from an array of images and overlays.
     * 
     * @param array $elements An array of associative arrays with keys: 'path', 'x', 'y', 'width', 'height'.
     * @param int $width The width of the collage.
     * @param int $height The height of the collage.
     * @return \GdImage The collage image resource or false if an error occurred.
     */
    public static function create(array $elements, int $width, int $height)
    {
        if (empty($elements)) {
            return false;
        }

        // Create a new true color image for the collage
        $collage = imagecreatetruecolor($width, $height);

        if ($collage === false) {
            return false;
        }

        // Set background color to white
        $backgroundColor = imagecolorallocate($collage, 255, 255, 255);
        imagefill($collage, 0, 0, $backgroundColor);

        foreach ($elements as $element) {
            // Check if the path is a base64 string or a file path
            if (strpos($element['path'], 'data:image') === 0) {
                // Handle base64 encoded image
                list($type, $data) = explode(';', $element['path']);
                list(, $data) = explode(',', $data);
                $img = imagecreatefromstring(base64_decode($data));

                if ($img === false) {
                    imagedestroy($collage);
                    return false;
                }
            } else {
                // Handle file path
                $img = imagecreatefromstring(file_get_contents($element['path']));
                if ($img === false) {
                    imagedestroy($collage);
                    return false;
                }
            }

            // Handle transparency if it's a PNG
            if (strpos($element['path'], '.png') !== false || strpos($element['path'], 'image/png') !== false) {
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }

            // Get original dimensions of the image
            $imgWidth = imagesx($img);
            $imgHeight = imagesy($img);

            // Create a scaled version of the image
            $scaledImg = imagecreatetruecolor($element['width'], $element['height']);
            if ($scaledImg === false) {
                imagedestroy($img);
                imagedestroy($collage);
                return false;
            }

            // Preserve transparency for the scaled image if it's a PNG
            if (strpos($element['path'], '.png') !== false || strpos($element['path'], 'image/png') !== false) {
                imagealphablending($scaledImg, false);
                imagesavealpha($scaledImg, true);
                $transparent = imagecolorallocatealpha($scaledImg, 0, 0, 0, 127);
                imagefill($scaledImg, 0, 0, $transparent);
            }

            imagecopyresampled($scaledImg, $img, 0, 0, 0, 0, $element['width'], $element['height'], $imgWidth, $imgHeight);
            imagecopy($collage, $scaledImg, $element['x'], $element['y'], 0, 0, $element['width'], $element['height']);

            imagedestroy($img);
            imagedestroy($scaledImg);
        }

        return $collage;
    }

    /**
     * Check if a base64 string is a valid image.
     * 
     * @param string $base64 The base64 string to check.
     * @return bool True if the base64 string is a valid image, false otherwise.
     */
    public static function isValidBase64Image($base64)
    {
        // Check if it's a valid base64 string and extract MIME type
        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
            // Extract MIME type and convert to lowercase
            $mimeType = strtolower($type[1]); // e.g., 'jpeg', 'png', 'gif', 'webp'
            
            // Validate the MIME type against allowed types from configuration
            $allowedMimes = Config::get('media.allowed');
            if (!isset($allowedMimes[$mimeType]) || $allowedMimes[$mimeType] !== 'image/' . $mimeType) {
                return false;
            }

            // Remove the base64 prefix and decode the string
            $base64 = substr($base64, strpos($base64, ',') + 1);
            $decodedData = base64_decode($base64, true);

            // Ensure the base64 string is valid
            if ($decodedData === false) {
                return false;
            }

            // Check if the decoded data is a valid image
            $img = imagecreatefromstring($decodedData);
            if ($img === false) {
                return false;
            }

            // Clean up
            imagedestroy($img);

            return true;
        }

        return false;
    }
}
