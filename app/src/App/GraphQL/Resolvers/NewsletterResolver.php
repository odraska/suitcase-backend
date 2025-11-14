<?php

namespace SLONline\App\GraphQL\Resolvers;

use InvalidArgumentException;
use SLONline\App\GraphQL\CaptchaBotID;
use SLONline\App\Mailchimp;

/**
 * Newsletter GraphQL Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class NewsletterResolver
{
    public static function resolveNewsletterSubscribe($obj, $args, $context, $info)
    {
        if (!CaptchaBotID::singleton()->validateToken($args['captchaToken'])) {
            throw new InvalidArgumentException('Invalid Captcha token', 110);
        }

        try {
            $status = Mailchimp::singleton()->subscribe($args['email']);
        } catch (\Exception $e) {
            $status = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return $status['success'];
    }

    public static function resolveNewsletterUnsubscribe($obj, $args, $context, $info)
    {
        if (!CaptchaBotID::singleton()->validateToken($args['captchaToken'])) {
            throw new InvalidArgumentException('Invalid Captcha token', 110);
        }

        try {
            return Mailchimp::singleton()->unsubscribe($args['email']);
        } catch (\Exception $e) {
            return false;
        }
    }
}
