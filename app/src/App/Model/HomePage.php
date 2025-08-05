<?php

namespace SLONline\App\Model;

use SilverStripe\SiteConfig\SiteConfig;

/**
 * Home Page
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class HomePage extends \Page
{
    private static string $table_name = 'HomePage';

    private static string $singluar_name = 'Home Page';
    private static string $plural_name = 'Home Pages';

    private static array $db = [];

    private static array $has_many = [];

    private static array $allowed_children = [];

    public function metaTitle(): string
    {
        return SiteConfig::current_site_config()->Title . (SiteConfig::current_site_config()->Tagline ? ' - ' . SiteConfig::current_site_config()->Tagline : '');
    }


}
