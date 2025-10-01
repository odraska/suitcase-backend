<?php

namespace SLONline\App\GraphQL\Schemas\Enums;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Enum;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\Model\WebsiteBlocks\TypeTesterItem;

/**
 * Type Tester Type Enum GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class TypeTesterTypeSchema implements PartialSchemaUpdater
{

    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addEnum(Enum::create('TypeTesterType', [
            TypeTesterItem::TYPE_SINGLE_WORD,
            TypeTesterItem::TYPE_SENTENCE,
            TypeTesterItem::TYPE_TWO_COLUMNS,
            TypeTesterItem::TYPE_THREE_COLUMNS,
        ]));
    }
}
