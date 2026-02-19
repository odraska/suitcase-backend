<?php

namespace SLONline\App\GraphQL\Schemas\Types;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Type;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Navigation Settings Type GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class NavigationSettings implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addType(Type::create('HamburgerMenuItem')
            ->addField('title', ['type' => 'String!'])
            ->addField('url', ['type' => 'String!'])
            ->addField('divider', ['type' => 'Boolean!'])
        );

        $schema->addType(Type::create('FooterMenuItem')
            ->addField('title', ['type' => 'String!'])
            ->addField('url', ['type' => 'String!'])
        );

        $schema->addType(Type::create('NavigationSettings')
            ->addField('hamburgerMenu', ['type' => '[HamburgerMenuItem!]!', 'plugins' => [
                'paginateList' => false,
                'sort' => false,
                'filter' => false
            ]])
            ->addField('fontFamilyPages', ['type' => '[FontFamilyPage!]!', 'plugins' => [
                'paginateList' => false,
                'sort' => false,
                'filter' => false
            ]])
            ->addField('customFontPages', ['type' => '[CustomFontPage!]!', 'plugins' => [
                'paginateList' => false,
                'sort' => false,
                'filter' => false
            ]])
            ->addField('footerMenu', ['type' => '[FooterMenuItem!]!', 'plugins' => [
                'paginateList' => false,
                'sort' => false,
                'filter' => false
            ]])
        );
    }
}
