<?php

namespace SLONline\App\GraphQL\Schemas\Enums;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Enum;
use SLONline\App\Extensions\FontFamilyPage;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Font Family Page Visual Styles Font Size Enum GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class FontFamilyPageVisualStylesSizeSchema implements PartialSchemaUpdater
{
    public static function updateSchema(Schema $schema): void
    {
        $schema->addEnum(Enum::create('FontFamilyPageVisualStylesSize', [
            FontFamilyPage::STYLES_ROW_FONT_SIZE_SMALL,
            FontFamilyPage::STYLES_ROW_FONT_SIZE_MEDIUM,
            FontFamilyPage::STYLES_ROW_FONT_SIZE_LARGE,
        ]));
    }
}
