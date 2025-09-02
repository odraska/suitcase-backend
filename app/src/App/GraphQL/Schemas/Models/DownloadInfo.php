<?php

namespace SLONline\App\GraphQL\Schemas\Models;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\ModelType;
use SLONline\App\GraphQL\PartialSchemaUpdater;

/**
 * Download Info Data Object GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class DownloadInfo implements PartialSchemaUpdater
{
    public static function updateSchema(Schema $schema): void
    {
        $schema->addModelbyClassName(\SLONline\App\Model\DownloadInfo::class, function (ModelType $model) {
            $model->addFields([
                'id' => [
                    'type' => 'ID!',
                ],
                'type' => [
                    'type' => 'DownloadInfoType!',
                ],
                'created' => [
                    'type' => 'String!',
                ],
                'status' => [
                    'type' => 'DownloadInfoStatus!',
                ],
                'file' => [
                    'type' => 'File',
                ],
                'hash' => [
                    'type' => 'String!',
                ],
            ]);
        });
    }
}
