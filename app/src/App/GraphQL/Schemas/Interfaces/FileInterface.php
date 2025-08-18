<?php

namespace SLONline\App\GraphQL\Schemas\Interfaces;

use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\InterfaceType;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\AssetResolver;

/**
 * File Interface GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class FileInterface implements PartialSchemaUpdater
{
    /**
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addInterface(InterfaceType::create('FileInterface')
            ->setTypeResolver([AssetResolver::class, 'resolveFileInterfaceType'])
            ->setDescription('Interface for files and folders')
            ->addField('id', 'ID!')
            ->addField('title', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('name', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('filename', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('extension', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('url', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
        );
    }
}
