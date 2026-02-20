<?php

namespace SLONline\App\Model;

use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\Upload;
use SilverStripe\Core\Config\Config;
use SilverStripe\AssetAdmin\Forms\UploadField;
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
 * @method ManyManyList|Image Images
 * @method ManyManyList|FontFamilyPage FontFamilyPages()
 */
class FontsInUse extends DataObject
{
    public const string FOLDER = 'fontsinuse';
    private static string $table_name = 'FontsInUse';
    private static string $singular_name = 'Fonts In Use Item';
    private static string $plural_name = 'Fonts In Use Items';

    private static array $db = [
        'Title' => 'Varchar(1000)',
        'Author' => 'Varchar(1000)',
        'SortOrder' => 'Int',
        'Spotlight' => 'Boolean',
    ];

    private static array $has_one = [];

    private static array $many_many = [
        'Images' => Image::class,
        'FontFamilyPages' => FontFamilyPage::class,
    ];

    private static array $many_many_extraFields = [
        'Images' => [
            'SortOrder' => 'Int'
        ],
    ];

    private static array $owns = [
        'Images',
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
        $fields->removeByName('Images');

        $imagesField = UploadField::create('Images', 'Images')
            ->setFolderName(static::folder())
            ->setAllowedFileCategories('image');

        $fields->addFieldToTab('Root.Main', $imagesField);

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

    public static function folder(): string
    {
        return File::join_paths(Config::inst()->get(Upload::class, 'uploads_folder'), static::FOLDER);
    }
}
