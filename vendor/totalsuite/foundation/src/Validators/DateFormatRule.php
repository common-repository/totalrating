<?php


namespace TotalRatingVendors\TotalSuite\Foundation\Validators;
! defined( 'ABSPATH' ) && exit();



use DateTime;
use TotalRatingVendors\Rakit\Validation\Rule;

class DateFormatRule extends Rule
{
    /**
     * @var string
     */
    protected $message = 'The :attribute is not a valid date';

    /**
     * @var bool
     */
    protected $implicit = true;

    /**
     * @var string[]
     */
    protected $fillableParams = ['format'];

    /**
     * @var string[]
     */
    protected $params = [
        'format' => 'Y-m-d',
    ];

    /**
     * @param $value
     *
     * @return bool
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $format = $this->parameter('format');

        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        $date = DateTime::createFromFormat('!' . $format, $value);

        return $date && $date->format($format) == $value;
    }
}