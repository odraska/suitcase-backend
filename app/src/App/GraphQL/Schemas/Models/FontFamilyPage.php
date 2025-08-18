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
            ->addField('footerButton', ['type' => 'FooterButton!', 'property' => 'footerButton']);
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
                        ]
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
