<?php

namespace SLONline\App\GraphQL\Schemas\Queries;

use SilverStripe\GraphQL\Schema\Field\Query;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\FontsInUseResolver;

/**
 * Read Fonts in Use Family Pages Query GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class ReadFontsInUseFamilyPages implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addQuery(
            Query::create('readFontsInUseFamilyPages')
                ->setDescription('Read all family pages for fonts in use filter')
                ->setType('[FontFamilyPage!]', true)
                ->setResolver([FontsInUseResolver::class, 'resolveReadFontsInUseFamilyPages'])
        );
    }
}
