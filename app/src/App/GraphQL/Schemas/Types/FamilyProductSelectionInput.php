<?php

namespace SLONline\App\GraphQL\Schemas\Types;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Type;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Family Product Selection Input Type GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class FamilyProductSelectionInput implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addType(Type::create('FamilyProductSelectionInput')
            ->setIsInput(true)
            ->addField('productID', 'ID!')
            ->addField('productType', 'String!')
            ->addField('licenses', '[LicenseInput!]!')
        );
    }
}
