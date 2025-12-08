<?php

namespace SLONline\App\GraphQL\Schemas\Enums;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Enum;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Currency Enum GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class CurrencySchema implements PartialSchemaUpdater
{
    const array ALLOWED_CURRENCIES = ['EUR', 'USD', 'CZK'];

    public static function updateSchema(Schema $schema): void
    {
        $schema->addEnum(Enum::create('Currency', self::ALLOWED_CURRENCIES));
    }
}
