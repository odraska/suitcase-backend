<?php

namespace SLONline\App\Model;

use Page;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\ManyManyList;

/**
 * Project Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string Annotation
 * @property bool Spotlight
 * @property int CoverImageID
 * @method Image CoverImage()
 * @method ManyManyList|Author Authors()
 */
class ProjectPage extends Page
{
    private static string $table_name = 'ProjectPage';

    private static string $singular_name = 'Project';
    private static string $plural_name = 'Projects';

    private static array $allowed_children = ['none'];

    private static bool $can_be_root = false;

    private static array $db = [
        'Annotation' => 'HTMLText',
        'Spotlight' => 'Boolean',
    ];

    private static array $has_one = [
        'CoverImage' => Image::class,
    ];

    private static array $many_many = [
        'Authors' => Author::class,
    ];

    private static array $owns = [
        'CoverImage',
    ];
}
