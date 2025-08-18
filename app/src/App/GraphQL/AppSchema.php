<?php

namespace SLONline\App\GraphQL;

use SilverStripe\Core\ClassInfo;
use SilverStripe\GraphQL\Modules\AssetAdmin\Resolvers\AssetAdminResolver;
use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\Interfaces\SchemaUpdater;
use SilverStripe\GraphQL\Schema\Schema;

/**
 * App GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class AppSchema implements SchemaUpdater
{
    /**
     * @throws SchemaBuilderException
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->getConfig()->apply([
            'resolvers' => [
                AssetAdminResolver::class,
            ],
            'modelConfig' => [
                'DataObject' => [
                    'plugins' => [
                        'versioning' => false,
                        'inheritance' => [
                            'useUnionQueries' => false,
                        ],
                    ],
                    'operations' => [
                        'read' => [
                            'plugins' => [
                                'readVersion' => false,
                                'paginateList' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        foreach (ClassInfo::implementorsOf(PartialSchemaUpdater::class) as $updaterClass) {
            $updaterClass::updateSchema($schema);
        }
    }
}
