<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\ModelType;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Press Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class Press implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\Press::class, function (ModelType $model) {
            $model->addFields([
                'id' => [
                    'type' => 'ID!',
                ],
                'title' => [
                    'type' => 'String!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ],
                'url' => [
                    'type' => 'String!',
                    'plugins' => [
                        'requiredField' => true,
                    ],
                ]]);
        });
    }
}
