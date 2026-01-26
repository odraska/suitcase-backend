<?php

namespace SLONline\App\Model;

use Page;
use SilverStripe\Assets\Image;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\ManyManyList;
use SLONline\EditorJSField\Forms\EditorJSField;

/**
 * Project Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string Annotation
 * @property string ContentJS
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
        'ContentJS' => 'Text',
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

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Content');
        $fields->addFieldToTab('Root.Main',
            EditorJSField::create('ContentJS', $this->fieldLabel('Content'), $this->ContentJS)
        );

        return $fields;
    }

    public function contentBlocks(): ArrayList
    {
        return EditorJSField::decodeData($this->ContentJS ?? '');
    }
}
