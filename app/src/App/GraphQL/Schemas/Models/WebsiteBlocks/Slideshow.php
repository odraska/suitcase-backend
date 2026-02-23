<?php

namespace SLONline\App\GraphQL\Schemas\Models\WebsiteBlocks;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\ModelType;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Slideshow Website Block GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class Slideshow implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\WebsiteBlocks\Slideshow::class, function (ModelType $model) {

            $model->addFields([
                'id' => [
                    'type' => 'ID!',
                ],
                'slides' => [
                    'type' => '[Slide!]!',
                    'plugins' => [
                        'requiredField' => true,
                        'filter' => false,
                        'paginateList' => false,
                        'sort' => false
                    ],
                ],
            ]);

            $model->addOperation('readOne', [
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => false,
                    'sort' => false,
                    'filter' => [
                        'fields' => [
                            'id' => true,
                        ]
                    ]
                ],
            ]);
        });
    }
}
