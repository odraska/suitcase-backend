<?php

namespace SLONline\App\GraphQL\Schemas\Models\WebsiteBlocks;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\ModelType;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Rich Text Website Block GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class RichText implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\WebsiteBlocks\RichText::class, function (ModelType $model) {
            $model->addFields([
                'id' => [
                    'type' => 'ID!',
                ],
                'content' => [
                    'type' => 'String!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ],
            ]);
            //$model->removeField('title');

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
