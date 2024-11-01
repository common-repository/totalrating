<?php
declare(strict_types=1);

namespace TotalRatingVendors\League\MimeTypeDetection;
! defined( 'ABSPATH' ) && exit();


interface ExtensionLookup
{
    public function lookupExtension(string $mimetype): ?string;

    /**
     * @return string[]
     */
    public function lookupAllExtensions(string $mimetype): array;
}
