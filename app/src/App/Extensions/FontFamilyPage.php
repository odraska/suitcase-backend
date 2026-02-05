<?php

namespace SLONline\App\Extensions;

use SilverStripe\Assets\File;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\ReadonlyField;
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
use SLONline\GridFieldExtensions\GridFieldAddExistingDropdown;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

/**
 * Font Family Page Application Extension
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property \SLONline\Elefont\Model\FontFamilyPage $owner
 *
 * @property string $SuperFamilyTitle
 * @property string $FooterButtonBorderColor
 * @property string $FooterButtonTextColor
 * @property string $FooterButtonBackgroundColor
 * @property int $FooterButtonBorderRadius
 * @property bool $ShowInBasicTrial
 * @property bool $ShowInFullTrial
 * @property string $StylesRow1FontSize
 * @property string $StylesRow2FontSize
 * @property string $StylesRow3FontSize
 * @property int $PDFSpecimenID
 * @method File PDFSpecimen
 * @method HasManyList|Slide Slides
 * @method ManyManyList|FontCategory FontCategories
 * @method ManyManyList|Font StylesRow1
 * @method ManyManyList|Font StylesRow2
 * @method ManyManyList|Font StylesRow3
 * @method ManyManyList|FontsInUse FontsInUse
 * @method ManyManyList|Author Authors
 * @method ManyManyList|\SLONline\Elefont\Model\FontFeature FontFeatures
 */
class FontFamilyPage extends Extension
{
    const string STYLES_ROW_FONT_SIZE_SMALL = 'Small';
    const string STYLES_ROW_FONT_SIZE_MEDIUM = 'Medium';
    const string STYLES_ROW_FONT_SIZE_LARGE = 'Large';

    private static array $db = [
        'SuperFamilyTitle' => 'Varchar(255)',
        'FooterButtonBorderColor' => DBColor::class,
        'FooterButtonTextColor' => DBColor::class,
        'FooterButtonBackgroundColor' => DBColor::class,
        'FooterButtonBorderRadius' => 'Int',
        'ShowInBasicTrial' => 'Boolean',
        'ShowInFullTrial' => 'Boolean',
        'StylesRow1FontSize' => 'Enum("' .
            self::STYLES_ROW_FONT_SIZE_SMALL . ',' .
            self::STYLES_ROW_FONT_SIZE_MEDIUM . ',' .
            self::STYLES_ROW_FONT_SIZE_LARGE . '","' .
            self::STYLES_ROW_FONT_SIZE_MEDIUM . '")',
        'StylesRow2FontSize' => 'Enum("' .
            self::STYLES_ROW_FONT_SIZE_SMALL . ',' .
            self::STYLES_ROW_FONT_SIZE_MEDIUM . ',' .
            self::STYLES_ROW_FONT_SIZE_LARGE . '","' .
            self::STYLES_ROW_FONT_SIZE_MEDIUM . '")',
        'StylesRow3FontSize' => 'Enum("' .
            self::STYLES_ROW_FONT_SIZE_SMALL . ',' .
            self::STYLES_ROW_FONT_SIZE_MEDIUM . ',' .
            self::STYLES_ROW_FONT_SIZE_LARGE . '","' .
            self::STYLES_ROW_FONT_SIZE_MEDIUM . '")',
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
        'FontFeatures' => \SLONline\Elefont\Model\FontFeature::class,
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
        'FontFeatures' => [
            'CustomExample' => 'HTMLText',
            'CustomAllGlyphs' => 'HTMLText',
        ],
    ];

    private static array $belongs_many_many = [
        'Authors' => Author::class,
        'FontsInUse' => FontsInUse::class
    ];

    private static array $owns = [
        'PDFSpecimen',
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
        $superFamilyField = $fields->fieldByName('Root.Main.SuperFamilyTitle');
        $fields->removeByName('Root.Main.SuperFamilyTitle');
        $fields->fieldByName('Root.Main')->insertAfter('MenuTitle', $superFamilyField);

        $fields->fieldByName('Root.Main.Content')->setEditorConfig('small');

        $fields->addFieldsToTab('Root.FooterButton', [
            ColorField::create('FooterButtonBorderColor', 'Border Color'),
            NumericField::create('FooterButtonBorderRadius', 'Border Radius'),
            ColorField::create('FooterButtonTextColor', 'Text Color'),
            ColorField::create('FooterButtonBackgroundColor', 'Background Color'),
        ]);

        $this->updateCMSFieldsVisualStyles($fields);

        $fields->removeFieldsFromTab('Root', ['StylesRow1', 'StylesRow2', 'StylesRow3', 'Authors', 'FontCategories']);

        $fields->addFieldToTab('Root.Main', SearchableMultiDropdownField::create(
            'Authors',
            $this->owner->fieldLabel('Authors'),
            DataList::create(Author::class),
            $this->owner->Authors()
        ), 'Metadata');

        $fields->addFieldToTab('Root.Main', SearchableMultiDropdownField::create(
            'FontCategories',
            $this->owner->fieldLabel('FontCategories'),
            DataList::create(FontCategory::class),
            $this->owner->FontCategories()
        ), 'Authors');

        $this->updateCMSFieldsFontFeatures($fields);
    }

