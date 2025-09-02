<?php

namespace SLONline\App\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\HasManyList;
use SilverStripe\ORM\ManyManyList;
use SLONline\App\Model\FontCategory;
use SLONline\App\Model\FontsInUse;
use SLONline\App\Model\Slide;
use SLONline\ColorField\Form\ColorField;
use SLONline\ColorField\ORM\FieldType\DBColor;

/**
 * Font Family Page Application Extension
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string $FooterButtonBorderColor
 * @property string $FooterButtonTextColor
 * @property string $FooterButtonBackgroundColor
 * @property int $FooterButtonBorderRadius
 * @property bool $ShowInBasicTrial
 * @property bool $ShowInFullTrial
 * @method HasManyList|Slide Slides
 * @method  ManyManyList|FontCategory FontCategories
 * @method  ManyManyList|FontsInUse FontsInUse
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

    private static array $has_many = [
        'Slides' => Slide::class,
    ];

    private static array $many_many = [
        'FontCategories' => FontCategory::class,
    ];

    private static array $belongs_many_many = [
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
        $fields->addFieldsToTab('Root.FooterButton', [
            ColorField::create('FooterButtonBorderColor', 'Border Color'),
            NumericField::create('FooterButtonBorderRadius', 'Border Radius'),
            ColorField::create('FooterButtonTextColor', 'Text Color'),
            ColorField::create('FooterButtonBackgroundColor', 'Background Color'),
        ]);
    }
}
