<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Font Feature Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class FontFeature implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->getModel('FontFeature')
            ->addField('example', [
                'type' => 'String!',
                'plugins' => ['requiredField' => true]
            ])
            ->addField('allGlyphs', [
                'type' => 'String!',
                'plugins' => ['requiredField' => true]
            ]);
    }
}
