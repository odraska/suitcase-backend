<?php

namespace SLONline\App\Extensions;

use SilverStripe\Core\Extension;


/**
 * File Data Object Application Extension
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class File extends Extension
{
    public function onAfterWrite(): void
    {
        $this->getOwner()->publishRecursive();
    }
}
