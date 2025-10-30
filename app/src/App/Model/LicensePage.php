<?php

namespace SLONline\App\Model;

use Page;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

/**
 * License Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class LicensePage extends Page
{
    private static string $table_name = 'LicensePage';

    private static string $singular_name = 'License Page';
    private static string $plural_name = 'License Pages';

    private static array $allowed_children = ['none'];
    private static bool $can_be_root = false;

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content', 'Content'));
        $fields->fieldByName('Root.Main.Content')->setEditorConfig('small');

        return $fields;
    }
}
