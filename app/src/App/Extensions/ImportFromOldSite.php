<?php

namespace SLONline\App\Extensions;

use SilverStripe\Core\Extension;

class ImportFromOldSite extends Extension
{
    private static array $db = [
        'OldID' => 'Int',
    ];

    private static array $summary_fields = [
        'OldID',
    ];
}
