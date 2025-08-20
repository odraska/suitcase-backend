<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\ModelType;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * HomePage Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class HomePage implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\HomePage::class, function (ModelType $model) {
            $model->addFields([
                'featuredFontFamilyPages' => [
                    'type' => '[FontFamilyPage!]!',
                    'plugins' => [
                        'filter' => false,
                        'paginateList' => false,
                        'sort' => false
                    ],
                ],
                'slides' => [
                    'type' => '[Slide!]',
                    'plugins' => [
                        'filter' => false,
                        'paginateList' => false,
                        'sort' => false
                    ],
                ],
                'aboutText' => [
                    'type' => 'String',
                ],
                'aboutImage' => [
                    'type' => 'Image',
                ],
            ]);
            $model->addOperation('readOne', [
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => false,
                    'sort' => false,
                    'filter' => false
                ],
            ]);
        });
    }
}
