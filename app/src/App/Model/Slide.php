<?php

namespace SLONline\App\Model;

use SilverStripe\Assets\File;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;

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
 * @property int $HomePageID
 * @property int $ImageID
 * @property int $VideoID
 * @method  HomePage HomePage
 * @method  File Image
 * @method  File Video
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
    ];

    private static array $has_one = [
        'HomePage' => HomePage::class,
        'Image' => File::class,
        'Video' => File::class,
    ];

    private static array $extensions = [
        Versioned::class,
    ];

    private static array $owns = [
        'Image',
        'Video'
    ];

    private static array $owned_by = [
        'HomePage',
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
        $fields->removeByName('HomePageID');
        $fields->removeByName('SortOrder');
        return $fields;
    }
}
