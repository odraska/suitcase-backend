<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Order Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class Order implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $orderModel = $schema->getModelByClassName(\SLONline\Commerce\Model\Order::class);
        $orderModel->addFields([
            'licenseFirstName' => [
                'type' => 'String!',
                'plugins' => ['requiredField' => true],
            ],
            'licenseSurname' => [
                'type' => 'String!',
                'plugins' => ['requiredField' => true],
            ],
            'licenseOrganisation' => [
                'type' => 'String!',
                'plugins' => ['requiredField' => true],
            ],
            'licenseStreet' => [
                'type' => 'String!',
                'plugins' => ['requiredField' => true],
            ],
            'licenseStreet2' => [
                'type' => 'String!',
                'plugins' => ['requiredField' => true],
            ],
            'licenseCity' => [
                'type' => 'String!',
                'plugins' => ['requiredField' => true],
            ],
            'licenseZip' => [
                'type' => 'String!',
                'plugins' => ['requiredField' => true],
            ],
            'licenseCountry' => [
                'type' => 'Country!',
                'plugins' => ['requiredField' => true],
            ],
            'licenseState' => [
                'type' => 'State!',
                'plugins' => ['requiredField' => true],
            ],
            'licensePhone' => [
                'type' => 'String!',
                'plugins' => ['requiredField' => true],
            ],
        ]);
    }
}
