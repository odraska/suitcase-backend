<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\ModelType;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Member Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class Member implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SilverStripe\Security\Member::class, function (ModelType $model) {
            $model->addFields([
                'id' => [
                    'type' => 'ID!',
                ],
                'email' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'firstName' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'surname' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'organisation' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'street' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'street2' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'city' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'zip' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'country' => [
                    'type' => 'Country!',
                    'plugins' => ['requiredField' => true],
                ],
                'state' => [
                    'type' => 'State!',
                    'plugins' => ['requiredField' => true],
                ],
                'phone' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'companyID' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'taxID' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'vatID' => [
                    'type' => 'String!',
                    'plugins' => ['requiredField' => true],
                ],
                'validVATID' => [
                    'type' => 'Boolean!',
                    'plugins' => ['requiredField' => true],
                ],
                'newsletter' => [
                    'type' => 'Boolean!',
                    'property' => 'newsletterSubscribed',
                    'plugins' => ['requiredField' => true],
                ],
                'locale' => [
                    'type' => 'Locale!',
                    'property' => 'getCustomisedLocale',
                    'plugins' => ['requiredField' => true],
                ],
                'licenseAddresses' => [
                    'type' => '[MemberAddress!]!',
                    'plugins' => [
                        'filter' => false,
                        'paginateList' => false,
                        'sort' => false,
                    ],
                ],
                'orders' => [
                    'type' => '[Order!]!',
                    'plugins' => [
                        'filter' => false,
                        'paginateList' => false,
                        'sort' => false,
                    ],
                ],
            ]);
            $model->addOperation('readOne');
        });
    }
}
