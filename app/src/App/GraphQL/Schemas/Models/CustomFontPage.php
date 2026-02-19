<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\CustomFontPageResolver;

/**
 * Custom Font Page Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class CustomFontPage implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\CustomFontPage::class, function ($model) {
            $model->addField('shortText', [
                'type' => 'String!',
                'plugins' => [
                    'requiredField' => true,
                ],
            ]);
            $model->addField('coverImage', [
                'type' => 'Image!',
            ]);
            $model->addField('category', [
                'type' => 'CustomFontCategory!',
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
                                'resolver' => [CustomFontPageResolver::class, 'resolveCategoryUrlSegmentFilter'],
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
                                'resolver' => [CustomFontPageResolver::class, 'resolveCategoryUrlSegmentFilter'],
                            ],
                        ],
                    ],
                ],
            ]);
        });
    }
}
