<?php

namespace SLONline\App\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\ManyManyList;
use SLONline\App\Model\FontCategory;
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
 * @method  ManyManyList|FontCategory FontCategories
 */
class FontFamilyPage extends Extension
{
    private static array $db = [
        'FooterButtonBorderColor' => DBColor::class,
        'FooterButtonTextColor' => DBColor::class,
        'FooterButtonBackgroundColor' => DBColor::class,
        'FooterButtonBorderRadius' => 'Int'
    ];

    private static array $has_many = [
        'Slides' => Slide::class,
    ];

    private static array $many_many = [
        'FontCategories' => FontCategory::class,
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
