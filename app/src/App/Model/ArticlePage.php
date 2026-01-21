<?php

namespace SLONline\App\Model;

use Page;
use SilverStripe\Assets\Image;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\ManyManyList;
use SLONline\EditorJSField\Forms\EditorJSField;

/**
 * Article Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string Annotation
 * @property string ContentJS
 * @property bool Spotlight
 * @property bool Pinned
 * @property int CoverImageID
 * @property int CategoryID
 * @method Image CoverImage()
 * @method ArticleCategory Category()
 * @method ManyManyList|Author Authors()
 */
class ArticlePage extends Page
{
    private static string $table_name = 'ArticlePage';

    private static string $singular_name = 'Article';
    private static string $plural_name = 'Articles';

    private static bool $can_be_root = false;
    private static array $allowed_children = ['none'];

    private static array $db = [
        'Annotation' => 'HTMLText',
        'ContentJS' => 'Text',
        'Spotlight' => 'Boolean',
        'Pinned' => 'Boolean',
    ];

    private static array $has_one = [
        'CoverImage' => Image::class,
        'Category' => ArticleCategory::class,
    ];

    private static array $many_many = [
        'Authors' => Author::class,
    ];

    private static array $owns = [
        'CoverImage',
    ];

    private static string $default_sort = 'Pinned DESC, Sort ASC';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Content');
        $fields->addFieldToTab('Root.Main',
            EditorJSField::create('ContentJS', $this->fieldLabel('Content'), $this->ContentJS)
        );

        return $fields;
    }

    public function contentBlocks(): ArrayList
    {
        return EditorJSField::decodeData($this->ContentJS ?? '');
    }
}
