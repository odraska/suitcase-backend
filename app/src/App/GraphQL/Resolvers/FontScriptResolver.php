<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\ORM\DataList;
use SLONline\Elefont\Model\FontScript;

/**
 * Font Script GraphQL Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class FontScriptResolver
{
    public static function resolveReadFontScripts($obj, array $args, array $context, ResolveInfo $info): DataList
    {
        return DataList::create(FontScript::class)
            ->filter('FontFamilies.Count():GreaterThan', 0)
            ->sort('Title', 'ASC');
    }
}
