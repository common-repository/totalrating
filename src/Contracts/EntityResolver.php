<?php

namespace TotalRating\Contracts;
! defined( 'ABSPATH' ) && exit();


interface EntityResolver
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @param       $id
     * @param       $subtype
     * @param array $meta
     *
     * @return mixed
     */
    public function resolve($id, $subtype = null, array $meta = []);
}
