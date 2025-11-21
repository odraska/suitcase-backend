<?php

namespace SLONline\App\GraphQL\Schemas\Types;

use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\InputType;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Member Input Type GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class MemberInput implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addType(InputType::create('MemberInput', [
            'fields' => [
                'id' => [
                    'type' => 'ID',
                    'description' => 'User`s ID'
                ],
                'email' => [
                    'type' => 'String!',
                    'description' => 'User`s email'
                ],
                'firstName' => [
                    'type' => 'String!',
                    'description' => 'User`s first name'
                ],
                'surname' => [
                    'type' => 'String!',
                    'description' => 'User`s surname'
                ],
                'organisation' => [
                    'type' => 'String',
                    'description' => 'User`s organisation'
                ],
                'street' => [
                    'type' => 'String',
                    'description' => 'User`s street address'
                ],
                'street2' => [
                    'type' => 'String',
                    'description' => 'User`s additional street address'
                ],
                'city' => [
                    'type' => 'String',
                    'description' => 'User`s city'
                ],
                'zip' => [
                    'type' => 'String',
                    'description' => 'User`s zip code'
                ],
                'phone' => [
                    'type' => 'String',
                    'description' => 'User`s phone number'
                ],
                'companyID' => [
                    'type' => 'String',
                    'description' => 'User`s company ID'
                ],
                'taxID' => [
                    'type' => 'String',
                    'description' => 'User`s tax ID'
                ],
                'vatID' => [
                    'type' => 'String',
                    'description' => 'User`s VAT ID'
                ],
                'countryID' => [
                    'type' => 'ID',
                    'description' => 'User`s country ID'
                ],
                'stateID' => [
                    'type' => 'ID',
                    'description' => 'User`s state ID'
                ],
                'locale' => [
                    'type' => 'Locale',
                    'description' => 'User`s locale'
                ],
            ]
        ]));
    }
}
