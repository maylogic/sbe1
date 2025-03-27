<?php

namespace App\Utils;

class ColorUtils
{
    public static function rgbStringToHex(string $rgbString)
    {
        [$red, $green, $blue] = explode(',', substr($rgbString, 4, strlen($rgbString) - 1));
        return self::rgbToHex($red, $green, $blue);
    }

    public static function rgbToHex($red, $green, $blue): string
    {
        $red = intval($red);
        $green = intval($green);
        $blue = intval($blue);
        // Проверяем, что значения находятся в допустимом диапазоне
        if ($red < 0 || $red > 255 || $green < 0 || $green > 255 || $blue < 0 || $blue > 255) {
            throw new \InvalidArgumentException('invalid color');
        }

        // Преобразуем каждое значение RGB в шестнадцатеричное
        $hexRed = str_pad(dechex($red), 2, '0', STR_PAD_LEFT);
        $hexGreen = str_pad(dechex($green), 2, '0', STR_PAD_LEFT);
        $hexBlue = str_pad(dechex($blue), 2, '0', STR_PAD_LEFT);

        // Возвращаем HEX-значение
        return "#" . $hexRed . $hexGreen . $hexBlue;
    }
}
