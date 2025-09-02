<?php

namespace SLONline\App\GraphQL\Schemas\Enums;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Enum;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\Model\DownloadInfo;

/**
 * Download Info Status Enum GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class DownloadInfoStatusSchema implements PartialSchemaUpdater
{
    /**
     * @inheritDoc
     */
    public static function updateSchema(Schema $schema): void
    {
        $schema->addEnum(Enum::create('DownloadInfoStatus', [
            DownloadInfo::STATUS_COMPLETED,
            DownloadInfo::STATUS_FAILED,
            DownloadInfo::STATUS_PENDING,
            DownloadInfo::STATUS_GENERATING,
        ]));
    }
}
