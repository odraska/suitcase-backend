<?php

namespace SLONline\App\GraphQL\Schemas\Enums;

use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Schema\Type\Enum;
use SLONline\App\GraphQL\PartialSchemaUpdater;
use SLONline\App\Model\DownloadInfo;

/**
 * Download Info Type Enum GraphQL Schema Updater
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class DownloadInfoTypeSchema implements PartialSchemaUpdater
{
    public static function updateSchema(Schema $schema): void
    {
        $schema->addEnum(Enum::create('DownloadInfoType', [
            DownloadInfo::TYPE_FULL,
            DownloadInfo::TYPE_DESKTOP,
            DownloadInfo::TYPE_WEBFONT,
            DownloadInfo::TYPE_BASIC_TRIAL,
            DownloadInfo::TYPE_FULL_TRIAL,
        ]));
    }
}
