<?php

namespace SLONline\App\GraphQL\Schemas\Types;

use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Type;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\AssetResolver;

/**
 * Image Type GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class Image implements PartialSchemaUpdater
{
    /**
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addType(Type::create('Image')
            ->addInterface('ImageInterface')
            ->setFieldResolver([AssetResolver::class, 'resolveField'])
            ->addField('id', 'ID!')
            ->addField('title', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('name', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('filename', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('extension', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('url', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('size', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('width', ['type' => 'Float!', 'plugins' => ['requiredField' => true]])
            ->addField('height', ['type' => 'Float!', 'plugins' => ['requiredField' => true]])
        );
    }
}
