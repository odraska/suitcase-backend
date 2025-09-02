<?php

namespace SLONline\App\GraphQL\Schemas\Queries;

use SilverStripe\GraphQL\Schema\Field\Query;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\DownloadInfoResolver;

/**
 * Read Download Info Query GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class ReadDownloadInfo implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addQuery(Query::create('readDownloadInfo')
            ->setDescription('Read Download Info')
            ->setType('DownloadInfo', false)
            ->addArg('id', ['type' => 'ID!'])
            ->addArg('hash', ['type' => 'String!'])
            ->setResolver([DownloadInfoResolver::class, 'resolveReadDownloadInfo'])
        );
    }
}
