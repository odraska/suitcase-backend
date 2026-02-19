<?php

namespace SLONline\App\Extensions;

use Page;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\ReadonlyField;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

/**
 * Site Config Extension
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 *
 * @property \SilverStripe\SiteConfig\SiteConfig|SiteConfig $owner
 */
class SiteConfig extends Extension
{
    private static array $many_many = [
        'HamburgerMenu' => Page::class,
        'FooterMenu' => Page::class,
    ];

    private static array $many_many_extraFields = [
        'HamburgerMenu' => [
            'HamburgerMenuSortOrder' => 'Int',
            'Divider' => 'Boolean',
        ],
        'FooterMenu' => [
            'FooterMenuSortOrder' => 'Int',
        ],
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        $gridFieldConfig = GridFieldConfig::create();
        $featuresGridFieldEditableColumns = GridFieldEditableColumns::create();
        $featuresGridFieldEditableColumns->setDisplayFields([
            'ID' => [
                'title' => $this->owner->fieldLabel('ID'),
                'field' => ReadonlyField::class,
            ],
            'Title' => [
                'title' => $this->owner->fieldLabel('Title'),
                'field' => ReadonlyField::class,
            ],
            'Divider' => [
                'title' => $this->owner->fieldLabel('Divider'),
                'field' => CheckboxField::class,
            ],
        ]);

        $gridFieldConfig
            ->addComponent(GridFieldButtonRow::create('before'))
            ->addComponent(GridFieldAddExistingAutocompleter::create('buttons-before-right'))
            ->addComponent(GridFieldToolbarHeader::create())
            ->addComponent(GridFieldTitleHeader::create())
            ->addComponent($featuresGridFieldEditableColumns)
            ->addComponent(GridFieldDeleteAction::create(true))
            ->addComponent(GridFieldSortableRows::create('HamburgerMenuSortOrder'));

        $fields->addFieldToTab('Root.HamburgerMenu', GridField::create(
            'HamburgerMenu',
            $this->owner->fieldLabel('HamburgerMenu'),
            $this->owner->HamburgerMenu(),
            $gridFieldConfig
        ));

        $gridFieldConfigFooter = GridFieldConfig_RelationEditor::create();
        $gridFieldConfigFooter->addComponent(GridFieldSortableRows::create('FooterMenuSortOrder'));
        $gridFieldConfigFooter->getComponentByType(GridFieldDataColumns::class)
            ?->setDisplayFields([
                'ID' => $this->owner->fieldLabel('ID'),
                'Title' => $this->owner->fieldLabel('Title'),
            ]);

        $fields->addFieldToTab('Root.FooterMenu', GridField::create(
            'FooterMenu',
            $this->owner->fieldLabel('FooterMenu'),
            $this->owner->FooterMenu(),
            $gridFieldConfigFooter
        ));
    }
}
