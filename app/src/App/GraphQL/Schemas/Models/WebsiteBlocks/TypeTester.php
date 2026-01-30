<?php

namespace SLONline\App\GraphQL\Schemas\Models\WebsiteBlocks;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\ModelType;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Type Tester Website Block GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class TypeTester implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\WebsiteBlocks\TypeTester::class, function (ModelType $model) {

            $model->addFields([
                'id' => [
                    'type' => 'ID!',
                ],
                'typeTesters' => [
                    'type' => '[TypeTesterItem!]!',
                    'plugins' => [
                        'requiredField' => true,
                        'filter' => false,
                        'paginateList' => false,
                        'sort' => false
                    ],
                ],
                'fontFamilies' => [
                    'type' => '[FontFamily!]!',
                    'plugins' => [
                        'filter' => false,
                        'paginateList' => false,
                        'sort' => false
                    ],
                ],
            ]);
            $model->removeField('title');

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
