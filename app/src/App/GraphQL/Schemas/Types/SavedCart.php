<?php

namespace SLONline\App\GraphQL\Schemas\Types;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Type;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Saved Cart Type GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class SavedCart implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addType(Type::create('SavedCartFamilyProductSelection')
            ->addField('productID', 'ID!')
            ->addField('productType', 'FamilyProductSelectionProductType!')
            ->addField('licenses', '[SavedCartLicense!]!')
        );

        $schema->addType(Type::create('SavedCartLicense')
            ->addField('id', 'ID!')
            ->addField('firstParameterCoefficientID', 'ID')
            ->addField('secondParameterCoefficientID', 'ID')
        );

        $schema->addType(Type::create('SavedCart')
            ->addField('hash', 'String!')
            ->addField('familyProductSelections', '[SavedCartFamilyProductSelection!]!')
            ->addField('discountCode', 'String')
            ->addField('downloadUrl', 'String')
        );


    }
}
