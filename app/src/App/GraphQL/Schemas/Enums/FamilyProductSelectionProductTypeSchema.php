<?php

namespace SLONline\App\GraphQL\Schemas\Enums;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Enum;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Family Product Selection Product Type Enum GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class FamilyProductSelectionProductTypeSchema implements PartialSchemaUpdater
{
    const string FamilyProduct = 'FamilyProduct';
    const string FamilyPackageProduct = 'FamilyPackageProduct';
    const string FontProduct = 'FontProduct';

    public static function updateSchema(Schema $schema): void
    {
        $schema->addEnum(Enum::create('FamilyProductSelectionProductType', [
            self::FamilyProduct,
            self::FamilyPackageProduct,
            self::FontProduct,
        ])
        );
    }
}
