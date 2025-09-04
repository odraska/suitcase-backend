<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\Elefont\GraphQL\Resolvers\FontFamilyPageResolver;

/**
 * Font Family Page Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class FontFamilyPage implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->getModel('FontFamilyPage')
            ->addField('content', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('footerButton', ['type' => 'FooterButton!', 'property' => 'footerButton'])
            ->addField('fontCategories', [
                'type' => '[FontCategory!]!',
                'property' => 'FontCategories',
                'plugins' => [
                    'paginateList' => false,
                    'sort' => false,
                    'filter' => false
                ]])
            ->addField('slides', [
                'type' => '[Slide!]!',
                'property' => 'Slides',
                'plugins' => [
                    'paginateList' => false,
                    'sort' => false,
                    'filter' => false
                ]
            ])
            ->addField('showInBasicTrial', [
                'type' => 'Boolean!',
                'plugins' => ['requiredField' => true]])
            ->addField('showInFullTrial', [
                'type' => 'Boolean!',
                'plugins' => ['requiredField' => true]])
            ->addField('fontsInUse', [
                'type' => '[FontsInUse!]!',
                'plugins' => [
                    'paginateList' => true,
                    'sort' => false,
                    'filter' => false
                ]
            ])
            ->addField('visualStyles', [
                'type' => '[[Font!]!]!',
                'plugins' => [
                    'paginateList' => false,
                    'sort' => false,
                    'filter' => false
                ]
            ])
            ->addField('authors', [
                'type' => '[Author!]!',
                'plugins' => [
                    'paginateList' => false,
                    'sort' => false,
                    'filter' => false
                ]
            ])
            ->addField('fonts', [
                'type' => '[Font!]!',
                'property' => 'Fonts',
                'plugins' => [
                    'paginateList' => true,
                    'sort' => false,
                    'filter' => false
                ]
            ])
            ->addField('pdfSpecimen', [
                'type' => 'File',
                'plugins' => [
                    'requiredField' => false,
                ]
            ]);

        $schema->getModel('FontFamilyPage')->removeOperation('readOne');
        $schema->getModel('FontFamilyPage')->removeOperation('read');
        $schema->getModel('FontFamilyPage')->addOperation('read', [
            'plugins' => [
                'filter' => [
                    'fields' => [
                        'id' => true,
                        'urlSegment' => true,
                        'scriptID' => true,
                        'scriptCode' => true,
                        'scriptUrlSegment' => true,
                        'categoryID' => true,
                        'categoryUrlSegment' => true,
                        'showInBasicTrial' => true,
                        'showInFullTrial' => true,
                    ],
                    'resolve' => [
                        'scriptID' => [
                            'type' => 'ID',
                            'description' => 'IDs of the scripts to filter by',
                            'resolver' => [FontFamilyPageResolver::class, 'resolveScriptIDFilter'],
                        ],
                        'scriptCode' => [
                            'type' => 'String',
                            'description' => 'Codes of the scripts to filter by',
                            'resolver' => [FontFamilyPageResolver::class, 'resolveScriptCodeFilter'],
                        ],
                        'scriptUrlSegment' => [
                            'type' => 'String',
                            'description' => 'URL segments of the scripts to filter by',
                            'resolver' => [FontFamilyPageResolver::class, 'resolveScriptUrlSegmentFilter'],
                        ],
                        'categoryID' => [
                            'type' => 'ID',
                            'description' => 'IDs of the categories to filter by',
                            'resolver' => [\SLONline\App\GraphQL\Resolvers\FontFamilyPageResolver::class, 'resolveCategoryIDFilter'],
                        ],
                        'categoryUrlSegment' => [
                            'type' => 'String',
                            'description' => 'URL segments of the categories to filter by',
                            'resolver' => [\SLONline\App\GraphQL\Resolvers\FontFamilyPageResolver::class, 'resolveCategoryUrlSegmentFilter'],
                        ],
                    ]
                ],
                'paginateList' => true,
                'sort' => [
                    'before' => 'paginateList',
                    'fields' => [
                        'title' => true,
                        'created' => true,
                        'releaseDate' => true,
                    ]
                ],
            ],
        ]);
    }
}
