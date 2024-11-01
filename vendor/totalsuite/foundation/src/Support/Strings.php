<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Support;
! defined( 'ABSPATH' ) && exit();


use Exception;

use function json_last_error;

/**
 * Class Strings
 */
class Strings
{
    /**
     * Create a text from template and values.
     *
     * @param        $text
     * @param        $values
     * @param string $default
     *
     * @return string
     */
    public static function template($text, $values, $default = ''): string
    {
        return preg_replace_callback(
            '/{{(?P<expressions>(.*?))}}/sim',
            static function ($matches) use ($values, $default) {
                $expressions         = array_map('trim', preg_split('/\s*\|\|\s*/', $matches['expressions']));
                $lastExpressionIndex = count($expressions) - 1;

                foreach ($expressions as $expressionIndex => $expression):
                    $replaceCount = 0;
                    $expression   = str_replace(['"', "'"], '', $expression, $replaceCount);

                    $item = Arrays::get(
                        (array)$values,
                        $expression,
                        $expressionIndex === $lastExpressionIndex && $replaceCount > 0 ? $expression : null
                    );

                    if (is_array($item)):
                        return implode(', ', $item);
                    elseif ($item != '' || $expressionIndex === $lastExpressionIndex):
                        return (string)$item;
                    endif;
                endforeach;

                return $default;
            },
            $text
        ) ?: $default;
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function uid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0xffff)
        );
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param int $length
     *
     * @return string
     * @throws Exception
     */
    public static function random($length = 16): string
    {
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size   = $length - $len;
            $bytes  = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
    }

    /**
     * Is json.
     *
     * @param $value
     *
     * @return bool
     */
    public static function isJson($value): bool
    {
        if ($value === '' || !is_string($value)) {
            return false;
        }

        json_decode($value);

        if (json_last_error()) {
            return false;
        }

        return true;
    }
}