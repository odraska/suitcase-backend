<?php

namespace SLONline\App\Model;

use Page;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

/**
 * License Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string ShortContent
 */
class LicensePage extends Page
{
    private static string $table_name = 'LicensePage';

    private static string $singular_name = 'License Page';
    private static string $plural_name = 'License Pages';

    private static array $allowed_children = ['none'];
    private static bool $can_be_root = false;

    private static array $db = [
        'ShortContent' => 'HTMLText',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Content');

        $fields->fieldByName('Root.Main.ShortContent')->setEditorConfig('small');

        $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content', 'Content'));
        $fields->fieldByName('Root.Main.Content')->setEditorConfig('small');

        return $fields;
    }
}
