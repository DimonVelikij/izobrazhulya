<?php

class ImagePalette
{
    /**
     * получаем наиболее часто встречающиеся цвета
     * @param $imageFile_URL
     * @param $numColors
     * @param int $image_granularity
     * @return array|bool
     */
    function getImageColors($imageFile_URL, $numColors, $image_granularity = 5)
    {
        $image_granularity = max(1, abs((int)$image_granularity));
        $colors = array();

        $size = @getimagesize($imageFile_URL);
        if ($size === false) {
            return false;
        }

        $img = @imagecreatefromjpeg($imageFile_URL);
        if (!$img) {
            return false;
        }

        for ($x = 0; $x < $size[0]; $x += $image_granularity) {
            for ($y = 0; $y < $size[1]; $y += $image_granularity) {
                $thisColor = imagecolorat($img, $x, $y);
                $rgb = imagecolorsforindex($img, $thisColor);
                $red = round(round(($rgb['red'] / 0x33)) * 0x33);
                $green = round(round(($rgb['green'] / 0x33)) * 0x33);
                $blue = round(round(($rgb['blue'] / 0x33)) * 0x33);
                $thisRGB = sprintf('%02X%02X%02X', $red, $green, $blue);
                if (array_key_exists($thisRGB, $colors)) {
                    $colors[$thisRGB]++;
                } else {
                    $colors[$thisRGB] = 1;
                }
            }
        }
        arsort($colors);

        return array_slice(array_keys($colors), 0, $numColors);
    }
}