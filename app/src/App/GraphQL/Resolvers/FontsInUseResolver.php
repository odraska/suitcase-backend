<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\GraphQL\Schema\DataObject\Plugin\QueryFilter\FilterRegistryInterface;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\Model\List\SS_List;
use SilverStripe\ORM\DataList;
use SLONline\App\Model\FontsInUse;
use SLONline\Elefont\Model\FontFamilyPage;

/**
 * Fonts in Use GraphQL Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class FontsInUseResolver
{
    public static function resolveReadFontsInUseFamilyPages($obj, array $args, array $context, ResolveInfo $info): DataList
    {
        $list = DataList::create(FontFamilyPage::class)
            ->filter('FontsInUse.Count():GreaterThan', 0)
            ->sort('Title', 'ASC');

        return $list;
    }

    public static function resolveFontFamilyPageIDFilter(SS_List $list, array $args, array $context)
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
            $list = $filter->apply($list, 'FontFamilyPages.ID', $context['filterValue']);
        }

        return $list;
    }

    public static function resolveFontFamilyPageUrlSegmentFilter(SS_List $list, array $args, array $context)
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
            $table = FontsInUse::getSchema()->tableName(FontsInUse::class);
            $pageTable = SiteTree::getSchema()->tableName(SiteTree::class);
            $manyMany = FontsInUse::getSchema()->manyManyComponent(FontsInUse::class, 'FontFamilyPages');

            $list = $list->innerJoin($manyMany['join'], '"' . $manyMany['join'] . '"."' . $manyMany['parentField'] . '" = "' . $table . '"."ID"')
                ->innerJoin($pageTable, '"' . $pageTable . '"."ID" = "' . $manyMany['join'] . '"."' . $manyMany['childField'] . '"');

            $list = $filter->apply($list, 'UrlSegment', $context['filterValue']);
        }

        return $list;
    }
}
