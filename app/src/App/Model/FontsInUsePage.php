<?php

namespace SLONline\App\Model;

use Page;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataList;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

/**
 * Fonts In Use Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class FontsInUsePage extends Page
{
    private static string $table_name = 'FontsInUsePage';
    private static string $singular_name = 'Fonts In Use';
    private static string $plural_name = 'Fonts In Use';

    private static bool $can_be_root = true;
    private static array $allowed_children = ['none'];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Items', GridField::create(
            'Items',
            'Items',
            $this->getItems(),
            GridFieldConfig_RecordEditor::create()
                ->addComponent(GridFieldSortableRows::create('SortOrder'))
        ));

        return $fields;
    }

    public function getItems(): DataList
    {
        return FontsInUse::get();
    }
}
