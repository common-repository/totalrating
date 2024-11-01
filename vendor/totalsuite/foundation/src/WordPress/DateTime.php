<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress;
! defined( 'ABSPATH' ) && exit();


/**
 * Class DateTime
 */
class DateTime extends \DateTime
{

    /**
     * Get JSON.
     *
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'timezone'  => $this->getTimezone(),
            'timestamp' => $this->getTimestamp(),
            'offset'    => $this->getOffset(),
            'date'      => $this->format($this->getDateFormat()),
            'time'      => $this->format($this->getTimeFormat()),
            'datetime'  => $this->format($this->getDateTimeFormat()),
        ];
    }

    /**
     * Format date.
     *
     * @param string $format
     *
     * @return bool|int|string
     */
    public function format($format)
    {
        return mysql2date($format, parent::format(DATE_ATOM));
    }

    /**
     * Get date format.
     *
     * @return string
     */
    public function getDateFormat(): string
    {
        return get_option('date_format', 'F j, Y');
    }

    /**
     * Get time format.
     *
     * @return string
     */
    public function getTimeFormat(): string
    {
        return get_option('time_format', 'g:i a');
    }

    /**
     * Get date and time format.
     *
     * @return string
     */
    public function getDateTimeFormat(): string
    {
        return $this->getDateFormat() . ' ' . $this->getTimeFormat();
    }

    /**
     * String representation.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->format($this->getDateTimeFormat());
    }

    /**
     * Get formatted date.
     *
     * @param string|null $format
     *
     * @return string
     */
    public function getFormattedDate($format = null): string
    {
        return $this->format($format ?: $this->getDateFormat());
    }

    /**
     * Get formatted time.
     *
     * @param string|null $format
     *
     * @return string
     */
    public function getFormattedTime($format = null): string
    {
        return $this->format($format ?: $this->getTimeFormat());
    }
}