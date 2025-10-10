<?php

namespace SLONline\App\GraphQL\Schemas\Queries;

use SilverStripe\GraphQL\Schema\Field\Query;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\ReadFamilyProductsResolver;

/**
 * Read Family Products Query GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class ReadFamilyProducts implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addQuery(Query::create('readFamilyProducts')
            ->setDescription('Read Family Products')
            ->setType('[FamilyProductResult!]', true)
            ->addArg('familyPageId', ['type' => 'ID!'])
            ->setResolver([ReadFamilyProductsResolver::class, 'resolve'])
        );
    }
}
