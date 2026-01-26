<?php

namespace SLONline\App\Model;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;

/**
 * Project Specification Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 *
 * @property string Title
 * @property string Value
 * @property int SortOrder
 * @property int ProjectPageID
 * @method ProjectPage ProjectPage()
 */
class ProjectSpecification extends DataObject
{
    private static string $table_name = 'ProjectSpecification';

    private static string $singular_name = 'Project Specification';
    private static string $plural_name = 'Project Specifications';

    private static array $db = [
        'Title' => 'Varchar(255)',
        'Value' => 'Varchar(255)',
        'SortOrder' => 'Int',
    ];

    private static array $has_one = [
        'ProjectPage' => ProjectPage::class,
    ];

    private static array $default_sort = ['SortOrder' => 'ASC'];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('SortOrder');
        $fields->removeByName('ProjectPageID');
        return $fields;
    }

    public function canView($member = null): bool
    {
        return true;
    }
}
