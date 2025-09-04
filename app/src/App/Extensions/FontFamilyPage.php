<?php

namespace SLONline\App\Extensions;

use SilverStripe\Assets\File;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\SearchableMultiDropdownField;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\Model\List\SS_List;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\HasManyList;
use SilverStripe\ORM\ManyManyList;
use SLONline\App\Model\Author;
use SLONline\App\Model\FontCategory;
use SLONline\App\Model\FontsInUse;
use SLONline\App\Model\Slide;
use SLONline\ColorField\Form\ColorField;
use SLONline\ColorField\ORM\FieldType\DBColor;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

/**
 * Font Family Page Application Extension
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property \SLONline\Elefont\Model\FontFamilyPage $owner
 *
 * @property string $FooterButtonBorderColor
 * @property string $FooterButtonTextColor
 * @property string $FooterButtonBackgroundColor
 * @property int $FooterButtonBorderRadius
 * @property bool $ShowInBasicTrial
 * @property bool $ShowInFullTrial
 * @property int $PDFSpecimenID
 * @method  File PDFSpecimen
 * @method HasManyList|Slide Slides
 * @method  ManyManyList|FontCategory FontCategories
 * @method  ManyManyList|Font StylesRow1
 * @method  ManyManyList|Font StylesRow2
 * @method  ManyManyList|Font StylesRow3
 * @method  ManyManyList|FontsInUse FontsInUse
 * @method  ManyManyList|Author Authors
 */
class FontFamilyPage extends Extension
{
    private static array $db = [
        'FooterButtonBorderColor' => DBColor::class,
        'FooterButtonTextColor' => DBColor::class,
        'FooterButtonBackgroundColor' => DBColor::class,
        'FooterButtonBorderRadius' => 'Int',
        'ShowInBasicTrial' => 'Boolean',
        'ShowInFullTrial' => 'Boolean',
    ];

    private static array $has_one = [
        'PDFSpecimen' => File::class,
    ];
    private static array $has_many = [
        'Slides' => Slide::class,
    ];

    private static array $many_many = [
        'FontCategories' => FontCategory::class,
        'StylesRow1' => \SLONline\Elefont\Model\Font::class,
        'StylesRow2' => \SLONline\Elefont\Model\Font::class,
        'StylesRow3' => \SLONline\Elefont\Model\Font::class,
    ];

    private static array $many_many_extraFields = [
        'StylesRow1' => [
            'StylesRowSortOrder' => 'Int',
        ],
        'StylesRow2' => [
            'StylesRowSortOrder' => 'Int',
        ],
        'StylesRow3' => [
            'StylesRowSortOrder' => 'Int',
        ],
    ];

    private static array $belongs_many_many = [
        'Authors' => Author::class,
        'FontsInUse' => FontsInUse::class
    ];

    public function footerButton(): array
    {
        return [
            'borderColor' => $this->owner->FooterButtonBorderColor,
            'textColor' => $this->owner->FooterButtonTextColor,
            'backgroundColor' => $this->owner->FooterButtonBackgroundColor,
            'borderRadius' => $this->owner->FooterButtonBorderRadius
        ];
    }

