<?php

namespace SLONline\App\Model;

use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\Upload;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\SearchableMultiDropdownField;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\Parsers\URLSegmentFilter;
use SLONline\App\TinyMCE\SmallTinyMCEConfig;
use SLONline\Elefont\Model\FontFamilyPage;

/**
 * Author Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string $Name
 * @property string $UrlSegment
 * @property string $Bio
 * @property bool $TeamMember
 * @property int $PhotoID
 * @method Image $Photo
 * @method ManyManyList|FontFamilyPage FontFamilyPages
 * @method ManyManyList|ArticlePage ArticlePages
 * @method ManyManyList|ProjectPage ProjectPages
 */
class Author extends DataObject
{
    const string FOLDER = 'authors';

    private static string $table_name = 'Authors';
    private static string $singular_name = 'Author';
    private static string $plural_name = 'Authors';

    private static bool $can_be_root = false;

    private static array $db = [
        'Name' => 'Varchar(255)',
        'UrlSegment' => 'Varchar(255)',
        'Bio' => 'HTMLText',
        'TeamMember' => 'Boolean',
    ];

    private static array $has_one = [
        'Photo' => Image::class,
    ];

    private static array $many_many = [
        'FontFamilyPages' => FontFamilyPage::class,
    ];

    private static array $belongs_many_many = [
        'ArticlePages' => ArticlePage::class,
        'ProjectPages' => ProjectPage::class,
    ];

    private static array $owns = [
        'Photo'
    ];

    private static array $cascade_deletes = [
        'Photo',
    ];

    private static array $summary_fields = [
        'ID',
        'Name',
    ];

    private static array $extensions = [
        Versioned::class,
    ];

    private static string $default_sort = 'Name ASC';

    private static bool $versioned_gridfield_extensions = true;

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        $fileUploadField = $fields->fieldByName('Root.Main.Photo');
        $fileUploadField?->setFolderName(self::folder());
        $fileUploadField?->getUpload()->setReplaceFile(true);

        $fields->fieldByName('Root.Main.Bio')->setEditorConfig('small');

        $fields->replaceField('FontFamilyPages', SearchableMultiDropdownField::create(
            'FontFamilyPages',
            $this->fieldLabel('FontFamilyPages'),
            DataList::create(FontFamilyPage::class),
            $this->FontFamilyPages()
        ));

        return $fields;
    }

    public static function folder(): string
    {
        return File::join_paths(Config::inst()->get(Upload::class, 'uploads_folder'), self::FOLDER);
    }

    public function canView($member = null): bool
    {
        return true;
    }

    protected function onBeforeWrite(): void
    {
        parent::onBeforeWrite();

        if (!$this->UrlSegment) {
            $this->UrlSegment = URLSegmentFilter::create()->filter($this->Name);
        }
    }
}
