<?php

namespace TotalRating\Contracts;
! defined( 'ABSPATH' ) && exit();



interface Exportable
{
    public function toExport($format);
}