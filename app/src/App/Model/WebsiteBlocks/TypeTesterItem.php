<?php

namespace SLONline\App\Model\WebsiteBlocks;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GroupedDropdownField;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use SLONline\Elefont\Model\Font;
use SLONline\Elefont\Model\FontFamily;

/**
 * Type Tester Item for Website Block
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string Type
 * @property int FontSize
 * @property int FontsSizeMobile
 * @property float Tracking
 * @property int TypeTesterID
 * @property int DefaultFontID
 * @method TypeTester TypeTester
 * @method Font DefaultFont
 */
class TypeTesterItem extends DataObject
{
    const string TYPE_SINGLE_WORD = 'SingleWord';
    const string TYPE_SENTENCE = 'Sentence';
    const string TYPE_TWO_COLUMNS = 'TwoColumns';
    const string TYPE_THREE_COLUMNS = 'ThreeColumns';

    private static string $table_name = 'TypeTesterItem';

    private static string $singular_name = 'Type tester';
    private static string $plural_name = 'Type tester';

    private static array $db = [
        'Type' => 'Enum("' . self::TYPE_SINGLE_WORD . ',' . self::TYPE_SENTENCE . ',' . self::TYPE_TWO_COLUMNS . ',' . self::TYPE_THREE_COLUMNS . '", "' . self::TYPE_SINGLE_WORD . '"))',
        'FontSize' => 'Int',
        'FontsSizeMobile' => 'Int',
        'Tracking' => 'Decimal(6,4)',
        'SortOrder' => 'Int',
    ];

    private static array $has_one = [
        'TypeTester' => TypeTester::class,
        'DefaultFont' => Font::class,
    ];

    private static array $summary_fields = [
        'ID',
        'Type',
        'DefaultFont.Name',
        'FontSize',
        'FontsSizeMobile',
        'Tracking',
    ];

    private static array $owned_by = [
        'TypeTester',
    ];

    private static array $defaults = [
        'Type' => self::TYPE_SINGLE_WORD,
    ];

    private static array $extensions = [
        Versioned::class,
    ];

    private static array $default_sort = ['SortOrder' => 'ASC'];

    private static bool $versioned_gridfield_extensions = true;

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('TypeTesterID');
        $fields->removeByName('SortOrder');

        $source = [];
        foreach (DataList::create(FontFamily::class) as $family) {
            $source[$family->Title] = $family->Fonts()->map('ID', 'Title')->toArray();
        }

        $fields->replaceField(
            'DefaultFontID',
            GroupedDropdownField::create('DefaultFontID', $this->fieldLabel('DefaultFont'), $source)
                ->setEmptyString('-- Select Font --')
        );

        return $fields;
    }

    public function getText()
    {
        return $this->TypeTester()->getTextForType($this->Type);
    }

    public function canView($member = null): bool
    {
        return true;
    }
}
