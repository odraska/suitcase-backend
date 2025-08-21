<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\ModelType;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * About Page Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class AboutPage implements PartialSchemaUpdater
{
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\AboutPage::class, function (ModelType $model) {
            $model->addField(
                'teamMembers',
                [
                    'type' => '[Author!]!',
                    'plugins' => [
                        'filter' => false,
                        'paginateList' => false,
                        'sort' => false
                    ],
                ]
            );
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
