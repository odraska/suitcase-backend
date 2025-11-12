<?php

namespace SLONline\App\GraphQL;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;

/**
 * GraphQL Captcha Vercel BotID Validator
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class CaptchaBotID
{
    use Configurable;
    use Injectable;

    private static string $secret_key = '';

    public function validateToken(string $token): bool
    {
        return $token == self::config()->get('secret_key');
    }
}
