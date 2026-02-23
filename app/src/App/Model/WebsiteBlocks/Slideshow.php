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
use SLONline\App\Model\Slide;
use SLONline\Elefont\Model\WebsiteBlocks\WebsiteBlock;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

/**
 * Slideshow Website Block
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 *
 * @method HasManyList|Slide Slides
 */
class Slideshow extends WebsiteBlock
{
    public const string FOLDER = 'Slideshows';
    private static string $table_name = 'SlideshowWebsiteBlock';

    private static string $singular_name = 'Slideshow block';
    private static string $plural_name = 'Slideshow blocks';

    private static array $has_many = [
        'Slides' => Slide::class,
    ];

    private static array $owns = [
        'Slides',
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
            'Slides'
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
            'Slides',
            'Slides',
            $this->Slides(),
            $config
        ));

        return $fields;
    }

    public static function folder(): string
    {
        return File::join_paths(Config::inst()->get(Upload::class, 'uploads_folder'), static::FOLDER);
    }
}
