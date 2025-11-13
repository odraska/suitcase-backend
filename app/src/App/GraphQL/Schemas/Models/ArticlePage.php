<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\ArticlePageResolver;

/**
 * Article Page Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class ArticlePage implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\ArticlePage::class, function ($model) {
            $model->addField('annotation', [
                'type' => 'String!',
                'plugins' => [
                    'requiredField' => true,
                ],
            ]);
            $model->addField('pinned', [
                'type' => 'Boolean!',
                'plugins' => [
                    'requiredField' => true,
                ],
            ]);
            $model->addField('spotlight', [
                'type' => 'Boolean!',
                'plugins' => [
                    'requiredField' => true,
                ],
            ]);
            $model->addField('coverImage', [
                'type' => 'Image!',
            ]);
            $model->addField('authors', [
                'type' => '[Author!]!',
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => false,
                    'sort' => false,
                    'filter' => false,
                ],
            ]);
            $model->addField('category', [
                'type' => 'ArticleCategory!',
            ]);

            $model->addOperation('read', [
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => true,
                    'sort' => true,
                    'filter' => [
                        'fields' => [
                            'id' => true,
                            'urlSegment' => true,
                            'categoryUrlSegment' => true,
                        ],
                        'resolve' => [
                            'categoryUrlSegment' => [
                                'type' => 'String',
                                'description' => 'URL segments of the categories to filter by',
                                'resolver' => [ArticlePageResolver::class, 'resolveCategoryUrlSegmentFilter'],
                            ],
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
                            'urlSegment' => true,
                            'categoryUrlSegment' => true,
                        ],
                        'resolve' => [
                            'categoryUrlSegment' => [
                                'type' => 'String',
                                'description' => 'URL segments of the categories to filter by',
                                'resolver' => [ArticlePageResolver::class, 'resolveCategoryUrlSegmentFilter'],
                            ],
                        ],
                    ],
                ],
            ]);
        });
    }
}
