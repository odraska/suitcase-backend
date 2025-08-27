<?php

namespace SLONline\App\Model;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\SearchableMultiDropdownField;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\Versioned\Versioned;
use SLONline\Elefont\Model\FontFamilyPage;

/**
 * Fonts In Use Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string $Title
 * @property string $Author
 * @property int $SortOrder
 * @property bool $Spotlight
 * @property int $ImageID
 * @method Image Image
 * @method ManyManyList|FontFamilyPage FontFamilyPages()
 */
class FontsInUse extends DataObject
{
    private static string $table_name = 'FontsInUse';
    private static string $singular_name = 'Fonts In Use Item';
    private static string $plural_name = 'Fonts In Use Items';

    private static array $db = [
        'Title' => 'Varchar(1000)',
        'Author' => 'Varchar(1000)',
        'SortOrder' => 'Int',
        'Spotlight' => 'Boolean',
    ];

    private static array $has_one = [
        'Image' => Image::class,
    ];

    private static array $many_many = [
        'FontFamilyPages' => FontFamilyPage::class,
    ];

    private static array $owns = [
        'Image',
    ];

    private static array $extensions = [
        Versioned::class,
    ];

    private static array $default_sort = ['SortOrder' => 'ASC'];

    private static bool $versioned_gridfield_extensions = true;

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('SortOrder');

        $fields->replaceField('FontFamilyPages', SearchableMultiDropdownField::create(
            'FontFamilyPages',
            $this->fieldLabel('FontFamilyPages'),
            DataList::create(FontFamilyPage::class),
            $this->FontFamilyPages()
        ));

        return $fields;
    }

    public function canView($member = null): bool
    {
        return true;
    }

    public function getFontsInUseAuthor(): ?string
    {
        return $this->Author;
    }
}