    private function updateCMSFieldsVisualStyles(FieldList $fields): void
    {
        if ($this->owner->FontFamilies()->count() == 0) {
            $fields->removeFieldFromTab('Root.VisualStyles', 'VisualStyles');
            $fields->addFieldToTab(
                'Root.VisualStyles',
                LiteralField::create(
                    'NoFonts',
                    '<p class="message warning">No font families available. Please add them first in tab "Main content".</p>'
                )
            );
            return;
        }

        $fields->addFieldsToTab('Root.VisualStyles', [
            $fields->fieldByName('Root.Main.StylesRow1FontSize'),
            $fields->fieldByName('Root.StylesRow1.StylesRow1'),
            $fields->fieldByName('Root.Main.StylesRow2FontSize'),
            $fields->fieldByName('Root.StylesRow2.StylesRow2'),
            $fields->fieldByName('Root.Main.StylesRow3FontSize'),
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
    }

    private function updateCMSFieldsFontFeatures(FieldList $fields): void
    {
        if (!$this->owner->ListDefaultFont()->exists()) {
            $fields->removeFieldFromTab('Root.FontFeatures', 'FontFeatures');
            $fields->addFieldToTab(
                'Root.FontFeatures',
                LiteralField::create(
                    'NoFont',
                    '<p class="message warning">No default font is set for this page. Please set it first in tab "Main content".</p>'
                )
            );
            return;
        }

        /** @var GridFieldConfig $featuresConfig */
        $fields->fieldByName('Root.FontFeatures.FontFeatures')?->setConfig(GridFieldConfig::create());
        $featuresConfig = $fields->fieldByName('Root.FontFeatures.FontFeatures')?->getConfig();
        if ($featuresConfig) {
            $featuresGridFieldEditableColumns = GridFieldEditableColumns::create();
            $featuresGridFieldEditableColumns->setDisplayFields([
                'ID' => [
                    'title' => 'ID',
                    'field' => ReadonlyField::class,
                ],
                'Name' => [
                    'title' => 'Name',
                    'field' => ReadonlyField::class,
                ],
                'CustomExample' => [
                    'title' => 'Custom example',
                    'callback' => function ($record, $column, $grid) {
                        $field = HTMLEditorField::create($column);
                        $field = $field->setRows(10)
                            ->setRightTitle('')
                            ->setLeftTitle('')
                            ->setTitle('')
                            ->setFieldHolderTemplate('SilverStripe\\Forms\\FormField_holder_small');
                        $field->getEditorConfig()
                            ->setOptions([
                                'friendly_name' => 'Default CMS',
                                'priority' => '50',
                                'skin' => 'silverstripe',
                                'contextmenu' => "",
                                'use_native_selects' => false,
                                'content_style' => 'body { font-size: 20px; }',
                            ])
                            ->setButtonsForLine(1, [
                                'bold'
                            ])
                            ->setButtonsForLine(2, [])
                            ->setButtonsForLine(3, []);
                        return $field;
                    },
                ],
                'CustomAllGlyphs' => [
                    'title' => 'Custom all glyphs',
                    'callback' => function ($record, $column, $grid) {
                        $field = HTMLEditorField::create($column);
                        $field = $field->setRows(10)
                            ->setRightTitle('')
                            ->setLeftTitle('')
                            ->setTitle('')
                            ->setFieldHolderTemplate('SilverStripe\\Forms\\FormField_holder_small');
                        $field->getEditorConfig()
                            ->setButtonsForLine(1, [
                                'bold'
                            ])
                            ->setButtonsForLine(2, [])
                            ->setButtonsForLine(3, []);

                        return $field;
                    },
                ],
            ]);

            $featuresConfig
                ->addComponent(GridFieldButtonRow::create('before'))
                ->addComponent(GridFieldToolbarHeader::create())
                ->addComponent(GridFieldTitleHeader::create())
                ->addComponent($featuresGridFieldEditableColumns)
                ->addComponent(GridFieldDeleteAction::create(true));

            $featuresConfig->addComponent(GridFieldAddExistingDropdown::create('buttons-before-right')
                ->setSearchList($this->owner->ListDefaultFont()->FontFeatures())
                ->setLabelField('NameInFont')
            );
        }
    }

    public function updateFontFamilyPageCMSFields(FieldList $fields)
    {
        $fields->removeFieldsFromTab('Root', [
            'ListText'
        ]);
    }

    public function visualStyles()
    {
        $list = [];
        if ($this->owner->StylesRow1()->exists()) {
            $list[] = [
                'fontSize' => $this->owner->StylesRow1FontSize,
                'styles' => $this->owner->StylesRow1()->sort('StylesRowSortOrder'),
            ];
        }
        if ($this->owner->StylesRow2()->exists()) {
            $list[] = [
                'fontSize' => $this->owner->StylesRow2FontSize,
                'styles' => $this->owner->StylesRow2()->sort('StylesRowSortOrder'),
            ];
        }
        if ($this->owner->StylesRow3()->exists()) {
            $list[] = [
                'fontSize' => $this->owner->StylesRow3FontSize,
                'styles' => $this->owner->StylesRow3()->sort('StylesRowSortOrder'),
            ];
        }
        return $list;
    }

    public function getFonts(): SS_List
    {
        return $this->owner->FontFamilyNull()?->Fonts() ?? ArrayList::create();
    }
}
