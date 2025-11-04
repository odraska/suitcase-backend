<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataList;
use SLONline\App\Model\ArticlePage;
use SLONline\App\Model\Author;
use SLONline\App\Model\ProjectPage;
use SLONline\Elefont\Model\FontFamilyPage;

/**
 * Search GraphQL Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class SearchResolver
{
    const string SEARCH_RESULT_TYPE_FONT_FAMILY_PAGE = 'FontFamilyPage';
    const string SEARCH_RESULT_TYPE_AUTHOR = 'Author';
    const string SEARCH_RESULT_TYPE_PAGE = 'Page';
    const string SEARCH_RESULT_TYPE_ARTICLE = 'ArticlePage';
    const string SEARCH_RESULT_TYPE_PROJECT = 'ProjectPage';

    public static function resolve($obj, array $args, array $context, ResolveInfo $info): ArrayList
    {
        $results = ArrayList::create();
        $results->push(ArrayData::create([
            'category' => self::SEARCH_RESULT_TYPE_FONT_FAMILY_PAGE,
            'results' => DataList::create(FontFamilyPage::class)->filterAny([
                'Title:PartialMatch' => $args['term'],
                'Authors.Name:PartialMatch' => $args['term'],
            ])->sort('LastEdited', 'DESC')->limit(10),
        ]));

        $results->push(ArrayData::create([
            'category' => self::SEARCH_RESULT_TYPE_AUTHOR,
            'results' => DataList::create(Author::class)->filterAny([
                'Name:PartialMatch' => $args['term'],
                'Bio:PartialMatch' => $args['term'],
            ])->sort('LastEdited', 'DESC')->limit(10),
        ]));

        $results->push(ArrayData::create([
            'category' => self::SEARCH_RESULT_TYPE_PAGE,
            'results' => DataList::create('Page')
                ->filter(['ClassName:not' => FontFamilyPage::class])
                ->filterAny([
                'Title:PartialMatch' => $args['term'],
                'Content:PartialMatch' => $args['term'],
            ])->sort('LastEdited', 'DESC')->limit(10),
        ]));

        $results->push(ArrayData::create([
            'category' => self::SEARCH_RESULT_TYPE_ARTICLE,
            'results' => DataList::create(ArticlePage::class)->filterAny([
                'Title:PartialMatch' => $args['term'],
                'Content:PartialMatch' => $args['term'],
                'Annotation:PartialMatch' => $args['term'],
                'Authors.Name:PartialMatch' => $args['term'],
            ])->sort('LastEdited', 'DESC')->limit(10),
        ]));

        $results->push(ArrayData::create([
            'category' => self::SEARCH_RESULT_TYPE_PROJECT,
            'results' => DataList::create(ProjectPage::class)->filterAny([
                'Title:PartialMatch' => $args['term'],
                'Content:PartialMatch' => $args['term'],
                'Annotation:PartialMatch' => $args['term'],
                'Authors.Name:PartialMatch' => $args['term'],
            ])->sort('LastEdited', 'DESC')->limit(10),
        ]));

        return $results;
    }

    public static function resolveSearchResultType($object): string
    {
        if ($object instanceof FontFamilyPage) {
            return self::SEARCH_RESULT_TYPE_FONT_FAMILY_PAGE;
        }
        if ($object instanceof Author) {
            return self::SEARCH_RESULT_TYPE_AUTHOR;
        }
        if ($object instanceof ArticlePage) {
            return self::SEARCH_RESULT_TYPE_ARTICLE;
        }
        if ($object instanceof ProjectPage) {
            return self::SEARCH_RESULT_TYPE_PROJECT;
        }

        return self::SEARCH_RESULT_TYPE_PAGE;
    }
}