    public function updateCMSFields(FieldList $fields)
    {
        $fields->fieldByName('Root.Main.Content')->setEditorConfig('small');

        $fields->addFieldsToTab('Root.FooterButton', [
            ColorField::create('FooterButtonBorderColor', 'Border Color'),
            NumericField::create('FooterButtonBorderRadius', 'Border Radius'),
            ColorField::create('FooterButtonTextColor', 'Text Color'),
            ColorField::create('FooterButtonBackgroundColor', 'Background Color'),
        ]);

        $fields->addFieldsToTab('Root.VisualStyles', [
            $fields->fieldByName('Root.StylesRow1.StylesRow1'),
            $fields->fieldByName('Root.StylesRow2.StylesRow2'),
            $fields->fieldByName('Root.StylesRow3.StylesRow3'),
        ]);

        /** @var GridFieldConfig $gridFieldConfig */
        $gridFieldConfig = $fields->fieldByName('Root.VisualStyles.StylesRow1')?->getConfig();
        $gridFieldConfig
            ->removeComponentsByType(GridFieldAddNewButton::class)
            ->addComponent(GridFieldSortableRows::create('StylesRowSortOrder'))
            ->removeComponentsByType(GridFieldEditButton::class);
        $gridFieldConfig->getComponentByType(GridFieldDataColumns::class)->setDisplayFields([
            'ID' => 'ID',
            'getFullName' => 'Title',
            'Style' => 'Style',
            'Weight' => 'Weight',
            'Width' => 'Width',
        ]);

        $gridFieldConfig->getComponentByType(GridFieldAddExistingAutocompleter::class)
            ->setSearchFields([
                'ID',
                'FontFamily.FamilyName:PartialMatch',
                'FontName:PartialMatch',
            ])
            ->setResultsFormat('$getFullName')
            ->setResultsLimit(30);


        $gridFieldConfig = $fields->fieldByName('Root.VisualStyles.StylesRow2')?->getConfig();
        $gridFieldConfig
            ->removeComponentsByType(GridFieldAddNewButton::class)
            ->addComponent(GridFieldSortableRows::create('StylesRowSortOrder'))
            ->removeComponentsByType(GridFieldEditButton::class);
        $gridFieldConfig->getComponentByType(GridFieldDataColumns::class)->setDisplayFields([
            'ID' => 'ID',
            'getFullName' => 'Title',
            'Style' => 'Style',
            'Weight' => 'Weight',
            'Width' => 'Width',
        ]);
        $gridFieldConfig->getComponentByType(GridFieldAddExistingAutocompleter::class)
            ->setSearchFields([
                'ID',
                'FontFamily.FamilyName:PartialMatch',
                'FontName:PartialMatch',
            ])
            ->setResultsFormat('$getFullName')
            ->setResultsLimit(30);

        $gridFieldConfig = $fields->fieldByName('Root.VisualStyles.StylesRow3')?->getConfig();
        $gridFieldConfig
            ->removeComponentsByType(GridFieldAddNewButton::class)
            ->addComponent(GridFieldSortableRows::create('StylesRowSortOrder'))
            ->removeComponentsByType(GridFieldEditButton::class);
        $gridFieldConfig->getComponentByType(GridFieldDataColumns::class)->setDisplayFields([
            'ID' => 'ID',
            'getFullName' => 'Title',
            'Style' => 'Style',
            'Weight' => 'Weight',
            'Width' => 'Width',
        ]);
        $gridFieldConfig->getComponentByType(GridFieldAddExistingAutocompleter::class)
            ->setSearchFields([
                'ID',
                'FontFamily.FamilyName:PartialMatch',
                'FontName:PartialMatch',
            ])
            ->setResultsFormat('$getFullName')
            ->setResultsLimit(30);

        $fields->removeFieldsFromTab('Root', ['StylesRow1', 'StylesRow2', 'StylesRow3', 'Authors']);

        $fields->addFieldToTab('Root.Main', SearchableMultiDropdownField::create(
            'Authors',
            $this->owner->fieldLabel('Authors'),
            DataList::create(Author::class),
            $this->owner->Authors()
        ), 'Metadata');
    }

    public function visualStyles()
    {
        $list = [];
        if ($this->owner->StylesRow1()->exists()) {
            $list[] = $this->owner->StylesRow1()->sort('StylesRowSortOrder');
        }
        if ($this->owner->StylesRow2()->exists()) {
            $list[] = $this->owner->StylesRow2()->sort('StylesRowSortOrder');
        }
        if ($this->owner->StylesRow3()->exists()) {
            $list[] = $this->owner->StylesRow3()->sort('StylesRowSortOrder');
        }
        return $list;
    }

    public function getFonts(): SS_List
    {
        return $this->owner->FontFamilyNull()?->Fonts() ?? ArrayList::create();
    }
}
