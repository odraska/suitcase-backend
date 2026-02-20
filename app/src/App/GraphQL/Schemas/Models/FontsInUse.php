<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\ModelType;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\FontsInUseResolver;
use SLONline\App\Model\FontsInUsePage;

/**
 * Fonts in Use Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class FontsInUse implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(FontsInUsePage::class, function (ModelType $model) {
            $model->addOperation('readOne', [
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => false,
                    'sort' => false,
                    'filter' => false
                ],
            ]);
        });

        $schema->addModelbyClassName(\SLONline\App\Model\FontsInUse::class, function (ModelType $model) {
            $model->addField(
                'title',
                [
                    'type' => 'String!',
                    'plugins' => [
                        'requiredField' => true
                    ],
                ]
            );
            $model->addField(
                'author',
                [
                    'type' => 'String!',
                    'property' => 'getFontsInUseAuthor',
                    'plugins' => [
                        'requiredField' => true
                    ],
                ]
            );
            $model->addField(
                'spotlight',
                [
                    'type' => 'Boolean!',
                    'plugins' => [
                        'requiredField' => true
                    ],
                ]
            );
            $model->addField('images', [
                'type' => '[Image!]!',
                'plugins' => [
                    'filter' => false,
                    'paginateList' => false,
                    'sort' => false
                ],
            ]);

            $model->addField('fontFamilyPages', [
                'type' => '[FontFamilyPage!]!',
                'plugins' => [
                    'filter' => false,
                    'paginateList' => false,
                    'sort' => false
                ],
            ]);

            $model->addOperation('read', [
                'plugins' => [
                    'filter' => [
                        'fields' => [
                            'id' => true,
                            'title' => true,
                            'author' => true,
                            'fontFamilyPageID' => true,
                            'fontFamilyPageUrlSegment' => true,
                        ],
                        'resolve' => [
                            'fontFamilyPageID' => [
                                'type' => 'ID',
                                'description' => 'IDs of the font family pages to filter by',
                                'resolver' => [FontsInUseResolver::class, 'resolveFontFamilyPageIDFilter'],
                            ],
                            'fontFamilyPageUrlSegment' => [
                                'type' => 'String',
                                'description' => 'URL segments of the font family pages to filter by',
                                'resolver' => [FontsInUseResolver::class, 'resolveFontFamilyPageUrlSegmentFilter'],
                            ],
                        ]
                    ],
                    'paginateList' => true,
                    'sort' => [
                        'before' => 'paginateList',
                        'fields' => [
                            'title' => true,
                            'author' => true,
                            'sortOrder' => true,
                        ]
                    ],
                ],
            ]);
        });
    }
}
