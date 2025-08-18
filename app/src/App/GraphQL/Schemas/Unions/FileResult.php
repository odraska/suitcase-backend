<?php

namespace SLONline\App\GraphQL\Schemas\Unions;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\UnionType;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\AssetResolver;

/**
 * File Result Union GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class FileResult implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addUnion(UnionType::create('FileResult', [
            'types' => [
                'Image',
                'File',
            ],
            'typeResolver' => [AssetResolver::class, 'resolveFileInterfaceType'],
        ]));
    }
}
