<?php

namespace SLONline\App\Model;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\ORM\DataList;
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
 * @property string AboutText
 * @property string MarqueeText
 * @property string MarqueeLink
 */
class HomePage extends \Page
{
    private static string $table_name = 'HomePage';

    private static string $singular_name = 'Home Page';
    private static string $plural_name = 'Home Pages';

    private static array $db = [
        'AboutText' => 'Text',
        'MarqueeText' => 'HTMLText',
        'MarqueeLink' => 'Varchar(1000)',
    ];

    private static array $has_one = [];

    private static array $has_many = [];

    private static array $owns = [
        'AboutImage',
    ];

    private static array $allowed_children = [];

    public function metaTitle(): string
    {
        return SiteConfig::current_site_config()->Title . (SiteConfig::current_site_config()->Tagline ? ' - ' . SiteConfig::current_site_config()->Tagline : '');
    }

    public function featuredFontFamilyPages(): DataList
    {
        return FontFamilyPage::get()
            ->filter('FamilyIsFeatured', true)
            ->sort('ReleaseDate', 'DESC');
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('Content');

        $fields->fieldByName('Root.Main.MarqueeText')?->setEditorConfig('small');

        return $fields;
    }
}
