<?php

namespace SLONline\App\GraphQL\Schemas\Models\WebsiteBlocks;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\ModelType;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Images Item Website Block GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class ImagesItem implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\WebsiteBlocks\ImagesItem::class, function (ModelType $model) {
            $model->addFields([
                'id' => [
                    'type' => 'ID!',
                ],
                'cropMethod' => [
                    'type' => 'CropMethod!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ],
                'image' => [
                    'type' => 'Image!',
                    'plugins' => [
                        'requiredField' => true,
                    ],

                ],
                'mobileImage' => [
                    'type' => 'Image',
                    'property' => 'mobileImageNull',
                ],
                'link' => [
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
