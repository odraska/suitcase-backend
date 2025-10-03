<?php

namespace SLONline\App\GraphQL\Schemas\Enums;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Enum;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\SearchResolver;

/**
 * Search Category Enum GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class SearchCategoryEnumSchema implements PartialSchemaUpdater
{

    public static function updateSchema(Schema $schema): void
    {
        $schema->addEnum(Enum::create('SearchCategoryEnum', [
            SearchResolver::SEARCH_RESULT_TYPE_FONT_FAMILY_PAGE,
            SearchResolver::SEARCH_RESULT_TYPE_AUTHOR,
            SearchResolver::SEARCH_RESULT_TYPE_PAGE,
        ]));
    }
}
