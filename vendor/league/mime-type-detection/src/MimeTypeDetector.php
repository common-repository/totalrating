<?php

declare(strict_types=1);

namespace TotalRatingVendors\League\MimeTypeDetection;
! defined( 'ABSPATH' ) && exit();


interface MimeTypeDetector
{
    /**
     * @param string|resource $contents
     */
    public function detectMimeType(string $path, $contents): ?string;

    public function detectMimeTypeFromBuffer(string $contents): ?string;

    public function detectMimeTypeFromPath(string $path): ?string;

    public function detectMimeTypeFromFile(string $path): ?string;
}
