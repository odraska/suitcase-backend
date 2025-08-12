<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Image;
use SilverStripe\GraphQL\Schema\DataObject\FieldAccessor;


/**
 * Asset Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class AssetResolver
{
    public static function resolveField($obj, array $args, array $context, ResolveInfo $info)
    {
        $field = $info->fieldName;

        if ($obj instanceof File && strtolower($field) == 'url') {
            return $obj->getAbsoluteURL();
        }

        if ($obj->hasMethod($field)) {
            return $obj->$field();
        }
        $fieldName = FieldAccessor::singleton()->normaliseField($obj, $field);
        return $obj->$fieldName;
    }

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
