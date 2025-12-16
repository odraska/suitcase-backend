<?php

namespace SLONline\App\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ManyManyList;

/**
 * Font Feature Extension
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string $Example
 * @property string $AllGlyphs
 * @property   \SLONline\Elefont\Model\FontFeature|FontFeature owner
 * @method ManyManyList|\SLONline\Elefont\Model\FontFamilyPage FontFamilyPages()
 */
class FontFeature extends Extension
{
    private static array $db = [
        'Example' => 'HTMLText',
        'AllGlyphs' => 'HTMLText',
    ];

    private static array $belongs_many_many = [
        'FontFamilyPages' => \SLONline\Elefont\Model\FontFamilyPage::class,
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        $fields->findTab('Root.Main')
            ->FieldList()
            ->changeFieldOrder(['Name', 'Code', 'Example', 'AllGlyphs']);

        $fields->fieldByName('Root.Main.Example')
            ->setRows(2)
            ->getEditorConfig()
            ->setButtonsForLine(1, [
                'bold'
            ])
            ->setButtonsForLine(2, [])
            ->setButtonsForLine(3, []);

        $fields->fieldByName('Root.Main.AllGlyphs')
            ->setRows(2)
            ->getEditorConfig()
            ->setButtonsForLine(1, [
                'bold'
            ])
            ->setButtonsForLine(2, [])
            ->setButtonsForLine(3, []);
    }

    public function Example()
    {
        if ($this->owner->CustomExample) {
            return $this->owner->CustomExample;
        }

        return $this->owner->Example;
    }

    public function allGlyphs()
    {
        if ($this->owner->CustomAllGlyphs) {
            return $this->owner->CustomAllGlyphs;
        }

        return $this->owner->AllGlyphs;
    }
}
