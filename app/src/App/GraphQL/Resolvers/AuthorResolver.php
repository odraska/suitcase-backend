<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\ORM\DataList;
use SLONline\App\Model\Author;

/**
 * Author GraphQL Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class AuthorResolver
{
    public static function resolveReadProjectsAuthors($obj, array $args, array $context, ResolveInfo $info): DataList
    {
        $list = DataList::create(Author::class)
            ->filter('ProjectPages.Count():GreaterThan', 0)
            ->sort('Name', 'ASC');

        return $list;
    }
}
