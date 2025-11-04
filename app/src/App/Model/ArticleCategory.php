<?php

namespace SLONline\App\Model;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;
use SilverStripe\View\Parsers\URLSegmentFilter;

/**
 * Article Category Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string Title
 * @property string $UrlSegment
 * @property int EditorialPageID
 * @method EditorialPage EditorialPage()
 * @method HasManyList|ArticlePage Articles()
 */
class ArticleCategory extends DataObject
{
    private static string $table_name = 'ArticleCategories';

    private static string $singular_name = 'Article Category';
    private static string $plural_name = 'Articles Categories';

    private static array $db = [
        'Title' => 'Varchar(255)',
        'UrlSegment' => 'Varchar(255)',
    ];

    private static array $has_one = [
        'EditorialPage' => EditorialPage::class,
    ];

    private static array $has_many = [
        'Articles' => ArticlePage::class,
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('EditorialPageID');
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
