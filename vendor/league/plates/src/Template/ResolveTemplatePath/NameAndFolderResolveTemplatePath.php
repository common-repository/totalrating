<?php

namespace TotalRatingVendors\League\Plates\Template\ResolveTemplatePath;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\League\Plates\Exception\TemplateNotFound;
use TotalRatingVendors\League\Plates\Template\Name;
use TotalRatingVendors\League\Plates\Template\ResolveTemplatePath;

/** Resolves the path from the logic in the Name class which resolves via folder lookup, and then the default directory */
final class NameAndFolderResolveTemplatePath implements ResolveTemplatePath
{
    public function __invoke(Name $name): string {
        $path = $name->getPath();
        if (is_file($path)) {
            return $path;
        }

        throw new TemplateNotFound($name->getName(), [$name->getPath()], 'The template "' . $name->getName() . '" could not be found at "' . $name->getPath() . '".');
    }
}
