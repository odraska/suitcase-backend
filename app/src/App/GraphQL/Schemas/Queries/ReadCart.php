<?php

namespace SLONline\App\GraphQL\Schemas\Queries;

use SilverStripe\GraphQL\Schema\Field\Query;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\ReadCartResolver;

/**
 * Read Cart Query GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class ReadCart implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addQuery(Query::create('readCart')
            ->setDescription('Read cart content')
            ->setType('Order', false)
            ->addArg('familyProductSelections', ['type' => '[FamilyProductSelectionInput!]!'])
            ->addArg('discountCode', ['type' => 'String'])
            ->setResolver([ReadCartResolver::class, 'resolve'])
        );
    }
}
