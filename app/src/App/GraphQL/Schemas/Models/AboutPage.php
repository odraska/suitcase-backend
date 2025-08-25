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
            $model->addField('coverImage', [
                'type' => 'Image',
            ]);
            $model->addField(
                'aboutText',
                [
                    'type' => 'String!',
                    'plugins' => [
                        'requiredField' => true
                    ],
                ]
            );
            $model->addField(
                'contactAddress',
                [
                    'type' => 'String!',
                    'plugins' => [
                        'requiredField' => true
                    ],
                ]
            );
            $model->addField(
                'contactSocial',
                [
                    'type' => 'String!',
                    'plugins' => [
                        'requiredField' => true
                    ],
                ]
            );
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
            $model->addField('awards', [
                'type' => '[Award!]!',
                'plugins' => [
                    'filter' => false,
                    'paginateList' => false,
                    'sort' => false
                ],
            ]);

            $model->addField('press', [
                'type' => '[Press!]!',
                'plugins' => [
                    'filter' => false,
                    'paginateList' => false,
                    'sort' => false
                ],
            ]);

            $model->addField('presentations', [
                'type' => '[Presentation!]!',
                'plugins' => [
                    'filter' => false,
                    'paginateList' => false,
                    'sort' => false
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
