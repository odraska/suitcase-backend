<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\ORM\DataList;
use SLONline\App\Model\FontCategory;

/**
 * Font Category GraphQL Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class FontCategoryResolver
{
    public static function resolveReadFontCategories($obj, array $args, array $context, ResolveInfo $info): DataList
    {
        $list = DataList::create(FontCategory::class)
            ->filter('FontFamilyPages.Count():GreaterThan', 0)
            ->sort('Title', 'ASC');

        if (!empty($args['scriptUrlSegment'])) {
            $list = $list->filter('FontFamilyPages.ListDefaultFont.FontFamily.Scripts.UrlSegment', $args['scriptUrlSegment']);
        }

        return $list;
    }
}
