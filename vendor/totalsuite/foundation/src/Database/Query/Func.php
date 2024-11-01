<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database\Query;
! defined( 'ABSPATH' ) && exit();



/**
 * Class Func
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Database\Query
 */
class Func
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * Aggregate constructor.
     *
     * @param Expression|string $name
     * @param array|string      $arguments
     */
    public function __construct($name, ...$arguments)
    {
        $this->name      = $name;
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }


}