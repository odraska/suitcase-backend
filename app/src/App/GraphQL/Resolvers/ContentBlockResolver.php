<?php

namespace SLONline\App\GraphQL\Resolvers;


use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\Assets\Image;

/**
 * Content Block Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class ContentBlockResolver
{
    public static function resolveType($object): string
    {
        return match ($object->type) {
            'header' => 'HeadingContentBlock',
            'code' => 'CodeContentBlock',
            'list' => 'ListContentBlock',
            'quote' => 'QuoteContentBlock',
            'image' => 'ImageContentBlock',
            default => 'ParagraphContentBlock',
        };
    }

    public static function resolveField($object, array $args, array $context, ResolveInfo $info, $x): string|array|null
    {
        $data = $object->data;
        $field = $info->fieldName;
        if ($object->hasMethod($field)) {
            return $object->$field();
        }

        if ($data->hasMethod($field)) {
            return $data->$field();
        }

        return $data->{$field};
    }

    public static function resolveFootnotesField($object, array $args, array $context, ResolveInfo $info): array
    {
        return $object?->tunes?->footnotes ?? [];
    }

    public static function resolveImageFields($object, array $args, array $context, ResolveInfo $info, $x): string|int|null
    {
        $value = static::resolveField($object, $args, $context, $info, $x);
        if (in_array($info->fieldName, ['width', 'height'])) {
            if ($object->data?->fileID) {
                $image = Image::get_by_id($object->data->fileID);
                if ($image) {
                    $value = $info->fieldName === 'width' ? $image->getWidth() : $image->getHeight();
                }
            }
        }
        return $value;
    }
}
