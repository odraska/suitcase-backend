<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Custom Font Category Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class CustomFontCategory implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\CustomFontCategory::class, function ($model) {
            $model->addField('title', [
                'type' => 'String!',
                'plugins' => [
                    'requiredField' => true,
                ],
            ]);
            $model->addField('urlSegment', [
                'type' => 'String!',
                'plugins' => [
                    'requiredField' => true,
                ],
            ]);
            $model->addField('customFonts', [
                'type' => '[CustomFontPage!]!',
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => true,
                    'sort' => false,
                    'filter' => false
                ],
            ]);
            $model->addOperation('read', [
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => false,
                    'sort' => false,
                    'filter' => false
                ],
            ]);
            $model->addOperation('readOne', [
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => false,
                    'sort' => false,
                    'filter' => false
                ],
            ]);
        });
    }
}
