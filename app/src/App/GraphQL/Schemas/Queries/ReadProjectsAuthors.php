<?php

namespace SLONline\App\GraphQL\Schemas\Queries;

use SilverStripe\GraphQL\Schema\Field\Query;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\AuthorResolver;

/**
 * Read Projects Authors Query GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class ReadProjectsAuthors implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addQuery(Query::create('readProjectsAuthors')
            ->setDescription('Read all projects authors')
            ->setType('[Author!]', true)
            ->addPlugin('paginateList')
            ->setResolver([AuthorResolver::class, 'resolveReadProjectsAuthors'])
        );
    }
}
