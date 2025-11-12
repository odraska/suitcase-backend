<?php

namespace SLONline\App\GraphQL\Schemas\Mutations;

use SilverStripe\GraphQL\Schema\Field\Mutation;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\CreateOrderResolver;

/**
 * Create Order Mutation GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class CreateOrder implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addMutation(Mutation::create('createOrder')
            ->setDescription('Create a new order')
            ->setType('Order', true)
            ->addArg('captchaToken', 'String!')
            ->addArg('member', 'MemberInput!')
            ->addArg('licenseAddress', 'AddressInput!')
            ->addArg('familyProductSelections', '[FamilyProductSelectionInput!]!')
            ->addArg('discountCode', 'String')
            ->addArg('paymentMethod', 'PaymentMethod!')
            ->setResolver([CreateOrderResolver::class, 'resolve'])
        );
    }
}
