<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\ProjectPageResolver;

/**
 * Project Page Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class ProjectPage implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\ProjectPage::class, function ($model) {
            $model->addField('annotation', [
                'type' => 'String!',
                'plugins' => [
                    'requiredField' => true,
                ],
            ]);
            $model->addField('coverImage', [
                'type' => 'Image!',
            ]);
            $model->addField(
                'spotlight',
                [
                    'type' => 'Boolean!',
                    'plugins' => [
                        'requiredField' => true
                    ],
                ]
            );
            $model->addField('authors', [
                'type' => '[Author!]!',
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => false,
                    'sort' => false,
                    'filter' => false,
                ],
            ]);

            $model->addField('contentBlocks', [
                'type' => '[ContentBlock!]!',
            ]);

            $model->addOperation('read', [
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => true,
                    'sort' => true,
                    'filter' => [
                        'fields' => [
                            'id' => true,
                            'title' => true,
                            'urlSegment' => true,
                            'authorUrlSegment' => true,
                        ],
                        'resolve' => [
                            'authorUrlSegment' => [
                                'type' => 'String',
                                'description' => 'URL segments of the font family pages to filter by',
                                'resolver' => [ProjectPageResolver::class, 'resolveAuthorUrlSegmentFilter'],
                            ],
                        ]
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
                            'title' => true,
                            'urlSegment' => true,
                            'authorUrlSegment' => true,
                        ],
                    ],
                ],
            ]);
        });
    }
}
