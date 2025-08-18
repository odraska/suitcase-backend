<?php

namespace SLONline\App\GraphQL;

use SilverStripe\GraphQL\Schema\Schema;

/**
 * Implementors of this interface can make a one-time, context-free partial update to the schema,
 * This interface is used in @AppSchema updater
 * e.g. adding a shared Enum type
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
*/
interface PartialSchemaUpdater
{
    /**
     * @param Schema $schema
     */
    public static function updateSchema(Schema $schema): void;
}
