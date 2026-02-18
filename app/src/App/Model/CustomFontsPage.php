<?php

namespace SLONline\App\Model;

use Page;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\HasManyList;

/**
 * Custom Fonts Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 *
 * @method HasManyList|CustomFontCategory Categories
 */
class CustomFontsPage extends Page
{
    private static string $table_name = 'CustomFontsPage';

    private static string $singular_name = 'Custom Fonts';
    private static string $plural_name = 'Custom Fonts';

    private static bool $can_be_root = true;
    private static array $allowed_children = [
        CustomFontPage::class
    ];

    private static array $has_many = [
        'Categories' => CustomFontCategory::class,
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root.Main.Content')?->setEditorConfig('small');

        return $fields;
    }
}
