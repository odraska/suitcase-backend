<?php

namespace SLONline\App\Model\WebsiteBlocks;

use Colymba\BulkUpload\BulkUploader;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Upload;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\HasManyList;
use SLONline\Elefont\Model\WebsiteBlocks\WebsiteBlock;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

/**
 * Images Website Block
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @method HasManyList|ImagesItem Images
 */
class Images extends WebsiteBlock
{
    public const string FOLDER = 'Images';

    private static string $table_name = 'ImagesWebsiteBlock';

    private static string $singular_name = 'Images block';
    private static string $plural_name = 'Images blocks';

    private static array $has_many = [
        'Images' => ImagesItem::class,
    ];

    private static array $owns = [
        'Images',
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
            'Images'
        ]);

        $config = GridFieldConfig_RecordEditor::create()
            ->addComponent(GridFieldSortableRows::create('SortOrder'));

        Folder::find_or_make(self::folder());


        $BulkUploadComponent = new BulkUploader('Image', null, true);
        $BulkUploadComponent
            ->setUfSetup('setFolderName', static::folder())
            ->setAutoPublishDataObject(true);

        $config->addComponent($BulkUploadComponent);

        $fields->addFieldToTab('Root.Main', GridField::create(
            'Images',
            'Images',
            $this->Images(),
            $config
        ));

        return $fields;
    }

    public static function folder(): string
    {
        return File::join_paths(Config::inst()->get(Upload::class, 'uploads_folder'), static::FOLDER);
    }
}
