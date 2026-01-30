<?php

namespace SLONline\App\GraphQL\Schemas\Enums;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Enum;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Crop Method Enum GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class CropMethodSchema implements PartialSchemaUpdater
{
    const string CROP_METHOD_COVER = 'Cover';
    const string CROP_METHOD_CONTAIN = 'Contain';

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addEnum(Enum::create('CropMethod', [
            self::CROP_METHOD_CONTAIN,
            self::CROP_METHOD_COVER,
        ]));
    }
}
