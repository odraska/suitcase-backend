<?php

namespace SLONline\App\GraphQL\Schemas\Models\WebsiteBlocks;

use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\ModelType;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Website Block GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class WebsiteBlock implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema
            ->removeModelByClassName(\SLONline\Elefont\Model\WebsiteBlocks\WebsiteBlock::class)
            ->removeInterface('WebsiteBlockInterface')
            ->removeModel('ImageBlock')
            ->removeModel('BuyingOptions')
            ->removeModel('ShortTextPreview')
            ->removeModel('LongTextPreview')
            ->removeModel('Text');

        $schema->addModel($schema->createModel(\SLONline\Elefont\Model\WebsiteBlocks\WebsiteBlock::class), function (ModelType $model) {

            $model->addFields([
                'id' => [
                    'type' => 'ID!',
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
                        ]
                    ]
                ],
            ]);
        });
    }
}
