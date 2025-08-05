<?php

namespace SLONline\App\GraphQL\Resolvers;

use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Image;


/**
 * Asset Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class AssetResolver
{
    public static function resolveFileInterfaceType($object): string
    {
        if ($object instanceof Folder) {
            return 'Folder';
        }
        if ($object instanceof Image) {
            return 'Image';
        }

        return 'File';
    }

    public static function resolveImageInterfaceType($object): string
    {
        return 'Image';
    }
}
