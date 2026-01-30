<?php

namespace SLONline\App\Model\WebsiteBlocks;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\HasManyList;
use SLONline\Elefont\Model\WebsiteBlocks\WebsiteBlock;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

/**
 * Type Tester Website Block
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @method HasManyList|TypeTester TypeTesters
 */
class TypeTester extends WebsiteBlock
{

    private static string $table_name = 'TypeTesterWebsiteBlock';

    private static string $singular_name = 'Type testers block';
    private static string $plural_name = 'Type testers blocks';

    private static array $has_many = [
        'TypeTesters' => TypeTesterItem::class,
    ];

    private static array $owns = [
        'TypeTesters',
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        $fields->removeFieldsFromTab('Root', [
            'Title',
            'SubTitle',
            'SortOrder',
            'Content',
            'Link',
            'BackgroundColor',
            'BackgroundGradientColor',
            'TitleColor',
            'ContentColor',
            'Image',
            'LinkToPageID',
            'BackgroundImage',
            'TypeTesters'
        ]);

        $fields->addFieldToTab('Root.Main', GridField::create(
            'TypeTesters',
            'Type testers',
            $this->TypeTesters(),
            GridFieldConfig_RecordEditor::create()
                ->addComponent(GridFieldSortableRows::create('SortOrder'))
        ));

        return $fields;
    }

    public function getTextForType(?string $type): string
    {
        return match ($type) {
            TypeTesterItem::TYPE_SINGLE_WORD => 'Hello',
            TypeTesterItem::TYPE_SENTENCE => 'The quick brown fox jumps over the lazy dog.',
            TypeTesterItem::TYPE_TWO_COLUMNS => 'The quick brown fox jumps over the lazy dog. | Pack my box with five dozen liquor jugs.',
            TypeTesterItem::TYPE_THREE_COLUMNS => 'The quick brown fox jumps over the lazy dog. | Pack my box with five dozen liquor jugs. | How vexingly quick daft zebras jump!',
            default => '',
        };
    }

    public function getFontFamilies()
    {
        $list = ArrayList::create();
        foreach ($this->TypeTesters() as $typeTester) {
            if ($list->find('ID', $typeTester->DefaultFont()->FontFamilyID)) {
                continue;
            }

            $list->add($typeTester->DefaultFont()->FontFamily());
        }

        return $list;
    }
}
