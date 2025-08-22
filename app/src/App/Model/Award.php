<?php

namespace SLONline\App\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;


/**
 * Award Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string $Title
 * @property string $Year
 * @property string $Url
 * @property int $SortOrder
 */
class Award extends DataObject
{
    private static string $table_name = 'Award';
    private static string $singular_name = 'Award';
    private static string $plural_name = 'Awards';

    private static array $db = [
        'Title' => 'Varchar(255)',
        'Year' => 'Varchar(50)',
        'Url' => 'Varchar(1000)',
        'SortOrder' => 'Int',
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
        return $fields;
    }

    public function canView($member = null): bool
    {
        return true;
    }
}
