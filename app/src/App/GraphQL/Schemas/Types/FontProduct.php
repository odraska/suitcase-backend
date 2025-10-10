<?php

namespace SLONline\App\GraphQL\Schemas\Types;

use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Type;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Font Product Type GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class FontProduct implements PartialSchemaUpdater
{
    /**
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addType(Type::create('FontProduct')
            ->addField('id', 'ID!')
            ->addField('title', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('fontFamily', ['type' => 'FontFamily!', 'plugins' => ['requiredField' => true]])
            ->addField('font', ['type' => 'Font!', 'plugins' => ['requiredField' => true]])
            ->addField('price', ['type' => 'Money!', 'plugins' => ['requiredField' => true]])
        );
    }
}
