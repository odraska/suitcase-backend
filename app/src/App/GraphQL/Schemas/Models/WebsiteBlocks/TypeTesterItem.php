<?php

namespace SLONline\App\GraphQL\Schemas\Models\WebsiteBlocks;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\ModelType;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Type Tester Item Website Block GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class TypeTesterItem implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\WebsiteBlocks\TypeTesterItem::class, function (ModelType $model) {

            $model->addFields([
                'id' => [
                    'type' => 'ID!',
                ],
                'type' => [
                    'type' => 'TypeTesterType!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ],
                'fontSize' => [
                    'type' => 'Int!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ],
                'fontsSizeMobile' => [
                    'type' => 'Int!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ],
                'tracking' => [
                    'type' => 'Float!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ],
                'lineHeight' => [
                    'type' => 'Float!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ],
                'maxHeight' => [
                    'type' => 'Int!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ],
                'defaultFont' => [
                    'type' => 'Font!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ],
                'text' => [
                    'type' => 'String!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ],
            ]);
            $model->removeField('title');
        });
    }
}
