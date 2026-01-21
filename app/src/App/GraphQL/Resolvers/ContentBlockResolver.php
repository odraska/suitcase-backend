<?php

namespace SLONline\App\GraphQL\Resolvers;


use GraphQL\Type\Definition\ResolveInfo;

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
}
