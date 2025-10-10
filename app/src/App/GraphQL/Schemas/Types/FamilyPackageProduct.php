<?php

namespace SLONline\App\GraphQL\Schemas\Types;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Type;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\GraphQL\Resolvers\AssetResolver;

/**
 * Family Package Product Type GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class FamilyPackageProduct implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addType(Type::create('FamilyPackageProduct')
            ->setFieldResolver([AssetResolver::class, 'resolveField'])
            ->addField('id', 'ID!')
            ->addField('title', ['type' => 'String!', 'plugins' => ['requiredField' => true]])
            ->addField('fontFamilies', ['type' => '[FontFamily!]!', 'plugins' => ['requiredField' => true]])
            ->addField('fonts', ['type' => '[Font!]!', 'plugins' => [
                'paginateList' => false,
                'sort' => false,
                'filter' => false
            ]])
            ->addField('price', ['type' => 'Money!', 'plugins' => ['requiredField' => true]])
        );
    }
}
