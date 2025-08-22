<?php

namespace SLONline\App\GraphQL\Schemas\Queries;

use SilverStripe\GraphQL\Schema\Field\Query;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\FontScriptResolver;

/**
 * Read Font Scripts Query GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class ReadFontScripts implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addQuery(
            Query::create('readFontScripts')
                ->setDescription('Read all font scripts')
                ->setType('[FontScript!]', true)
                ->addArg('categoryUrlSegment', ['type' => 'String', 'description' => 'Filter by category URL segment'])
                ->addPlugin('paginateList')
                ->setResolver([FontScriptResolver::class, 'resolveReadFontScripts'])
                ->removePlugin('paginateList')
        );
    }
}
