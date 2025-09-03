<?php

namespace {

    use SilverStripe\CMS\Model\SiteTree;
    use SilverStripe\Core\Config\Config;
    use SilverStripe\Forms\GridField\GridField;
    use SilverStripe\Forms\GridField\GridFieldAddNewButton;
    use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
    use SilverStripe\Forms\GridField\GridFieldDataColumns;
    use SilverStripe\ORM\HasManyList;
    use SilverStripe\SiteConfig\SiteConfig;
    use SLONline\Elefont\Model\FontFamilyPage;
    use SLONline\Elefont\Model\WebsiteBlocks\WebsiteBlock;
    use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
    use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

    /**
     * Page
     *
     * @author    Lubos Odraska <odraska@slonline.sk>
     * @copyright Copyright (c) 2025, SLONline, s.r.o.
     *
     * @property boolean $ShowInHeader
     * @method HasManyList|WebsiteBlock WebsiteBlocks
     */
    class Page extends SiteTree
    {
        private static array $db = [
            'ShowInHeader' => 'Boolean',
        ];

        private static array $has_one = [];

        private static array $has_many = [
            'WebsiteBlocks' => WebsiteBlock::class . '.Page',
        ];

        public function canView($member = null): bool
        {
            return true;
        }

        public function getCMSFields()
        {
            $fields = parent::getCMSFields();
            $config = GridFieldConfig_RecordEditor::create();
            $config->getComponentByType(GridFieldDataColumns::class)
                ->setDisplayFields(WebsiteBlock::create()->summaryFields());
            $config->addComponent(new GridFieldSortableRows('SortOrder'));

            $websiteBlockClasses = $this->config()->get('allowedWebsiteBlocks', Config::UNINHERITED) ?? [];
            if (empty($websiteBlockClasses)) {
                $websiteBlockClasses = $this->config()->get('allowedWebsiteBlocks') ?? [];
            }

            $config->removeComponentsByType(GridFieldAddNewButton::class)
                ->addComponent(GridFieldAddNewMultiClass::create()
                    ->setClasses($websiteBlockClasses));

            $fields->addFieldToTab('Root.WebsiteBlocks',
                GridField::create('WebsiteBlocks', 'Website blocks', $this->WebsiteBlocks(), $config));

            if (!($this instanceof FontFamilyPage)) {
                $fields->removeByName('Content');
            }

            return $fields;
        }

        public function metaTitle(): string
        {
            return $this->Title . ' | ' . SiteConfig::current_site_config()->Title;
        }
    }
}
