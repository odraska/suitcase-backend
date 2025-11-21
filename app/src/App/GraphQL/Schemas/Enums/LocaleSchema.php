<?php

namespace SLONline\App\GraphQL\Schemas\Enums;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Enum;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Locale Enum GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class LocaleSchema implements PartialSchemaUpdater
{
    const array ALLOWED_LOCALES = ['en_US', 'cs_CZ'];

    public static function updateSchema(Schema $schema): void
    {
        $schema->addEnum(Enum::create('Locale', self::ALLOWED_LOCALES));
    }
}
