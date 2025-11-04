<?php

namespace SLONline\App\GraphQL\Schemas\Unions;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\UnionType;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\SearchResolver;

/**
 * Search Result Type GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class SearchResult implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addUnion(UnionType::create('SearchResult', [
            'types' => [
                SearchResolver::SEARCH_RESULT_TYPE_FONT_FAMILY_PAGE,
                SearchResolver::SEARCH_RESULT_TYPE_AUTHOR,
                SearchResolver::SEARCH_RESULT_TYPE_PAGE,
                SearchResolver::SEARCH_RESULT_TYPE_ARTICLE,
                SearchResolver::SEARCH_RESULT_TYPE_PROJECT,
            ],
            'typeResolver' => [SearchResolver::class, 'resolveSearchResultType'],
        ]));
    }
}
