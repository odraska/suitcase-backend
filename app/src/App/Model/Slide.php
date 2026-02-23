<?php

namespace SLONline\App\Model;

use Page;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use SLONline\App\Model\WebsiteBlocks\Slideshow;
use SLONline\Elefont\Model\FontFamilyPage;

/**
 * Slide Data Object
 *
 * Represents a slide in the home page slider.
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string $Title
 * @property int $SortOrder
 * @property string $Link
 * @property string $CropMethod
 * @property int $PageID
 * @property int $ImageID
 * @property int $MobileImageID
 * @property int $VideoID
 * @property int $SlideshowWebsiteBlockID
 *
 * @method  Page Page
 * @method  File Image
 * @method  File MobileImage
 * @method  File Video
 * @method  Slideshow SlideshowWebsiteBlock
 */
class Slide extends DataObject
{
    private static string $table_name = 'Slides';
    private static string $singular_name = 'Slide';
    private static string $plural_name = 'Slides';

    private static array $db = [
        'Title' => 'Varchar(255)',
        'SortOrder' => 'Int',
        'Link' => 'Varchar(255)',
        'CropMethod' => "Enum('Cover,Contain', 'Cover')",
    ];

    private static array $has_one = [
        'Page' => Page::class,
        'Image' => Image::class,
        'MobileImage' => Image::class,
        'Video' => File::class,
        'SlideshowWebsiteBlock' => Slideshow::class,
    ];

    private static array $extensions = [
        Versioned::class,
    ];

    private static array $owns = [
        'Image',
        'MobileImage',
        'Video'
    ];

    private static array $owned_by = [
        'Page',
    ];

    private static array $summary_fields = [
        'ID' => 'ID',
        'Title' => 'Title',
        'Image.CMSThumbnail' => 'Image',
        'MobileImage.CMSThumbnail' => 'Mobile Image',
        'CropMethod' => 'Crop Method',
    ];

    private static string $default_sort = "SortOrder ASC";

    private static bool $versioned_gridfield_extensions = true;

    public function imageNull(): ?File
    {
        if ($this->Image()->exists()) {
            return $this->Image();
        }

        return null;
    }

    public function mobileImageNull(): ?File
    {
        if ($this->MobileImage()->exists()) {
            return $this->MobileImage();
        }

        return null;
    }

    public function videoNull(): ?File
    {
        if ($this->Video()->exists()) {
            return $this->Video();
        }

        return null;
    }

    public function canView($member = null): bool
    {
        return true;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('PageID');
        $fields->removeByName('SortOrder');
        $fields->removeByName('SlideshowWebsiteBlockID');

        if ($this->Page() instanceof FontFamilyPage) {
            $fields->removeByName('Video');
        }

        if ($this->Page() instanceof HomePage) {
            $fields->removeByName('MobileImage');
        }

        return $fields;
    }
}
