<?php

namespace TotalRating\Events;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Event;

class OnBackofficeAssetsEnqueued extends Event
{
    const ALIAS = 'totalrating/backoffice/assets';
}