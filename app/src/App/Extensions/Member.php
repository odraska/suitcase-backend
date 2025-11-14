<?php

namespace SLONline\App\Extensions;

use SilverStripe\Core\Extension;
use SLONline\App\Mailchimp;

/**
 * Member Extension for Mailchimp integration
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 *
 * @property \SilverStripe\Security\Member|Member $owner
 */
class Member extends Extension
{
    public function newsletterSubscribed(): bool
    {
        if ($this->owner->Email) {
            return Mailchimp::singleton()->isSubscribed($this->owner->Email) ||
                Mailchimp::singleton()->isPending($this->owner->Email);
        }

        return false;
    }
}
