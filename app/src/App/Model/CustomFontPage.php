<?php

namespace SLONline\App\Model;

use Page;
use SilverStripe\Assets\Image;

/**
 * Custom Font Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 *
 * @property string ShortText
 * @property int CoverImageID
 * @property int CategoryID
 * @method Image CoverImage()
 * @method CustomFontCategory Category()
 */
class CustomFontPage extends Page
{
    private static string $table_name = 'CustomFontPage';
    private static string $singular_name = 'Custom Font';
    private static string $plural_name = 'Custom Fonts';

    private static bool $can_be_root = false;
    private static array $allowed_children = ['none'];

    private static array $db = [
        'ShorText' => 'HTMLText',
    ];

    private static array $has_one = [
        'CoverImage' => Image::class,
        'Category' => CustomFontCategory::class,
    ];

    private static array $owns = [
        'CoverImage',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('SortOrder');
        $fields->removeByName('Content');

        $fields->fieldByName('Root.Main.ShorText')?->setEditorConfig('small');

        return $fields;
    }

    public function canView($member = null): bool
    {
        return true;
    }
}
