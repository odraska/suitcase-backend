<?php

namespace SLONline\App\Model;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;
use SilverStripe\View\Parsers\URLSegmentFilter;

/**
 * Custom Font Category Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 *
 * @property string Title
 * @property string $UrlSegment
 * @property int CustomFontsPageID
 * @method CustomFontsPage CustomFontsPage()
 * @method HasManyList|CustomFontPage CustomFonts()
 */
class CustomFontCategory extends DataObject
{
    private static string $table_name = 'CustomFontCategories';

    private static string $singular_name = 'Custom Font Category';
    private static string $plural_name = 'Custom Font Categories';

    private static array $db = [
        'Title' => 'Varchar(255)',
        'UrlSegment' => 'Varchar(255)',
    ];

    private static array $has_one = [
        'CustomFontsPage' => CustomFontsPage::class,
    ];

    private static array $has_many = [
        'CustomFonts' => CustomFontPage::class,
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('CustomFontsPageID');
        return $fields;
    }

    public function canView($member = null): bool
    {
        return true;
    }

    protected function onBeforeWrite(): void
    {
        parent::onBeforeWrite();

        if (!$this->UrlSegment) {
            $this->UrlSegment = URLSegmentFilter::create()->filter($this->Title);
        }
    }
}
