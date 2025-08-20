<?php

namespace SLONline\App\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\View\Parsers\URLSegmentFilter;
use SLONline\Elefont\Model\FontFamilyPage;

/**
 * Font Category Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string Title
 * @property string UrlSegment
 * @method ManyManyList|FontFamilyPage FontFamilyPages()
 */
class FontCategory extends DataObject
{
    private static string $table_name = 'FontCategory';
    private static string $singular_name = 'Font category';
    private static string $plural_name = 'Font categories';

    private static array $db = [
        'Title' => 'Varchar(100)',
        'UrlSegment' => 'Varchar(100)',
    ];

    private static array $belongs_many_many = [
        'FontFamilyPages' => FontFamilyPage::class,
    ];

    private static array $summary_fields = [
        'Title' => 'Title',
        'UrlSegment' => 'URL Segment',
    ];

    public function canView($member = null): bool
    {
        return true;
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (empty($this->UrlSegment)) {
            $this->UrlSegment = $this->Title;
        }

        if (!empty($this->UrlSegment) && $this->isChanged('UrlSegment')) {
            $filter = URLSegmentFilter::create();
            $this->UrlSegment = $filter->filter($this->UrlSegment);
        }
    }
}
