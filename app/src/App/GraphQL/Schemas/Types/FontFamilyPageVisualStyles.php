<?php

namespace SLONline\App\GraphQL\Schemas\Types;

use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Type;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Font Family Page Visual Styles GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class FontFamilyPageVisualStyles implements PartialSchemaUpdater
{
    /**
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addType(Type::create('FontFamilyPageVisualStyles')
            ->addField('fontSize', ['type' => 'FontFamilyPageVisualStylesSize!', 'plugins' => ['requiredField' => true]])
            ->addField('styles', ['type' => '[Font!]!', 'plugins' => ['requiredField' => true]])
        );
    }
}
