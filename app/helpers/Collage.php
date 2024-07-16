<?php

namespace Camagru\helpers;

use Camagru\helpers\Config;

class Collage
{

    const ELEMENTS = [
        [
            'path' => Config::get('collage.path') . 'hand.png',
            'x' => 0,
            'y' => 0,
            'width' => 500,
            'height' => 500
        ],
        [
            'path' => Config::get('collage.path') . 'heart.png',
            'x' => 0,
            'y' => 0,
            'width' => 500,
            'height' => 500
        ],
        [
            'path' => Config::get('collage.path') . 'love.png',
            'x' => 0,
            'y' => 0,
            'width' => 500,
            'height' => 500
        ],
        [
            'path' => Config::get('collage.path') . 'pow.png',
            'x' => 0,
            'y' => 0,
            'width' => 500,
            'height' => 500
        ],
        [
            'path' => Config::get('collage.path') . 'smiley.png',
            'x' => 0,
            'y' => 0,
            'width' => 500,
            'height' => 500
        ]
    ];

    /**
     * Create a collage from an array of images and overlays.
     * 
     * @param array $elements An array of associative arrays with keys: 'path', 'x', 'y', 'width', 'height'.
     * @param int $width The width of the collage.
     * @param int $height The height of the collage.
     * @return resource|bool The collage image resource, or false if the elements array is empty.
     */

    public static function create(array $elements, int $width, int $height)
    {
        if (empty($elements)) {
            return false;
        }

        $collage = imagecreatetruecolor($width, $height);
        $backgroundColor = imagecolorallocate($collage, 255, 255, 255); // white background
        imagefill($collage, 0, 0, $backgroundColor);

        foreach ($elements as $element) {
            $img = imagecreatefromstring(file_get_contents($element['path']));
            $imgWidth = imagesx($img);
            $imgHeight = imagesy($img);

            $scaledImg = imagecreatetruecolor($element['width'], $element['height']);
            imagecopyresampled($scaledImg, $img, 0, 0, 0, 0, $element['width'], $element['height'], $imgWidth, $imgHeight);
            imagecopy($collage, $scaledImg, $element['x'], $element['y'], 0, 0, $element['width'], $element['height']);

            imagedestroy($img);
            imagedestroy($scaledImg);
        }
        return $collage;
    }
}
