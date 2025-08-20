<?php

namespace SLONline\App\GraphQL\Schemas\Queries;

use SilverStripe\GraphQL\Schema\Field\Query;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\FontCategoryResolver;

/**
 * Read Font Categories Query GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class ReadFontCategories implements PartialSchemaUpdater
{
    public static function updateSchema(Schema $schema): void
    {
        $schema->addQuery(Query::create('readFontCategories')
            ->setDescription('Read all font categories')
            ->setType('[FontCategory]', true)
            ->addArg('scriptUrlSegment', ['type' => 'String', 'description' => 'Filter by script URL segment'])
            ->addPlugin('paginateList')
            ->setResolver([FontCategoryResolver::class, 'resolveReadFontCategories'])
            ->removePlugin('paginateList')
        );
    }
}
