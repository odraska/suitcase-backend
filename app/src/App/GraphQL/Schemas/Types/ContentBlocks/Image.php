<?php

namespace SLONline\App\GraphQL\Schemas\Types\ContentBlocks;

use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Type;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\ContentBlockResolver;

class Image implements PartialSchemaUpdater
{
    /**
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addType(Type::create('ImageContentBlock')
            ->addField('url', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('caption', ['type' => 'String!', 'plugins' => ['requiredField' => false]])
            ->addField('withBackground', ['type' => 'Boolean'])
            ->addField('stretched', ['type' => 'Boolean'])
            ->setFieldResolver([ContentBlockResolver::class, 'resolveField'])
        );
    }
}
