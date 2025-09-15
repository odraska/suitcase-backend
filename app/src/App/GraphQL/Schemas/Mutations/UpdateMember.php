<?php

namespace SLONline\App\GraphQL\Schemas\Mutations;

use SilverStripe\GraphQL\Schema\Field\Mutation;
use SilverStripe\GraphQL\Schema\Schema;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\UpdateMemberResolver;

/**
 * Update Member Mutation GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class UpdateMember implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addMutation(Mutation::create('updateMember')
            ->setDescription('Create a new member')
            ->setType('Member', true)
            ->addArg('firstName', 'String!')
            ->addArg('surname', 'String!')
            ->addArg('email', 'String!')
            ->addArg('password', 'String!')
            ->addArg('organisation', 'String!')
            ->addArg('street', 'String!')
            ->addArg('city', 'String!')
            ->addArg('zip', 'String!')
            ->addArg('countryID', 'ID!')
            ->addArg('stateID', 'ID!')
            ->addArg('vatID', 'String!')
            ->addArg('newsletter', 'Boolean!')
            ->setResolver([UpdateMemberResolver::class, 'resolve'])
        );
    }
}
