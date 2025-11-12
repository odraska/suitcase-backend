<?php

namespace SLONline\App\GraphQL\Schemas\Mutations;

use SilverStripe\GraphQL\Schema\Field\Mutation;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\CartResolver;

/**
 * Save Cart Mutation GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class SaveCart implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addMutation(Mutation::create('saveCart')
            ->setDescription('Save the current cart')
            ->setType('ID', true)
            ->addArg('captchaToken', 'String!')
            ->addArg('familyProductSelections', '[FamilyProductSelectionInput!]!')
            ->addArg('discountCode', 'String')
            ->setResolver([CartResolver::class, 'resolveSaveCart'])
        );
    }
}
