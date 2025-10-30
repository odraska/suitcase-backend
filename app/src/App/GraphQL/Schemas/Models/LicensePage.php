<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\ModelType;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * License Page Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class LicensePage implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\LicensePage::class, function (ModelType $model) {
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
