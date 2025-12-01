<?php

namespace SLONline\App\Model;

use Page;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

/**
 * Licensing Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class LicensingPage extends Page
{
    private static string $table_name = 'LicensingPage';

    private static string $singular_name = 'Licensing Page';
    private static string $plural_name = 'Licensing Pages';

    private static array $allowed_children = [LicensePage::class];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Content');

        $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content', 'Content'));
        $fields->fieldByName('Root.Main.Content')->setEditorConfig('small');

        return $fields;
    }
}
