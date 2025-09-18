<?php

namespace SLONline\App\Model\WebsiteBlocks;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SLONline\Elefont\Model\WebsiteBlocks\WebsiteBlock;

/**
 * Images Website Block
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class Images extends WebsiteBlock
{
    private static string $table_name = 'ImagesWebsiteBlock';

    private static string $singular_name = 'Images block';
    private static string $plural_name = 'Images blocks';

    private static array $many_many = [
        'Images' => Image::class,
    ];

    private static array $owns = [
        'Images',
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        $fields->removeFieldsFromTab('Root', [
            'Title',
            'SubTitle',
            'SortOrder',
            'Content',
            'Link',
            'BackgroundColor',
            'BackgroundGradientColor',
            'TitleColor',
            'ContentColor',
            'Image',
            'LinkToPageID',
            'BackgroundImage',

        ]);

        return $fields;
    }
}
