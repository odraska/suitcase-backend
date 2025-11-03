<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Project Page Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class ProjectPage implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\ProjectPage::class, function ($model) {
            $model->addField('annotation', [
                'type' => 'String!',
                'plugins' => [
                    'requiredField' => true,
                ],
            ]);
            $model->addField('coverImage', [
                'type' => 'Image!',
            ]);
            $model->addField('authors', [
                'type' => '[Author!]!',
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => false,
                    'sort' => false,
                    'filter' => false,
                ],
            ]);

            $model->addOperation('read', [
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => true,
                    'sort' => true,
                    'filter' => true,
                ],
            ]);
            $model->addOperation('readOne', [
                'plugins' => [
                    'readVersion' => false,
                    'paginateList' => false,
                    'sort' => false,
                    'filter' => true
                ],
            ]);
        });
    }
}
