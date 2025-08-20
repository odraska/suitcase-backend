<?php

namespace SLONline\App\Extensions;

use SilverStripe\Core\Extension;
use SLONline\App\Model\FontCategory;

class FontScriptAdmin extends Extension
{
    private static array $managed_models = [
        FontCategory::class,
    ];
}
