<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Helpers;
! defined( 'ABSPATH' ) && exit();



class Colors
{
    /**
     * @param  string  $hex
     *
     * @return array|int
     */
    public static function convertHexToRGB($hex)
    {
        return sscanf($hex, '#%2x%2x%2x');
    }

    /**
     * @param $hex
     *
     * @return array
     */
    public static function convertHexToHSL($hex)
    {
        list($r, $g, $b) = static::convertHexToRGB($hex);

        $r /= 255;
        $g /= 255;
        $b /= 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $h = 0;
        $l = ($max + $min) / 2;
        if ($max === $min) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            switch ($max) {
                case $r:
                    $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                    break;
                case $g:
                    $h = ($b - $r) / $d + 2;
                    break;
                case $b:
                    $h = ($r - $g) / $d + 4;
                    break;
            }
            $h /= 6;
        }
        $h = floor($h * 360);
        $s = floor($s * 100);
        $l = floor($l * 100);

        return [$h, $s, $l];
    }

    /**
     * @param  string  $hex
     * @param  int  $amount
     *
     * @return string
     */
    public static function lignten($hex, $amount = 0)
    {
        list($h, $s, $l) = static::convertHexToHSL($hex);

        if ($amount > 0) {
            $value = floor($l * $amount / 100);
            $l = min($value + $l, 100);
        }

        return sprintf('hsl(%s, %s%%, %s%%)', $h, $s, $l);
    }

    /**
     * @param  string  $hex
     * @param  int  $amount
     *
     * @return string
     */
    public static function darken($hex, $amount = 0)
    {
        list($h, $s, $l) = static::convertHexToHSL($hex);

        if ($amount > 0) {
            $value = floor($l * $amount / 100);
            $l = max($l - $value, 0);
        }

        return sprintf('hsl(%s, %s%%, %s%%)', $h, $s, $l);
    }

    /**
     * @param  string  $hex
     * @param  float  $alpha
     *
     * @return string
     */
    public static function opacity($hex, $alpha = 0.0)
    {
        list($r, $g, $b) = static::convertHexToRGB($hex);


        return sprintf('rgba(%s, %s, %s, %f)', $r, $g, $b, $alpha);
    }

}