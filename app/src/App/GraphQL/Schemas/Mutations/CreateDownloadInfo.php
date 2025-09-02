<?php

namespace SLONline\App\GraphQL\Schemas\Mutations;

use SilverStripe\GraphQL\Schema\Field\Mutation;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\DownloadInfoResolver;

/**
 * Create Download Info Mutation GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class CreateDownloadInfo implements PartialSchemaUpdater
{
    public static function updateSchema(Schema $schema): void
    {
        $schema->addMutation(Mutation::create('createDownloadInfo')
            ->setDescription('Create a new download info record')
            ->setType('DownloadInfo', true)
            ->addArg('fontFamilyPageIDs', '[ID!]')
            ->addArg('type', 'DownloadInfoType!')
            ->setResolver([DownloadInfoResolver::class, 'resolveCreateDownloadInfo'])
        );
    }
}
