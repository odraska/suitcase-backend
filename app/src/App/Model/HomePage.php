<?php

namespace SLONline\App\Model;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\ORM\HasManyList;
use SilverStripe\SiteConfig\SiteConfig;
use SLONline\Elefont\Model\FontFamilyPage;
use SLONline\Elefont\Model\WebsiteBlocks\WebsiteBlock;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

/**
 * Home Page
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property HasManyList|Slide Slides
 */
class HomePage extends \Page
{
    private static string $table_name = 'HomePage';

    private static string $singluar_name = 'Home Page';
    private static string $plural_name = 'Home Pages';

    private static array $db = [];

    private static array $has_many = [
        'Slides' => Slide::class
    ];

    private static array $owns = [
        'Slides',
    ];

    private static array $allowed_children = [];

    public function metaTitle(): string
    {
        return SiteConfig::current_site_config()->Title . (SiteConfig::current_site_config()->Tagline ? ' - ' . SiteConfig::current_site_config()->Tagline : '');
    }

    public function featuredFontFamilyPages()
    {
        return FontFamilyPage::get()
            ->filter('IsFeatured', true)
            ->sort('ReleaseDate', 'DESC');
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $config = GridFieldConfig_RecordEditor::create();;
        $config->addComponent(new GridFieldSortableRows('SortOrder'));

        $fields->addFieldToTab('Root.Slides',
            GridField::create('Slides', 'Slides', $this->Slides(), $config));

        return $fields;
    }
}
