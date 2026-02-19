<?php

namespace SLONline\App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\SiteConfig\SiteConfig;
use SLONline\App\Model\CustomFontPage;
use SLONline\Elefont\Model\FontFamilyPage;

/**
 * Navigation Settings GraphQL Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class NavigationSettingsResolver
{
    public static function resolve($obj, array $args, array $context, ResolveInfo $info): ArrayData
    {
        $hamburgerMenu = ArrayList::create();
        foreach (SiteConfig::current_site_config()->HamburgerMenu()->sort('HamburgerMenuSortOrder ASC') as $page) {
            $hamburgerMenu->push(ArrayData::create([
                'title' => $page->Title,
                'url' => $page->Link(),
                'divider' => $page->Divider
            ]));
        }

        $footerMenu = ArrayList::create();
        foreach (SiteConfig::current_site_config()->FooterMenu()->sort('FooterMenuSortOrder ASC') as $page) {
            $footerMenu->push(ArrayData::create([
                'title' => $page->Title,
                'url' => $page->Link(),
            ]));
        }

        return ArrayData::create([
            'hamburgerMenu' => $hamburgerMenu,
            'fontFamilyPages' => DataList::create(FontFamilyPage::class),
            'customFontPages' => DataList::create(CustomFontPage::class),
            'footerMenu' => $footerMenu
        ]);
    }
}
