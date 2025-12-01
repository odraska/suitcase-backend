<?php

namespace SLONline\App\Model;

use Page;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataList;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

/**
 * About Page Data Object
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property string $AboutText
 * @property string $ContactAddress
 * @property string $ContactSocial
 * @property int $CoverImageID
 * @method Image CoverImage
 */
class AboutPage extends Page
{
    private static string $table_name = 'AboutPage';
    private static string $singular_name = 'About Page';
    private static string $plural_name = 'About Pages';

    private static array $db = [
        'AboutText' => 'Text',
        'ContactAddress' => 'HTMLText',
        'ContactSocial' => 'HTMLText',
    ];

    private static array $has_one = [
        'CoverImage' => Image::class,
    ];

    private static array $has_many = [];

    private static array $owns = [
        'CoverImage',
    ];

    private static array $allowed_children = ['none'];

    public function getTeamMembers(): DataList
    {
        return DataList::create(Author::class)->filter([
            'TeamMember' => true,
        ])->sort('Name', 'ASC');
    }

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Content');

        $fields->fieldByName('Root.Main.ContactAddress')->setEditorConfig('small');
        $fields->fieldByName('Root.Main.ContactSocial')->setEditorConfig('small');


        $fields->addFieldToTab('Root.Awards', GridField::create(
            'Awards',
            'Awards',
            $this->getAwards(),
            GridFieldConfig_RecordEditor::create()
                ->addComponent(GridFieldSortableRows::create('SortOrder'))
        ));

        $fields->addFieldToTab('Root.Press', GridField::create(
            'Press',
            'Press',
            $this->getPress(),
            GridFieldConfig_RecordEditor::create()
                ->addComponent(GridFieldSortableRows::create('SortOrder'))
        ));

        $fields->addFieldToTab('Root.Presentations', GridField::create(
            'Presentations',
            'Presentations',
            $this->getPresentations(),
            GridFieldConfig_RecordEditor::create()
                ->addComponent(GridFieldSortableRows::create('SortOrder'))
        ));

        return $fields;
    }

    public function getAwards(): DataList
    {
        return DataList::create(Award::class);
    }

    public function getPress(): DataList
    {
        return DataList::create(Press::class);
    }

    public function getPresentations(): DataList
    {
        return DataList::create(Presentation::class);
    }
}
