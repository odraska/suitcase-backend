<?php

namespace SLONline\App\GraphQL\Resolvers;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\GraphQL\Schema\DataObject\Plugin\QueryFilter\FilterRegistryInterface;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\Model\List\SS_List;

/**
 * Project Page GraphQL Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class ProjectPageResolver
{
    public static function resolveAuthorUrlSegmentFilter(SS_List $list, array $args, array $context)
    {
        if ($list === null) {
            return null;
        }

        if (empty($context['filterValue'])) {
            return $list;
        }

        $filterID = $context['filterComparator'];

        $registry = Injector::inst()->get(FilterRegistryInterface::class);

        $filter = $registry->getFilterByIdentifier($filterID);
        Schema::invariant(
            $filter,
            'No registered filters match the identifier "%s". Did you register it with %s?',
            $filterID,
            FilterRegistryInterface::class
        );

        if ($filter) {
            $list = $filter->apply($list, 'Authors.UrlSegment', $context['filterValue']);
        }

        return $list;
    }
}
