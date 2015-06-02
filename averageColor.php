<?php

/**
 * imageColor
 *
 * Shows three methods to find the 'average' image color.
 *
 * Each function expects a gd image object.
 *
 * imageColor::averageResize($image) resizing to 1px, and checking the color.
 * imageColor::averageBorder($image) find the average color of all border pixels.
 * imageColor::averageImage($image) find the average color of all pixels.
 *
 */
class imageColor {

    function scanLine($image, $height, $width, $axis, $line) {
        $i = 0;

        if ("x" == $axis) {
            $limit = $width;
            $y = $line;
            $x = & $i;

            if (-1 == $line) {
                $y = 0;
                $y2 = $width - 1;
                $x2 = & $i;
            }
        } else {
            $limit = $height;
            $x = $line;
            $y = & $i;

            if (-1 == $line) {
                $x = 0;
                $x2 = $width - 1;
                $y2 = & $i;
            }
        }

        $colors = array();

        if (-1 == $line) {
            for ($i = 0; $i < $limit; $i++) {
                self::addPixel($colors, $image, $x, $y);
                self::addPixel($colors, $image, $x2, $y2);
            }
        } else {
            for ($i = 0; $i < $limit; $i++) {
                self::addPixel($colors, $image, $x, $y);
            }
        }

        return $colors;
    }

    function addPixel(&$colors, $image, $x, $y) {
        $rgb = imagecolorat($image, $x, $y);
        $color = imagecolorsforindex($image, $rgb);
        $colors['red'][] = $color['red'];
        $colors['green'][] = $color['green'];
        $colors['blue'][] = $color['blue'];
    }

    function totalColors($color, $colors) {
        $color['red'] += array_sum($colors['red']);
        $color['green'] += array_sum($colors['green']);
        $color['blue'] += array_sum($colors['blue']);

        return $colors;
    }

    function averageTotal($color, $count) {
        $color['red'] = intval($color['red'] / $count);
        $color['green'] = intval($color['green'] / $count);
        $color['blue'] = intval($color['blue'] / $count);

        return $color;
    }

    function averageResize($image) {
        $width = imagesx($image);
        $height = imagesy($image);

        $pixel = imagecreatetruecolor(1, 1);
        imagecopyresampled($pixel, $image, 0, 0, 0, 0, 1, 1, $width, $height);
        $rgb = imagecolorat($pixel, 0, 0);
        $color = imagecolorsforindex($pixel, $rgb);

        return $color;
    }

    function averageBorder($image) {
        $width = imagesx($image);
        $height = imagesy($image);

        $colors = self::scanLine($image, $height, $width, 'x', -1);
        self::totalColors(&$color, $colors);

        $colors = self::scanLine($image, $height, $width, 'y', -1);
        self::totalColors(&$color, $colors);

        $borderSize = ($height = $width) * 2;
        self::averageTotal(&$color, $borderSize);

        return $color;
    }

    function averageImage($image) {
        $width = imagesx($image);
        $height = imagesy($image);

        $colors = array();

        for ($line = 0; $line < $height; $line++) {
            $colors = self::scanLine($image, $height, $width, 'x', $line);
            self::totalColors(&$color, $colors);
        }

        $count = $width * $height;
        self::averageTotal(&$color, $count);

        return $color;
    }

}
?>