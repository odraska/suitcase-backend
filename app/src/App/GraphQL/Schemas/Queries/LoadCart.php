<?php

namespace SLONline\App\GraphQL\Schemas\Queries;

use SilverStripe\GraphQL\Schema\Field\Query;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\CartResolver;

/**
 * Load Saved Cart Query GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class LoadCart implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addQuery(Query::create('loadCart')
            ->setDescription('Load saved cart content')
            ->setType('SavedCart', false)
            ->addArg('hash', ['type' => 'String!'])
            ->setResolver([CartResolver::class, 'resolveLoadCart'])
        );
    }
}
