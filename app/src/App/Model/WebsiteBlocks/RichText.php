<?php

namespace SLONline\App\Model\WebsiteBlocks;

use SilverStripe\Forms\FieldList;
use SLONline\Elefont\Model\WebsiteBlocks\WebsiteBlock;

/**
 * Rich Text Website Block
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class RichText extends WebsiteBlock
{
    private static string $table_name = 'RichTextWebsiteBlock';

    private static string $singular_name = 'Rich Text Website block';
    private static string $plural_name = 'Rich Text Website blocks';

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        $fields->removeFieldsFromTab('Root', [
            'Title',
            'SubTitle',
            'SortOrder',
            'Link',
            'BackgroundColor',
            'BackgroundGradientColor',
            'TitleColor',
            'ContentColor',
            'Image',
            'LinkToPageID',
            'BackgroundImage',
            'Images'
        ]);

        $fields->fieldByName('Root.Main.Content')?->setEditorConfig('small');

        return $fields;
    }
}
