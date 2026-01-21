<?php

namespace SLONline\App\GraphQL\Schemas\Types\ContentBlocks;

use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Type;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\ContentBlockResolver;

/**
 * Paragraph Content Block Type GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class Paragraph implements PartialSchemaUpdater
{
    /**
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addType(Type::create('ParagraphContentBlock')
            ->addField('text', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('footnotes', ['type' => '[FootnotesContentBlock!]!', 'resolver' => [ContentBlockResolver::class, 'resolveFootnotesField']])
            ->setFieldResolver([ContentBlockResolver::class, 'resolveField'])
        );

    }
}
