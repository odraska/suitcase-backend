<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\Elefont\GraphQL\Resolvers\PageTypeResolver;

/**
 * Page Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class Page implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModel($schema->createModel(\Page::class)
            ->addFields([
                'id' => [
                    'type' => 'ID!',
                ],
                'className' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'title' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'menuTitle' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'parentID' => [
                    'type' => 'Int!',
                ],
                'parent' => [
                    'type' => 'SiteTreeInterface',
                ],
                'showInMenus' => [
                    'type' => 'Boolean!',
                ],
                'content' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'urlSegment' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'metaTitle' => [
                    'type' => 'String!',
                    'property' => 'metaTitle',
                    'plugins' => ['requiredField' => true],
                ],
                'metaDescription' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'url' => [
                    'type' => 'String!',
                    'property' => 'Link',
                    'plugins' => ['requiredField' => true],
                ],
                'pageType' => [
                    'type' => 'String!',
                    'property' => 'pageType',
                    'resolver' => [PageTypeResolver::class, 'resolve'],
                    'plugins' => ['requiredField' => true],
                ],
                'websiteBlocks' => [
                    'type' => '[WebsiteBlockInterface!]',
                    'plugins' => [
                        'requiredField' => false,
                        'filter' => false,
                        'paginateList' => false,
                        'sort' => false
                    ],
                ],
                'taglineText' => [
                    'type' => 'String!',
                    'property' => 'taglineText',
                    'plugins' => ['requiredField' => true],
                ],
            ])
            ->addOperation('read', [
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => true,
                    'filter' => [
                        'fields' => [
                            'id' => true,
                            'title' => true,
                            'parentID' => true,
                            'showInMenus' => true,
                            'urlSegment' => true,
                            'pageType' => true,
                        ],
                        'resolve' => [
                            'pageType' => [
                                'resolver' => [PageTypeResolver::class, 'applyFilter'],
                                'type' => 'String'
                            ]
                        ]
                    ]
                ],
            ])
        );
    }
}
