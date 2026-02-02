<?php

namespace SLONline\App\Model\WebsiteBlocks;

use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use SLONline\App\GraphQL\Schemas\Enums\CropMethodSchema;

/**
 * Images Item Data Object
 *
 * Represents an item in a images website block.
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 *
 * @property int $SortOrder
 * @property string $Link
 * @property string $CropMethod
 * @property int $WebsiteBlockID
 * @property int $ImageID
 * @property int $MobileImageID
 * @method Images WebsiteBlock
 * @method  File Image
 * @method  File MobileImage
 */
class ImagesItem extends DataObject
{
    private static string $table_name = 'ImagesItems';
    private static string $singular_name = 'Image Item';
    private static string $plural_name = 'Image Items';

    private static array $db = [
        'SortOrder' => 'Int',
        'Link' => 'Varchar(1024)',
        'CropMethod' => "Enum('" . CropMethodSchema::CROP_METHOD_COVER . "," . CropMethodSchema::CROP_METHOD_CONTAIN . "', '" . CropMethodSchema::CROP_METHOD_COVER . "')",
    ];

    private static array $has_one = [
        'WebsiteBlock' => Images::class,
        'Image' => Image::class,
        'MobileImage' => Image::class,
    ];

    private static array $summary_fields = [
        'Name' => 'ID',
        'Image.CMSThumbnail' => 'Image',
        'MobileImage.CMSThumbnail' => 'Mobile Image',
        'Link' => 'Link',
        'CropMethod' => 'Crop Method',
    ];

    private static array $extensions = [
        Versioned::class,
    ];

    private static array $owns = [
        'Image',
        'MobileImage',
    ];

    private static array $owned_by = [
        'WebsiteBlock',
    ];

    private static string $default_sort = "SortOrder ASC";

    private static bool $versioned_gridfield_extensions = true;

    public function getName()
    {
        return $this->ID;
    }

    public function mobileImageNull(): ?File
    {
        if ($this->MobileImage()->exists()) {
            return $this->MobileImage();
        }

        return null;
    }

    public function canView($member = null): bool
    {
        return true;
    }

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('WebsiteBlockID');
        $fields->removeByName('SortOrder');

        $fileUploadField = $fields->fieldByName('Root.Main.Image');
        $fileUploadField?->setFolderName($this->WebsiteBlock()::folder());
        $fileUploadField?->getUpload()->setReplaceFile(true);

        $fileUploadField = $fields->fieldByName('Root.Main.MobileImage');
        $fileUploadField?->setFolderName(Slideshow::folder());
        $fileUploadField?->getUpload()->setReplaceFile(true);

        return $fields;
    }
}
