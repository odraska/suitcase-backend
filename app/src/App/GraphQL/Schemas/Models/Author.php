<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\ModelType;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Author Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class Author implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\Author::class, function (ModelType $model) {
            $model->addFields([
                'id' => [
                    'type' => 'ID!',
                ],
                'name' => [
                    'type' => 'String!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ],
                'urlSegment' => [
                    'type' => 'String!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ],
                'biography' => [
                    'type' => 'String!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ],
                'photo' => [
                    'type' => 'Image',
                ],
                'fontFamilyPages' => [
                    'type' => '[FontFamilyPage!]!',
                    'plugins' => [
                        'filter' => false,
                        'paginateList' => true,
                        'sort' => false
                    ],
                ],
            ]);

            $model->addOperation('read', [
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => true,
                    'sort' => false,
                    'filter' => [
                        'fields' => [
                            'id' => true,
                            'name' => true,
                            'urlSegment' => true,
                        ],
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
                            'name' => true,
                            'urlSegment' => true,
                        ]
                    ]
                ],
            ]);
        });

    }
}
